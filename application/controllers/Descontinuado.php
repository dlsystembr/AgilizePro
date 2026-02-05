<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Controller para módulos descontinuados.
 * Ex.: antigo CRUD de Permissões (substituído por Grupos de Usuário).
 */
class Descontinuado extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Exibe mensagem de módulo descontinuado para o antigo menu Permissões.
     */
    public function permissoes($action = null, $id = null)
    {
        $this->data['view'] = 'descontinuado/permissoes_descontinuado';
        return $this->layout();
    }
}
