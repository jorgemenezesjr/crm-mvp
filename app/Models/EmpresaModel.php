<?php

namespace App\Models;

use CodeIgniter\Model;

class EmpresaModel extends Model
{
    protected $table            = 'empresas';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['nome_fantasia', 'razao_social', 'cnpj'];
    protected $useTimestamps    = true; // Ativa o preenchimento automático de created_at e updated_at
}