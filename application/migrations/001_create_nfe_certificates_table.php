<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_nfe_certificates_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'certificado_digital' => [
                'type' => 'LONGBLOB',
                'null' => false
            ],
            'senha_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'data_validade' => [
                'type' => 'DATE',
                'null' => false
            ],
            'nome_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP'
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('nfe_certificates');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_certificates');
    }
} 