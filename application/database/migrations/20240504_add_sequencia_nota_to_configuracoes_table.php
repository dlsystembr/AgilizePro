<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_add_sequencia_nota_to_configuracoes_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('configuracoes', [
            'sequencia_nota' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 1,
                'after' => 'orientacao_danfe'
            ],
            'sequencia_nfce' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 1,
                'after' => 'csc_id'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('configuracoes', 'sequencia_nota');
        $this->dbforge->drop_column('configuracoes', 'sequencia_nfce');
    }
} 