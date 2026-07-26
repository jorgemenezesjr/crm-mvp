<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClientModel; 
use App\Models\ClientLogModel;

class Clients extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Traz os clientes juntando com a tabela de usuários para capturar o nome do responsável
        $data['clientes'] = $db->table('clients')
            ->select('clients.*, users.username as responsable_nome')
            ->join('users', 'users.id = clients.usuario_id', 'left')
            ->where('clients.empresa_id', $this->empresa_id)
            ->get()
            ->getResultArray();

        $data['titulo'] = "Meus Clientes";

        return view('admin/clients_list_view', $data);
    }
    
    public function kanban()
    {
        $model = new \App\Models\ClientModel();

        // CORREÇÃO DA QUERY + JOIN COM VENDEDOR:
        $clientes = $model->select('clients.*, users.username as responsable_nome')
                          ->join('users', 'users.id = clients.usuario_id', 'left')
                          ->where('clients.empresa_id', $this->empresa_id)
                          ->groupStart()
                              ->where('clients.status_final', 'aberto')
                              ->orWhere('clients.status_final', 'ganho')
                          ->groupEnd()
                          ->findAll();

        $data['titulo'] = "Fluxo de Vendas";

        $totais = [
            'lead'       => 0,
            'proposta'   => 0,
            'negociacao' => 0,
            'fechado'    => 0
        ];

        foreach ($clientes as $c) {
            if (isset($totais[$c['status']])) {
                $totais[$c['status']] += $c['valor'];
            }
        }

        $data['clientes'] = $clientes;
        $data['totais']   = $totais;

        return view('admin/kanban_view', $data);
    }
    
    public function create()
    {
        $db = \Config\Database::connect();

        // Busca usuários da mesma empresa para exibir no select do formulário
        $data['vendedores'] = $db->table('users')
                                ->where('empresa_id', $this->empresa_id)
                                ->get()
                                ->getResultArray();

        return view('admin/clients_form_view', $data);
    }

    public function store()
    {
        $model = new ClientModel();

        $valorRaw   = $this->request->getPost('valor'); 
        $valorLimpo = $this->formatValue($valorRaw);
        
        $dados = [
            'empresa_id' => $this->empresa_id,
            'usuario_id' => $this->request->getPost('usuario_id') ?: null, // Captura o vendedor selecionado
            'nome'       => $this->request->getPost('nome'),
            'email'      => $this->request->getPost('email'),
            'telefone'   => $this->request->getPost('telefone'),
            'status'     => $this->request->getPost('status'),
            'origem'     => $this->request->getPost('origem') ?? 'Não Informado',
            'valor'      => $valorLimpo
        ];

        if ($model->save($dados)) {
            return redirect()->to('/admin/clientes')->with('msg', 'Cliente salvo com sucesso!');
        }
    }
    
    public function edit($id)
    {
        $model = new \App\Models\ClientModel();
        $db    = \Config\Database::connect();

        $cliente = $model->findForCompany($id, $this->empresa_id);

        if (!$cliente) {
            return redirect()->to('/admin/clientes')->with('error', 'Cliente não encontrado.');
        }

        $data['cliente']    = $cliente;
        // Passa a lista de vendedores para carregar no select da tela de edição
        $data['vendedores'] = $db->table('users')
                                ->where('empresa_id', $this->empresa_id)
                                ->get()
                                ->getResultArray();

        return view('admin/clients_edit_view', $data);
    }

    public function update($id)
    {
        $model = new \App\Models\ClientModel();

        $clienteExistente = $model->where(['id' => $id, 'empresa_id' => $this->empresa_id])->first();

        if (!$clienteExistente) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $dadosParaAtualizar = $this->request->getPost();

        // Garante a gravação dos campos formatados e seguros
        $dadosParaAtualizar['empresa_id'] = $this->empresa_id;
        $dadosParaAtualizar['usuario_id'] = $this->request->getPost('usuario_id') ?: null;
        $dadosParaAtualizar['valor']      = $this->formatValue($this->request->getPost('valor'));

        $model->update($id, $dadosParaAtualizar);

        return redirect()->to('/admin/clientes')->with('message', 'Atualizado com sucesso!');
    }
    
    public function updateStatus()
    {
        $json = $this->request->getJSON();

        if ($json) {
            $model = new \App\Models\ClientModel();

            $clienteAntigo = $model->find($json->id);
            $statusAntigo  = $clienteAntigo['status'] ?? 'desconhecido';

            if ($model->update($json->id, ['status' => $json->status])) {

                $logModel = new \App\Models\ClientLogModel();

                $logModel->save([
                    'client_id'  => $json->id,
                    'usuario_id' => auth()->id(), 
                    'empresa_id' => auth()->user()->empresa_id,
                    'type'       => 'system',
                    'acao'       => "Alterou status de [{$statusAntigo}] para [{$json->status}]",
                ]);

                return $this->response->setJSON(['status' => 'success']);
            }           
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Falha ao atualizar'], 400);
    }
    
    public function delete($id)
    {
        $model = new \App\Models\ClientModel();

        $cliente = $model->where([
            'id'         => $id, 
            'empresa_id' => $this->empresa_id
        ])->first();

        if (!$cliente) {
            return redirect()->to('/admin/clientes')
                             ->with('error', 'Operação inválida ou cliente não encontrado.');
        }

        $model->delete($id);

        return redirect()->to('/admin/clientes')
                         ->with('message', 'Cliente removido com sucesso!');
    }
   
    public function historico($id)
    {
        try {
            $db = \Config\Database::connect();

            $builder = $db->table('client_logs');
            $builder->select('client_logs.acao, client_logs.created_at, users.username as usuario_nome');
            $builder->join('users', 'users.id = client_logs.usuario_id', 'left');
            $builder->where('client_id', $id);
            $builder->orderBy('client_logs.created_at', 'DESC');
            $logs = $builder->get()->getResultArray();

            $cliente = $db->table('clients')
                          ->select('next_step_desc, next_step_at')
                          ->where('id', $id)
                          ->get()
                          ->getRowArray();

            return $this->response->setJSON([
                'logs'           => $logs ?: [],
                'next_step_desc' => $cliente['next_step_desc'] ?? null,
                'next_step_at'   => $cliente['next_step_at']   ?? null
            ]);

        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
    
    public function addNota()
    {
        $usuarioId = user_id();
        
        if (!$usuarioId) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Usuário não autenticado pelo Shield.'
            ])->setStatusCode(401);
        }   

        $clienteId = $this->request->getPost('cliente_id');
        $mensagem  = $this->request->getPost('mensagem');
        
        if (!$clienteId || !$mensagem) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Dados incompletos']);
        }

        $db = \Config\Database::connect();

        $data = [
            'client_id'  => $clienteId,
            'usuario_id' => $usuarioId,
            'acao'       => $mensagem,
            'type'       => 'manual',
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            $db->table('client_logs')->insert($data);

            return $this->response->setJSON([
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function setNextStep() 
    {
        $id   = $this->request->getPost('id');
        $desc = $this->request->getPost('desc');
        $date = $this->request->getPost('date');

        $db = \Config\Database::connect();
        $db->table('clients')->where('id', $id)->update([
            'next_step_desc' => $desc,
            'next_step_at'   => $date
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    public function completeNextStep() 
    {
        $id = $this->request->getPost('id');
        $db = \Config\Database::connect();

        $cliente = $db->table('clients')->where('id', $id)->get()->getRow();

        if ($cliente && $cliente->next_step_desc) {
            $db->table('client_logs')->insert([
                'client_id'  => $id,
                'usuario_id' => user_id(),
                'acao'       => "✅ Tarefa Concluída: " . $cliente->next_step_desc,
                'type'       => 'manual',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $db->table('clients')->where('id', $id)->update([
                'next_step_desc' => null,
                'next_step_at'   => null
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }
    
    private function formatValue($value) 
    {
        return str_replace(',', '.', str_replace('.', '', $value));
    }
    
    public function finalizar()
    {
        $id          = $this->request->getPost('id');
        $statusFinal = $this->request->getPost('status_final');
        $motivo      = $this->request->getPost('motivo');
        $usuarioId   = session()->get('user_id') ?? 1; 

        $db = \Config\Database::connect();

        $novoStatusPipeline = ($statusFinal === 'ganho') ? 'fechado' : 'perdido';

        $upd = $db->table('clients')->update([
            'status'        => $novoStatusPipeline,
            'status_final'  => $statusFinal,
            'motivo_perda'  => $motivo,
            'finalizado_em' => date('Y-m-d H:i:s')
        ], ['id' => $id]);

        if (!$upd) {
            $err = $db->error();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro no Update: ' . $err['message']]);
        }

        $ins = $db->table('client_logs')->insert([
            'client_id'  => $id,
            'usuario_id' => $usuarioId,
            'acao'       => "Finalizado como $statusFinal" . ($motivo ? " (Motivo: $motivo)" : ""),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if (!$ins) {
            $err = $db->error();
            return $this->response->setJSON(['status' => 'error', 'message' => 'Erro no Log: ' . $err['message']]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }
}