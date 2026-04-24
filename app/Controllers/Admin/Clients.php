<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClientModel; 
use App\Models\ClientLogModel;

class Clients extends BaseController
{
    public function index()
    {
        $model = new ClientModel();
        
        // Buscamos todos os clientes do banco
        //$data['clientes'] = $model->findAll();
        // Em vez de $model->findAll(), usamos o nosso novo filtro
        // Lembra que o $this->empresa_id vem lá do BaseController que configuramos?
        $data['clientes'] = $model->getPerCompany($this->empresa_id);
        $data['titulo']   = "Meus Clientes";

        return view('admin/clients_list_view', $data);
    }
    
    
    
    public function kanban()
    {
        $model = new \App\Models\ClientModel();

        // Lembra da nossa segurança de ontem? 
        // Filtramos pela empresa para ninguém ver o card do outro!
        $clientes = $data['clientes'] = $model->where('empresa_id', $this->empresa_id)->findAll();
        $data['titulo'] = "Fluxo de Vendas";
        
        
        $totais = [
            'lead'       => 0,
            'proposta'   => 0,
            'negociacao' => 0,
            'fechado'    => 0
        ];

        foreach ($clientes as $c) {
            $totais[$c['status']] += $c['valor'];
        }
        
        $data['clientes'] = $clientes;
        $data['totais'] = $totais;

        
        return view('admin/kanban_view', $data);
    }
    
    
    public function create()
    {
        return view('admin/clients_form_view'); // Vamos criar essa view agora
    }

    
    public function store()
    {
        $model = new ClientModel();

        // Pega o valor que veio do formulário com máscara
        $valorRaw = $this->request->getPost('valor'); 

        // Limpeza: Remove o ponto de milhar e troca a vírgula por ponto decimal
        $valorLimpo = $this->formatValue($valorRaw);
        
        // Pegamos os dados do formulário
        $dados = [
            'nome'     => $this->request->getPost('nome'),
            'email'    => $this->request->getPost('email'),
            'telefone' => $this->request->getPost('telefone'),
            'status'   => $this->request->getPost('status'),
            'valor'    => $valorLimpo
        ];

        // O Model salva automaticamente no banco
        if ($model->save($dados)) {
            return redirect()->to('/admin/clientes')->with('msg', 'Cliente salvo com sucesso!');
        }
    }
    
