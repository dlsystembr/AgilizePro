<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Redireciona para o módulo descontinuado.
 * O sistema passou a usar Grupos de Usuário para permissões (grupo_usuario + grupo_usuario_permissoes).
 */
class Permissoes extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect('descontinuado/permissoes');
    }

    public function gerenciar()
    {
        redirect('descontinuado/permissoes');
    }

    public function adicionar()
    {
        redirect('descontinuado/permissoes/adicionar');
    }

    public function editar($id = null)
    {
        redirect('descontinuado/permissoes/editar/' . $id);
    }

    public function desativar()
    {
        redirect('descontinuado/permissoes');
    }

    public function exportarXml()
    {
        $this->load->library('permission');
        if (!$this->permission->checkPermission(null, 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para exportar XMLs de NF-e.');
            redirect(base_url());
        }
        redirect('nfe/gerenciar');
    }
}
