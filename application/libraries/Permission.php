<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Permission Class
 *
 * Biblioteca para controle de permissões
 *
 * @author      Ramon Silva
 * @copyright   Copyright (c) 2013, Ramon Silva.
 *
 * @since       Version 1.0
 * v... Visualizar
 * e... Editar
 * d... Deletar ou Desabilitar
 * c... Cadastrar
 */
class Permission
{
    private $permissions = [];

    private $table = 'permissoes'; //Nome tabela onde ficam armazenadas as permissões

    private $pk = 'idPermissao'; // Nome da chave primaria da tabela

    private $select = 'permissoes'; // Campo onde fica o array de permissoes.

    public function __construct()
    {
        log_message('debug', 'Permission Class Initialized');
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    public function checkPermission($idPermissao = null, $atividade = null)
    {
        if ($idPermissao == null || $atividade == null) {
            return false;
        }

        $CI = &get_instance();
        $ten_id = $CI->session->userdata('ten_id');
        $is_super = $CI->session->userdata('is_super');

        // Super usuário tem acesso a tudo
        if ($is_super) {
            return true;
        }

        // Se for um tenant, verificar permissões específicas do tenant
        if ($ten_id) {
            // Primeiro verificar se a permissão está habilitada para o tenant
            // Limpar query builder para evitar problemas de cache
            $CI->db->reset_query();
            $CI->db->select('TPM_ATIVO, TPM_PERMISSAO');
            $CI->db->from('tenant_permissoes_menu');
            $CI->db->where('TPM_TEN_ID', $ten_id);
            $CI->db->where('TPM_PERMISSAO', $atividade);
            $CI->db->where('TPM_ATIVO', 1);
            $query = $CI->db->get();
            $result = $query->row();

            // Debug: log da verificação
            log_message('debug', "Permission::checkPermission - ten_id: {$ten_id}, atividade: {$atividade}, encontrado: " . ($result ? 'SIM' : 'NÃO'));
            if ($result) {
                log_message('debug', "Permission::checkPermission - TPM_ATIVO: {$result->TPM_ATIVO}, TPM_PERMISSAO: {$result->TPM_PERMISSAO}");
            } else {
                // Verificar se há alguma permissão para este tenant (para debug)
                $CI->db->reset_query();
                $CI->db->select('COUNT(*) as total');
                $CI->db->from('tenant_permissoes_menu');
                $CI->db->where('TPM_TEN_ID', $ten_id);
                $CI->db->where('TPM_ATIVO', 1);
                $count_query = $CI->db->get();
                $count_result = $count_query->row();
                log_message('debug', "Permission::checkPermission - Total de permissões ativas para tenant {$ten_id}: " . ($count_result ? $count_result->total : 0));
            }

            if ($result && $result->TPM_ATIVO == 1) {
                // Permissão habilitada para o tenant - retorna true imediatamente
                // Isso permite que o menu apareça mesmo se não estiver no perfil do usuário
                // As ações específicas ainda podem verificar o perfil do usuário se necessário
                log_message('debug', "Permission::checkPermission - Permissão {$atividade} habilitada para tenant {$ten_id} - retornando TRUE");
                return true;
            } else {
                // Permissão não habilitada para este tenant
                log_message('debug', "Permission::checkPermission - Permissão {$atividade} NÃO habilitada para tenant {$ten_id} - retornando FALSE");
                return false;
            }
        } else {
            // Lógica de permissão padrão (para usuários não-super e sem ten_id)
            // Se as permissões não estiverem carregadas, requisita o carregamento
            if ($this->permissions == null) {
                // Se não carregar retorna falso
                if (! $this->loadPermission($idPermissao)) {
                    return false;
                }
            }

            if (is_array($this->permissions[0])) {
                if (array_key_exists($atividade, $this->permissions[0])) {
                    // compara a atividade requisitada com a permissão.
                    if ($this->permissions[0][$atividade] == 1) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function loadPermission($id = null)
    {
        if ($id != null) {
            $this->CI->db->select($this->table . '.' . $this->select);
            $this->CI->db->where($this->pk, $id);
            $this->CI->db->limit(1);
            $array = $this->CI->db->get($this->table)->row_array();

            if (count($array) > 0) {
                $array = unserialize($array[$this->select]);
                //Atribui as permissoes ao atributo permissions
                $this->permissions = [$array];

                return true;
            }
        }

        return false;
    }
}
