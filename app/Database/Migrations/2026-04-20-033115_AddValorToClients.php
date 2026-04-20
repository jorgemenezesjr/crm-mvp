<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValorToClients extends Migration
{
    public function up() {
        $this->forge->addColumn('clients', [
            'valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'after'      => 'status'
            ],
        ]);
    }
    public function down() { 
        $this->forge->dropColumn('clients', 'valor'); 
    }

}
