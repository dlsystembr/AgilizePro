<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_tipo_clientes_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'TPC_ID' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'TPC_NOME' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'TPC_CODIGO_CLIENTE' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'TPC_DATA_CADASTRO' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->dbforge->add_key('TPC_ID', true);
        $this->dbforge->create_table('tipos_clientes');
    }

    public function down()
    {
        $this->dbforge->drop_table('tipos_clientes');
    }
}
