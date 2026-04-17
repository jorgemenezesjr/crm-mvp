<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmpresaToClientes extends Migration
{
    public function up()
    {
        $fields = [
            'empresa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Deixamos true por enquanto para não dar erro em registros já existentes
                'after'      => 'id',
            ],
        ];

        // IMPORTANTE: Verifique se o nome da sua tabela é 'clientes' ou 'clients'
        $this->forge->addColumn('clients', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('clients', 'empresa_id');
    }
}