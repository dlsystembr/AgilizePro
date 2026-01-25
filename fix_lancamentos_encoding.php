<?php
/**
 * Script para corrigir encoding corrompido na tabela lancamentos
 * Data: 2026-01-25
 * 
 * Este script corrige os caracteres acentuados e especiais que foram corrompidos durante a conversão de collation
 * 
 * IMPORTANTE: Faça backup do banco de dados antes de executar este script!
 * 
 * Uso:
 * php fix_lancamentos_encoding.php
 * ou acesse via navegador: http://localhost/mapos/fix_lancamentos_encoding.php
 */

// Configurações do banco de dados
// Tenta carregar do CodeIgniter ou usar variáveis de ambiente
$db_config = [
    'hostname' => $_ENV['DB_HOSTNAME'] ?? 'localhost',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'database' => $_ENV['DB_DATABASE'] ?? 'mapos',
    'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
];

// Se não encontrar nas variáveis de ambiente, tenta carregar do CodeIgniter
if (file_exists(__DIR__ . '/application/config/database.php')) {
    require_once __DIR__ . '/application/config/database.php';
    if (isset($db['default'])) {
        $db_config = [
            'hostname' => $db['default']['hostname'] ?? $db_config['hostname'],
            'username' => $db['default']['username'] ?? $db_config['username'],
            'password' => $db['default']['password'] ?? $db_config['password'],
            'database' => $db['default']['database'] ?? $db_config['database'],
            'charset' => $db['default']['char_set'] ?? $db_config['charset']
        ];
    }
}

// Mapeamento de correções: [campo, texto_errado, texto_correto]
$correcoes = [
    ['descricao', 'N┬║', 'Nº'],
    ['descricao', '1┬¬', '1ª'],
    ['forma_pgto', 'Promiss├│ria', 'Promissória'],
    ['forma_pgto', 'Cart├úo de Cr├®dito', 'Cartão de Crédito'],
];

$is_web = (php_sapi_name() !== 'cli');

