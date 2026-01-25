<?php
/**
 * Script para gerar script SQL completo para padronizar collation
 * 
 * Uso:
 * php generate_fix_collation_script.php
 * ou acesse via navegador: http://localhost/mapos/generate_fix_collation_script.php
 */

$db_config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'db' => 'mapos'
];

$charset = 'utf8mb4';
$collation = 'utf8mb4_unicode_ci';

try {
    $conn = new mysqli($db_config['host'], $db_config['user'], $db_config['pass'], $db_config['db']);
    if ($conn->connect_error) {
        die("Erro ao conectar: " . $conn->connect_error);
    }
    $conn->set_charset($charset);
    
    $scripts = [];
    $scripts[] = "-- Script para padronizar collation para $collation";
    $scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
    $scripts[] = "-- Banco: {$db_config['db']}";
    $scripts[] = "";
    $scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
    $scripts[] = "";
    
    // 1. Alterar collation do banco
    $scripts[] = "-- ============================================";
    $scripts[] = "-- 1. ALTERAR COLLATION DO BANCO";
    $scripts[] = "-- ============================================";
    $scripts[] = "ALTER DATABASE `{$db_config['db']}` CHARACTER SET $charset COLLATE $collation;";
    $scripts[] = "";
    
    // 2. Listar tabelas que precisam ser alteradas
    $scripts[] = "-- ============================================";
    $scripts[] = "-- 2. ALTERAR COLLATION DAS TABELAS";
    $scripts[] = "-- ============================================";
    
    $result = $conn->query("
        SELECT TABLE_NAME, TABLE_COLLATION
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = '{$db_config['db']}'
        AND TABLE_COLLATION != '$collation'
        ORDER BY TABLE_NAME
    ");
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $scripts[] = "ALTER TABLE `{$row['TABLE_NAME']}` CONVERT TO CHARACTER SET $charset COLLATE $collation;";
        }
    } else {
        $scripts[] = "-- Todas as tabelas já estão com a collation correta!";
    }
    
    $scripts[] = "";
    
    // 3. Verificação final
    $scripts[] = "-- ============================================";
    $scripts[] = "-- 3. VERIFICAR COLLATION APÓS EXECUÇÃO";
    $scripts[] = "-- ============================================";
    $scripts[] = "-- Execute estas queries para verificar:";
    $scripts[] = "";
    $scripts[] = "-- Ver collation do banco:";
    $scripts[] = "SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME";
    $scripts[] = "FROM information_schema.SCHEMATA";
    $scripts[] = "WHERE SCHEMA_NAME = '{$db_config['db']}';";
    $scripts[] = "";
    $scripts[] = "-- Ver collation de todas as tabelas:";
    $scripts[] = "SELECT TABLE_NAME, TABLE_COLLATION";
    $scripts[] = "FROM information_schema.TABLES";
    $scripts[] = "WHERE TABLE_SCHEMA = '{$db_config['db']}'";
    $scripts[] = "ORDER BY TABLE_NAME;";
    
    $sql_content = implode("\n", $scripts);
    
    // Salvar arquivo
    $filename = 'fix_collation_completo_' . date('Y-m-d_His') . '.sql';
    file_put_contents($filename, $sql_content);
    
    // Exibir
    header('Content-Type: text/html; charset=utf-8');
    echo "<h1>Script SQL Gerado</h1>";
    echo "<p><strong>Arquivo:</strong> <a href='$filename' download>$filename</a></p>";
    echo "<h2>Conteúdo do Script:</h2>";
    echo "<pre style='background: #f5f5f5; padding: 15px; overflow-x: auto;'>";
    echo htmlspecialchars($sql_content);
    echo "</pre>";
    echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do banco antes de executar!</p>";
    echo "<p><strong>Como executar:</strong></p>";
    echo "<pre>mysql -u root -p {$db_config['db']} < $filename</pre>";
    
    $conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
