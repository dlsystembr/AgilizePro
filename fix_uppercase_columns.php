<?php
/**
 * Script para corrigir referências de colunas em maiúsculas para minúsculas no código
 * ⚠️ IMPORTANTE: Faça backup do código antes de executar!
 * 
 * Uso:
 * php fix_uppercase_columns.php
 * ou acesse via navegador: http://localhost/mapos/fix_uppercase_columns.php
 */

$codebase_path = __DIR__ . '/application';
$is_web = (php_sapi_name() !== 'cli');

// Padrões de colunas em maiúsculas comuns (adicionar mais conforme necessário)
$column_patterns = [
    // Padrão: COLUNA_MAIUSCULA -> coluna_maiuscula
    '/\b([A-Z]{2,}_[A-Z_]+)\b/' => function($matches) {
        return strtolower($matches[1]);
    },
    // Padrão específico: USS_* -> uss_*
    '/\bUSS_([A-Z_]+)\b/' => function($matches) {
        return 'uss_' . strtolower($matches[1]);
    },
    // Padrão específico: CLF_* -> clf_*
    '/\bCLF_([A-Z_]+)\b/' => function($matches) {
        return 'clf_' . strtolower($matches[1]);
    },
    // Padrão específico: PRO_* -> pro_*
    '/\bPRO_([A-Z_]+)\b/' => function($matches) {
        return 'pro_' . strtolower($matches[1]);
    },
    // Padrão específico: CLN_* -> cln_*
    '/\bCLN_([A-Z_]+)\b/' => function($matches) {
        return 'cln_' . strtolower($matches[1]);
    },
    // Padrão específico: PES_* -> pes_*
    '/\bPES_([A-Z_]+)\b/' => function($matches) {
        return 'pes_' . strtolower($matches[1]);
    },
    // Padrão específico: OPC_* -> opc_*
    '/\bOPC_([A-Z_]+)\b/' => function($matches) {
        return 'opc_' . strtolower($matches[1]);
    },
    // Padrão específico: NFC_* -> nfc_*
    '/\bNFC_([A-Z_]+)\b/' => function($matches) {
        return 'nfc_' . strtolower($matches[1]);
    },
    // Padrão específico: CTR_* -> ctr_*
    '/\bCTR_([A-Z_]+)\b/' => function($matches) {
        return 'ctr_' . strtolower($matches[1]);
    },
    // Padrão específico: CTI_* -> cti_*
    '/\bCTI_([A-Z_]+)\b/' => function($matches) {
        return 'cti_' . strtolower($matches[1]);
    },
    // Padrão específico: ORV_* -> orv_*
    '/\bORV_([A-Z_]+)\b/' => function($matches) {
        return 'orv_' . strtolower($matches[1]);
    },
];

