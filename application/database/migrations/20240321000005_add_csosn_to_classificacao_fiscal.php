<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_csosn_to_classificacao_fiscal extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('classificacao_fiscal', [
            'csosn' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('classificacao_fiscal', 'csosn');
    }
} 