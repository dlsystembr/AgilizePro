<?php
/**
 * Script para padronizar collation do banco de dados para utf8mb4_unicode_ci
 * Recomendado para português brasileiro
 * 
 * Uso:
 * php fix_collation_utf8mb4.php
 * ou acesse via navegador: http://localhost/mapos/fix_collation_utf8mb4.php
 */

// Configurações do banco
$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'mapos'
];

// Collation recomendada para português brasileiro
$charset = 'utf8mb4';
$collation = 'utf8mb4_unicode_ci'; // Melhor para português brasileiro

// Conectar ao banco
try {
    $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['db']);
    if ($conn->connect_error) {
        die("Erro ao conectar: " . $conn->connect_error);
    }
    $conn->set_charset($charset);
    
    echo "<h1>Padronização de Collation</h1>";
    echo "<h2>Banco: {$db_config['db']}</h2>";
    echo "<p><strong>Collation padrão:</strong> $collation</p>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #2196F3; color: white; }
        .warning { background-color: #ffeb3b; }
        .success { background-color: #4CAF50; color: white; }
        .section { margin: 30px 0; padding: 15px; background-color: #f5f5f5; border-left: 4px solid #2196F3; }
        pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin: 10px 0; }
    </style>";
    
    // 1. Verificar collation atual do banco
    echo "<div class='section'>";
    echo "<h3>1. Collation Atual do Banco</h3>";
    $result = $conn->query("SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
                           FROM information_schema.SCHEMATA 
                           WHERE SCHEMA_NAME = '{$db_config['db']}'");
    $db_info = $result->fetch_assoc();
    echo "<p><strong>Charset:</strong> {$db_info['DEFAULT_CHARACTER_SET_NAME']}</p>";
    echo "<p><strong>Collation:</strong> {$db_info['DEFAULT_COLLATION_NAME']}</p>";
    
    if ($db_info['DEFAULT_COLLATION_NAME'] != $collation) {
        echo "<p class='warning'><strong>Atenção:</strong> O banco precisa ser alterado para $collation</p>";
    } else {
        echo "<p class='success'>✓ Banco já está com a collation correta!</p>";
    }
    echo "</div>";
    
    // 2. Listar todas as tabelas e suas collations
    echo "<div class='section'>";
    echo "<h3>2. Collation das Tabelas</h3>";
    
    $tables = [];
    $result = $conn->query("SHOW TABLES");
    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }
    
    $tables_to_fix = [];
    echo "<table>";
    echo "<tr><th>Tabela</th><th>Charset</th><th>Collation</th><th>Status</th></tr>";
    
    foreach ($tables as $table) {
        $result = $conn->query("SELECT TABLE_COLLATION 
                                FROM information_schema.TABLES 
                                WHERE TABLE_SCHEMA = '{$db_config['db']}' 
                                AND TABLE_NAME = '$table'");
        $table_info = $result->fetch_assoc();
        $table_collation = $table_info['TABLE_COLLATION'];
        
        // Extrair charset da collation
        $table_charset = explode('_', $table_collation)[0];
        
        $status = '';
        if ($table_collation != $collation) {
            $status = "<span class='warning'>⚠ Precisa corrigir</span>";
            $tables_to_fix[] = $table;
        } else {
            $status = "<span class='success'>✓ OK</span>";
        }
        
        echo "<tr>";
        echo "<td><strong>$table</strong></td>";
        echo "<td>$table_charset</td>";
        echo "<td>$table_collation</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // 3. Listar colunas com collation incorreta
    echo "<div class='section'>";
    echo "<h3>3. Colunas com Collation Incorreta</h3>";
    
    $columns_to_fix = [];
    $result = $conn->query("
        SELECT TABLE_NAME, COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME, DATA_TYPE
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = '{$db_config['db']}'
        AND CHARACTER_SET_NAME IS NOT NULL
        AND COLLATION_NAME != '$collation'
        AND DATA_TYPE IN ('varchar', 'char', 'text', 'tinytext', 'mediumtext', 'longtext', 'enum', 'set')
        ORDER BY TABLE_NAME, ORDINAL_POSITION
    ");
    
    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Tabela</th><th>Coluna</th><th>Charset</th><th>Collation Atual</th><th>Tipo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $columns_to_fix[] = $row;
            echo "<tr>";
            echo "<td>{$row['TABLE_NAME']}</td>";
            echo "<td>{$row['COLUMN_NAME']}</td>";
            echo "<td>{$row['CHARACTER_SET_NAME']}</td>";
            echo "<td>{$row['COLLATION_NAME']}</td>";
            echo "<td>{$row['DATA_TYPE']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Total de colunas a corrigir:</strong> " . count($columns_to_fix) . "</p>";
    } else {
        echo "<p class='success'>✓ Todas as colunas já estão com a collation correta!</p>";
    }
    echo "</div>";
    
    // 4. Gerar scripts SQL
    if (!empty($tables_to_fix) || !empty($columns_to_fix)) {
        echo "<div class='section'>";
        echo "<h3>4. Scripts SQL para Padronização</h3>";
        
        $scripts = [];
        
        // Script para alterar collation do banco
        if ($db_info['DEFAULT_COLLATION_NAME'] != $collation) {
            $scripts[] = "-- Alterar collation do banco de dados";
            $scripts[] = "ALTER DATABASE `{$db_config['db']}` CHARACTER SET $charset COLLATE $collation;";
            $scripts[] = "";
        }
        
        // Scripts para alterar collation das tabelas
        if (!empty($tables_to_fix)) {
            $scripts[] = "-- Alterar collation das tabelas";
            foreach ($tables_to_fix as $table) {
                $scripts[] = "ALTER TABLE `$table` CONVERT TO CHARACTER SET $charset COLLATE $collation;";
            }
            $scripts[] = "";
        }
        
        // Scripts para alterar collation de colunas específicas (se necessário)
        if (!empty($columns_to_fix)) {
            $scripts[] = "-- Alterar collation de colunas específicas";
            $current_table = '';
            foreach ($columns_to_fix as $col) {
                if ($current_table != $col['TABLE_NAME']) {
                    $current_table = $col['TABLE_NAME'];
                    $scripts[] = "-- Tabela: $current_table";
                }
                
                $col_type = strtoupper($col['DATA_TYPE']);
                $scripts[] = "ALTER TABLE `{$col['TABLE_NAME']}` MODIFY COLUMN `{$col['COLUMN_NAME']}` $col_type CHARACTER SET $charset COLLATE $collation;";
            }
        }
        
        echo "<pre>" . htmlspecialchars(implode("\n", $scripts)) . "</pre>";
        
        // Salvar em arquivo
        $filename = 'fix_collation_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, "-- Script para padronizar collation para $collation\n");
        file_put_contents($filename, "-- Gerado em: " . date('Y-m-d H:i:s') . "\n");
        file_put_contents($filename, "-- Banco: {$db_config['db']}\n\n", FILE_APPEND);
        file_put_contents($filename, implode("\n", $scripts), FILE_APPEND);
        
        echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download class='btn'>Baixar Script SQL</a></p>";
        echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do banco antes de executar os scripts!</p>";
        echo "</div>";
    } else {
        echo "<div class='section'>";
        echo "<h3>4. Status</h3>";
        echo "<p class='success'>✓ Tudo já está padronizado com $collation!</p>";
        echo "</div>";
    }
    
    // 5. Informações sobre collations
    echo "<div class='section'>";
    echo "<h3>5. Informações sobre Collations</h3>";
    echo "<h4>Para Português Brasileiro, use:</h4>";
    echo "<ul>";
    echo "<li><strong>utf8mb4_unicode_ci</strong> (Recomendado) - Melhor para português, suporta emojis e caracteres especiais</li>";
    echo "<li><strong>utf8mb4_unicode_520_ci</strong> - Versão mais recente (se disponível no seu MySQL)</li>";
    echo "<li><strong>utf8mb4_general_ci</strong> - Mais rápida, mas menos precisa para português</li>";
    echo "</ul>";
    echo "<h4>Não use:</h4>";
    echo "<ul>";
    echo "<li><strong>utf8_general_ci</strong> - Antigo, não suporta emojis (4 bytes)</li>";
    echo "<li><strong>latin1</strong> - Não suporta acentos corretamente</li>";
    echo "</ul>";
    echo "</div>";
    
    $conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
