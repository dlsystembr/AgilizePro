<?php
/**
 * Script para substituir referências de colunas no código PHP
 * ⚠️ IMPORTANTE: Faça backup do código antes de executar!
 * 
 * Uso:
 * php replace_column_names.php
 * ou acesse via navegador: http://localhost/mapos/replace_column_names.php
 */

// Procurar arquivo de mapeamento mais recente
$mapping_files = glob(__DIR__ . '/column_rename_mapping_*.json');
if (empty($mapping_files)) {
    die("Erro: Arquivo de mapeamento não encontrado. Execute primeiro analyze_column_names.php");
}

rsort($mapping_files);
$json_mapping_file = $mapping_files[0];

echo "Usando mapeamento: " . basename($json_mapping_file) . "\n\n";

$mapping = json_decode(file_get_contents($json_mapping_file), true);

if (empty($mapping)) {
    die("Erro: Mapeamento vazio ou inválido.\n");
}

echo "Total de colunas a substituir: " . count($mapping) . "\n\n";

// Verificar se é execução via navegador ou CLI
$is_web = (php_sapi_name() !== 'cli');

if ($is_web) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Substituição de Colunas</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; }
        pre { background-color: #f5f5f5; padding: 10px; }
    </style></head><body>";
    echo "<h1>Substituição de Referências de Colunas</h1>";
}

// Criar array de substituições
$replacements = [];
foreach ($mapping as $map) {
    $old = $map['old'];
    $new = $map['new'];
    
    // Padrões de busca e substituição
    $replacements["`{$old}`"] = "`{$new}`";
    $replacements["'{$old}'"] = "'{$new}'";
    $replacements["\"{$old}\""] = "\"{$new}\"";
    $replacements["->{$old}"] = "->{$new}";
    $replacements["['{$old}']"] = "['{$new}']";
    $replacements["[\"{$old}\"]"] = "[\"{$new}\"]";
    // Cuidado com substituições mais genéricas
    // $replacements["{$old}"] = "{$new}"; // Muito perigoso, pode substituir em strings
}

$codebase_path = __DIR__ . '/application';
$files_updated = [];
$files_skipped = [];
$total_replacements = 0;

// Buscar arquivos PHP
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($codebase_path),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $file_path = $file->getPathname();
        $content = file_get_contents($file_path);
        $original = $content;
        
        // Aplicar substituições
        foreach ($replacements as $old => $new) {
            $count = substr_count($content, $old);
            if ($count > 0) {
                $content = str_replace($old, $new, $content);
                $total_replacements += $count;
            }
        }
        
        // Se houve mudanças, salvar
        if ($content !== $original) {
            // Em modo web, apenas mostrar o que seria feito
            if ($is_web) {
                $files_updated[] = [
                    'file' => str_replace(__DIR__ . '\\', '', $file_path),
                    'changes' => substr_count($original, $old) // Aproximado
                ];
            } else {
                // Em modo CLI, realmente fazer a substituição
                file_put_contents($file_path, $content);
                $files_updated[] = str_replace(__DIR__ . '\\', '', $file_path);
                echo "Atualizado: " . str_replace(__DIR__ . '\\', '', $file_path) . "\n";
            }
        }
    }
}

if ($is_web) {
    echo "<div class='warning'>";
    echo "<strong>⚠️ MODO DE VISUALIZAÇÃO</strong><br>";
    echo "Este script está em modo de visualização. Para realmente fazer as substituições, execute via linha de comando:<br>";
    echo "<code>php replace_column_names.php</code>";
    echo "</div>";
    
    echo "<h2>Arquivos que seriam atualizados: " . count($files_updated) . "</h2>";
    echo "<ul>";
    foreach ($files_updated as $file_info) {
        if (is_array($file_info)) {
            echo "<li>{$file_info['file']}</li>";
        } else {
            echo "<li>{$file_info}</li>";
        }
    }
    echo "</ul>";
    
    echo "<div class='success'>";
    echo "<strong>Total de substituições:</strong> $total_replacements";
    echo "</div>";
    
    echo "<p><strong>⚠️ IMPORTANTE:</strong> Faça backup do código antes de executar em modo CLI!</p>";
    echo "</body></html>";
} else {
    echo "\nConcluído!\n";
    echo "Arquivos atualizados: " . count($files_updated) . "\n";
    echo "Total de substituições: $total_replacements\n";
}
?>
