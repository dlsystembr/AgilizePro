<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_nfe_documentos_table extends CI_Migration
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
            'nfe_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'tipo' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'justificativa' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'protocolo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'data_evento' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'xml' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('nfe_id');
        $this->dbforge->create_table('nfe_documentos');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_documentos');
    }
} 