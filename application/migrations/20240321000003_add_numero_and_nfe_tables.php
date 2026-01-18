<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_numero_and_nfe_tables extends CI_Migration
{
    public function up()
    {
        // Adiciona a coluna numero se não existir
        if (!$this->db->field_exists('numero', 'emitente')) {
            $this->dbforge->add_column('emitente', [
                'numero' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                    'after' => 'rua'
                ]
            ]);
        }

        // Cria a tabela nfe_emitidas se não existir
        if (!$this->db->table_exists('nfe_emitidas')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'venda_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true
                ],
                'numero_nfe' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'chave_nfe' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20
                ],
                'xml' => [
                    'type' => 'LONGTEXT'
                ],
                'protocolo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50
                ],
                'motivo' => [
                    'type' => 'TEXT',
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
            $this->dbforge->add_key('venda_id');
            $this->dbforge->create_table('nfe_emitidas');
        }

        // Cria a tabela nfe_certificates se não existir
        if (!$this->db->table_exists('nfe_certificates')) {
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'certificado_digital' => [
                    'type' => 'LONGBLOB',
                    'null' => false
                ],
                'senha_certificado' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false
                ],
                'data_validade' => [
                    'type' => 'DATE',
                    'null' => false
                ],
                'nome_certificado' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => false
                ],
                'created_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'CURRENT_TIMESTAMP'
                ],
                'updated_at' => [
                    'type' => 'TIMESTAMP',
                    'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'
                ]
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->create_table('nfe_certificates');
        }
    }

    public function down()
    {
        // Remove a coluna numero
        if ($this->db->field_exists('numero', 'emitente')) {
            $this->dbforge->drop_column('emitente', 'numero');
        }

        // Remove as tabelas se existirem
        if ($this->db->table_exists('nfe_emitidas')) {
            $this->dbforge->drop_table('nfe_emitidas');
        }

        if ($this->db->table_exists('nfe_certificates')) {
            $this->dbforge->drop_table('nfe_certificates');
        }
    }
} 