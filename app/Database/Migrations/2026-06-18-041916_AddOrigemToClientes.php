<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrigemToClientes extends Migration
{
    public function up()
    {
        $fields = [
            'origem' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'Não Informado',
                'after'      => 'status' // Posiciona após a coluna status
            ],
        ];
        
        $this->forge->addColumn('clients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('clients', 'origem');
    }
}