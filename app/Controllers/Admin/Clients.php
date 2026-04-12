<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClientModel; // Importamos o modelo que você criou

class Clients extends BaseController
{
    public function index()
    {
        $model = new ClientModel();
        
        // Buscamos todos os clientes do banco
        $data['clientes'] = $model->findAll();
        $data['titulo']   = "Meus Clientes";

        return view('admin/clients_list_view', $data);
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
        $model = new ClientModel();
        $data['cliente'] = $model->find($id); // Busca o cliente específico

        if (!$data['cliente']) {
            return redirect()->to('/admin/clientes')->with('msg', 'Cliente não encontrado!');
        }

        return view('admin/clients_edit_view', $data);
    }

    
   public function update($id = null)
    {
        $model = new \App\Models\ClientModel();
        $data = $this->request->getPost();

        if ($model->update($id, $data)) {
            return redirect()->to('/admin/clientes')->with('success', 'Cliente atualizado com sucesso!');
        }
    }
    
    
    public function delete($id)
{
    $model = new ClientModel();
    
    // Verifica se o cliente existe antes de deletar
    if ($model->find($id)) {
        $model->delete($id);
        return redirect()->to('/admin/clientes')->with('msg', 'Cliente removido com sucesso!');
    }

    return redirect()->to('/admin/clientes')->with('msg', 'Erro ao tentar excluir o cliente.');
}
    
}