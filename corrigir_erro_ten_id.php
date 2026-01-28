<?php
/**
 * Script rápido para corrigir erro "Field 'ten_id' doesn't have a default value"
 * Execute via navegador: http://localhost/mapos/corrigir_erro_ten_id.php
 */

// Carregar variáveis de ambiente
$env_file = __DIR__ . '/application/.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
        }
    }
}

// Conectar ao banco de dados
$host = $_ENV['DB_HOSTNAME'] ?? 'localhost';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? '';
$database = $_ENV['DB_DATABASE'] ?? 'mapos';

$conn = new mysqli($host, $user, $pass, $database);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$CI =& get_instance();
$CI->load->database();

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Corrigir Erro ten_id</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 20px; border-radius: 8px; max-width: 800px; margin: 0 auto; }
        h1 { color: #2D3E50; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Corrigir Erro: Field 'ten_id' doesn't have a default value</h1>
        
        <?php
        // 1. Verificar/criar tenant
        $result = $conn->query("SHOW TABLES LIKE 'tenants'");
        if ($result->num_rows == 0) {
            echo '<p class="error">❌ Tabela tenants não existe. Execute a migration primeiro.</p>';
            exit;
        }
        
        $result = $conn->query("SELECT * FROM tenants WHERE ten_nome = 'Tenant Padrão' LIMIT 1");
        $tenant = $result->fetch_assoc();
        
        if (!$tenant) {
            $sql = "INSERT INTO tenants (ten_nome, ten_cnpj, ten_email, ten_telefone, ten_data_cadastro) 
                    VALUES ('Tenant Padrão', '00.000.000/0001-00', 'tenant@padrao.com', '(00) 0000-0000', NOW())";
            
            if ($conn->query($sql)) {
                $ten_id = $conn->insert_id;
                echo '<p class="success">✓ Tenant padrão criado (ID: ' . $ten_id . ')</p>';
            } else {
                echo '<p class="error">❌ Erro ao criar tenant: ' . $conn->error . '</p>';
                exit;
            }
        } else {
            $ten_id = $tenant['ten_id'];
            echo '<p class="success">✓ Tenant padrão encontrado (ID: ' . $ten_id . ')</p>';
        }
        
        // 2. Lista de tabelas críticas para NFCom
        $tabelas_criticas = [
            'nfecom_capa',
            'nfecom_itens',
            'usuarios',
            'clientes',
            'produtos',
            'servicos',
            'operacao_comercial',
            'classificacao_fiscal',
            'empresas'
        ];
        
        echo '<h2>Atualizando registros...</h2>';
        
        $total_atualizados = 0;
        
        foreach ($tabelas_criticas as $tabela) {
            // Verificar se tabela existe
            $result = $conn->query("SHOW TABLES LIKE '$tabela'");
            if ($result->num_rows == 0) {
                echo '<p class="warning">⚠ Tabela ' . $tabela . ' não existe</p>';
                continue;
            }
            
            // Verificar se tem campo ten_id
            $result = $conn->query("SHOW COLUMNS FROM `$tabela` LIKE 'ten_id'");
            if ($result->num_rows == 0) {
                echo '<p class="warning">⚠ Tabela ' . $tabela . ' não tem campo ten_id</p>';
                continue;
            }
            
            // Contar registros sem ten_id ou com ten_id = 0
            $result = $conn->query("SELECT COUNT(*) as total FROM `$tabela` WHERE ten_id IS NULL OR ten_id = 0");
            $row = $result->fetch_assoc();
            $count = $row['total'];
            
            if ($count > 0) {
                // Atualizar registros
                $sql = "UPDATE `$tabela` SET `ten_id` = $ten_id WHERE `ten_id` IS NULL OR `ten_id` = 0";
                $conn->query($sql);
                $atualizados = $conn->affected_rows;
                $total_atualizados += $atualizados;
                echo '<p class="success">✓ ' . $tabela . ': ' . $atualizados . ' registro(s) atualizado(s)</p>';
            } else {
                echo '<p>✓ ' . $tabela . ': OK</p>';
            }
        }
        
        echo '<h2>Resumo</h2>';
        echo '<p><strong>Tenant ID:</strong> ' . $ten_id . '</p>';
        echo '<p><strong>Registros atualizados:</strong> ' . $total_atualizados . '</p>';
        
        echo '<h2>Próximos Passos</h2>';
        echo '<ol>';
        echo '<li>Faça logout e login novamente no sistema</li>';
        echo '<li>Tente cadastrar a NFCom novamente</li>';
        echo '<li>Se ainda der erro, verifique qual tabela está causando o problema</li>';
        echo '</ol>';
        
        echo '<h2>Verificar Sessão</h2>';
        echo '<p>Após fazer login, verifique se o ten_id está na sessão:</p>';
        echo '<pre>';
        echo 'No PHP, você pode verificar com:\n';
        echo '$this->session->userdata(\'ten_id\');\n';
        echo 'Deve retornar: ' . $ten_id;
        echo '</pre>';
        
        $conn->close();
        ?>
    </div>
</body>
</html>
