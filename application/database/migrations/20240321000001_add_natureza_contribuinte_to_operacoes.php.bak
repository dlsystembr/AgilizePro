<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_natureza_contribuinte_to_operacoes extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('operacao_comercial', [
            'natureza_contribuinte' => [
                'type' => 'ENUM("inscrito", "nao_inscrito")',
                'null' => false,
                'default' => 'nao_inscrito',
                'after' => 'destino'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('operacao_comercial', 'natureza_contribuinte');
    }
} 