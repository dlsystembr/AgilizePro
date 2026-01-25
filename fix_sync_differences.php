<?php
/**
 * Script para corrigir diferenças de estrutura entre MapOS e AgilizePro
 * 
 * Uso:
 * php fix_sync_differences.php
 * ou acesse via navegador: http://localhost/mapos/fix_sync_differences.php
 */

// Configurações dos bancos
$mapos_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'mapos'
];

$agilizepro_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'agilizepro'
];

try {
    $mapos_conn = new mysqli($mapos_config['host'], $mapos_config['user'], $mapos_config['pass'], $mapos_config['db']);
    if ($mapos_conn->connect_error) {
        die("Erro ao conectar ao MapOS: " . $mapos_conn->connect_error);
    }
    $mapos_conn->set_charset("utf8mb4");
    
    $agilizepro_conn = new mysqli($agilizepro_config['host'], $agilizepro_config['user'], $agilizepro_config['pass'], $agilizepro_config['db']);
    if ($agilizepro_conn->connect_error) {
        die("Erro ao conectar ao AgilizePro: " . $agilizepro_conn->connect_error);
    }
    $agilizepro_conn->set_charset("utf8mb4");
    
    echo "<h1>Correção de Diferenças de Estrutura</h1>";
    echo "<h2>MapOS vs AgilizePro</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f44336; color: white; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
        .section { margin: 30px 0; padding: 15px; background-color: #f5f5f5; border-left: 4px solid #2196F3; }
        pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>";
    
    // 1. Verificar ten_id
    echo "<div class='section'>";
    echo "<h3>1. Verificando Coluna ten_id</h3>";
    
    $tables_to_check = ['aliquotas', 'anexos', 'anotacoes_os', 'bairros', 'categorias', 'certificados_digitais', 'classificacao_fiscal'];
    $missing_ten_id = [];
    
    foreach ($tables_to_check as $table) {
        $result = $agilizepro_conn->query("
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = '{$agilizepro_config['db']}' 
            AND TABLE_NAME = '$table' 
            AND COLUMN_NAME = 'ten_id'
        ");
        
        if ($result->num_rows == 0) {
            $missing_ten_id[] = $table;
        }
    }
    
    if (empty($missing_ten_id)) {
        echo "<p class='success'>✓ Todas as tabelas têm a coluna ten_id!</p>";
    } else {
        echo "<p class='warning'><strong>Tabelas sem ten_id:</strong> " . implode(', ', $missing_ten_id) . "</p>";
    }
    echo "</div>";
    
    // 2. Verificar classificacao_fiscal
    echo "<div class='section'>";
    echo "<h3>2. Verificando Tabela classificacao_fiscal</h3>";
    
    // Obter estrutura do MapOS
    $mapos_columns = [];
    $result = $mapos_conn->query("DESCRIBE `classificacao_fiscal`");
    while ($row = $result->fetch_assoc()) {
        $mapos_columns[$row['Field']] = $row;
    }
    
    // Obter estrutura do AgilizePro
    $agilizepro_columns = [];
    $result = $agilizepro_conn->query("DESCRIBE `classificacao_fiscal`");
    while ($row = $result->fetch_assoc()) {
        $agilizepro_columns[$row['Field']] = $row;
    }
    
    $mapos_col_names = array_keys($mapos_columns);
    $agilizepro_col_names = array_keys($agilizepro_columns);
    
    $only_mapos = array_diff($mapos_col_names, $agilizepro_col_names);
    $only_agilizepro = array_diff($agilizepro_col_names, $mapos_col_names);
    
    echo "<h4>Colunas apenas no MapOS:</h4>";
    if (!empty($only_mapos)) {
        echo "<ul>";
        foreach ($only_mapos as $col) {
            echo "<li><strong>$col</strong> - " . $mapos_columns[$col]['Type'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nenhuma</p>";
    }
    
    echo "<h4>Colunas apenas no AgilizePro:</h4>";
    if (!empty($only_agilizepro)) {
        echo "<ul>";
        foreach ($only_agilizepro as $col) {
            echo "<li><strong>$col</strong> - " . $agilizepro_columns[$col]['Type'] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nenhuma</p>";
    }
    
    // Verificar se CLF_NATUREZA_CONTRIB existe mas CLF_NATUREZA_CONTRIBUINTE não
    if (in_array('CLF_NATUREZA_CONTRIB', $agilizepro_col_names) && !in_array('CLF_NATUREZA_CONTRIBUINTE', $agilizepro_col_names)) {
        echo "<p class='warning'><strong>Atenção:</strong> CLF_NATUREZA_CONTRIB existe mas CLF_NATUREZA_CONTRIBUINTE não. Será necessário migrar os dados.</p>";
    }
    echo "</div>";
    
    // 3. Gerar scripts de correção
    echo "<div class='section'>";
    echo "<h3>3. Scripts SQL para Correção</h3>";
    
    $scripts = [];
    $scripts[] = "-- Script para corrigir diferenças de estrutura";
    $scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
    $scripts[] = "-- Banco: {$agilizepro_config['db']}";
    $scripts[] = "";
    $scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
    $scripts[] = "";
    
    // Adicionar ten_id
    if (!empty($missing_ten_id)) {
        $scripts[] = "-- Adicionar ten_id nas tabelas que faltam";
        $scripts[] = "SET @default_tenant_id = 1;";
        $scripts[] = "";
        
        foreach ($missing_ten_id as $table) {
            $scripts[] = "ALTER TABLE `$table` ADD COLUMN `ten_id` INT(11) UNSIGNED NOT NULL DEFAULT @default_tenant_id;";
            $scripts[] = "ALTER TABLE `$table` ADD INDEX `idx_{$table}_ten_id` (`ten_id`);";
        }
        $scripts[] = "";
    }
    
    // Corrigir classificacao_fiscal
    if (in_array('CLF_NATUREZA_CONTRIB', $agilizepro_col_names) && !in_array('CLF_NATUREZA_CONTRIBUINTE', $agilizepro_col_names)) {
        $scripts[] = "-- Adicionar CLF_NATUREZA_CONTRIBUINTE e migrar dados";
        $scripts[] = "ALTER TABLE `classificacao_fiscal` ADD COLUMN IF NOT EXISTS `CLF_NATUREZA_CONTRIBUINTE` ENUM('Contribuinte','Não Contribuinte') NOT NULL DEFAULT 'Não Contribuinte';";
        $scripts[] = "";
        $scripts[] = "-- Migrar dados de CLF_NATUREZA_CONTRIB para CLF_NATUREZA_CONTRIBUINTE";
        $scripts[] = "UPDATE `classificacao_fiscal` SET `CLF_NATUREZA_CONTRIBUINTE` = CASE WHEN `CLF_NATUREZA_CONTRIB` = 'inscrito' OR `CLF_NATUREZA_CONTRIB` = 'Contribuinte' THEN 'Contribuinte' ELSE 'Não Contribuinte' END WHERE `CLF_NATUREZA_CONTRIB` IS NOT NULL;";
        $scripts[] = "";
    }
    
    // Adicionar colunas que existem no MapOS mas não no AgilizePro
    if (!empty($only_mapos)) {
        $scripts[] = "-- Adicionar colunas que existem no MapOS";
        foreach ($only_mapos as $col) {
            // Pular se já foi tratado acima
            if ($col == 'CLF_NATUREZA_CONTRIBUINTE') {
                continue; // Já foi tratado acima
            }
            
            $col_info = $mapos_columns[$col];
            $type = $col_info['Type'];
            $null = $col_info['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
            $default = $col_info['Default'] !== null ? "DEFAULT '" . addslashes($col_info['Default']) . "'" : '';
            $extra = $col_info['Extra'];
            
            $scripts[] = "ALTER TABLE `classificacao_fiscal` ADD COLUMN `$col` $type $null $default $extra;";
        }
        $scripts[] = "";
    }
    
    echo "<pre>" . htmlspecialchars(implode("\n", $scripts)) . "</pre>";
    
    // Salvar em arquivo
    $filename = 'fix_sync_differences_' . date('Y-m-d_His') . '.sql';
    file_put_contents($filename, implode("\n", $scripts));
    
    echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download>Baixar Script SQL</a></p>";
    echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do banco antes de executar!</p>";
    echo "</div>";
    
    $mapos_conn->close();
    $agilizepro_conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
