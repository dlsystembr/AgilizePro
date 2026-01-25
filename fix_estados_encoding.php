<?php
/**
 * Script para corrigir nomes de estados corrompidos devido a problemas de encoding/collation
 * Data: 2026-01-25
 * 
 * Este script corrige os caracteres acentuados que foram corrompidos durante a conversão de collation
 * 
 * IMPORTANTE: Faça backup do banco de dados antes de executar este script!
 * 
 * Uso:
 * php fix_estados_encoding.php
 * ou acesse via navegador: http://localhost/mapos/fix_estados_encoding.php
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

// Mapeamento de correções: est_id => nome_correto
$correcoes = [
    9 => 'Goiás',      // Goi├ís -> Goiás
    10 => 'Maranhão',  // Maranh├úo -> Maranhão
    14 => 'Pará',      // Par├í -> Pará
    15 => 'Paraíba',   // Para├¡ba -> Paraíba
    16 => 'Paraná',    // Paran├í -> Paraná
    18 => 'Piauí',     // Piau├¡ -> Piauí
    22 => 'Rondônia',  // Rond├┤nia -> Rondônia
    25 => 'São Paulo'  // S├úo Paulo -> São Paulo
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
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Correção de Estados</title>";
        echo "<style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .success { background-color: #4CAF50; color: white; padding: 10px; margin: 10px 0; }
            .error { background-color: #f44336; color: white; padding: 10px; margin: 10px 0; }
            .info { background-color: #2196F3; color: white; padding: 10px; margin: 10px 0; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #4CAF50; color: white; }
            tr:nth-child(even) { background-color: #f2f2f2; }
        </style></head><body>";
        echo "<h1>Correção de Nomes de Estados</h1>";
    }
    
    // Verificar estado atual
    if ($is_web) {
        echo "<div class='info'><strong>Verificando estado atual dos registros...</strong></div>";
    } else {
        echo "Verificando estado atual dos registros...\n";
    }
    
    $verificacao = [];
    foreach ($correcoes as $est_id => $nome_correto) {
        $stmt = $mysqli->prepare("SELECT est_id, est_nome, est_uf FROM estados WHERE est_id = ?");
        $stmt->bind_param("i", $est_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $verificacao[$est_id] = [
                'atual' => $row['est_nome'],
                'correto' => $nome_correto,
                'uf' => $row['est_uf'],
                'precisa_correcao' => ($row['est_nome'] !== $nome_correto)
            ];
        }
        $stmt->close();
    }
    
    // Mostrar tabela de verificação
    if ($is_web) {
        echo "<h2>Estado Atual dos Registros</h2>";
        echo "<table>";
        echo "<tr><th>ID</th><th>UF</th><th>Nome Atual</th><th>Nome Correto</th><th>Status</th></tr>";
        foreach ($verificacao as $est_id => $info) {
            $status = $info['precisa_correcao'] ? '<span style="color: red;">✗ Precisa correção</span>' : '<span style="color: green;">✓ OK</span>';
            echo "<tr>";
            echo "<td>{$est_id}</td>";
            echo "<td>{$info['uf']}</td>";
            echo "<td>" . htmlspecialchars($info['atual']) . "</td>";
            echo "<td>" . htmlspecialchars($info['correto']) . "</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "\nEstado atual dos registros:\n";
        foreach ($verificacao as $est_id => $info) {
            $status = $info['precisa_correcao'] ? '✗ Precisa correção' : '✓ OK';
            echo "ID {$est_id} ({$info['uf']}): '{$info['atual']}' -> '{$info['correto']}' [{$status}]\n";
        }
    }
    
    // Aplicar correções
    $corrigidos = 0;
    $erros = [];
    
    foreach ($correcoes as $est_id => $nome_correto) {
        if (isset($verificacao[$est_id]) && $verificacao[$est_id]['precisa_correcao']) {
            $stmt = $mysqli->prepare("UPDATE estados SET est_nome = ?, est_data_alteracao = NOW() WHERE est_id = ?");
            $stmt->bind_param("si", $nome_correto, $est_id);
            
            if ($stmt->execute()) {
                $corrigidos++;
                if (!$is_web) {
                    echo "✓ Corrigido: ID {$est_id} -> '{$nome_correto}'\n";
                }
            } else {
                $erros[] = "Erro ao corrigir ID {$est_id}: " . $stmt->error;
            }
            $stmt->close();
        }
    }
    
    // Mostrar resultados
    if ($is_web) {
        if ($corrigidos > 0) {
            echo "<div class='success'><strong>✓ Correções aplicadas com sucesso!</strong><br>";
            echo "Total de registros corrigidos: {$corrigidos}</div>";
        } else {
            echo "<div class='info'><strong>ℹ Nenhuma correção necessária.</strong><br>";
            echo "Todos os registros já estão com os nomes corretos.</div>";
        }
        
        if (!empty($erros)) {
            echo "<div class='error'><strong>✗ Erros encontrados:</strong><br>";
            foreach ($erros as $erro) {
                echo htmlspecialchars($erro) . "<br>";
            }
            echo "</div>";
        }
        
        // Mostrar resultado final
        echo "<h2>Resultado Final</h2>";
        $stmt = $mysqli->prepare("SELECT est_id, est_nome, est_uf, est_data_alteracao FROM estados WHERE est_id IN (" . implode(',', array_keys($correcoes)) . ") ORDER BY est_id");
        $stmt->execute();
        $result = $stmt->get_result();
        
        echo "<table>";
        echo "<tr><th>ID</th><th>UF</th><th>Nome</th><th>Data Alteração</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['est_id']}</td>";
            echo "<td>{$row['est_uf']}</td>";
            echo "<td>" . htmlspecialchars($row['est_nome']) . "</td>";
            echo "<td>" . ($row['est_data_alteracao'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        $stmt->close();
        
        echo "</body></html>";
    } else {
        if ($corrigidos > 0) {
            echo "\n✓ Correções aplicadas com sucesso!\n";
            echo "Total de registros corrigidos: {$corrigidos}\n";
        } else {
            echo "\nℹ Nenhuma correção necessária. Todos os registros já estão corretos.\n";
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
