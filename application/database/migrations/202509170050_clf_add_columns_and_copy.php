<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Clf_add_columns_and_copy extends CI_Migration
{
    public function up()
    {
        // Adiciona colunas CLF_* se não existirem e copia dados das colunas legadas
        $this->db->query('SET FOREIGN_KEY_CHECKS=0');

        // Adições de colunas (MySQL 8+ suporta IF NOT EXISTS)
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_id` INT(11) NOT NULL AUTO_INCREMENT FIRST");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_OPC_ID` INT(11) NULL AFTER `clf_id`");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_cst` VARCHAR(2) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_csosn` VARCHAR(3) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_NATUREZA_CONTRIB` ENUM('inscrito','nao_inscrito') NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_cfop` VARCHAR(4) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_destinacao` VARCHAR(100) NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_objetivo_comercial` ENUM('consumo','revenda') NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_TIPO_ICMS` ENUM('normal','st') NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_data_inclusao` DATETIME NULL");
        $this->db->query("ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `clf_data_alteracao` DATETIME NULL");

        // Garante PK em clf_id
        $this->db->query("ALTER TABLE `classificacao_fiscal` DROP PRIMARY KEY, ADD PRIMARY KEY (`clf_id`)");

        // Copia dados das colunas legadas, quando existirem
        $this->db->query("UPDATE `classificacao_fiscal` SET 
            CLF_OPC_ID = IFNULL(CLF_OPC_ID, operacao_comercial_id),
            clf_cst = IFNULL(clf_cst, cst),
            clf_csosn = IFNULL(clf_csosn, csosn),
            CLF_NATUREZA_CONTRIB = IFNULL(CLF_NATUREZA_CONTRIB, natureza_contribuinte),
            clf_cfop = IFNULL(clf_cfop, cfop),
            clf_destinacao = IFNULL(clf_destinacao, destinacao),
            clf_objetivo_comercial = IFNULL(clf_objetivo_comercial, objetivo_comercial),
            clf_data_inclusao = IFNULL(clf_data_inclusao, created_at),
            clf_data_alteracao = IFNULL(clf_data_alteracao, updated_at)
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
            $this->db->query('ALTER TABLE `classificacao_fiscal` ADD CONSTRAINT `clf_opc_fk` FOREIGN KEY (`CLF_OPC_ID`) REFERENCES `operacao_comercial`(`opc_id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // Apenas remove as colunas novas (não apaga a tabela)
        $cols = ['clf_id','CLF_OPC_ID','clf_cst','clf_csosn','CLF_NATUREZA_CONTRIB','clf_cfop','clf_destinacao','clf_objetivo_comercial','CLF_TIPO_ICMS','clf_data_inclusao','clf_data_alteracao'];
        foreach ($cols as $c) {
            $this->db->query('ALTER TABLE `classificacao_fiscal` DROP COLUMN IF EXISTS `'.$c.'`');
        }
    }
}


