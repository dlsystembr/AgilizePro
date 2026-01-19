<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_tpc_id_to_clientes extends CI_Migration
{
    public function up()
    {
        $fields = [
            'TPC_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'PES_ID'
            ],
        ];
        $this->dbforge->add_column('clientes', $fields);

        // Adicionar FK
        $this->db->query('ALTER TABLE `clientes` ADD CONSTRAINT `fk_clientes_tipos` FOREIGN KEY (`TPC_ID`) REFERENCES `TIPOS_CLIENTES` (`TPC_ID`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `clientes` DROP FOREIGN KEY `fk_clientes_tipos`');
        $this->dbforge->drop_column('clientes', 'TPC_ID');
    }
}
