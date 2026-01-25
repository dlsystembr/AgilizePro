<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_faturamento_entrada_structure extends CI_Migration
{
    public function up()
    {
        // Verifica se a tabela faturamento_entrada existe
        if (!$this->db->table_exists('faturamento_entrada')) {
            // Cria a tabela se não existir
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'operacao_comercial_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false
                ],
                'fornecedor_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false
                ],
                'chave_acesso' => [
                    'type' => 'VARCHAR',
                    'constraint' => 44,
                    'null' => true
                ],
                'numero_nota' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => false
                ],
                'data_entrada' => [
                    'type' => 'date',
                    'null' => false
                ],
                'data_emissao' => [
                    'type' => 'date',
                    'null' => false
                ],
                'valor_total' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_produtos' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_icms' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_ipi' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_frete' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_outras_despesas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'total_base_icms' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'total_base_icms_st' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'total_icms_st' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'observacoes' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'default' => 'pendente'
                ],
                'data_fechamento' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'data_cadastro' => [
                    'type' => 'DATETIME',
                    'null' => false
                ],
                'data_atualizacao' => [
                    'type' => 'DATETIME',
                    'null' => true
                ],
                'usuario_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false
                ],
                'xml_conteudo' => [
                    'type' => 'TEXT',
                    'null' => true
                ],
                'emitida_nfe' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0
                ]
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->add_field('CONSTRAINT fk_faturamento_entrada_operacao_comercial FOREIGN KEY (operacao_comercial_id) REFERENCES operacao_comercial(id) ON DELETE NO ACTION ON UPDATE NO ACTION');
            $this->dbforge->add_field('CONSTRAINT fk_faturamento_entrada_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES clientes(idClientes) ON DELETE NO ACTION ON UPDATE NO ACTION');
            $this->dbforge->add_field('CONSTRAINT fk_faturamento_entrada_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(idUsuarios) ON DELETE NO ACTION ON UPDATE NO ACTION');
            
            $this->dbforge->create_table('faturamento_entrada');
        }

        // Verifica se a tabela faturamento_entrada_itens existe
        if (!$this->db->table_exists('faturamento_entrada_itens')) {
            // Cria a tabela se não existir
            $this->dbforge->add_field([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true
                ],
                'faturamento_entrada_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false
                ],
                'produto_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => false
                ],
                'quantidade' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => false
                ],
                'valor_unitario' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'null' => false
                ],
                'desconto' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'base_calculo_icms' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'aliquota_icms' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_icms' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'base_calculo_icms_st' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'aliquota_icms_st' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_icms_st' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'valor_ipi' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'total_item' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'default' => 0.00
                ],
                'cst' => [
                    'type' => 'VARCHAR',
                    'constraint' => 2,
                    'null' => false
                ],
                'cfop' => [
                    'type' => 'VARCHAR',
                    'constraint' => 4,
                    'null' => false
                ]
            ]);

            $this->dbforge->add_key('id', true);
            $this->dbforge->add_field('CONSTRAINT fk_faturamento_entrada_itens_faturamento_entrada FOREIGN KEY (faturamento_entrada_id) REFERENCES faturamento_entrada(id) ON DELETE CASCADE ON UPDATE NO ACTION');
            $this->dbforge->add_field('CONSTRAINT fk_faturamento_entrada_itens_produto FOREIGN KEY (produto_id) REFERENCES produtos(idProdutos) ON DELETE NO ACTION ON UPDATE NO ACTION');
            
            $this->dbforge->create_table('faturamento_entrada_itens');
        }

        // Adiciona índices para melhorar a performance
        $this->db->query('ALTER TABLE faturamento_entrada ADD INDEX idx_operacao_comercial (operacao_comercial_id)');
        $this->db->query('ALTER TABLE faturamento_entrada ADD INDEX idx_fornecedor (fornecedor_id)');
        $this->db->query('ALTER TABLE faturamento_entrada ADD INDEX idx_usuario (usuario_id)');
        $this->db->query('ALTER TABLE faturamento_entrada ADD INDEX idx_data_entrada (data_entrada)');
        $this->db->query('ALTER TABLE faturamento_entrada ADD INDEX idx_status (status)');
        
        $this->db->query('ALTER TABLE faturamento_entrada_itens ADD INDEX idx_faturamento_entrada (faturamento_entrada_id)');
        $this->db->query('ALTER TABLE faturamento_entrada_itens ADD INDEX idx_produto (produto_id)');
    }

    public function down()
    {
        // Remove os índices
        $this->db->query('ALTER TABLE faturamento_entrada DROP INDEX idx_operacao_comercial');
        $this->db->query('ALTER TABLE faturamento_entrada DROP INDEX idx_fornecedor');
        $this->db->query('ALTER TABLE faturamento_entrada DROP INDEX idx_usuario');
        $this->db->query('ALTER TABLE faturamento_entrada DROP INDEX idx_data_entrada');
        $this->db->query('ALTER TABLE faturamento_entrada DROP INDEX idx_status');
        
        $this->db->query('ALTER TABLE faturamento_entrada_itens DROP INDEX idx_faturamento_entrada');
        $this->db->query('ALTER TABLE faturamento_entrada_itens DROP INDEX idx_produto');

        // Remove as tabelas
        $this->dbforge->drop_table('faturamento_entrada_itens');
        $this->dbforge->drop_table('faturamento_entrada');
    }
} 