// Substituições diretas mais seguras (apenas em contextos SQL/strings)
$direct_replacements = [
    // Em strings SQL
    "`USS_EMAIL`" => "`uss_email`",
    "`USS_SITUACAO`" => "`uss_situacao`",
    "`USS_SENHA`" => "`uss_senha`",
    "`USS_DATA_EXPIRACAO`" => "`uss_data_expiracao`",
    "`USS_NOME`" => "`uss_nome`",
    "`USS_URL_IMAGE_USER`" => "`uss_url_image_user`",
    "`USS_ID`" => "`uss_id`",
    "`USS_CPF`" => "`uss_cpf`",
    "`USS_RG`" => "`uss_rg`",
    "`USS_TELEFONE`" => "`uss_telefone`",
    "`USS_CELULAR`" => "`uss_celular`",
    "`USS_DATA_CADASTRO`" => "`uss_data_cadastro`",
    
    // Em propriedades de objeto
    "->USS_EMAIL" => "->uss_email",
    "->USS_SITUACAO" => "->uss_situacao",
    "->USS_SENHA" => "->uss_senha",
    "->USS_DATA_EXPIRACAO" => "->uss_data_expiracao",
    "->USS_NOME" => "->uss_nome",
    "->USS_URL_IMAGE_USER" => "->uss_url_image_user",
    "->USS_ID" => "->uss_id",
    "->USS_CPF" => "->uss_cpf",
    "->USS_RG" => "->uss_rg",
    "->USS_TELEFONE" => "->uss_telefone",
    "->USS_CELULAR" => "->uss_celular",
    "->USS_DATA_CADASTRO" => "->uss_data_cadastro",
    
    // Em arrays
    "['USS_EMAIL']" => "['uss_email']",
    "['USS_SITUACAO']" => "['uss_situacao']",
    "['USS_SENHA']" => "['uss_senha']",
    "['USS_DATA_EXPIRACAO']" => "['uss_data_expiracao']",
    "['USS_NOME']" => "['uss_nome']",
    "['USS_URL_IMAGE_USER']" => "['uss_url_image_user']",
    "['USS_ID']" => "['uss_id']",
    "['USS_CPF']" => "['uss_cpf']",
    "['USS_RG']" => "['uss_rg']",
    "['USS_TELEFONE']" => "['uss_telefone']",
    "['USS_CELULAR']" => "['uss_celular']",
    "['USS_DATA_CADASTRO']" => "['uss_data_cadastro']",
    
    "[\"USS_EMAIL\"]" => "[\"uss_email\"]",
    "[\"USS_SITUACAO\"]" => "[\"uss_situacao\"]",
    "[\"USS_SENHA\"]" => "[\"uss_senha\"]",
    "[\"USS_DATA_EXPIRACAO\"]" => "[\"uss_data_expiracao\"]",
    "[\"USS_NOME\"]" => "[\"uss_nome\"]",
    "[\"USS_URL_IMAGE_USER\"]" => "[\"uss_url_image_user\"]",
    "[\"USS_ID\"]" => "[\"uss_id\"]",
    "[\"USS_CPF\"]" => "[\"uss_cpf\"]",
    "[\"USS_RG\"]" => "[\"uss_rg\"]",
    "[\"USS_TELEFONE\"]" => "[\"uss_telefone\"]",
    "[\"USS_CELULAR\"]" => "[\"uss_celular\"]",
    "[\"USS_DATA_CADASTRO\"]" => "[\"uss_data_cadastro\"]",
];

if ($is_web) {
    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Correção de Colunas</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .warning { background-color: #ffeb3b; padding: 10px; margin: 10px 0; }
        .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
        pre { background-color: #f5f5f5; padding: 10px; }
    </style></head><body>";
    echo "<h1>Correção de Referências de Colunas</h1>";
    echo "<div class='warning'>";
    echo "<strong>⚠️ MODO DE VISUALIZAÇÃO</strong><br>";
    echo "Este script está em modo de visualização. Para realmente fazer as substituições, execute via linha de comando:<br>";
    echo "<code>php fix_uppercase_columns.php</code>";
    echo "</div>";
}

$files_updated = [];
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
        
        // Aplicar substituições diretas
        foreach ($direct_replacements as $old => $new) {
            $count = substr_count($content, $old);
            if ($count > 0) {
                $content = str_replace($old, $new, $content);
                $total_replacements += $count;
            }
        }
        
        // Aplicar padrões regex (mais cuidadoso)
        foreach ($column_patterns as $pattern => $callback) {
            $content = preg_replace_callback($pattern, $callback, $content);
        }
        
        // Se houve mudanças, salvar
        if ($content !== $original) {
            if ($is_web) {
                $files_updated[] = str_replace(__DIR__ . '\\', '', $file_path);
            } else {
                file_put_contents($file_path, $content);
                $files_updated[] = str_replace(__DIR__ . '\\', '', $file_path);
                echo "Atualizado: " . str_replace(__DIR__ . '\\', '', $file_path) . "\n";
            }
        }
    }
}

if ($is_web) {
    echo "<h2>Arquivos que seriam atualizados: " . count($files_updated) . "</h2>";
    echo "<ul>";
    foreach ($files_updated as $file) {
        echo "<li>$file</li>";
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
