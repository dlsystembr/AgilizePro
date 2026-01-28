<?php
/**
 * Script para atualizar ten_id em todos os registros
 * Use este script se já tiver um tenant criado e quiser atualizar todos os registros
 */

// Carregar CodeIgniter sem redefinir constantes
if (!defined('BASEPATH')) {
    define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : 'development');
    define('BASEPATH', __DIR__ . '/application/');
    define('APPPATH', __DIR__ . '/application/');
    define('FCPATH', __DIR__ . '/');
}

// Incluir bootstrap do CodeIgniter
require_once __DIR__ . '/application/core/CodeIgniter.php';

// Obter instância do CodeIgniter
$CI =& get_instance();
$CI->load->database();

echo "========================================\n";
echo "Atualizar ten_id em Todos os Registros\n";
echo "========================================\n\n";

// 1. Verificar se existe tenant
$tenants = $CI->db->get('tenants')->result();

if (empty($tenants)) {
    echo "❌ Nenhum tenant encontrado. Execute primeiro o script criar_usuario_super_e_tenant.php\n";
    exit;
}

// Se houver mais de um tenant, perguntar qual usar
if (count($tenants) > 1) {
    echo "Tenants encontrados:\n";
    foreach ($tenants as $index => $tenant) {
        echo "  " . ($index + 1) . ". ID: {$tenant->ten_id} - {$tenant->ten_nome}\n";
    }
    echo "\n";
    
    // Usar o primeiro tenant por padrão
    $ten_id = $tenants[0]->ten_id;
    echo "Usando Tenant ID: $ten_id ({$tenants[0]->ten_nome})\n\n";
} else {
    $ten_id = $tenants[0]->ten_id;
    echo "Tenant encontrado: ID $ten_id - {$tenants[0]->ten_nome}\n\n";
}

// 2. Lista de tabelas para atualizar
$tabelas = [
    'usuarios' => 'idusuarios',
    'clientes' => 'cln_id',
    'produtos' => 'idProdutos',
    'servicos' => 'idServicos',
    'vendas' => 'idVendas',
    'os' => 'idOs',
    'contratos' => 'ctr_id',
    'nfecom_capa' => 'nfc_id',
    'nfecom_itens' => 'nfi_id',
    'empresas' => 'emp_id',
    'classificacao_fiscal' => 'clf_id',
    'operacao_comercial' => 'opc_id',
    'configuracoes_fiscais' => 'cfg_id',
    'certificados' => 'cert_id',
    'permissoes' => 'idPermissao',
    'faturamento_entrada' => 'fat_id',
    'itens_faturamento_entrada' => 'ife_id',
    'pedidos' => 'ped_id',
    'itens_pedidos' => 'ipe_id',
    'protocolos' => 'pro_id',
    'tipos_clientes' => 'tpc_id',
    'ncms' => 'ncm_id'
];

$total_atualizados = 0;
$tabelas_atualizadas = 0;

echo "Atualizando registros...\n\n";

foreach ($tabelas as $tabela => $primary_key) {
    if (!$CI->db->table_exists($tabela)) {
        echo "  ⚠ Tabela $tabela não existe, pulando...\n";
        continue;
    }
    
    // Verificar se a tabela tem campo ten_id
    $fields = $CI->db->list_fields($tabela);
    if (!in_array('ten_id', $fields)) {
        echo "  ⚠ Tabela $tabela não tem campo ten_id, pulando...\n";
        continue;
    }
    
    // Contar registros sem ten_id ou com ten_id = 0
    $CI->db->where('(ten_id IS NULL OR ten_id = 0)', null, false);
    $count = $CI->db->count_all_results($tabela);
    
    if ($count > 0) {
        // Atualizar registros
        $CI->db->where('(ten_id IS NULL OR ten_id = 0)', null, false);
        $CI->db->update($tabela, ['ten_id' => $ten_id]);
        
        $atualizados = $CI->db->affected_rows();
        $total_atualizados += $atualizados;
        $tabelas_atualizadas++;
        echo "  ✓ $tabela: $atualizados registro(s) atualizado(s)\n";
    } else {
        echo "  ✓ $tabela: todos os registros já têm ten_id válido\n";
    }
}

echo "\n========================================\n";
echo "Resumo\n";
echo "========================================\n";
echo "Tenant ID usado: $ten_id\n";
echo "Tabelas atualizadas: $tabelas_atualizadas\n";
echo "Total de registros atualizados: $total_atualizados\n";
echo "\n✓ Concluído!\n";
