<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Clf_add_columns_and_copy extends CI_Migration
{
    public function up()
    {
        // Adiciona colunas CLF_* se não existirem e copia dados das colunas legadas
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Adições de colunas (MySQL 8+ suporta IF NOT EXISTS)
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_ID` INT(11) NOT NULL AUTO_INCREMENT FIRST");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_OPC_ID` INT(11) NULL AFTER `CLF_ID`");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_CST` VARCHAR(2) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_CSOSN` VARCHAR(3) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_NATUREZA_CONTRIB` ENUM('inscrito','nao_inscrito') NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_CFOP` VARCHAR(4) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_DESTINACAO` VARCHAR(100) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_OBJETIVO_COMERCIAL` ENUM('consumo','revenda') NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_TIPO_ICMS` ENUM('normal','st') NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_DATA_INCLUSAO` DATETIME NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_DATA_ALTERACAO` DATETIME NULL");

        // Garante PK em CLF_ID
        $this->db->query("ALTER TABLE `classificacao_fiscal` DROP PRIMARY KEY, ADD PRIMARY KEY (`CLF_ID`)");

        // Copia dados das colunas legadas, quando existirem
        $this->db->query("UPDATE `classificacao_fiscal` SET 
            CLF_OPC_ID = IFNULL(CLF_OPC_ID, operacao_comercial_id),
            CLF_CST = IFNULL(CLF_CST, cst),
            CLF_CSOSN = IFNULL(CLF_CSOSN, csosn),
            CLF_NATUREZA_CONTRIB = IFNULL(CLF_NATUREZA_CONTRIB, natureza_contribuinte),
            CLF_CFOP = IFNULL(CLF_CFOP, cfop),
            CLF_DESTINACAO = IFNULL(CLF_DESTINACAO, destinacao),
            CLF_OBJETIVO_COMERCIAL = IFNULL(CLF_OBJETIVO_COMERCIAL, objetivo_comercial),
            CLF_DATA_INCLUSAO = IFNULL(CLF_DATA_INCLUSAO, created_at),
            CLF_DATA_ALTERACAO = IFNULL(CLF_DATA_ALTERACAO, updated_at)
        ");

        // Cria FK nova se ainda não existir
        $fkExists = $this->db->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='classificacao_fiscal' AND CONSTRAINT_TYPE='FOREIGN KEY' AND CONSTRAINT_NAME='clf_opc_fk'")->row();
        if (!$fkExists) {
            // Remove FKs antigas
            $constraints = $this->db->query("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='classificacao_fiscal' AND CONSTRAINT_TYPE='FOREIGN KEY'")->result();
            foreach ($constraints as $c) {
                $this->db->query('ALTER TABLE `classificacao_fiscal` DROP FOREIGN KEY `'.$c->CONSTRAINT_NAME.'`');
            }
            // Cria a nova FK
            $this->db->query('ALTER TABLE `classificacao_fiscal` ADD CONSTRAINT `clf_opc_fk` FOREIGN KEY (`CLF_OPC_ID`) REFERENCES `operacao_comercial`(`OPC_ID`) ON DELETE CASCADE ON UPDATE CASCADE');
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // Apenas remove as colunas novas (não apaga a tabela)
        $cols = ['CLF_ID','CLF_OPC_ID','CLF_CST','CLF_CSOSN','CLF_NATUREZA_CONTRIB','CLF_CFOP','CLF_DESTINACAO','CLF_OBJETIVO_COMERCIAL','CLF_TIPO_ICMS','CLF_DATA_INCLUSAO','CLF_DATA_ALTERACAO'];
        foreach ($cols as $c) {
            $this->db->query('ALTER TABLE `classificacao_fiscal` DROP COLUMN IF EXISTS `'.$c.'`');
        }
    }
}


