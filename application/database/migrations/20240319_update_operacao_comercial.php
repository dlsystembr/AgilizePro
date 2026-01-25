<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_operacao_comercial extends CI_Migration
{
    public function up()
    {
        // Primeiro, fazer backup dos dados existentes
        $this->db->query('CREATE TABLE IF NOT EXISTS operacao_comercial_backup AS SELECT * FROM operacao_comercial');

        // Remover colunas antigas
        $this->dbforge->drop_column('operacao_comercial', 'nome');
        $this->dbforge->drop_column('operacao_comercial', 'descricao');
        $this->dbforge->drop_column('operacao_comercial', 'cfop');
        $this->dbforge->drop_column('operacao_comercial', 'destinacao');
        $this->dbforge->drop_column('operacao_comercial', 'cst');
        $this->dbforge->drop_column('operacao_comercial', 'csosn');
        $this->dbforge->drop_column('operacao_comercial', 'mensagem_nota_fiscal');

        // Adicionar novas colunas
        $fields = [
            'opc_sigla' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'after' => 'id'
            ],
            'opc_nome' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'after' => 'opc_sigla'
            ],
            'opc_natureza_operacao' => [
                'type' => 'ENUM',
                'constraint' => ['Compra', 'Venda', 'Transferencia', 'Outras'],
                'null' => false,
                'after' => 'opc_nome'
            ],
            'opc_tipo_movimento' => [
                'type' => 'ENUM',
                'constraint' => ['Entrada', 'Saida'],
                'null' => false,
                'after' => 'opc_natureza_operacao'
            ],
            'opc_afeta_custo' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'opc_tipo_movimento'
            ],
            'opc_fato_fiscal' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'opc_afeta_custo'
            ],
            'opc_gera_financeiro' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'opc_fato_fiscal'
            ],
            'OPC_EMITE_CUPOM' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'opc_gera_financeiro'
            ],
            'opc_situacao' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
                'after' => 'OPC_EMITE_CUPOM'
            ],
            'opc_finalidade_nfe' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'after' => 'opc_situacao'
            ]
        ];

        $this->dbforge->add_column('operacao_comercial', $fields);

        // Renomear a coluna id para opc_id
        $this->db->query('ALTER TABLE operacao_comercial CHANGE id opc_id INT(11) NOT NULL AUTO_INCREMENT');

        // Inserir dados padrão
        $this->db->query("INSERT INTO operacao_comercial (opc_sigla, opc_nome, opc_natureza_operacao, opc_tipo_movimento, opc_afeta_custo, opc_fato_fiscal, opc_gera_financeiro, OPC_EMITE_CUPOM, opc_situacao, opc_finalidade_nfe) VALUES
            ('VENDA', 'Venda de Mercadorias', 'Venda', 'Saida', 1, 1, 1, 1, 1, 1),
            ('COMPRA', 'Compra de Mercadorias', 'Compra', 'Entrada', 1, 1, 1, 0, 1, 1),
            ('DEV', 'Devolução de Mercadorias', 'Venda', 'Entrada', 1, 1, 1, 0, 1, 4),
            ('BONIF', 'Bonificação', 'Venda', 'Saida', 0, 1, 0, 0, 1, 1),
            ('TRANSF', 'Transferência', 'Transferencia', 'Saida', 1, 1, 0, 0, 1, 1)");
    }

    public function down()
    {
        // Restaurar backup
        $this->db->query('DROP TABLE IF EXISTS operacao_comercial');
        $this->db->query('RENAME TABLE operacao_comercial_backup TO operacao_comercial');
    }
} 