<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_marcas_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'mrc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'mrc_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ],
            'mrc_descricao' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'mrc_status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false
            ],
            'mrc_data_cadastro' => [
                'type' => 'DATETIME',
                'null' => false
            ],
            'mrc_data_alteracao' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'mrc_usuario_cadastro' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'mrc_usuario_alteracao' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true
            ]
        ]);

        $this->dbforge->add_key('mrc_id', true);
        $this->dbforge->create_table('marcas');
    }

    public function down()
    {
        $this->dbforge->drop_table('marcas');
    }
} 