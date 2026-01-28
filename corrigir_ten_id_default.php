<?php
/**
 * Script para corrigir campo ten_id sem valor padrão
 * Atualiza todos os registros NULL/0 e adiciona DEFAULT se necessário
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


echo "========================================\n";
echo "Corrigir ten_id sem Valor Padrão\n";
echo "========================================\n\n";

// 1. Verificar/criar tenant padrão
$result = $conn->query("SHOW TABLES LIKE 'tenants'");
if ($result->num_rows == 0) {
    echo "❌ Tabela tenants não existe. Execute a migration primeiro.\n";
    exit;
}

$result = $conn->query("SELECT * FROM tenants WHERE ten_nome = 'Tenant Padrão' LIMIT 1");
$tenant = $result->fetch_assoc();

if (!$tenant) {
    // Criar tenant padrão
    $sql = "INSERT INTO tenants (ten_nome, ten_cnpj, ten_email, ten_telefone, ten_data_cadastro) 
            VALUES ('Tenant Padrão', '00.000.000/0001-00', 'tenant@padrao.com', '(00) 0000-0000', NOW())";
    
    if ($conn->query($sql)) {
        $ten_id = $conn->insert_id;
        echo "✓ Tenant padrão criado (ID: $ten_id)\n\n";
    } else {
        echo "❌ Erro ao criar tenant: " . $conn->error . "\n";
        exit;
    }
} else {
    $ten_id = $tenant['ten_id'];
    echo "✓ Tenant padrão encontrado (ID: $ten_id)\n\n";
}

// 2. Lista de tabelas para atualizar
$tabelas = [
    'usuarios',
    'clientes',
    'produtos',
    'servicos',
    'vendas',
    'os',
    'contratos',
    'nfecom_capa',
    'nfecom_itens',
    'empresas',
    'classificacao_fiscal',
    'operacao_comercial',
    'configuracoes_fiscais',
    'certificados',
    'permissoes',
    'faturamento_entrada',
    'itens_faturamento_entrada',
    'pedidos',
    'itens_pedidos',
    'protocolos',
    'tipos_clientes',
    'ncms'
];

echo "Atualizando registros sem ten_id...\n\n";

$total_atualizados = 0;
$tabelas_atualizadas = 0;

foreach ($tabelas as $tabela) {
    // Verificar se tabela existe
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result->num_rows == 0) {
        echo "  ⚠ Tabela $tabela não existe, pulando...\n";
        continue;
    }
    
    // Verificar se tem campo ten_id
    $result = $conn->query("SHOW COLUMNS FROM `$tabela` LIKE 'ten_id'");
    if ($result->num_rows == 0) {
        echo "  ⚠ Tabela $tabela não tem campo ten_id, pulando...\n";
        continue;
    }
    
    // Contar registros sem ten_id ou com ten_id = 0
    $result = $conn->query("SELECT COUNT(*) as total FROM `$tabela` WHERE ten_id IS NULL OR ten_id = 0");
    $row = $result->fetch_assoc();
    $count = $row['total'];
    
    if ($count > 0) {
        // Atualizar registros
        $sql = "UPDATE `$tabela` SET `ten_id` = $ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0";
        $conn->query($sql);
        $atualizados = $conn->affected_rows;
        $total_atualizados += $atualizados;
        $tabelas_atualizadas++;
        echo "  ✓ $tabela: $atualizados registro(s) atualizado(s)\n";
    } else {
        echo "  ✓ $tabela: todos os registros já têm ten_id válido\n";
    }
    
    // Tentar adicionar DEFAULT ao campo (se possível)
    try {
        // Verificar se o campo permite NULL
        $result = $conn->query("SHOW COLUMNS FROM `$tabela` LIKE 'ten_id'");
        $column = $result->fetch_assoc();
        
        if ($column && $column['Null'] === 'NO' && $column['Default'] === null) {
            // Campo é NOT NULL mas não tem default - adicionar default
            $sql = "ALTER TABLE `$tabela` MODIFY COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT $ten_id";
            $conn->query($sql);
            echo "    → Valor padrão DEFAULT adicionado ao campo ten_id\n";
        }
    } catch (Exception $e) {
        // Ignorar erros ao modificar coluna (pode não ter permissão)
    }
}

$conn->close();

echo "\n========================================\n";
echo "Resumo\n";
echo "========================================\n";
echo "Tenant ID usado: $ten_id\n";
echo "Tabelas atualizadas: $tabelas_atualizadas\n";
echo "Total de registros atualizados: $total_atualizados\n";
echo "\n✓ Concluído!\n";
echo "\nAgora você pode inserir registros sem especificar ten_id explicitamente.\n";
echo "(O valor padrão será usado automaticamente)\n";
