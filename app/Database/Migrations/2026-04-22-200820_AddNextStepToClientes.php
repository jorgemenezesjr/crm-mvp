<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNextStepToClients extends Migration{
    
    public function up()
    {
        $fields = [
            'next_step_desc' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'valor', // Ajuste conforme sua tabela
            ],
            'next_step_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'after'      => 'next_step_desc',
            ],
        ];
        $this->forge->addColumn('clients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('clients', ['next_step_desc', 'next_step_at']);
    }

}