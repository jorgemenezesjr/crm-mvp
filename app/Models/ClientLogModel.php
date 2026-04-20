<?php
namespace App\Models;
use CodeIgniter\Model;

class ClientLogModel extends Model {
    protected $table = 'client_logs';
    protected $allowedFields = ['client_id', 'usuario_id', 'acao', 'data_criacao'];
}