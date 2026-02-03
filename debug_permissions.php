<?php
// Script de debug para verificar permissões do usuário logado
// Execute este arquivo acessando: http://localhost/mapos/debug_permissions.php

define('BASEPATH', TRUE);

// Configuração do banco
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'mapos';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

echo "<h2>Debug de Permissões - Mapos</h2>";
echo "<hr>";

// Verificar sessão PHP
session_start();
echo "<h3>1. Dados da Sessão</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];

    // Buscar dados do usuário
    echo "<h3>2. Dados do Usuário (ID: $user_id)</h3>";
    $sql = "SELECT * FROM usuarios WHERE idUsuarios = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    echo "<pre>";
    print_r($user);
    echo "</pre>";

    if ($user) {
        $perm_id = $user['permissoes_id'];
        $ten_id = $user['ten_id'];

        // Buscar permissões do perfil
        echo "<h3>3. Permissões do Perfil (ID: $perm_id)</h3>";
        $sql = "SELECT * FROM permissoes WHERE idPermissao = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $perm_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $perfil = $result->fetch_assoc();

        if ($perfil) {
            echo "<strong>Nome do Perfil:</strong> " . $perfil['nome'] . "<br>";
            echo "<strong>Permissões Serializadas:</strong><br>";
            $perms = unserialize($perfil['permissoes']);
            echo "<pre>";
            print_r($perms);
            echo "</pre>";

            echo "<strong>Tem cSistema?</strong> ";
            if (isset($perms['cSistema']) && $perms['cSistema'] == '1') {
                echo "<span style='color: green;'>✓ SIM</span><br>";
            } else {
                echo "<span style='color: red;'>✗ NÃO</span><br>";
            }
        }

        // Buscar permissões do tenant
        echo "<h3>4. Permissões do Tenant (ID: $ten_id)</h3>";
        $sql = "SELECT * FROM tenant_permissoes_menu WHERE tpm_ten_id = ? AND tpm_permissao = 'cSistema'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ten_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $tenant_perm = $result->fetch_assoc();

        if ($tenant_perm) {
            echo "<pre>";
            print_r($tenant_perm);
            echo "</pre>";

            echo "<strong>Permissão cSistema ativa para o tenant?</strong> ";
            if ($tenant_perm['tpm_ativo'] == 1) {
                echo "<span style='color: green;'>✓ SIM</span><br>";
            } else {
                echo "<span style='color: red;'>✗ NÃO (tpm_ativo = 0)</span><br>";
            }
        } else {
            echo "<span style='color: red;'>✗ Permissão cSistema NÃO encontrada para este tenant</span><br>";
        }

        // Verificar todas as permissões do tenant
        echo "<h3>5. Todas as Permissões do Tenant</h3>";
        $sql = "SELECT tpm_permissao, tpm_ativo FROM tenant_permissoes_menu WHERE tpm_ten_id = ? ORDER BY tpm_permissao";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $ten_id);
        $stmt->execute();
        $result = $stmt->get_result();

        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Permissão</th><th>Ativo</th></tr>";
        while ($row = $result->fetch_assoc()) {
            $color = $row['tpm_ativo'] == 1 ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . $row['tpm_permissao'] . "</td>";
            echo "<td style='color: $color;'>" . ($row['tpm_ativo'] == 1 ? 'SIM' : 'NÃO') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>Nenhum usuário logado. Faça login primeiro.</p>";
}

$conn->close();
