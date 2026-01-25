<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_ncms_table extends CI_Migration
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
            'codigo' => [
                'type' => 'VARCHAR',
                'constraint' => 8,
                'null' => false
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => false
            ],
            'data_inicio' => [
                'type' => 'date',
                'null' => true
            ],
            'data_fim' => [
                'type' => 'date',
                'null' => true
            ],
            'tipo_ato' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'numero_ato' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'ano_ato' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
                'null' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->dbforge->add_key('id', true);
        $this->dbforge->add_key('codigo');
        $this->dbforge->create_table('ncms');
    }

    public function down()
    {
        $this->dbforge->drop_table('ncms');
    }
} 