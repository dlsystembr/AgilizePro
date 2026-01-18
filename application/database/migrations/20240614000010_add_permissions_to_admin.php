<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_permissions_to_admin extends CI_Migration
{
    public function up()
    {
        // Busca o grupo de permissões padrão (Administrador)
        $this->db->where('idPermissao', 1);
        $permissao = $this->db->get('permissoes')->row();

        if ($permissao) {
            // Deserializa as permissões existentes
            $permissoes = unserialize($permissao->permissoes);
            
            // Adiciona as novas permissões
            $permissoes['vConfiguracao'] = '1';
            $permissoes['vNcm'] = '1';
            
            // Serializa novamente
            $permissoes = serialize($permissoes);
            
            // Atualiza o grupo de permissões
            $this->db->where('idPermissao', 1);
            $this->db->update('permissoes', ['permissoes' => $permissoes]);
        }
    }

    public function down()
    {
        // Busca o grupo de permissões padrão (Administrador)
        $this->db->where('idPermissao', 1);
        $permissao = $this->db->get('permissoes')->row();

        if ($permissao) {
            // Deserializa as permissões existentes
            $permissoes = unserialize($permissao->permissoes);
            
            // Remove as permissões
            unset($permissoes['vConfiguracao']);
            unset($permissoes['vNcm']);
            
            // Serializa novamente
            $permissoes = serialize($permissoes);
            
            // Atualiza o grupo de permissões
            $this->db->where('idPermissao', 1);
            $this->db->update('permissoes', ['permissoes' => $permissoes]);
        }
    }
} 