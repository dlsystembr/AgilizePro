<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_tpc_id_to_clientes extends CI_Migration
{
    public function up()
    {
        $fields = [
            'tpc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'pes_id'
            ],
        ];
        $this->dbforge->add_column('clientes', $fields);

        // Adicionar FK
        $this->db->query('ALTER TABLE `clientes` ADD CONSTRAINT `fk_clientes_tipos` FOREIGN KEY (`tpc_id`) REFERENCES `TIPOS_CLIENTES` (`tpc_id`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `clientes` DROP FOREIGN KEY `fk_clientes_tipos`');
        $this->dbforge->drop_column('clientes', 'tpc_id');
    }
}
