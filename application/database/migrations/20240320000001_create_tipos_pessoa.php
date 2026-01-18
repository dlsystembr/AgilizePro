<?php

class Migration_create_tipos_pessoa extends CI_Migration
{
    public function up()
    {
        // Criar tabela tipos_pessoa
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'ativo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->dbforge->add_key('id', true);
        $this->dbforge->create_table('tipos_pessoa');

        // Criar tabela pessoa_tipos
        $this->dbforge->add_field([
            'pessoa_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tipo_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'data_cadastro' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);
        
        $this->dbforge->add_key(['pessoa_id', 'tipo_id'], true);
        $this->dbforge->add_field('CONSTRAINT fk_pessoa_tipos_pessoa FOREIGN KEY (pessoa_id) REFERENCES clientes(idClientes) ON DELETE CASCADE');
        $this->dbforge->add_field('CONSTRAINT fk_pessoa_tipos_tipo FOREIGN KEY (tipo_id) REFERENCES tipos_pessoa(id) ON DELETE CASCADE');
        $this->dbforge->create_table('pessoa_tipos');

        // Inserir tipos padrão
        $tipos_padrao = [
            ['nome' => 'Cliente', 'descricao' => 'Pessoa que realiza compras ou utiliza serviços'],
            ['nome' => 'Fornecedor', 'descricao' => 'Pessoa que fornece produtos ou serviços'],
            ['nome' => 'Funcionário', 'descricao' => 'Pessoa que trabalha na empresa'],
            ['nome' => 'Técnico', 'descricao' => 'Pessoa que realiza serviços técnicos'],
            ['nome' => 'Usuário', 'descricao' => 'Pessoa que tem acesso ao sistema']
        ];

        foreach ($tipos_padrao as $tipo) {
            $this->db->insert('tipos_pessoa', $tipo);
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('pessoa_tipos');
        $this->dbforge->drop_table('tipos_pessoa');
    }
} 