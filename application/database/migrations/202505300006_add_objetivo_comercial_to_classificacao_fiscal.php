<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_objetivo_comercial_to_classificacao_fiscal extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_column('classificacao_fiscal', [
            'objetivo_comercial' => [
                'type' => 'ENUM',
                'constraint' => ['consumo', 'revenda'],
                'null' => false,
                'default' => 'consumo'
            ]
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_column('classificacao_fiscal', 'objetivo_comercial');
    }
} 