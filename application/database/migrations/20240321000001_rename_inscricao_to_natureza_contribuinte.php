<?php
class Migration_rename_inscricao_to_natureza_contribuinte extends CI_Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE clientes CHANGE COLUMN inscricao natureza_contribuinte ENUM("inscrito", "nao_inscrito") NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE clientes CHANGE COLUMN natureza_contribuinte inscricao ENUM("inscrito", "nao_inscrito") NULL DEFAULT NULL');
    }
} 