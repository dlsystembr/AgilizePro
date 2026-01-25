<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_allow_null_orv_id_documentos_faturados extends CI_Migration {

    public function up()
    {
        // Permitir ORV_ID NULL em documentos_faturados para faturamento de entrada
        $this->db->query('ALTER TABLE `documentos_faturados` 
            MODIFY COLUMN `ORV_ID` INT(11) NULL');
    }

    public function down()
    {
        // Reverter para NOT NULL (pode causar problemas se houver registros NULL)
        $this->db->query('UPDATE `documentos_faturados` SET `ORV_ID` = 0 WHERE `ORV_ID` IS NULL');
        $this->db->query('ALTER TABLE `documentos_faturados` 
            MODIFY COLUMN `ORV_ID` INT(11) NOT NULL');
    }
}

