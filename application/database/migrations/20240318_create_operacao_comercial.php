<?php

class Migration_Create_operacao_comercial extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => FALSE
            ],
            'cfop' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => FALSE
            ],
            'destino' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'null' => FALSE
            ],
            'cst' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE
            ],
            'csosn' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => TRUE
            ],
            'mensagem_nota' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);
        
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('operacao_comercial');
    }

    public function down()
    {
        $this->dbforge->drop_table('operacao_comercial');
    }
} 