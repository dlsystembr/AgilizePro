<?php
// Script de debug para verificar permissões - Versão 2
// Execute: http://localhost/mapos/debug_permissions_v2.php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'mapos';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

echo "<h2>Debug de Permissões - Mapos v2</h2>";
echo "<hr>";

// Verificar cookie de sessão
$cookie_name = 'app_session';
echo "<h3>1. Cookie de Sessão</h3>";
if (isset($_COOKIE[$cookie_name])) {
    $session_id = $_COOKIE[$cookie_name];
    echo "<strong>Session ID:</strong> $session_id<br>";

    // Buscar dados da sessão no banco
    $sql = "SELECT * FROM ci_sessions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $session_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $session_data = $result->fetch_assoc();

    if ($session_data) {
        echo "<h3>2. Dados da Sessão (Banco de Dados)</h3>";
        echo "<strong>IP:</strong> " . $session_data['ip_address'] . "<br>";
        echo "<strong>Timestamp:</strong> " . date('Y-m-d H:i:s', $session_data['timestamp']) . "<br>";
        echo "<strong>Dados (raw):</strong><br>";
        echo "<pre>" . htmlspecialchars($session_data['data']) . "</pre>";

        // Tentar decodificar os dados da sessão
        $decoded = [];
        $parts = explode(';', $session_data['data']);
        foreach ($parts as $part) {
            if (empty($part))
                continue;
            if (preg_match('/^([^|]+)\|(.+)$/', $part, $matches)) {
                $key = $matches[1];
                $value = $matches[2];
                // Tentar unserialize
                $unserialized = @unserialize($value);
                $decoded[$key] = $unserialized !== false ? $unserialized : $value;
            }
        }

        echo "<h3>3. Dados da Sessão (Decodificados)</h3>";
        echo "<pre>";
        print_r($decoded);
        echo "</pre>";

        // Verificar dados importantes
        if (isset($decoded['id'])) {
            $user_id = $decoded['id'];
            $ten_id = $decoded['ten_id'] ?? null;
            $permissao_id = $decoded['permissao'] ?? null;

            echo "<h3>4. Informações do Usuário</h3>";
            echo "<strong>User ID:</strong> $user_id<br>";
            echo "<strong>Tenant ID:</strong> " . ($ten_id ?? 'N/A') . "<br>";
            echo "<strong>Permissão ID:</strong> " . ($permissao_id ?? 'N/A') . "<br>";

            // Buscar dados completos do usuário
            $sql = "SELECT * FROM usuarios WHERE idUsuarios = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                echo "<h3>5. Dados Completos do Usuário</h3>";
                echo "<pre>";
                print_r($user);
                echo "</pre>";

                $perm_id = $user['permissoes_id'];

                // Buscar permissões do perfil
                echo "<h3>6. Permissões do Perfil (ID: $perm_id)</h3>";
                $sql = "SELECT * FROM permissoes WHERE idPermissao = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $perm_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $perfil = $result->fetch_assoc();

                if ($perfil) {
                    echo "<strong>Nome do Perfil:</strong> " . $perfil['nome'] . "<br>";
                    $perms = unserialize($perfil['permissoes']);

                    echo "<strong>Tem cSistema no perfil?</strong> ";
                    if (isset($perms['cSistema']) && $perms['cSistema'] == '1') {
                        echo "<span style='color: green; font-size: 20px;'>✓ SIM</span><br>";
                    } else {
                        echo "<span style='color: red; font-size: 20px;'>✗ NÃO</span><br>";
                        echo "<em>Permissões disponíveis no perfil:</em><br>";
                        echo "<pre>";
                        print_r(array_keys($perms));
                        echo "</pre>";
                    }
                }

                // Buscar permissões do tenant
                if ($ten_id) {
                    echo "<h3>7. Permissões do Tenant (ID: $ten_id)</h3>";
                    $sql = "SELECT * FROM tenant_permissoes_menu WHERE tpm_ten_id = ? AND tpm_permissao = 'cSistema'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $ten_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $tenant_perm = $result->fetch_assoc();

                    if ($tenant_perm) {
                        echo "<strong>Permissão cSistema ativa para o tenant?</strong> ";
                        if ($tenant_perm['tpm_ativo'] == 1) {
                            echo "<span style='color: green; font-size: 20px;'>✓ SIM</span><br>";
                        } else {
                            echo "<span style='color: red; font-size: 20px;'>✗ NÃO (tpm_ativo = 0)</span><br>";
                        }
                        echo "<pre>";
                        print_r($tenant_perm);
                        echo "</pre>";
                    } else {
                        echo "<span style='color: red; font-size: 20px;'>✗ Permissão cSistema NÃO encontrada para este tenant</span><br>";
                    }

                    // Listar todas as permissões do tenant
                    echo "<h3>8. Todas as Permissões do Tenant</h3>";
                    $sql = "SELECT tpm_permissao, tpm_ativo FROM tenant_permissoes_menu WHERE tpm_ten_id = ? ORDER BY tpm_permissao";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $ten_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
                    echo "<tr><th>Permissão</th><th>Ativo</th></tr>";
                    $count = 0;
                    while ($row = $result->fetch_assoc()) {
                        $color = $row['tpm_ativo'] == 1 ? 'green' : 'red';
                        echo "<tr>";
                        echo "<td>" . $row['tpm_permissao'] . "</td>";
                        echo "<td style='color: $color;'>" . ($row['tpm_ativo'] == 1 ? 'SIM' : 'NÃO') . "</td>";
                        echo "</tr>";
                        $count++;
                    }
                    echo "</table>";
                    echo "<p><strong>Total:</strong> $count permissões</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>Não foi possível decodificar os dados da sessão corretamente.</p>";
        }
    } else {
        echo "<p style='color: red;'>Sessão não encontrada no banco de dados.</p>";
    }
} else {
    echo "<p style='color: red;'>Cookie de sessão '$cookie_name' não encontrado. Faça login primeiro.</p>";
    echo "<p><em>Cookies disponíveis:</em></p>";
    echo "<pre>";
    print_r($_COOKIE);
    echo "</pre>";
}

$conn->close();
