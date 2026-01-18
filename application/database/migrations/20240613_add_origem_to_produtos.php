<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_origem_to_produtos extends CI_Migration {
    public function up()
    {
        $fields = [
            'origem' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 0,
                'comment' => '0-Nacional, 1-Importação direta, 2-Mercado interno, 3-Nacional+70%, 4-Nacional PPB, 5-Nacional até 40%, 6-Importação sem similar, 7-Importação sem similar mercado, 8-Nacional+70%',
                'after' => 'entrada'
            ]
        ];
        $this->dbforge->add_column('produtos', $fields);
    }

    public function down()
    {
        $this->dbforge->drop_column('produtos', 'origem');
    }
} F