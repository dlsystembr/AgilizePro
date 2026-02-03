<?php
// Script para adicionar flag is_super na sessão atual
session_start();

$conn = new mysqli('localhost', 'root', '', 'mapos');

echo "<h2>Correção de Sessão - Adicionar flag is_super</h2>";
echo "<style>body { font-family: Arial; } .success { color: green; } .error { color: red; }</style>";

// Buscar sessão mais recente
$sql = "SELECT id, data FROM ci_sessions WHERE ip_address = '192.168.1.15' ORDER BY timestamp DESC LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $session = $result->fetch_assoc();
    $session_id = $session['id'];
    $data = $session['data'];

    echo "<p><strong>Session ID:</strong> $session_id</p>";
    echo "<p><strong>Dados atuais:</strong></p>";
    echo "<pre>" . htmlspecialchars($data) . "</pre>";

    // Verificar se já tem is_super
    if (strpos($data, 'is_super') !== false) {
        echo "<p class='success'>✓ A sessão já tem a flag is_super!</p>";
    } else {
        echo "<p class='error'>✗ A sessão NÃO tem a flag is_super</p>";

        // Adicionar is_super
        $new_data = $data . 'is_super|b:1;';

        echo "<p><strong>Novos dados (com is_super):</strong></p>";
        echo "<pre>" . htmlspecialchars($new_data) . "</pre>";

        echo "<form method='post'>";
        echo "<input type='hidden' name='update' value='1'>";
        echo "<input type='hidden' name='session_id' value='$session_id'>";
        echo "<input type='hidden' name='new_data' value='" . htmlspecialchars($new_data) . "'>";
        echo "<button type='submit' style='padding: 10px 20px; background: #4CAF50; color: white; border: none; cursor: pointer;'>ADICIONAR FLAG is_super</button>";
        echo "</form>";
    }
}

// Processar atualização
if (isset($_POST['update'])) {
    $session_id = $_POST['session_id'];
    $new_data = $_POST['new_data'];

    $stmt = $conn->prepare("UPDATE ci_sessions SET data = ? WHERE id = ?");
    $stmt->bind_param("ss", $new_data, $session_id);

    if ($stmt->execute()) {
        echo "<div style='background: #d4edda; padding: 20px; margin: 20px 0; border: 1px solid #c3e6cb;'>";
        echo "<h3 class='success'>✓ SESSÃO ATUALIZADA COM SUCESSO!</h3>";
        echo "<p>A flag is_super foi adicionada à sua sessão.</p>";
        echo "<p><strong>Próximo passo:</strong> Acesse <a href='mapos/configurar'>Configurações do Sistema</a> - deve funcionar agora!</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; margin: 20px 0; border: 1px solid #f5c6cb;'>";
        echo "<h3 class='error'>✗ ERRO AO ATUALIZAR</h3>";
        echo "<p>" . $conn->error . "</p>";
        echo "</div>";
    }
}

$conn->close();
