<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_ncm_permissions extends CI_Migration {

    public function up() {
        // Verifica se a permissão vNcm já existe
        $this->db->where('situacao', 1);
        $this->db->where('nome', 'vNcm');
        $vNcm = $this->db->get('permissoes')->row();

        if (!$vNcm) {
            $this->db->insert('permissoes', [
                'nome' => 'vNcm',
                'permissao' => 'Visualizar NCMs',
                'situacao' => 1
            ]);
        }

        // Verifica se a permissão aNcm já existe
        $this->db->where('situacao', 1);
        $this->db->where('nome', 'aNcm');
        $aNcm = $this->db->get('permissoes')->row();

        if (!$aNcm) {
            $this->db->insert('permissoes', [
                'nome' => 'aNcm',
                'permissao' => 'Adicionar NCMs',
                'situacao' => 1
            ]);
        }

        // Adiciona as permissões ao grupo de administradores (id = 1)
        $this->db->where('id', 1);
        $admin = $this->db->get('usuarios')->row();

        if ($admin) {
            $permissoes = explode(',', $admin->situacao);
            
            if (!in_array('vNcm', $permissoes)) {
                $permissoes[] = 'vNcm';
            }
            
            if (!in_array('aNcm', $permissoes)) {
                $permissoes[] = 'aNcm';
            }

            $this->db->where('id', 1);
            $this->db->update('usuarios', [
                'situacao' => implode(',', $permissoes)
            ]);
        }
    }

    public function down() {
        // Remove as permissões do grupo de administradores
        $this->db->where('id', 1);
        $admin = $this->db->get('usuarios')->row();

        if ($admin) {
            $permissoes = explode(',', $admin->situacao);
            $permissoes = array_diff($permissoes, ['vNcm', 'aNcm']);

            $this->db->where('id', 1);
            $this->db->update('usuarios', [
                'situacao' => implode(',', $permissoes)
            ]);
        }

        // Remove as permissões da tabela de permissões
        $this->db->where_in('nome', ['vNcm', 'aNcm']);
        $this->db->delete('permissoes');
    }
} 