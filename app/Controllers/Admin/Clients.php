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

        // Pegamos os dados do formulário
        $dados = [
            'nome'     => $this->request->getPost('nome'),
            'email'    => $this->request->getPost('email'),
            'telefone' => $this->request->getPost('telefone'),
            'status'   => $this->request->getPost('status'),
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
    
}