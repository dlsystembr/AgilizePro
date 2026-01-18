<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_natureza_contribuinte_to_clientes extends CI_Migration
{
    public function up()
    {
        if (!$this->db->field_exists('natureza_contribuinte', 'clientes')) {
            $this->dbforge->add_column('clientes', [
                'natureza_contribuinte' => [
                    'type' => 'ENUM',
                    'constraint' => ['inscrito', 'nao_inscrito'],
                    'null' => true,
                    'after' => 'inscricao'
                ]
            ]);
        }
    }

    public function down()
    {
        $this->dbforge->drop_column('clientes', 'natureza_contribuinte');
    }
} 