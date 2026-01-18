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
            'cliente' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'produto' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2'
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'pendente'
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ]
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('nfe_emitidas');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_emitidas');
    }
} 