<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_ncm_permission_fix2 extends CI_Migration
{
    public function up()
    {
        // Remove a permissão se existir
        $this->db->where('permissao', 'vNcm');
        $this->db->delete('permissoes');

        // Insere a permissão novamente
        $data = array(
            'situacao' => 1,
            'nome' => 'Visualizar NCMs',
            'permissao' => 'vNcm'
        );
        $this->db->insert('permissoes', $data);

        // Verifica se a inserção foi bem sucedida
        $this->db->where('permissao', 'vNcm');
        $result = $this->db->get('permissoes')->row();
        
        if (!$result) {
            // Se falhar, tenta inserir diretamente via SQL
            $sql = "INSERT INTO permissoes (situacao, nome, permissao) VALUES (1, 'Visualizar NCMs', 'vNcm')";
            $this->db->query($sql);
        }
    }

    public function down()
    {
        $this->db->where('permissao', 'vNcm');
        $this->db->delete('permissoes');
    }
} 