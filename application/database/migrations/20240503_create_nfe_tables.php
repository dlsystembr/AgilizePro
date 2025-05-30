<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_nfe_tables extends CI_Migration
{
    public function up()
    {
        // Tabela de certificados
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'certificado_digital' => [
                'type' => 'LONGBLOB',
                'null' => FALSE
            ],
            'senha_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'data_validade' => [
                'type' => 'DATE',
                'null' => TRUE
            ],
            'nome_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'emissor_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'proprietario_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE
            ],
            'cnpj_certificado' => [
                'type' => 'VARCHAR',
                'constraint' => '14',
                'null' => TRUE
            ],
            'valido_de' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'valido_ate' => [
                'type' => 'DATETIME',
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
        $this->dbforge->create_table('nfe_certificates');

        // Create nfe_configurations table
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'ambiente' => [
                'type' => 'ENUM',
                'constraint' => ['1', '2'],
                'default' => '2'
            ],
            'uf' => [
                'type' => 'VARCHAR',
                'constraint' => 2
            ],
            'csc' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'csc_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'created_at' => [
                'type' => 'DATETIME'
            ],
            'updated_at' => [
                'type' => 'DATETIME'
            ]
        ]);
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('nfe_configurations');
    }

    public function down()
    {
        $this->dbforge->drop_table('nfe_certificates');
        $this->dbforge->drop_table('nfe_configurations');
    }
} 