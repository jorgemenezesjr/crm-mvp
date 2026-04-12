<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientsTable extends Migration
{
    public function up()
{
    $this->forge->addField([
        'id' => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
        'nome' => [
            'type'       => 'VARCHAR',
            'constraint' => '100',
        ],
        'email' => [
            'type'       => 'VARCHAR',
            'constraint' => '100',
            'null'       => true,
        ],
        'telefone' => [
            'type'       => 'VARCHAR',
            'constraint' => '20',
            'null'       => true,
        ],
        'status' => [
            'type'       => 'ENUM',
            'constraint' => ['ativo', 'inativo'],
            'default'    => 'ativo',
        ],
        'created_at' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
        'updated_at' => [
            'type' => 'DATETIME',
            'null' => true,
        ],
    ]);

    $this->forge->addKey('id', true);
    $this->forge->createTable('clients');
}

public function down()
{
    $this->forge->dropTable('clients');
}

  
}