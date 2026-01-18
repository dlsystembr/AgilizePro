<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Fix_ncm_permissions extends CI_Migration {

    public function up() {
        // Busca o grupo de permissões padrão (Administrador)
        $this->db->where('idPermissao', 1);
        $permissao = $this->db->get('permissoes')->row();

        if ($permissao) {
            // Deserializa as permissões existentes
            $permissoes = unserialize($permissao->permissoes);
            
            // Adiciona as novas permissões
            $permissoes['vNcm'] = '1';
            $permissoes['aNcm'] = '1';
            $permissoes['eNcm'] = '1';
            $permissoes['dNcm'] = '1';
            
            // Serializa novamente
            $permissoes = serialize($permissoes);
            
            // Atualiza o grupo de permissões
            $this->db->where('idPermissao', 1);
            $this->db->update('permissoes', ['permissoes' => $permissoes]);

            // Adiciona as permissões ao arquivo de configuração
            $config_file = APPPATH . 'config/permission.php';
            if (file_exists($config_file)) {
                $config_content = file_get_contents($config_file);
                
                // Verifica se as permissões já existem
                if (strpos($config_content, "'vNcm'") === false) {
                    $config_content = str_replace(
                        "'vConfiguracao' => 'Visualizar Configurações'",
                        "'vConfiguracao' => 'Visualizar Configurações',
    'vNcm' => 'Visualizar NCMs',
    'aNcm' => 'Adicionar NCMs',
    'eNcm' => 'Editar NCMs',
    'dNcm' => 'Excluir NCMs'",
                        $config_content
                    );
                    file_put_contents($config_file, $config_content);
                }
            }
        }
    }

    public function down() {
        // Busca o grupo de permissões padrão (Administrador)
        $this->db->where('idPermissao', 1);
        $permissao = $this->db->get('permissoes')->row();

        if ($permissao) {
            // Deserializa as permissões existentes
            $permissoes = unserialize($permissao->permissoes);
            
            // Remove as permissões
            unset($permissoes['vNcm']);
            unset($permissoes['aNcm']);
            unset($permissoes['eNcm']);
            unset($permissoes['dNcm']);
            
            // Serializa novamente
            $permissoes = serialize($permissoes);
            
            // Atualiza o grupo de permissões
            $this->db->where('idPermissao', 1);
            $this->db->update('permissoes', ['permissoes' => $permissoes]);

            // Remove as permissões do arquivo de configuração
            $config_file = APPPATH . 'config/permission.php';
            if (file_exists($config_file)) {
                $config_content = file_get_contents($config_file);
                $config_content = preg_replace(
                    "/'vNcm' => 'Visualizar NCMs',\s*'aNcm' => 'Adicionar NCMs',\s*'eNcm' => 'Editar NCMs',\s*'dNcm' => 'Excluir NCMs',/",
                    '',
                    $config_content
                );
                file_put_contents($config_file, $config_content);
            }
        }
    }
} 