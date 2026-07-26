<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUsuarioIdToClientes extends Migration
{
    public function up()
    {
        $fields = [
            'usuario_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'empresa_id',
            ],
        ];

        // 1. Adiciona a coluna na tabela clients
        $this->forge->addColumn('clients', $fields);

        // 2. Adiciona a Foreign Key em uma instrução SQL direta para evitar falhas silenciosas do Forge
        $this->db->query("
            ALTER TABLE `clients` 
            ADD CONSTRAINT `fk_clients_usuario` 
            FOREIGN KEY (`usuario_id`) REFERENCES `users`(`id`) 
            ON DELETE SET NULL ON UPDATE CASCADE;
        ");
    }

    public function down()
    {
        // Remove a Foreign Key e depois a coluna
        $this->db->query("ALTER TABLE `clients` DROP FOREIGN KEY `fk_clients_usuario`;");
        $this->forge->dropColumn('clients', 'usuario_id');
    }
}