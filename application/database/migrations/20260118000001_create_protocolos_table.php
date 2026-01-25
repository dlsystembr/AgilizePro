<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_protocolos_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'prt_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nfc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'prt_numero_protocolo' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => false,
            ],
            'prt_tipo' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'prt_motivo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'prt_data' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->dbforge->add_key('prt_id', true);
        $this->dbforge->add_key('nfc_id');
        $this->dbforge->create_table('protocolos');
        $this->db->query('ALTER TABLE `protocolos` ADD CONSTRAINT `fk_protocolos_nfecom` FOREIGN KEY (`nfc_id`) REFERENCES `nfecom_capa` (`nfc_id`) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `protocolos` DROP FOREIGN KEY `fk_protocolos_nfecom`');
        $this->dbforge->drop_table('protocolos');
    }
}

