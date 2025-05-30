<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_nfe_emitidas_table extends CI_Migration
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
            'venda_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true
            ],
            'numero_nfe' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'chave_nfe' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'xml' => [
                'type' => 'LONGTEXT'
            ],
            'protocolo' => [
                'type' => 'VARCHAR',
                'constraint' => 50
            ],
            'motivo' => [
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
        $this->dbforge->add_key('venda_id');
        $this->dbforge->create_table('nfe_emitidas');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_emitidas');
    }
} 