<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_finalidade_to_produtos extends CI_Migration
{
    public function up()
    {
        $fields = [
            'PRO_FINALIDADE' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'PRO_TIPO',
                'comment' => 'Finalidade do produto (ex: comercializacao, consumo)'
            ]
        ];

        $this->dbforge->add_column('produtos', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('produtos', 'PRO_FINALIDADE');
    }
}
