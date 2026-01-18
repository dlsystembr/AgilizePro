<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Update_operacao_comercial_columns extends CI_Migration
{
    public function up()
    {
        // Remover a coluna OPC_EMITE_CUPOM
        $this->dbforge->drop_column('operacao_comercial', 'OPC_EMITE_CUPOM');

        // Adicionar a coluna OPC_MOVIMENTA_ESTOQUE
        $fields = [
            'OPC_MOVIMENTA_ESTOQUE' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'OPC_GERA_FINANCEIRO'
            ]
        ];

        $this->dbforge->add_column('operacao_comercial', $fields);

        // Atualizar os registros existentes
        $this->db->query("UPDATE operacao_comercial SET OPC_MOVIMENTA_ESTOQUE = 1 WHERE OPC_SIGLA IN ('VENDA', 'COMPRA', 'DEV', 'TRANSF')");
    }

    public function down()
    {
        // Remover a coluna OPC_MOVIMENTA_ESTOQUE
        $this->dbforge->drop_column('operacao_comercial', 'OPC_MOVIMENTA_ESTOQUE');

        // Adicionar a coluna OPC_EMITE_CUPOM de volta
        $fields = [
            'OPC_EMITE_CUPOM' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'OPC_GERA_FINANCEIRO'
            ]
        ];

        $this->dbforge->add_column('operacao_comercial', $fields);

        // Restaurar os valores originais
        $this->db->query("UPDATE operacao_comercial SET OPC_EMITE_CUPOM = 1 WHERE OPC_SIGLA = 'VENDA'");
    }
} 