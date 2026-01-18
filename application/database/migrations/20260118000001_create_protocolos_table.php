<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_protocolos_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'PRT_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'NFC_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'PRT_NUMERO_PROTOCOLO' => [
                'type' => 'VARCHAR',
                'constraint' => 60,
                'null' => false,
            ],
            'PRT_TIPO' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => false,
            ],
            'PRT_MOTIVO' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'PRT_DATA' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->dbforge->add_key('PRT_ID', true);
        $this->dbforge->add_key('NFC_ID');
        $this->dbforge->create_table('protocolos');
        $this->db->query('ALTER TABLE `protocolos` ADD CONSTRAINT `fk_protocolos_nfecom` FOREIGN KEY (`NFC_ID`) REFERENCES `nfecom_capa` (`NFC_ID`) ON DELETE CASCADE ON UPDATE CASCADE');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `protocolos` DROP FOREIGN KEY `fk_protocolos_nfecom`');
        $this->dbforge->drop_table('protocolos');
    }
}

