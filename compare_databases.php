<?php
/**
 * Script para comparar estrutura de banco de dados entre MapOS e AgilizePro
 * 
 * Uso:
 * php compare_databases.php
 * ou acesse via navegador: http://localhost/mapos/compare_databases.php
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
    'db' => 'agilizepro' // Altere para o nome do banco do AgilizePro
];

// Conectar aos bancos
try {
    $mapos_conn = new mysqli($mapos_config['host'], $mapos_config['user'], $mapos_config['pass'], $mapos_config['db']);
    if ($mapos_conn->connect_error) {
        die("Erro ao conectar ao MapOS: " . $mapos_conn->connect_error);
    }
    $mapos_conn->set_charset("utf8");
    
    $agilizepro_conn = new mysqli($agilizepro_config['host'], $agilizepro_config['user'], $agilizepro_config['pass'], $agilizepro_config['db']);
    if ($agilizepro_conn->connect_error) {
        die("Erro ao conectar ao AgilizePro: " . $agilizepro_conn->connect_error);
    }
    $agilizepro_conn->set_charset("utf8");
    
    echo "<h1>Comparação de Bancos de Dados</h1>";
    echo "<h2>MapOS vs AgilizePro</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .diff { background-color: #ffeb3b; }
        .missing { background-color: #f44336; color: white; }
        .new { background-color: #4CAF50; color: white; }
        .section { margin: 30px 0; padding: 15px; background-color: #f5f5f5; border-left: 4px solid #2196F3; }
        pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
    </style>";
    
    // 1. Comparar tabelas
    echo "<div class='section'>";
    echo "<h3>1. Comparação de Tabelas</h3>";
    
    $mapos_tables = [];
    $result = $mapos_conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $mapos_tables[] = $row[0];
    }
    
    $agilizepro_tables = [];
    $result = $agilizepro_conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $agilizepro_tables[] = $row[0];
    }
    
    $tables_only_mapos = array_diff($mapos_tables, $agilizepro_tables);
    $tables_only_agilizepro = array_diff($agilizepro_tables, $mapos_tables);
    $common_tables = array_intersect($mapos_tables, $agilizepro_tables);
    
    echo "<h4>Tabelas apenas no MapOS (" . count($tables_only_mapos) . "):</h4>";
    if (count($tables_only_mapos) > 0) {
        echo "<ul>";
        foreach ($tables_only_mapos as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nenhuma</p>";
    }
    
    echo "<h4>Tabelas apenas no AgilizePro (" . count($tables_only_agilizepro) . "):</h4>";
    if (count($tables_only_agilizepro) > 0) {
        echo "<ul>";
        foreach ($tables_only_agilizepro as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>Nenhuma</p>";
    }
    
    echo "<h4>Tabelas comuns (" . count($common_tables) . "):</h4>";
    echo "<p>Serão comparadas em detalhes abaixo</p>";
    echo "</div>";
    
    // 2. Comparar estrutura de tabelas comuns
    echo "<div class='section'>";
    echo "<h3>2. Comparação de Estrutura de Tabelas Comuns</h3>";
    
    $differences = [];
    $scripts_needed = [];
    
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
    
    // Exibir diferenças
    if (!empty($differences)) {
        echo "<table>";
        echo "<tr><th>Tabela</th><th>Colunas apenas no MapOS</th><th>Colunas apenas no AgilizePro</th><th>Colunas com diferenças</th></tr>";
        foreach ($differences as $table => $diff) {
            echo "<tr>";
            echo "<td><strong>$table</strong></td>";
            echo "<td>" . (count($diff['columns_only_mapos']) > 0 ? implode(', ', $diff['columns_only_mapos']) : '-') . "</td>";
            echo "<td>" . (count($diff['columns_only_agilizepro']) > 0 ? implode(', ', $diff['columns_only_agilizepro']) : '-') . "</td>";
            echo "<td>" . (count($diff['column_differences']) > 0 ? implode(', ', array_keys($diff['column_differences'])) : '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>Nenhuma diferença encontrada nas tabelas comuns!</p>";
    }
    echo "</div>";
    
    // 3. Gerar scripts SQL
    if (!empty($scripts_needed)) {
        echo "<div class='section'>";
        echo "<h3>3. Scripts SQL Necessários</h3>";
        echo "<p>Execute estes scripts no banco AgilizePro para sincronizar com o MapOS:</p>";
        
        $all_scripts = [];
        foreach ($scripts_needed as $table => $scripts) {
            echo "<h4>Tabela: $table</h4>";
            echo "<pre>";
            foreach ($scripts as $script) {
                echo $script . "\n";
                $all_scripts[] = $script;
            }
            echo "</pre>";
        }
        
        // Salvar em arquivo
        $filename = 'sync_agilizepro_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, "-- Scripts para sincronizar AgilizePro com MapOS\n");
        file_put_contents($filename, "-- Gerado em: " . date('Y-m-d H:i:s') . "\n\n", FILE_APPEND);
        file_put_contents($filename, implode("\n", $all_scripts), FILE_APPEND);
        
        echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download>$filename</a></p>";
        echo "</div>";
    }
    
    // 4. Tabelas que precisam ser criadas
    if (!empty($tables_only_mapos)) {
        echo "<div class='section'>";
        echo "<h3>4. Scripts CREATE TABLE para Tabelas Faltantes</h3>";
        echo "<p>Estas tabelas existem no MapOS mas não no AgilizePro. Será necessário criar:</p>";
        
        $create_scripts = [];
        foreach ($tables_only_mapos as $table) {
            $result = $mapos_conn->query("SHOW CREATE TABLE `$table`");
            $row = $result->fetch_assoc();
            $create_scripts[] = $row['Create Table'] . ";";
            
            echo "<h4>Tabela: $table</h4>";
            echo "<pre>" . htmlspecialchars($row['Create Table'] . ";") . "</pre>";
        }
        
        // Salvar em arquivo
        $filename = 'create_tables_agilizepro_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, "-- Scripts CREATE TABLE para AgilizePro\n");
        file_put_contents($filename, "-- Gerado em: " . date('Y-m-d H:i:s') . "\n\n", FILE_APPEND);
        file_put_contents($filename, implode("\n\n", $create_scripts), FILE_APPEND);
        
        echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download>$filename</a></p>";
        echo "</div>";
    }
    
    // Fechar conexões
    $mapos_conn->close();
    $agilizepro_conn->close();
    
    echo "<div class='section'>";
    echo "<h3>Comparação Concluída!</h3>";
    echo "<p>Verifique os arquivos SQL gerados acima.</p>";
    echo "</div>";
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