try {
    // Conectar ao banco de dados
    $mysqli = new mysqli(
        $db_config['hostname'],
        $db_config['username'],
        $db_config['password'],
        $db_config['database']
    );
    
    if ($mysqli->connect_error) {
        throw new Exception("Erro de conexão: " . $mysqli->connect_error);
    }
    
    // Definir charset UTF-8
    $mysqli->set_charset($db_config['charset']);
    
    if ($is_web) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Correção de Lançamentos</title>";
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
            .error { background-color: #f44336; color: white; padding: 10px; margin: 10px 0; }
            .info { background-color: #2196F3; color: white; padding: 10px; margin: 10px 0; }
            .warning { background-color: #ff9800; color: white; padding: 10px; margin: 10px 0; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
        </style></head><body>";
        echo "<h1>Correção de Encoding na Tabela Lançamentos</h1>";
    }
    
    // Verificar estado atual
    if ($is_web) {
        echo "<div class='info'><strong>Verificando registros com encoding corrompido...</strong></div>";
    } else {
        echo "Verificando registros com encoding corrompido...\n";
    }
    
    // Buscar registros com problemas
    $problemas = [];
    foreach ($correcoes as $correcao) {
        list($campo, $texto_errado, $texto_correto) = $correcao;
        
        $stmt = $mysqli->prepare("SELECT idlancamentos, descricao, forma_pgto, cliente_fornecedor FROM lancamentos WHERE {$campo} LIKE ?");
        $pattern = "%{$texto_errado}%";
        $stmt->bind_param("s", $pattern);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $problemas[] = [
                'id' => $row['idlancamentos'],
                'campo' => $campo,
                'texto_errado' => $texto_errado,
                'texto_correto' => $texto_correto,
                'descricao' => $row['descricao'],
                'forma_pgto' => $row['forma_pgto'],
                'cliente' => $row['cliente_fornecedor']
            ];
        }
        $stmt->close();
    }
    
    // Mostrar problemas encontrados
    if ($is_web) {
        if (!empty($problemas)) {
            echo "<h2>Problemas Encontrados: " . count($problemas) . "</h2>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Campo</th><th>Texto Errado</th><th>Texto Correto</th><th>Descrição</th><th>Forma Pgto</th></tr>";
            foreach ($problemas as $problema) {
                echo "<tr>";
                echo "<td>{$problema['id']}</td>";
                echo "<td>{$problema['campo']}</td>";
                echo "<td><pre>" . htmlspecialchars($problema['texto_errado']) . "</pre></td>";
                echo "<td><pre>" . htmlspecialchars($problema['texto_correto']) . "</pre></td>";
                echo "<td>" . htmlspecialchars($problema['descricao']) . "</td>";
                echo "<td>" . htmlspecialchars($problema['forma_pgto']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='success'><strong>✓ Nenhum problema encontrado!</strong><br>";
            echo "Todos os registros já estão com encoding correto.</div>";
        }
    } else {
        if (!empty($problemas)) {
            echo "\nProblemas encontrados: " . count($problemas) . "\n";
            foreach ($problemas as $problema) {
                echo "ID {$problema['id']} - Campo: {$problema['campo']}\n";
                echo "  Errado: '{$problema['texto_errado']}' -> Correto: '{$problema['texto_correto']}'\n";
                echo "  Descrição: {$problema['descricao']}\n";
                echo "  Forma Pgto: {$problema['forma_pgto']}\n\n";
            }
        } else {
            echo "\n✓ Nenhum problema encontrado! Todos os registros já estão corretos.\n";
        }
    }
    
    // Aplicar correções
    $total_corrigidos = 0;
    $erros = [];
    
    if (!empty($problemas)) {
        if ($is_web) {
            echo "<div class='warning'><strong>Aplicando correções...</strong></div>";
        } else {
            echo "\nAplicando correções...\n";
        }
        
        foreach ($correcoes as $correcao) {
            list($campo, $texto_errado, $texto_correto) = $correcao;
            
            $stmt = $mysqli->prepare("UPDATE lancamentos SET {$campo} = REPLACE({$campo}, ?, ?) WHERE {$campo} LIKE ?");
            $pattern = "%{$texto_errado}%";
            $stmt->bind_param("sss", $texto_errado, $texto_correto, $pattern);
            
            if ($stmt->execute()) {
                $afetados = $mysqli->affected_rows;
                $total_corrigidos += $afetados;
                if (!$is_web) {
                    echo "✓ Corrigido '{$texto_errado}' -> '{$texto_correto}' no campo {$campo}: {$afetados} registro(s)\n";
                }
            } else {
                $erros[] = "Erro ao corrigir '{$texto_errado}' no campo {$campo}: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Mostrar resultados
    if ($is_web) {
        if ($total_corrigidos > 0) {
            echo "<div class='success'><strong>✓ Correções aplicadas com sucesso!</strong><br>";
            echo "Total de registros corrigidos: {$total_corrigidos}</div>";
        }
        
        if (!empty($erros)) {
            echo "<div class='error'><strong>✗ Erros encontrados:</strong><br>";
            foreach ($erros as $erro) {
                echo htmlspecialchars($erro) . "<br>";
            }
            echo "</div>";
        }
        
        // Verificar se ainda há problemas
        echo "<h2>Verificação Final</h2>";
        $problemas_restantes = [];
        foreach ($correcoes as $correcao) {
            list($campo, $texto_errado, $texto_correto) = $correcao;
            
            $stmt = $mysqli->prepare("SELECT COUNT(*) as total FROM lancamentos WHERE {$campo} LIKE ?");
            $pattern = "%{$texto_errado}%";
            $stmt->bind_param("s", $pattern);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row['total'] > 0) {
                $problemas_restantes[] = "Ainda existem {$row['total']} registro(s) com '{$texto_errado}' no campo {$campo}";
            }
            $stmt->close();
        }
        
        if (empty($problemas_restantes)) {
            echo "<div class='success'><strong>✓ Todas as correções foram aplicadas com sucesso!</strong></div>";
        } else {
            echo "<div class='warning'>";
            foreach ($problemas_restantes as $problema) {
                echo htmlspecialchars($problema) . "<br>";
            }
            echo "</div>";
        }
        
        echo "</body></html>";
    } else {
        if ($total_corrigidos > 0) {
            echo "\n✓ Correções aplicadas com sucesso!\n";
            echo "Total de registros corrigidos: {$total_corrigidos}\n";
        }
        
        if (!empty($erros)) {
            echo "\n✗ Erros encontrados:\n";
            foreach ($erros as $erro) {
                echo "  - {$erro}\n";
            }
        }
        
        echo "\nConcluído!\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    if ($is_web) {
        echo "<div class='error'><strong>Erro:</strong> " . htmlspecialchars($e->getMessage()) . "</div>";
        echo "</body></html>";
    } else {
        echo "Erro: " . $e->getMessage() . "\n";
    }
    exit(1);
}
?>
