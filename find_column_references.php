<?php
/**
 * Script para encontrar todas as referências a colunas no código PHP
 * 
 * Uso:
 * Acesse via navegador: http://localhost/mapos/find_column_references.php
 */

$codebase_path = __DIR__ . '/application';
$json_mapping_file = null;

// Procurar arquivo de mapeamento mais recente
$mapping_files = glob(__DIR__ . '/column_rename_mapping_*.json');
if (!empty($mapping_files)) {
    rsort($mapping_files);
    $json_mapping_file = $mapping_files[0];
}

if (!$json_mapping_file || !file_exists($json_mapping_file)) {
    die("Erro: Arquivo de mapeamento não encontrado. Execute primeiro analyze_column_names.php");
}

$mapping = json_decode(file_get_contents($json_mapping_file), true);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Busca de Referências de Colunas</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
    h1 { color: #2196F3; }
    h2 { color: #4CAF50; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; background-color: white; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    .section { margin: 30px 0; padding: 20px; background-color: white; border-left: 4px solid #2196F3; }
    pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
    code { background-color: #f0f0f0; padding: 2px 5px; border-radius: 3px; }
    .file-path { color: #666; font-size: 0.9em; }
    .line-number { color: #999; }
</style></head><body>";

echo "<h1>🔍 Busca de Referências de Colunas no Código</h1>";
echo "<div class='section'>";
echo "<p><strong>Arquivo de mapeamento:</strong> " . basename($json_mapping_file) . "</p>";
echo "<p><strong>Total de colunas a buscar:</strong> " . count($mapping) . "</p>";
echo "</div>";

$references = [];
$files_to_check = [];

// Buscar arquivos PHP
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($codebase_path),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files_to_check[] = $file->getPathname();
    }
}

echo "<div class='section'>";
echo "<h2>Arquivos encontrados: " . count($files_to_check) . "</h2>";
echo "</div>";

// Buscar referências
foreach ($mapping as $map) {
    $old_column = $map['old'];
    $new_column = $map['new'];
    $table = $map['table'];
    
    // Padrões de busca
    $patterns = [
        "`{$old_column}`",
        "'{$old_column}'",
        "\"{$old_column}\"",
        "->{$old_column}",
        "['{$old_column}']",
        "[\"{$old_column}\"]",
        "{$old_column}",
    ];
    
    foreach ($files_to_check as $file_path) {
        $content = file_get_contents($file_path);
        $lines = explode("\n", $content);
        
        foreach ($lines as $line_num => $line) {
            foreach ($patterns as $pattern) {
                if (stripos($line, $pattern) !== false) {
                    $line_content = trim($line);
                    if (strlen($line_content) > 0) {
                        $references[] = [
                            'file' => str_replace(__DIR__ . '\\', '', $file_path),
                            'line' => $line_num + 1,
                            'old_column' => $old_column,
                            'new_column' => $new_column,
                            'table' => $table,
                            'code' => $line_content
                        ];
                    }
                }
            }
        }
    }
}

// Agrupar por arquivo
$grouped = [];
foreach ($references as $ref) {
    $file = $ref['file'];
    if (!isset($grouped[$file])) {
        $grouped[$file] = [];
    }
    $grouped[$file][] = $ref;
}

echo "<div class='section'>";
echo "<h2>Referências Encontradas: " . count($references) . "</h2>";

if (!empty($grouped)) {
    foreach ($grouped as $file => $refs) {
        echo "<h3>📄 {$file} (" . count($refs) . " referências)</h3>";
        echo "<table>";
        echo "<tr><th>Linha</th><th>Coluna Antiga</th><th>Coluna Nova</th><th>Tabela</th><th>Código</th></tr>";
        
        foreach ($refs as $ref) {
            echo "<tr>";
            echo "<td class='line-number'>{$ref['line']}</td>";
            echo "<td><code>{$ref['old_column']}</code></td>";
            echo "<td><code style='color: #4CAF50;'>{$ref['new_column']}</code></td>";
            echo "<td>{$ref['table']}</td>";
            echo "<td><code>" . htmlspecialchars(substr($ref['code'], 0, 100)) . (strlen($ref['code']) > 100 ? '...' : '') . "</code></td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p>Nenhuma referência encontrada.</p>";
}

echo "</div>";

// Gerar script de substituição
if (!empty($references)) {
    echo "<div class='section'>";
    echo "<h2>Script de Substituição</h2>";
    echo "<p>Use este script para substituir automaticamente (use com cuidado!):</p>";
    
    $replace_script = "<?php\n";
    $replace_script .= "// Script para substituir referências de colunas no código\n";
    $replace_script .= "// ⚠️ IMPORTANTE: Faça backup do código antes de executar!\n\n";
    
    $replace_script .= "\$replacements = [\n";
    foreach ($mapping as $map) {
        $old = $map['old'];
        $new = $map['new'];
        $replace_script .= "    '`{$old}`' => '`{$new}`',\n";
        $replace_script .= "    '\"{$old}\"' => '\"{$new}\"',\n";
        $replace_script .= "    \"'{$old}'\" => \"'{$new}'\",\n";
        $replace_script .= "    '->{$old}' => '->{$new}',\n";
        $replace_script .= "    \"['{$old}']\" => \"['{$new}']\",\n";
        $replace_script .= "    \"[\\\"{$old}\\\"]\" => \"[\\\"{$new}\\\"]\",\n";
    }
    $replace_script .= "];\n\n";
    
    $replace_script .= "// Substituir em todos os arquivos PHP\n";
    $replace_script .= "\$iterator = new RecursiveIteratorIterator(\n";
    $replace_script .= "    new RecursiveDirectoryIterator(__DIR__ . '/application'),\n";
    $replace_script .= "    RecursiveIteratorIterator::SELF_FIRST\n";
    $replace_script .= ");\n\n";
    
    $replace_script .= "foreach (\$iterator as \$file) {\n";
    $replace_script .= "    if (\$file->isFile() && \$file->getExtension() === 'php') {\n";
    $replace_script .= "        \$content = file_get_contents(\$file->getPathname());\n";
    $replace_script .= "        \$original = \$content;\n";
    $replace_script .= "        foreach (\$replacements as \$old => \$new) {\n";
    $replace_script .= "            \$content = str_replace(\$old, \$new, \$content);\n";
    $replace_script .= "        }\n";
    $replace_script .= "        if (\$content !== \$original) {\n";
    $replace_script .= "            file_put_contents(\$file->getPathname(), \$content);\n";
    $replace_script .= "            echo \"Atualizado: \" . \$file->getPathname() . \"\\n\";\n";
    $replace_script .= "        }\n";
    $replace_script .= "    }\n";
    $replace_script .= "}\n";
    $replace_script .= "echo \"Concluído!\\n\";\n";
    $replace_script .= "?>\n";
    
    echo "<pre>" . htmlspecialchars($replace_script) . "</pre>";
    
    $script_filename = 'replace_column_names.php';
    file_put_contents($script_filename, $replace_script);
    
    echo "<p><strong>📥 Script gerado:</strong> <a href='$script_filename' download class='btn'>Baixar Script de Substituição</a></p>";
    echo "<p class='warning'>⚠️ <strong>ATENÇÃO:</strong> Revise o script antes de executar! Faça backup do código!</p>";
    echo "</div>";
}

echo "</body></html>";
?>
