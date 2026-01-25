<?php
/**
 * Script para corrigir valores truncados na coluna uf_origem
 * 
 * Uso:
 * php fix_uf_origem_truncate.php
 * ou acesse via navegador: http://localhost/mapos/fix_uf_origem_truncate.php
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
    
    echo "<h1>Correção de Valores Truncados - uf_origem</h1>";
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
    </style>";
    
    // 1. Verificar estrutura da coluna
    echo "<div class='section'>";
    echo "<h3>1. Estrutura da Coluna uf_origem</h3>";
    
    $result = $conn->query("
        SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, IS_NULLABLE, COLUMN_DEFAULT
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = '{$db_config['db']}'
        AND TABLE_NAME = 'aliquotas'
        AND COLUMN_NAME = 'uf_origem'
    ");
    
    if ($result && $result->num_rows > 0) {
        $col_info = $result->fetch_assoc();
        echo "<p><strong>Tipo:</strong> {$col_info['DATA_TYPE']}</p>";
        echo "<p><strong>Tamanho máximo:</strong> {$col_info['CHARACTER_MAXIMUM_LENGTH']} caracteres</p>";
        echo "<p><strong>Permite NULL:</strong> " . ($col_info['IS_NULLABLE'] == 'YES' ? 'Sim' : 'Não') . "</p>";
    } else {
        echo "<p class='warning'>Coluna uf_origem não encontrada na tabela aliquotas!</p>";
    }
    echo "</div>";
    
    // 2. Verificar valores problemáticos
    echo "<div class='section'>";
    echo "<h3>2. Valores Problemáticos na Coluna uf_origem</h3>";
    
    // Buscar registros com valores que podem causar truncamento
    $result = $conn->query("
        SELECT id, uf_origem, uf_destino, LENGTH(uf_origem) as tamanho, 
               HEX(uf_origem) as hex_value
        FROM aliquotas
        WHERE LENGTH(uf_origem) > 2
           OR uf_origem LIKE '% %'
           OR TRIM(uf_origem) != uf_origem
           OR uf_origem = ''
        ORDER BY id
    ");
    
    $problematic_records = [];
    if ($result && $result->num_rows > 0) {
        echo "<p class='warning'><strong>Encontrados " . $result->num_rows . " registros com problemas:</strong></p>";
        echo "<table>";
        echo "<tr><th>ID</th><th>UF Origem (Atual)</th><th>Tamanho</th><th>Valor Hex</th><th>UF Corrigido</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            $uf_original = $row['uf_origem'];
            $uf_corrigido = strtoupper(trim($uf_original));
            
            // Se ainda tiver mais de 2 caracteres, pegar apenas os 2 primeiros
            if (strlen($uf_corrigido) > 2) {
                $uf_corrigido = substr($uf_corrigido, 0, 2);
            }
            
            // Se estiver vazio, usar um valor padrão ou NULL
            if (empty($uf_corrigido)) {
                $uf_corrigido = 'SP'; // Valor padrão (ou NULL se permitir)
            }
            
            $problematic_records[] = [
                'id' => $row['id'],
                'uf_original' => $uf_original,
                'uf_corrigido' => $uf_corrigido,
                'tamanho' => $row['tamanho']
            ];
            
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>" . htmlspecialchars($uf_original) . "</td>";
            echo "<td>{$row['tamanho']}</td>";
            echo "<td>{$row['hex_value']}</td>";
            echo "<td><strong>$uf_corrigido</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='success'>✓ Nenhum registro problemático encontrado!</p>";
    }
    echo "</div>";
    
    // 3. Verificar valores vazios ou NULL
    echo "<div class='section'>";
    echo "<h3>3. Verificando Valores Vazios ou NULL</h3>";
    
    $result = $conn->query("
        SELECT COUNT(*) as total
        FROM aliquotas
        WHERE uf_origem IS NULL OR uf_origem = '' OR TRIM(uf_origem) = ''
    ");
    
    if ($result) {
        $empty_count = $result->fetch_assoc()['total'];
        if ($empty_count > 0) {
            echo "<p class='warning'><strong>Encontrados $empty_count registros com UF origem vazia ou NULL</strong></p>";
        } else {
            echo "<p class='success'>✓ Nenhum registro com UF origem vazia!</p>";
        }
    }
    echo "</div>";
    
    // 4. Gerar scripts de correção
    if (!empty($problematic_records)) {
        echo "<div class='section'>";
        echo "<h3>4. Scripts SQL para Correção</h3>";
        
        $scripts = [];
        $scripts[] = "-- Script para corrigir valores truncados na coluna uf_origem";
        $scripts[] = "-- Gerado em: " . date('Y-m-d H:i:s');
        $scripts[] = "-- Banco: {$db_config['db']}";
        $scripts[] = "";
        $scripts[] = "-- ⚠️ IMPORTANTE: Faça backup do banco antes de executar!";
        $scripts[] = "";
        
        foreach ($problematic_records as $record) {
            $uf_corrigido_escaped = $conn->real_escape_string($record['uf_corrigido']);
            $scripts[] = "UPDATE `aliquotas` SET `uf_origem` = '$uf_corrigido_escaped' WHERE `id` = {$record['id']};";
        }
        
        // Script genérico para limpar espaços e truncar
        $scripts[] = "";
        $scripts[] = "-- Limpar espaços e garantir que todos os valores tenham exatamente 2 caracteres";
        $scripts[] = "UPDATE `aliquotas` SET `uf_origem` = UPPER(TRIM(SUBSTRING(TRIM(`uf_origem`), 1, 2))) WHERE LENGTH(TRIM(`uf_origem`)) > 2 OR TRIM(`uf_origem`) != `uf_origem`;";
        
        // Garantir que valores vazios tenham um padrão
        $scripts[] = "";
        $scripts[] = "-- Corrigir valores vazios (ajuste conforme necessário)";
        $scripts[] = "-- UPDATE `aliquotas` SET `uf_origem` = 'SP' WHERE `uf_origem` IS NULL OR `uf_origem` = '' OR TRIM(`uf_origem`) = '';";
        
        echo "<pre>" . htmlspecialchars(implode("\n", $scripts)) . "</pre>";
        
        // Salvar em arquivo
        $filename = 'fix_uf_origem_' . date('Y-m-d_His') . '.sql';
        file_put_contents($filename, implode("\n", $scripts));
        
        echo "<p><strong>Arquivo gerado:</strong> <a href='$filename' download>Baixar Script SQL</a></p>";
        echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do banco antes de executar!</p>";
        echo "</div>";
    }
    
    // 5. Verificar se a coluna precisa ser alterada
    echo "<div class='section'>";
    echo "<h3>5. Verificar Tipo da Coluna</h3>";
    
    if (isset($col_info)) {
        $max_length = $col_info['CHARACTER_MAXIMUM_LENGTH'];
        if ($max_length != 2) {
            echo "<p class='warning'><strong>Atenção:</strong> A coluna uf_origem tem tamanho $max_length, mas deveria ser CHAR(2) ou VARCHAR(2)</p>";
            echo "<p>Script para corrigir:</p>";
            echo "<pre>ALTER TABLE `aliquotas` MODIFY COLUMN `uf_origem` CHAR(2) NOT NULL;</pre>";
        } else {
            echo "<p class='success'>✓ O tamanho da coluna está correto (2 caracteres)</p>";
        }
    }
    echo "</div>";
    
    $conn->close();
    
} catch (Exception $e) {
    die("Erro: " . $e->getMessage());
}
?>
