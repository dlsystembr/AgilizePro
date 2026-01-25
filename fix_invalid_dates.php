<?php
/**
 * Script para corrigir datas inválidas (0000-00-00) no banco de dados
 * 
 * Uso:
 * php fix_invalid_dates.php
 * ou acesse via navegador: http://localhost/mapos/fix_invalid_dates.php
 */

// Configurações do banco
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'agilizepro' // Altere para o banco que está com erro
];

try {
    $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['db']);
    if ($conn->connect_error) {
        die("Erro ao conectar: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    
    // Desabilitar modo strict temporariamente para permitir verificação
    $conn->query("SET SESSION sql_mode = ''");
    
    echo "<h1>Correção de Datas Inválidas</h1>";
    echo "<h2>Banco: {$db_config['db']}</h2>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f44336; color: white; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
        .section { margin: 30px 0; padding: 15px; background-color: #f5f5f5; border-left: 4px solid #2196F3; }
        pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin: 10px 0; cursor: pointer; border: none; }
    </style>";
    
    echo "<div class='warning'>";
    echo "<strong>⚠️ ATENÇÃO:</strong> Este script irá corrigir datas inválidas (0000-00-00) convertendo-as para NULL.";
    echo "</div>";
    
    // 1. Encontrar todas as colunas DATE/DATETIME com valores inválidos
    echo "<div class='section'>";
    echo "<h3>1. Buscando Colunas com Datas Inválidas</h3>";
    
    $invalid_dates = [];
    
    // Buscar todas as colunas do tipo DATE, DATETIME, TIMESTAMP
    $result = $conn->query("
        SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = '{$db_config['db']}'
        AND DATA_TYPE IN ('date', 'datetime', 'timestamp')
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ");
    
    $date_columns = [];
    while ($row = $result->fetch_assoc()) {
        $date_columns[] = $row;
    }
    
    echo "<p><strong>Total de colunas de data encontradas:</strong> " . count($date_columns) . "</p>";
    
    // Verificar cada coluna
    foreach ($date_columns as $col) {
        $table = $col['TABLE_NAME'];
        $column = $col['COLUMN_NAME'];
        $data_type = $col['DATA_TYPE'];
        $is_nullable = $col['IS_NULLABLE'] == 'YES';
        
        // Verificar se há valores inválidos
        $check_query = "SELECT COUNT(*) as total 
                       FROM `$table` 
                       WHERE `$column` = '0000-00-00' 
                       OR `$column` = '0000-00-00 00:00:00'";
        
        $check_result = $conn->query($check_query);
        if ($check_result) {
            $check_row = $check_result->fetch_assoc();
            if ($check_row['total'] > 0) {
                $invalid_dates[] = [
                    'table' => $table,
                    'column' => $column,
                    'data_type' => $data_type,
                    'is_nullable' => $is_nullable,
                    'count' => $check_row['total']
                ];
            }
        }
    }
    
    if (empty($invalid_dates)) {
        echo "<p class='success'>✓ Nenhuma data inválida encontrada!</p>";
    } else {
        echo "<p class='warning'><strong>Encontradas " . count($invalid_dates) . " colunas com datas inválidas:</strong></p>";
        echo "<table>";
        echo "<tr><th>Tabela</th><th>Coluna</th><th>Tipo</th><th>Registros com 0000-00-00</th><th>Permite NULL</th></tr>";
        foreach ($invalid_dates as $item) {
            echo "<tr>";
            echo "<td><strong>{$item['table']}</strong></td>";
            echo "<td>{$item['column']}</td>";
            echo "<td>{$item['data_type']}</td>";
            echo "<td>{$item['count']}</td>";
            echo "<td>" . ($item['is_nullable'] ? 'Sim' : 'Não') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    echo "</div>";
    
    // 2. Gerar scripts de correção
    if (!empty($invalid_dates)) {
        echo "<div class='section'>";
        echo "<h3>2. Scripts SQL para Correção</h3>";
        
        $scripts = [];
        $scripts[] = "-- Script para corrigir datas inválidas (0000-00-00)";
        $scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
        $scripts[] = "-- Banco: {$db_config['db']}";
        $scripts[] = "";
        $scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
        $scripts[] = "";
        $scripts[] = "-- Desabilitar modo strict temporariamente";
        $scripts[] = "SET SESSION sql_mode = '';";
        $scripts[] = "";
        
        $current_table = '';
        foreach ($invalid_dates as $item) {
            if ($current_table != $item['table']) {
                $current_table = $item['table'];
                $scripts[] = "-- Tabela: $current_table";
            }
            
            // Se a coluna permite NULL, converter para NULL
            // Se não permite NULL, usar uma data padrão (1900-01-01 ou data atual)
            if ($item['is_nullable']) {
                $scripts[] = "UPDATE `{$item['table']}` SET `{$item['column']}` = NULL WHERE `{$item['column']}` = '0000-00-00' OR `{$item['column']}` = '0000-00-00 00:00:00';";
            } else {
                // Se não permite NULL, usar uma data padrão segura
                if ($item['data_type'] == 'date') {
                    $scripts[] = "UPDATE `{$item['table']}` SET `{$item['column']}` = '1900-01-01' WHERE `{$item['column']}` = '0000-00-00' OR `{$item['column']}` = '0000-00-00 00:00:00';";
                } else {
                    $scripts[] = "UPDATE `{$item['table']}` SET `{$item['column']}` = '1900-01-01 00:00:00' WHERE `{$item['column']}` = '0000-00-00' OR `{$item['column']}` = '0000-00-00 00:00:00';";
                }
            }
        }
        
        $scripts[] = "";
        $scripts[] = "-- Reabilitar modo strict (recomendado)";
        $scripts[] = "SET SESSION sql_mode = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';";
        
        echo "<pre>" . htmlspecialchars(implode("\n", $scripts)) . "</pre>";
        
        // Salvar em arquivo
        $filename = 'fix_invalid_dates_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, implode("\n", $scripts));
        
        echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download class='btn'>Baixar Script SQL</a></p>";
        echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do banco antes de executar!</p>";
        echo "</div>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
