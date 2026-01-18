<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_tributacao_produto_id_to_produtos extends CI_Migration {
    public function up()
    {
        $fields = [
            'tributacao_produto_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE,
                'after' => 'entrada'
            ]
        ];
        $this->dbforge->add_column('produtos', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('produtos', 'tributacao_produto_id');
    }
} 