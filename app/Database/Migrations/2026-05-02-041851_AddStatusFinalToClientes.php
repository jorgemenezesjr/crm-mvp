<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusFinalToClientes extends Migration
{
    public function up()
    {
        $fields = [
            'status_final' => [
                'type'       => 'ENUM',
                'constraint' => ['aberto', 'ganho', 'perdido'],
                'default'    => 'aberto',
                'after'      => 'status' // Coloca logo após a coluna de status do Kanban
            ],
            'motivo_perda' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'after'      => 'status_final'
            ],
            'finalizado_em' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'motivo_perda'
            ],
        ];

        $this->forge->addColumn('clients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('clients', ['status_final', 'motivo_perda', 'finalizado_em']);
    }
}