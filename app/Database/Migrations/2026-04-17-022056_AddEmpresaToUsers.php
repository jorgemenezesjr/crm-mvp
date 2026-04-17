<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'empresa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id', // Organização: coloca logo após o ID do usuário
            ],
        ];

        // O comando addColumn traduz para "ALTER TABLE users ADD COLUMN..."
        $this->forge->addColumn('users', $fields);
        
        // Opcional: Adicionar uma chave estrangeira para integridade
        // $this->forge->addForeignKey('empresa_id', 'empresas', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'empresa_id');
    }
}