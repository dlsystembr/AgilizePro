<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_nfe_nsu_table extends CI_Migration
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
            'ult_nsu' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false
            ],
            'max_nsu' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false
            ],
            'data_consulta' => [
                'type' => 'DATETIME',
                'null' => false
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
        $this->dbforge->create_table('nfe_nsu');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_nsu');
    }
} 