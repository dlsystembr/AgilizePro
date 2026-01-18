<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Alter_configuracoes_columns extends CI_Migration
{
    public function up()
    {
        // Ampliar o tamanho de 'config' para suportar chaves maiores
        $this->db->query('ALTER TABLE `configuracoes` CHANGE `config` `config` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;');

        // Garantir que 'valor' seja TEXT (já migrado em versões anteriores, mas reforçamos)
        $this->db->query('ALTER TABLE `configuracoes` CHANGE `valor` `valor` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;');
    }

    public function down()
    {
        // Reverter para o tamanho anterior
        $this->db->query('ALTER TABLE `configuracoes` CHANGE `config` `config` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;');

        // Reverter 'valor' para VARCHAR(30) (compatível com migração antiga)
        $this->db->query('ALTER TABLE `configuracoes` CHANGE `valor` `valor` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;');
    }
}



