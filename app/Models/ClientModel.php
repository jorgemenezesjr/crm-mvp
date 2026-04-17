<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';      // Nome da tabela no banco
    protected $primaryKey       = 'id';           // Chave primária
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';        // Vamos trabalhar com arrays simples
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    // IMPORTANTÍSSIMO: Liste os campos que podem ser gravados no banco
    protected $allowedFields    = ['empresa_id','nome', 'email', 'telefone', 'status'];
    protected $beforeInsert     = ['injectEmpresaId'];

    
    
    // Datas automáticas
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    
    
    protected function injectEmpresaId(array $data)
    {
        // Se o usuário estiver logado, injeta o ID da empresa dele nos dados
        if (auth()->loggedIn()) {
            $data['data']['empresa_id'] = auth()->user()->empresa_id;
        }
        
    return $data;
    }
    
    
    public function getPerCompany($empresa_id)
    {
        // Isso garante que SEMPRE haverá um WHERE empresa_id = X nas consultas
        return $this->where('empresa_id', $empresa_id)->findAll();
    }
    
    public function findForCompany($id, $empresa_id)
    {
        // Tenta achar o cliente com o ID informado E que pertença à empresa logada
        return $this->where([
            'id'         => $id,
            'empresa_id' => $empresa_id
        ])->first();
    }
    
}