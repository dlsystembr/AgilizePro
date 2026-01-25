<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_finalidade_to_classificacao_fiscal extends CI_Migration
{
    public function up()
    {
        $fields = [
            'clf_finalidade' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
                'after' => 'clf_objetivo_comercial',
                'comment' => 'Finalidade da classificação fiscal (ex: Comercialização, Consumo, Serviço)'
            ]
        ];

        $this->dbforge->add_column('classificacao_fiscal', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('classificacao_fiscal', 'clf_finalidade');
    }
}
