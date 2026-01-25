<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_tipo_clientes_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'tpc_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'tpc_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'tpc_codigo_cliente' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'tpc_data_cadastro' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->dbforge->add_key('tpc_id', true);
        $this->dbforge->create_table('tipos_clientes');
    }

    public function down()
    {
        $this->dbforge->drop_table('tipos_clientes');
    }
}
