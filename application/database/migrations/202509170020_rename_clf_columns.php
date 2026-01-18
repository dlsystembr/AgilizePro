<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Rename_clf_columns extends CI_Migration
{
    private function dropAllForeignKeys($table)
    {
        // Busca todas as FKs existentes e remove com segurança
        $sql = "SELECT CONSTRAINT_NAME FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = '" . $table . "' AND constraint_type = 'FOREIGN KEY'";
        $constraints = $this->db->query($sql)->result();
        foreach ($constraints as $c) {
            $this->db->query('ALTER TABLE `'.$table.'` DROP FOREIGN KEY `'.$c->CONSTRAINT_NAME.'`');
        }
    }

    public function up()
    {
        // Remover quaisquer FKs existentes para permitir renomeações
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->dropAllForeignKeys('classificacao_fiscal');
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        // Renomear colunas conforme padrão CLF_
        $renameMap = [
            'id' => 'CLF_ID INT(11) NOT NULL AUTO_INCREMENT',
            'operacao_comercial_id' => 'CLF_OPC_ID INT(11) NOT NULL',
            'cst' => 'CLF_CST VARCHAR(2) NULL',
            'csosn' => 'CLF_CSOSN VARCHAR(3) NULL',
            'natureza_contribuinte' => 'CLF_NATUREZA_CONTRIB ENUM("inscrito","nao_inscrito") NOT NULL DEFAULT "nao_inscrito"',
            'cfop' => 'CLF_CFOP VARCHAR(4) NOT NULL',
            'destinacao' => 'CLF_DESTINACAO VARCHAR(100) NOT NULL',
            'objetivo_comercial' => 'CLF_OBJETIVO_COMERCIAL ENUM("consumo","revenda") NOT NULL DEFAULT "consumo"',
            'tipo_icms' => 'CLF_TIPO_ICMS ENUM("normal","st") NOT NULL DEFAULT "normal"',
            'created_at' => 'CLF_DATA_INCLUSAO DATETIME NULL',
            'updated_at' => 'CLF_DATA_ALTERACAO DATETIME NULL',
        ];

        foreach ($renameMap as $old => $definition) {
            if ($this->db->field_exists($old, 'classificacao_fiscal')) {
                $this->db->query("ALTER TABLE `classificacao_fiscal` CHANGE `$old` $definition");
            }
        }

        // Ajustar chave primária se necessário
        // (Se a PK anterior era em `id`, permanece em `CLF_ID` após o CHANGE)

        // Recriar FK para Operação Comercial usando CLF_OPC_ID -> operacao_comercial.OPC_ID
        if ($this->db->field_exists('CLF_OPC_ID', 'classificacao_fiscal')) {
            $this->db->query('SET FOREIGN_KEY_CHECKS=0');
            $this->db->query('ALTER TABLE `classificacao_fiscal` ADD CONSTRAINT `clf_opc_fk` FOREIGN KEY (`CLF_OPC_ID`) REFERENCES `operacao_comercial`(`OPC_ID`) ON DELETE CASCADE ON UPDATE CASCADE');
            $this->db->query('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    public function down()
    {
        // Remover FK criada
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        $this->dropAllForeignKeys('classificacao_fiscal');
        $this->db->query('SET FOREIGN_KEY_CHECKS=1');

        // Reverter nomes para o padrão antigo
        $renameBack = [
            'CLF_ID' => 'id INT(11) NOT NULL AUTO_INCREMENT',
            'CLF_OPC_ID' => 'operacao_comercial_id INT(11) NOT NULL',
            'CLF_CST' => 'cst VARCHAR(2) NULL',
            'CLF_CSOSN' => 'csosn VARCHAR(3) NULL',
            'CLF_NATUREZA_CONTRIB' => 'natureza_contribuinte ENUM("inscrito","nao_inscrito") NOT NULL DEFAULT "nao_inscrito"',
            'CLF_CFOP' => 'cfop VARCHAR(4) NOT NULL',
            'CLF_DESTINACAO' => 'destinacao VARCHAR(100) NOT NULL',
            'CLF_OBJETIVO_COMERCIAL' => 'objetivo_comercial ENUM("consumo","revenda") NOT NULL DEFAULT "consumo"',
            'CLF_TIPO_ICMS' => 'tipo_icms ENUM("normal","st") NOT NULL DEFAULT "normal"',
            'CLF_DATA_INCLUSAO' => 'created_at DATETIME NULL',
            'CLF_DATA_ALTERACAO' => 'updated_at DATETIME NULL',
        ];

        foreach ($renameBack as $old => $definition) {
            if ($this->db->field_exists($old, 'classificacao_fiscal')) {
                $this->db->query("ALTER TABLE `classificacao_fiscal` CHANGE `$old` $definition");
            }
        }

        // FK antiga (padrão) de volta
        if ($this->db->field_exists('operacao_comercial_id', 'classificacao_fiscal')) {
            $this->db->query('ALTER TABLE `classificacao_fiscal` ADD CONSTRAINT `classificacao_fiscal_operacao_comercial_fk` FOREIGN KEY (`operacao_comercial_id`) REFERENCES `operacao_comercial`(`OPC_ID`) ON DELETE CASCADE ON UPDATE CASCADE');
        }
    }
}


