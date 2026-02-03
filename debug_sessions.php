<?php
// Script simplificado - mostra TODAS as sessões ativas
$conn = new mysqli('localhost', 'root', '', 'mapos');

echo "<h2>Sessões Ativas no Banco</h2>";
echo "<hr>";

$sql = "SELECT * FROM ci_sessions ORDER BY timestamp DESC LIMIT 10";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Session ID</th><th>IP</th><th>Timestamp</th><th>Dados (preview)</th></tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . substr($row['id'], 0, 20) . "...</td>";
        echo "<td>" . $row['ip_address'] . "</td>";
        echo "<td>" . date('Y-m-d H:i:s', $row['timestamp']) . "</td>";
        echo "<td><pre style='max-width: 500px; overflow: auto;'>" . htmlspecialchars(substr($row['data'], 0, 300)) . "...</pre></td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<hr>";
    echo "<h3>Análise da Sessão Mais Recente</h3>";

    $sql = "SELECT * FROM ci_sessions ORDER BY timestamp DESC LIMIT 1";
    $result = $conn->query($sql);
    $session = $result->fetch_assoc();

    if ($session) {
        echo "<strong>Session ID:</strong> " . $session['id'] . "<br>";
        echo "<strong>Dados completos:</strong><br>";
        echo "<pre>" . htmlspecialchars($session['data']) . "</pre>";

        // Tentar extrair ID do usuário
        if (preg_match('/id\|s:\d+:"(\d+)"/', $session['data'], $matches)) {
            $user_id = $matches[1];
            echo "<br><strong style='color: green;'>✓ Usuário ID encontrado: $user_id</strong><br>";

            // Buscar dados do usuário
            $sql = "SELECT u.*, p.nome as perfil_nome FROM usuarios u LEFT JOIN permissoes p ON u.permissoes_id = p.idPermissao WHERE u.idUsuarios = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user) {
                echo "<h3>Dados do Usuário Logado</h3>";
                echo "<strong>Nome:</strong> " . $user['nome'] . "<br>";
                echo "<strong>Email:</strong> " . $user['email'] . "<br>";
                echo "<strong>Tenant ID:</strong> " . ($user['ten_id'] ?? 'N/A') . "<br>";
                echo "<strong>Perfil:</strong> " . $user['perfil_nome'] . " (ID: " . $user['permissoes_id'] . ")<br>";

                // Verificar permissão cSistema no perfil
                $perm_id = $user['permissoes_id'];
                $sql = "SELECT permissoes FROM permissoes WHERE idPermissao = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $perm_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $perfil = $result->fetch_assoc();

                if ($perfil) {
                    $perms = unserialize($perfil['permissoes']);
                    echo "<br><strong>Permissão cSistema no perfil:</strong> ";
                    if (isset($perms['cSistema']) && $perms['cSistema'] == '1') {
                        echo "<span style='color: green; font-size: 18px;'>✓ SIM</span><br>";
                    } else {
                        echo "<span style='color: red; font-size: 18px;'>✗ NÃO</span><br>";
                        echo "<details><summary>Ver todas as permissões do perfil</summary><pre>";
                        print_r($perms);
                        echo "</pre></details>";
                    }
                }

                // Verificar permissão no tenant
                if ($user['ten_id']) {
                    $ten_id = $user['ten_id'];
                    $sql = "SELECT * FROM tenant_permissoes_menu WHERE tpm_ten_id = ? AND tpm_permissao = 'cSistema'";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $ten_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $tenant_perm = $result->fetch_assoc();

                    echo "<br><strong>Permissão cSistema no tenant (ID: $ten_id):</strong> ";
                    if ($tenant_perm && $tenant_perm['tpm_ativo'] == 1) {
                        echo "<span style='color: green; font-size: 18px;'>✓ SIM (ativo)</span><br>";
                    } else if ($tenant_perm) {
                        echo "<span style='color: orange; font-size: 18px;'>⚠ EXISTE mas tpm_ativo = 0</span><br>";
                    } else {
                        echo "<span style='color: red; font-size: 18px;'>✗ NÃO EXISTE</span><br>";
                    }
                }
            }
        } else {
            echo "<br><strong style='color: red;'>✗ Nenhum usuário logado nesta sessão</strong><br>";
        }
    }
} else {
    echo "<p style='color: red;'>Nenhuma sessão encontrada no banco de dados.</p>";
}

$conn->close();
