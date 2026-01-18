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
            'mrc_status' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false
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