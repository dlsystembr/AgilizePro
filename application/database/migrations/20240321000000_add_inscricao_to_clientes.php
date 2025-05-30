<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_inscricao_to_clientes extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('clientes', [
            'inscricao' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'documento'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('clientes', 'inscricao');
    }
} 