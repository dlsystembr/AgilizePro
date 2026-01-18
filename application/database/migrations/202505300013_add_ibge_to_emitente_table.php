<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_ibge_to_emitente_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('emitente', [
            'ibge' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'default' => null,
            ],
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('emitente', 'ibge');
    }
} 