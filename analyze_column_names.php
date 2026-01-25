<?php
/**
 * Script para analisar nomes de colunas e identificar quais precisam ser padronizadas para minúsculas
 */

$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'mapos'
];

try {
    $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['db']);
    if ($conn->connect_error) {
        die("Erro ao conectar: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Análise de Colunas</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        h1 { color: #2196F3; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; background-color: white; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
        .section { margin: 30px 0; padding: 20px; background-color: white; border-left: 4px solid #2196F3; }
        pre { background-color: #f5f5f5; padding: 15px; overflow-x: auto; }
        .summary { background-color: #e3f2fd; padding: 15px; margin: 20px 0; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
    </style></head><body>";
    
    echo "<h1>Análise de Nomes de Colunas</h1>";
    
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    $columns_to_rename = [];
    $total_columns = 0;
    $columns_uppercase = 0;
    $columns_mixed = 0;
    $columns_lowercase = 0;
    
    foreach ($tables as $table) {
        $result = $conn->query("DESCRIBE `$table`");
        $table_columns = [];
        
        while ($row = $result->fetch_assoc()) {
            $column_name = $row['Field'];
            $total_columns++;
            
            $is_lowercase = (strtolower($column_name) === $column_name);
            $is_uppercase = (strtoupper($column_name) === $column_name && $column_name !== strtolower($column_name));
            $is_mixed = !$is_lowercase && !$is_uppercase;
            
            if ($is_uppercase) {
                $columns_uppercase++;
            } elseif ($is_mixed) {
                $columns_mixed++;
            } else {
                $columns_lowercase++;
            }
            
            if (!$is_lowercase) {
                $new_name = strtolower($column_name);
                $table_columns[] = [
                    'old' => $column_name,
                    'new' => $new_name,
                    'type' => $is_uppercase ? 'uppercase' : 'mixed',
                    'definition' => $row
                ];
            }
        }
        
        if (!empty($table_columns)) {
            $columns_to_rename[$table] = $table_columns;
        }
    }
    
    echo "<div class='summary'>";
    echo "<h2>Resumo</h2>";
    echo "<p>Total de colunas: $total_columns</p>";
    echo "<p>Colunas em minúsculas: <span style='color: #4CAF50;'>$columns_lowercase</span></p>";
    echo "<p>Colunas em MAIÚSCULAS: <span style='color: #f44336;'>$columns_uppercase</span></p>";
    echo "<p>Colunas mistas: <span style='color: #ff9800;'>$columns_mixed</span></p>";
    echo "<p>Tabelas com colunas a renomear: " . count($columns_to_rename) . "</p>";
    echo "</div>";
    
    if (!empty($columns_to_rename)) {
        echo "<div class='section'>";
        echo "<h2>Colunas que precisam ser renomeadas</h2>";
        echo "<table>";
        echo "<tr><th>Tabela</th><th>Coluna Atual</th><th>Coluna Nova</th></tr>";
        
        foreach ($columns_to_rename as $table => $columns) {
            foreach ($columns as $col) {
                echo "<tr>";
                echo "<td><strong>$table</strong></td>";
                echo "<td><code>{$col['old']}</code></td>";
                echo "<td><code style='color: #4CAF50;'>{$col['new']}</code></td>";
                echo "</tr>";
            }
        }
        echo "</table>";
        echo "</div>";
        
        // Gerar scripts SQL
        $scripts = [];
        $scripts[] = "-- Script para padronizar nomes de colunas para minúsculas";
        $scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
        $scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
        $scripts[] = "";
        
        foreach ($columns_to_rename as $table => $columns) {
            $scripts[] = "-- Tabela: $table";
            foreach ($columns as $col) {
                $type = $col['definition']['Type'];
                $null = $col['definition']['Null'] == 'YES' ? 'NULL' : 'NOT NULL';
                $default = $col['definition']['Default'] !== null ? "DEFAULT '" . addslashes($col['definition']['Default']) . "'" : '';
                $extra = $col['definition']['Extra'];
                
                $scripts[] = "ALTER TABLE `$table` CHANGE COLUMN `{$col['old']}` `{$col['new']}` $type $null $default $extra;";
            }
            $scripts[] = "";
        }
        
        echo "<div class='section'>";
        echo "<h2>Script SQL Gerado</h2>";
        echo "<pre>" . htmlspecialchars(implode("\n", $scripts)) . "</pre>";
        
        $filename = 'rename_columns_lowercase_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, implode("\n", $scripts));
        echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download class='btn'>Baixar SQL</a></p>";
        echo "</div>";
        
        // Gerar JSON de mapeamento
        $replacements = [];
        foreach ($columns_to_rename as $table => $columns) {
            foreach ($columns as $col) {
                $replacements[] = [
                    'table' => $table,
                    'old' => $col['old'],
                    'new' => $col['new']
                ];
            }
        }
        
        $json_filename = 'column_rename_mapping_' . date('Y-m-d_His') . '.json';
        file_put_contents($json_filename, json_encode($replacements, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo "<p><strong>Mapeamento JSON:</strong> <a href='$json_filename' download class='btn'>Baixar JSON</a></p>";
    } else {
        echo "<p class='success'>Todas as colunas já estão em minúsculas!</p>";
    }
    
    $conn->close();
    echo "</body></html>";
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
