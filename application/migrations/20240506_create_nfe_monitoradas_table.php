<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_nfe_monitoradas_table extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'chave_acesso' => [
                'type' => 'VARCHAR',
                'constraint' => 44,
                'unique' => true
            ],
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => 20
            ],
            'serie' => [
                'type' => 'VARCHAR',
                'constraint' => 10
            ],
            'fornecedor' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],
            'data_emissao' => [
                'type' => 'date'
            ],
            'valor' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2'
            ],
            'xml' => [
                'type' => 'LONGTEXT'
            ],
            'processada' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('nfe_monitoradas');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_monitoradas');
    }
} 