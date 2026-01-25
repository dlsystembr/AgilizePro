<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Verifica se uma permissão está habilitada para o tenant
 * 
 * @param string $codigo Código da permissão (ex: 'vCliente')
 * @param array $permissoes_habilitadas Array de permissões habilitadas
 * @param int $ten_id ID do tenant
 * @return bool
 */
if (!function_exists('permissao_habilitada')) {
    function permissao_habilitada($codigo, $permissoes_habilitadas, $ten_id) {
        // Se não houver ten_id (super usuário ou sistema sem tenant), mostrar todas
        if (!$ten_id) {
            return true;
        }
        // Se houver ten_id mas array vazio, significa que nenhuma permissão está habilitada
        if (empty($permissoes_habilitadas)) {
            return false;
        }
        return in_array($codigo, $permissoes_habilitadas);
    }
}

/**
 * Verifica se pelo menos uma das permissões de um módulo está habilitada
 * 
 * @param array $codigos Array de códigos de permissão (ex: ['vCliente', 'aCliente', 'eCliente', 'dCliente'])
 * @param array $permissoes_habilitadas Array de permissões habilitadas
 * @param int $ten_id ID do tenant
 * @return bool
 */
if (!function_exists('modulo_habilitado')) {
    function modulo_habilitado($codigos, $permissoes_habilitadas, $ten_id) {
        // Se não houver ten_id, mostrar todas
        if (!$ten_id || empty($permissoes_habilitadas)) {
            return true;
        }
        foreach ($codigos as $codigo) {
            if (in_array($codigo, $permissoes_habilitadas)) {
                return true;
            }
        }
        return false;
    }
}

