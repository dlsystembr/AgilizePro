<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_finalidade_to_classificacao_fiscal extends CI_Migration
{
    public function up()
    {
        $fields = [
            'CLF_FINALIDADE' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'CLF_OBJETIVO_COMERCIAL',
                'comment' => 'Finalidade da classificação fiscal (ex: Comercialização, Consumo, Serviço)'
            ]
        ];

        $this->dbforge->add_column('classificacao_fiscal', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('classificacao_fiscal', 'CLF_FINALIDADE');
    }
}
