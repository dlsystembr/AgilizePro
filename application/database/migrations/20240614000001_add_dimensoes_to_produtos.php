<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_dimensoes_to_produtos extends CI_Migration {
    public function up()
    {
        $fields = [
            'peso_bruto' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => TRUE,
                'comment' => 'Peso bruto do produto em kg',
                'after' => 'NCMs'
            ],
            'peso_liquido' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => TRUE,
                'comment' => 'Peso líquido do produto em kg',
                'after' => 'peso_bruto'
            ],
            'largura' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'comment' => 'Largura do produto em cm',
                'after' => 'peso_liquido'
            ],
            'altura' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'comment' => 'Altura do produto em cm',
                'after' => 'largura'
            ],
            'comprimento' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => TRUE,
                'comment' => 'Comprimento do produto em cm',
                'after' => 'altura'
            ]
        ];
        $this->dbforge->add_column('produtos', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('produtos', 'peso_bruto');
        $this->dbforge->drop_column('produtos', 'peso_liquido');
        $this->dbforge->drop_column('produtos', 'largura');
        $this->dbforge->drop_column('produtos', 'altura');
        $this->dbforge->drop_column('produtos', 'comprimento');
    }
} 