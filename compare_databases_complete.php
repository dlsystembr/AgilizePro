<?php
/**
 * Script COMPLETO para comparar estrutura de banco de dados entre MapOS e AgilizePro
 * Versão melhorada com mais detalhes e verificações
 * 
 * Uso:
 * Acesse via navegador: http://localhost/mapos/compare_databases_complete.php
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

// Conectar aos bancos
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
    
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Comparação Completa de Bancos</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h1 { color: #2196F3; }
        h2 { color: #4CAF50; border-bottom: 2px solid #4CAF50; padding-bottom: 5px; }
        h3 { color: #f44336; margin-top: 30px; }
        h4 { color: #666; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; background-color: white; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #4CAF50; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        tr:hover { background-color: #f5f5f5; }
        .diff { background-color: #ffeb3b; }
        .missing { background-color: #f44336; color: white; }
        .new { background-color: #4CAF50; color: white; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; border-left: 4px solid #ff9800; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; border-left: 4px solid #2e7d32; }
        .error { background-color: #f44336; color: white; padding: 10px; margin: 10px 0; border-left: 4px solid #c62828; }
        .section { margin: 30px 0; padding: 20px; background-color: white; border-left: 4px solid #2196F3; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        pre { background-color: #f5f5f5; padding: 15px; overflow-x: auto; border: 1px solid #ddd; border-radius: 4px; }
        .summary { background-color: #e3f2fd; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .summary-item { margin: 5px 0; }
        ul { margin: 10px 0; padding-left: 20px; }
        li { margin: 5px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .btn:hover { background-color: #45a049; }
        .col-detail { font-size: 0.9em; color: #666; }
    </style></head><body>";
    
    echo "<h1>🔍 Comparação Completa de Bancos de Dados</h1>";
    echo "<div class='summary'>";
    echo "<h2>MapOS vs AgilizePro</h2>";
    echo "<p><strong>Data da comparação:</strong> " . date('d/m/Y H:i:s') . "</p>";
    echo "</div>";
    
    // 1. Comparar tabelas
    echo "<div class='section'>";
    echo "<h3>1. 📊 Comparação de Tabelas</h3>";
    
    $mapos_tables = [];
    $result = $mapos_conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $mapos_tables[] = $row[0];
    }
    sort($mapos_tables);
    
    $agilizepro_tables = [];
    $result = $agilizepro_conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $agilizepro_tables[] = $row[0];
    }
    sort($agilizepro_tables);
    
    $tables_only_mapos = array_diff($mapos_tables, $agilizepro_tables);
    $tables_only_agilizepro = array_diff($agilizepro_tables, $mapos_tables);
    $common_tables = array_intersect($mapos_tables, $agilizepro_tables);
    
    echo "<div class='summary'>";
    echo "<div class='summary-item'><strong>Total de tabelas no MapOS:</strong> " . count($mapos_tables) . "</div>";
    echo "<div class='summary-item'><strong>Total de tabelas no AgilizePro:</strong> " . count($agilizepro_tables) . "</div>";
    echo "<div class='summary-item'><strong>Tabelas comuns:</strong> " . count($common_tables) . "</div>";
    echo "<div class='summary-item'><strong>Tabelas apenas no MapOS:</strong> " . count($tables_only_mapos) . "</div>";
    echo "<div class='summary-item'><strong>Tabelas apenas no AgilizePro:</strong> " . count($tables_only_agilizepro) . "</div>";
    echo "</div>";
    
    if (count($tables_only_mapos) > 0) {
        echo "<h4>⚠️ Tabelas apenas no MapOS (" . count($tables_only_mapos) . "):</h4>";
        echo "<ul>";
        foreach ($tables_only_mapos as $table) {
            echo "<li><strong>$table</strong></li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='success'>✓ Nenhuma tabela exclusiva do MapOS</p>";
    }
    
    if (count($tables_only_agilizepro) > 0) {
        echo "<h4>⚠️ Tabelas apenas no AgilizePro (" . count($tables_only_agilizepro) . "):</h4>";
        echo "<ul>";
        foreach ($tables_only_agilizepro as $table) {
            echo "<li><strong>$table</strong></li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='success'>✓ Nenhuma tabela exclusiva do AgilizePro</p>";
    }
    echo "</div>";
    
    // 2. Comparar estrutura de tabelas comuns
    echo "<div class='section'>";
    echo "<h3>2. 🔧 Comparação Detalhada de Estrutura de Tabelas Comuns</h3>";
    
    $differences = [];
    $scripts_needed = [];
    $total_differences = 0;
    
    foreach ($common_tables as $table) {
        // Obter estrutura do MapOS
        $mapos_structure = [];
        $result = $mapos_conn->query("DESCRIBE `$table`");
        while ($row = $result->fetch_assoc()) {
            $mapos_structure[$row['Field']] = $row;
        }
        
        // Obter estrutura do AgilizePro
        $agilizepro_structure = [];
        $result = $agilizepro_conn->query("DESCRIBE `$table`");
        while ($row = $result->fetch_assoc()) {
            $agilizepro_structure[$row['Field']] = $row;
        }
        
        // Comparar colunas
        $mapos_columns = array_keys($mapos_structure);
        $agilizepro_columns = array_keys($agilizepro_structure);
        
        $columns_only_mapos = array_diff($mapos_columns, $agilizepro_columns);
        $columns_only_agilizepro = array_diff($agilizepro_columns, $mapos_columns);
        $common_columns = array_intersect($mapos_columns, $agilizepro_columns);
        
        // Verificar diferenças em colunas comuns
        $column_differences = [];
        foreach ($common_columns as $column) {
            $mapos_col = $mapos_structure[$column];
            $agilizepro_col = $agilizepro_structure[$column];
            
            $diff = [];
            if ($mapos_col['Type'] != $agilizepro_col['Type']) {
                $diff['Type'] = ['mapos' => $mapos_col['Type'], 'agilizepro' => $agilizepro_col['Type']];
            }
            if ($mapos_col['Null'] != $agilizepro_col['Null']) {
                $diff['Null'] = ['mapos' => $mapos_col['Null'], 'agilizepro' => $agilizepro_col['Null']];
            }
            if ($mapos_col['Key'] != $agilizepro_col['Key']) {
                $diff['Key'] = ['mapos' => $mapos_col['Key'], 'agilizepro' => $agilizepro_col['Key']];
            }
            if ($mapos_col['Default'] != $agilizepro_col['Default']) {
                $diff['Default'] = ['mapos' => $mapos_col['Default'], 'agilizepro' => $agilizepro_col['Default']];
            }
            if ($mapos_col['Extra'] != $agilizepro_col['Extra']) {
                $diff['Extra'] = ['mapos' => $mapos_col['Extra'], 'agilizepro' => $agilizepro_col['Extra']];
            }
            
            if (!empty($diff)) {
                $column_differences[$column] = $diff;
            }
        }
        
        // Se houver diferenças, adicionar ao relatório
        if (!empty($columns_only_mapos) || !empty($columns_only_agilizepro) || !empty($column_differences)) {
            $differences[$table] = [
                'columns_only_mapos' => $columns_only_mapos,
                'columns_only_agilizepro' => $columns_only_agilizepro,
                'column_differences' => $column_differences
            ];
            
            $total_differences += count($columns_only_mapos) + count($columns_only_agilizepro) + count($column_differences);
            
            // Gerar scripts SQL para sincronizar
            $scripts = [];
            
            // Scripts para adicionar colunas que existem no MapOS mas não no AgilizePro
            foreach ($columns_only_mapos as $column) {
                $col_info = $mapos_structure[$column];
                $type = $col_info['Type'];
                $null = $col_info['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col_info['Default'] !== null ? "DEFAULT '" . addslashes($col_info['Default']) . "'" : '';
                $extra = $col_info['Extra'];
                
                $scripts[] = "ALTER TABLE `$table` ADD COLUMN `$column` $type $null $default $extra;";
            }
            
            // Scripts para modificar colunas que diferem
            foreach ($column_differences as $column => $diff) {
                $col_info = $mapos_structure[$column];
                $type = $col_info['Type'];
                $null = $col_info['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col_info['Default'] !== null ? "DEFAULT '" . addslashes($col_info['Default']) . "'" : '';
                $extra = $col_info['Extra'];
                
                $scripts[] = "ALTER TABLE `$table` MODIFY COLUMN `$column` $type $null $default $extra;";
            }
            
            if (!empty($scripts)) {
                $scripts_needed[$table] = $scripts;
            }
        }
    }
    
    // Exibir resumo
    echo "<div class='summary'>";
    echo "<h4>📈 Resumo de Diferenças</h4>";
    echo "<div class='summary-item'><strong>Total de tabelas com diferenças:</strong> " . count($differences) . "</div>";
    echo "<div class='summary-item'><strong>Total de diferenças encontradas:</strong> " . $total_differences . "</div>";
    echo "</div>";
    
    // Exibir diferenças em tabela
    if (!empty($differences)) {
        echo "<table>";
        echo "<tr><th>Tabela</th><th>Colunas apenas no MapOS</th><th>Colunas apenas no AgilizePro</th><th>Colunas com diferenças</th></tr>";
        foreach ($differences as $table => $diff) {
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>" . (count($diff['columns_only_mapos']) > 0 ? "<span class='new'>" . implode(', ', $diff['columns_only_mapos']) . "</span>" : '<span class="success">-</span>') . "</td>";
            echo "<td>" . (count($diff['columns_only_agilizepro']) > 0 ? "<span class='missing'>" . implode(', ', $diff['columns_only_agilizepro']) . "</span>" : '<span class="success">-</span>') . "</td>";
            echo "<td>" . (count($diff['column_differences']) > 0 ? "<span class='diff'>" . implode(', ', array_keys($diff['column_differences'])) . "</span>" : '<span class="success">-</span>') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Detalhes das diferenças
        echo "<h4>📋 Detalhes das Diferenças</h4>";
        foreach ($differences as $table => $diff) {
            if (!empty($diff['column_differences'])) {
                echo "<div class='section'>";
                echo "<h5>Tabela: <strong>$table</strong></h5>";
                
                foreach ($diff['column_differences'] as $column => $changes) {
                    echo "<p><strong>Coluna: $column</strong></p>";
                    echo "<ul>";
                    foreach ($changes as $attr => $values) {
                        echo "<li><strong>$attr:</strong> MapOS = <code>{$values['mapos']}</code>, AgilizePro = <code>{$values['agilizepro']}</code></li>";
                    }
                    echo "</ul>";
                }
                echo "</div>";
            }
        }
    } else {
        echo "<p class='success'>✓ Nenhuma diferença encontrada nas tabelas comuns!</p>";
    }
    echo "</div>";
    
    // 3. Gerar scripts SQL
    if (!empty($scripts_needed)) {
        echo "<div class='section'>";
        echo "<h3>3. 📝 Scripts SQL Necessários para Sincronização</h3>";
        echo "<p class='warning'>⚠️ <strong>IMPORTANTE:</strong> Faça backup do banco AgilizePro antes de executar estes scripts!</p>";
        echo "<p>Execute estes scripts no banco AgilizePro para sincronizar com o MapOS:</p>";
        
        $all_scripts = [];
        $all_scripts[] = "-- Scripts para sincronizar AgilizePro com MapOS";
        $all_scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
        $all_scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
        $all_scripts[] = "";
        
        foreach ($scripts_needed as $table => $scripts) {
            echo "<h4>Tabela: <strong>$table</strong></h4>";
            echo "<pre>";
            foreach ($scripts as $script) {
                echo htmlspecialchars($script) . "\n";
                $all_scripts[] = $script;
            }
            echo "</pre>";
        }
        
        // Salvar em arquivo
        $filename = 'sync_agilizepro_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, implode("\n", $all_scripts));
        
        echo "<p><strong>📥 Arquivo gerado:</strong> <a href='$filename' download class='btn'>Baixar Script SQL Completo</a></p>";
        echo "</div>";
    }
    
    // 4. Tabelas que precisam ser criadas
    if (!empty($tables_only_mapos)) {
        echo "<div class='section'>";
        echo "<h3>4. 🆕 Scripts CREATE TABLE para Tabelas Faltantes</h3>";
        echo "<p class='warning'>⚠️ Estas tabelas existem no MapOS mas não no AgilizePro. Será necessário criar:</p>";
        
        $create_scripts = [];
        $create_scripts[] = "-- Scripts CREATE TABLE para AgilizePro";
        $create_scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
        $create_scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
        $create_scripts[] = "";
        
        foreach ($tables_only_mapos as $table) {
            $result = $mapos_conn->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_assoc();
            $create_scripts[] = $row['Create Table'] . ";";
            $create_scripts[] = "";
            
            echo "<h4>Tabela: <strong>$table</strong></h4>";
            echo "<pre>" . htmlspecialchars($row['Create Table'] . ";") . "</pre>";
        }
        
        // Salvar em arquivo
        $filename = 'create_tables_agilizepro_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, implode("\n", $create_scripts));
        
        echo "<p><strong>📥 Arquivo gerado:</strong> <a href='$filename' download class='btn'>Baixar Script CREATE TABLE</a></p>";
        echo "</div>";
    }
    
    // Fechar conexões
    $mapos_conn->close();
    $agilizepro_conn->close();
    
    echo "<div class='section'>";
    echo "<h3>✅ Comparação Concluída!</h3>";
    echo "<p>Verifique os arquivos SQL gerados acima e execute-os no banco AgilizePro após fazer backup.</p>";
    echo "<p><strong>Ordem recomendada de execução:</strong></p>";
    echo "<ol>";
    echo "<li>Fazer backup do banco AgilizePro</li>";
    echo "<li>Executar scripts de correção (fix_uf_origem.sql, fix_invalid_dates.sql, fix_sync_differences.sql)</li>";
    echo "<li>Executar scripts CREATE TABLE (se houver tabelas faltantes)</li>";
    echo "<li>Executar scripts de sincronização (sync_agilizepro_*.sql)</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "</body></html>";
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
