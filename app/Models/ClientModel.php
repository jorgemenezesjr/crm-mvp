<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'clients';      // Nome da tabela no banco
    protected $primaryKey       = 'id';           // Chave primária
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';        // Vamos trabalhar com arrays simples
    protected $useSoftDeletes   = false;

    // IMPORTANTÍSSIMO: Liste os campos que podem ser gravados no banco
    protected $allowedFields    = ['nome', 'email', 'telefone', 'status'];

    // Datas automáticas
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
}