<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeToClientsLogs extends Migration
{
    public function up()
    {
        $fields = [
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'system',
                'after'      => 'usuario_id', // Tenta colocar após o user_id para organização
            ],
        ];
        $this->forge->addColumn('client_logs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('client_logs', 'type');
    }
}