<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_ncm_permission extends CI_Migration
{
    public function up()
    {
        $this->db->query("INSERT INTO permissoes (situacao, nome, permissao) VALUES (1, 'Visualizar NCMs', 'vNcm')");
    }

    public function down()
    {
        $this->db->query("DELETE FROM permissoes WHERE permissao = 'vNcm'");
    }
} 