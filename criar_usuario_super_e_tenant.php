<?php
/**
 * Script para criar usuário super e tenant padrão
 * Execute este script via navegador ou linha de comando
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

echo "========================================\n";
echo "Criar Usuário Super e Tenant\n";
echo "========================================\n\n";

// 1. Verificar se tabela usuarios_super existe
$result = $conn->query("SHOW TABLES LIKE 'usuarios_super'");
if ($result->num_rows == 0) {
    echo "❌ Tabela usuarios_super não existe. Execute a migration primeiro.\n";
    exit;
}

// 2. Verificar se já existe usuário super
$result = $conn->query("SELECT * FROM usuarios_super WHERE uss_email = 'admin@super.com' LIMIT 1");
$super_existe = $result->fetch_assoc();

if ($super_existe) {
    echo "✓ Usuário super já existe (ID: {$super_existe['uss_id']})\n";
    echo "  Email: admin@super.com\n";
    echo "  Senha: admin123\n\n";
} else {
    // Criar usuário super
    $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios_super (uss_nome, uss_cpf, uss_email, uss_senha, uss_telefone, uss_situacao, uss_data_cadastro) 
            VALUES ('Administrador Super', '000.000.000-00', 'admin@super.com', ?, '(00) 0000-0000', 1, CURDATE())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $senha_hash);
    
    if ($stmt->execute()) {
        $uss_id = $conn->insert_id;
        echo "✓ Usuário super criado com sucesso (ID: $uss_id)\n";
        echo "  Email: admin@super.com\n";
        echo "  Senha: admin123\n\n";
    } else {
        echo "❌ Erro ao criar usuário super: " . $conn->error . "\n";
        exit;
    }
    $stmt->close();
}

// 3. Verificar se tabela tenants existe
$result = $conn->query("SHOW TABLES LIKE 'tenants'");
if ($result->num_rows == 0) {
    echo "❌ Tabela tenants não existe. Execute a migration primeiro.\n";
    exit;
}

// 4. Verificar se já existe tenant padrão
$result = $conn->query("SELECT * FROM tenants WHERE ten_nome = 'Tenant Padrão' LIMIT 1");
$tenant_existe = $result->fetch_assoc();

if ($tenant_existe) {
    echo "✓ Tenant padrão já existe (ID: {$tenant_existe['ten_id']})\n";
    $ten_id = $tenant_existe['ten_id'];
} else {
    // Criar tenant padrão
    $sql = "INSERT INTO tenants (ten_nome, ten_cnpj, ten_email, ten_telefone, ten_data_cadastro) 
            VALUES ('Tenant Padrão', '00.000.000/0001-00', 'tenant@padrao.com', '(00) 0000-0000', NOW())";
    
    if ($conn->query($sql)) {
        $ten_id = $conn->insert_id;
        echo "✓ Tenant padrão criado com sucesso (ID: $ten_id)\n\n";
    } else {
        echo "❌ Erro ao criar tenant: " . $conn->error . "\n";
        exit;
    }
}

// 5. Atualizar registros sem ten_id
echo "Atualizando registros sem ten_id...\n\n";

$tabelas = [
    'usuarios' => 'idusuarios',
    'clientes' => 'cln_id',
    'produtos' => 'idProdutos',
    'servicos' => 'idServicos',
    'vendas' => 'idVendas',
    'os' => 'idOs',
    'contratos' => 'ctr_id',
    'nfecom_capa' => 'nfc_id',
    'nfecom_itens' => 'nfi_id',
    'empresas' => 'emp_id',
    'classificacao_fiscal' => 'clf_id',
    'operacao_comercial' => 'opc_id',
    'configuracoes_fiscais' => 'cfg_id',
    'certificados' => 'cert_id',
    'permissoes' => 'idPermissao',
    'faturamento_entrada' => 'fat_id',
    'itens_faturamento_entrada' => 'ife_id'
];

$total_atualizados = 0;

foreach ($tabelas as $tabela => $primary_key) {
    // Verificar se tabela existe
    $result = $conn->query("SHOW TABLES LIKE '$tabela'");
    if ($result->num_rows == 0) {
        echo "  ⚠ Tabela $tabela não existe, pulando...\n";
        continue;
    }
    
    // Verificar se tem campo ten_id
    $result = $conn->query("SHOW COLUMNS FROM `$tabela` LIKE 'ten_id'");
    if ($result->num_rows == 0) {
        echo "  ⚠ Tabela $tabela não tem campo ten_id, pulando...\n";
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
        echo "  ✓ $tabela: $atualizados registro(s) atualizado(s)\n";
    } else {
        echo "  ✓ $tabela: todos os registros já têm ten_id\n";
    }
}

echo "\n========================================\n";
echo "Resumo\n";
echo "========================================\n";
echo "Tenant ID padrão: $ten_id\n";
echo "Total de registros atualizados: $total_atualizados\n";
echo "\n";

// 6. Verificar se existe usuário comum para o tenant
$CI->db->where('ten_id', $ten_id);
$usuario_existe = $CI->db->get('usuarios')->row();

if (!$usuario_existe) {
    echo "⚠ Não existe usuário comum para o tenant. Criando usuário padrão...\n";
    
    // Criar permissão padrão primeiro
    $CI->db->where('ten_id', $ten_id);
    $permissao_existe = $CI->db->get('permissoes')->row();
    
    if (!$permissao_existe) {
        $data_permissao = [
            'nome' => 'Administrador',
            'data' => date('Y-m-d'),
            'situacao' => 1,
            'ten_id' => $ten_id,
            // Adicionar todas as permissões
            'aCliente' => 1, 'eCliente' => 1, 'dCliente' => 1, 'vCliente' => 1,
            'aProduto' => 1, 'eProduto' => 1, 'dProduto' => 1, 'vProduto' => 1,
            'aServico' => 1, 'eServico' => 1, 'dServico' => 1, 'vServico' => 1,
            'aOs' => 1, 'eOs' => 1, 'dOs' => 1, 'vOs' => 1,
            'aVenda' => 1, 'eVenda' => 1, 'dVenda' => 1, 'vVenda' => 1,
            'aContrato' => 1, 'eContrato' => 1, 'dContrato' => 1, 'vContrato' => 1,
            'aNfecom' => 1, 'eNfecom' => 1, 'dNfecom' => 1, 'vNfecom' => 1,
            'rCliente' => 1, 'rProduto' => 1, 'rServico' => 1, 'rOs' => 1, 'rVenda' => 1, 'rContrato' => 1,
            'aUsuario' => 1, 'eUsuario' => 1, 'dUsuario' => 1, 'vUsuario' => 1,
            'aPermissao' => 1, 'ePermissao' => 1, 'dPermissao' => 1, 'vPermissao' => 1,
            'aConfiguracao' => 1, 'eConfiguracao' => 1, 'dConfiguracao' => 1, 'vConfiguracao' => 1
        ];
        
        if ($CI->db->insert('permissoes', $data_permissao)) {
            $permissao_id = $CI->db->insert_id();
            echo "  ✓ Permissão padrão criada (ID: $permissao_id)\n";
        } else {
            echo "  ❌ Erro ao criar permissão: " . $CI->db->error()['message'] . "\n";
            $permissao_id = 1; // Tentar usar ID 1
        }
    } else {
        $permissao_id = $permissao_existe->idPermissao ?? $permissao_existe->idpermissao ?? 1;
    }
    
    // Criar usuário padrão
    $senha_hash = password_hash('admin', PASSWORD_DEFAULT);
    $data_usuario = [
        'nome' => 'Administrador',
        'email' => 'admin@tenant.com',
        'senha' => $senha_hash,
        'cpf' => '000.000.000-00',
        'cep' => '00000-000',
        'telefone' => '(00) 0000-0000',
        'situacao' => 1,
        'dataCadastro' => date('Y-m-d'),
        'dataExpiracao' => '3000-01-01',
        'permissoes_id' => $permissao_id,
        'ten_id' => $ten_id
    ];
    
    if ($CI->db->insert('usuarios', $data_usuario)) {
        $usuario_id = $CI->db->insert_id();
        echo "  ✓ Usuário padrão criado (ID: $usuario_id)\n";
        echo "    Email: admin@tenant.com\n";
        echo "    Senha: admin\n\n";
    } else {
        echo "  ❌ Erro ao criar usuário: " . $CI->db->error()['message'] . "\n";
    }
}

echo "========================================\n";
echo "Concluído!\n";
echo "========================================\n";
echo "\nCredenciais:\n";
echo "Super Usuário:\n";
echo "  Email: admin@super.com\n";
echo "  Senha: admin123\n";
echo "\nUsuário Comum (se criado):\n";
echo "  Email: admin@tenant.com\n";
echo "  Senha: admin\n";
echo "\nTenant ID: $ten_id\n";
echo "\nAgora você pode fazer login e cadastrar NFCom!\n";
