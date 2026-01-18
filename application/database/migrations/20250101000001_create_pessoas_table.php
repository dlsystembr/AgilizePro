<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_pessoas_table extends CI_Migration
{
    public function up()
    {
        // Criar tabela pessoas
        $this->dbforge->add_field([
            'pes_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pes_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'pes_cpf_cnpj' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
            ],
            'pes_tipo_pessoa' => [
                'type' => 'ENUM',
                'constraint' => ['F', 'J'],
                'default' => 'F',
                'comment' => 'F=Física, J=Jurídica',
            ],
            'pes_inscricao_estadual' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'pes_inscricao_municipal' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'pes_telefone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'pes_celular' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'pes_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'pes_senha' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'pes_contato' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'pes_cep' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'pes_logradouro' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'pes_numero' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'pes_complemento' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pes_bairro' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pes_cidade' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'pes_estado' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
                'null' => true,
            ],
            'pes_ibge' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'pes_observacoes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'pes_situacao' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => '1=Ativo, 0=Inativo',
            ],
            'pes_data_cadastro' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'pes_data_atualizacao' => [
                'type' => 'DATETIME',
                'null' => true,
                'on_update' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        
        $this->dbforge->add_key('pes_id', true);
        $this->dbforge->create_table('pessoas');

        // Criar tabela pessoa_tipos para relacionar pessoas com tipos
        $this->dbforge->add_field([
            'pt_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pt_pessoa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'pt_tipo_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'pt_data_cadastro' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        
        $this->dbforge->add_key('pt_id', true);
        $this->dbforge->add_field('CONSTRAINT fk_pessoa_tipos_pessoa FOREIGN KEY (pt_pessoa_id) REFERENCES pessoas(pes_id) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT fk_pessoa_tipos_tipo FOREIGN KEY (pt_tipo_id) REFERENCES tipos_pessoa(id) ON DELETE CASCADE');
        $this->dbforge->create_table('pessoa_tipos');

        // Criar índices para melhor performance
        $this->db->query('CREATE INDEX idx_pessoas_cpf_cnpj ON pessoas(pes_cpf_cnpj)');
        $this->db->query('CREATE INDEX idx_pessoas_email ON pessoas(pes_email)');
        $this->db->query('CREATE INDEX idx_pessoas_nome ON pessoas(pes_nome)');
        $this->db->query('CREATE INDEX idx_pessoa_tipos_pessoa ON pessoa_tipos(pt_pessoa_id)');
        $this->db->query('CREATE INDEX idx_pessoa_tipos_tipo ON pessoa_tipos(pt_tipo_id)');
    }

    public function down()
    {
        $this->dbforge->drop_table('pessoa_tipos');
        $this->dbforge->drop_table('pessoas');
    }
} 