    public function edit($id)
    {
        $model = new \App\Models\ClientModel();

        // Busca garantindo que o cliente pertence à empresa do usuário logado
        $cliente = $model->findForCompany($id, $this->empresa_id);

        if (!$cliente) {
            // Se não achou, redireciona com erro (segurança!)
            return redirect()->to('/admin/clientes')->with('error', 'Cliente não encontrado.');
        }

        $data['cliente'] = $cliente;
        return view('admin/clients_form_view', $data);
    }

    
   public function update($id)
    {
        $model = new \App\Models\ClientModel();

        // Primeiro, garante que esse cara pode editar esse ID
        $clienteExistente = $model->where(['id' => $id, 'empresa_id' => $this->empresa_id])->first();

        if (!$clienteExistente) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        // Pega os dados do POST
        $dadosParaAtualizar = $this->request->getPost();

        // FORÇA o empresa_id da sessão, ignorando qualquer coisa que venha do form
        $dadosParaAtualizar['empresa_id'] = $this->empresa_id;
        $dadosParaAtualizar['valor'] = $this->formatValue($this->empresa_id);

        $model->update($id, $dadosParaAtualizar);

        return redirect()->to('/admin/clientes')->with('message', 'Atualizado com sucesso!');
    }
    
    
    public function updateStatus()
    {
        $json = $this->request->getJSON();

        if ($json) {
            $model = new \App\Models\ClientModel();

            // 1. Buscar o registro ATUAL antes de mudar
            $clienteAntigo = $model->find($json->id);
            $statusAntigo = $clienteAntigo['status'] ?? 'desconhecido';

            // 2. Tentar atualizar para o novo status
            if ($model->update($json->id, ['status' => $json->status])) {

                $logModel = new \App\Models\ClientLogModel();

                // 3. Gravar o log com o histórico completo
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

        // 1. Tenta localizar o cliente garantindo que ele pertence à empresa do usuário
        $cliente = $model->where([
            'id'         => $id, 
            'empresa_id' => $this->empresa_id
        ])->first();

        // 2. Se não encontrar (ou se for de outra empresa), bloqueia a ação
        if (!$cliente) {
            return redirect()->to('/admin/clientes')
                             ->with('error', 'Operação inválida ou cliente não encontrado.');
        }

        // 3. Se passou no teste, agora sim deleta
        $model->delete($id);

        return redirect()->to('/admin/clientes')
                         ->with('message', 'Cliente removido com sucesso!');
    }
    
   
    public function historico($id)
    {
        try {
            $db = \Config\Database::connect();

            // 1. Busca os logs
            $builder = $db->table('client_logs');
            $builder->select('client_logs.acao, client_logs.created_at, users.username as usuario_nome');
            $builder->join('users', 'users.id = client_logs.usuario_id', 'left');
            $builder->where('client_id', $id);
            $builder->orderBy('client_logs.created_at', 'DESC');
            $logs = $builder->get()->getResultArray();

            // 2. Busca os dados do cliente (evitando erro se o cliente não existir)
            $cliente = $db->table('clients')
                          ->select('next_step_desc, next_step_at')
                          ->where('id', $id)
                          ->get()
                          ->getRowArray();

            // 3. Retorna o objeto estruturado
            return $this->response->setJSON([
                'logs'           => $logs ?: [], // Se for nulo, retorna array vazio
                'next_step_desc' => $cliente['next_step_desc'] ?? null,
                'next_step_at'   => $cliente['next_step_at']   ?? null
            ]);

        } catch (\Exception $e) {
            // Se der erro, avisa o que foi
            return $this->response->setStatusCode(500)->setJSON(['error' => $e->getMessage()]);
        }
    }
    
    
    
    public function addNota()
    {
        // Pega o ID do usuário logado na sessão (ajuste conforme seu sistema de login)
        $usuarioId = user_id();
        
        if (!$usuarioId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuário não autenticado pelo Shield.'
            ])->setStatusCode(401);
        }   

        // Captura os dados enviados pelo fetch
        $clienteId = $this->request->getPost('cliente_id');
        $mensagem  = $this->request->getPost('mensagem');
        
        if (!$clienteId || !$mensagem) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Dados incompletos']);
        }

        $db = \Config\Database::connect();

        // Dados para inserir
        $data = [
            'client_id'  => $clienteId,
            'usuario_id' => $usuarioId,
            'acao'       => $mensagem, // Aqui salvamos o texto da nota como uma "ação"
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
    
    
   // Salva ou atualiza o próximo passo
    public function setNextStep() {
        $id = $this->request->getPost('id');
        $desc = $this->request->getPost('desc');
        $date = $this->request->getPost('date');

        $db = \Config\Database::connect();
        $db->table('clients')->where('id', $id)->update([
            'next_step_desc' => $desc,
            'next_step_at'   => $date
        ]);

        return $this->response->setJSON(['status' => 'success']);
    }

    // Conclui a tarefa e joga para o log
    public function completeNextStep() {
        $id = $this->request->getPost('id');
        $db = \Config\Database::connect();

        // 1. Pega os dados atuais antes de apagar
        $cliente = $db->table('clients')->where('id', $id)->get()->getRow();

        if ($cliente && $cliente->next_step_desc) {
            // 2. Registra no histórico (logs)
            $db->table('client_logs')->insert([
                'client_id'  => $id,
                'usuario_id'    => user_id(),
                'acao'     => "✅ Tarefa Concluída: " . $cliente->next_step_desc,
                'type'       => 'manual',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            // 3. Limpa o agendamento no cliente
            $db->table('clients')->where('id', $id)->update([
                'next_step_desc' => null,
                'next_step_at'   => null
            ]);
        }

        return $this->response->setJSON(['status' => 'success']);
    }
    
    
    private function formatValue($value) {
        // Remove o ponto de milhar e troca a vírgula decimal por ponto
        return str_replace(',', '.', str_replace('.', '', $value));
    }
    
}