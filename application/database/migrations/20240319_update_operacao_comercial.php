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
            'OPC_SIGLA' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
                'after' => 'id'
            ],
            'OPC_NOME' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'after' => 'OPC_SIGLA'
            ],
            'OPC_NATUREZA_OPERACAO' => [
                'type' => 'ENUM',
                'constraint' => ['Compra', 'Venda', 'Transferencia', 'Outras'],
                'null' => false,
                'after' => 'OPC_NOME'
            ],
            'OPC_TIPO_MOVIMENTO' => [
                'type' => 'ENUM',
                'constraint' => ['Entrada', 'Saida'],
                'null' => false,
                'after' => 'OPC_NATUREZA_OPERACAO'
            ],
            'OPC_AFETA_CUSTO' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'OPC_TIPO_MOVIMENTO'
            ],
            'OPC_FATO_FISCAL' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'OPC_AFETA_CUSTO'
            ],
            'OPC_GERA_FINANCEIRO' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'OPC_FATO_FISCAL'
            ],
            'OPC_EMITE_CUPOM' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'OPC_GERA_FINANCEIRO'
            ],
            'OPC_SITUACAO' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'null' => false,
                'after' => 'OPC_EMITE_CUPOM'
            ],
            'OPC_FINALIDADE_NFE' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'after' => 'OPC_SITUACAO'
            ]
        ];

        $this->dbforge->add_column('operacao_comercial', $fields);

        // Renomear a coluna id para OPC_ID
        $this->db->query('ALTER TABLE operacao_comercial CHANGE id OPC_ID INT(11) NOT NULL AUTO_INCREMENT');

        // Inserir dados padrão
        $this->db->query("INSERT INTO operacao_comercial (OPC_SIGLA, OPC_NOME, OPC_NATUREZA_OPERACAO, OPC_TIPO_MOVIMENTO, OPC_AFETA_CUSTO, OPC_FATO_FISCAL, OPC_GERA_FINANCEIRO, OPC_EMITE_CUPOM, OPC_SITUACAO, OPC_FINALIDADE_NFE) VALUES
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