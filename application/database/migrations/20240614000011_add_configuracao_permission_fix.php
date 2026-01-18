<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_configuracao_permission_fix extends CI_Migration
{
    public function up()
    {
        // Remove a permissão se existir
        $this->db->where('permissao', 'vConfiguracao');
        $this->db->delete('permissoes');

        // Insere a permissão novamente
        $data = array(
            'situacao' => 1,
            'nome' => 'Visualizar Configurações',
            'permissao' => 'vConfiguracao'
        );
        $this->db->insert('permissoes', $data);

        // Verifica se a inserção foi bem sucedida
        $this->db->where('permissao', 'vConfiguracao');
        $result = $this->db->get('permissoes')->row();
        
        if (!$result) {
            // Se falhar, tenta inserir diretamente via SQL
            $sql = "INSERT INTO permissoes (situacao, nome, permissao) VALUES (1, 'Visualizar Configurações', 'vConfiguracao')";
            $this->db->query($sql);
        }
    }

    public function down()
    {
        $this->db->where('permissao', 'vConfiguracao');
        $this->db->delete('permissoes');
    }
} 