<?php
// Script para limpar sessões antigas
require_once 'application/config/database.php';

try {
    $host = $db['default']['hostname'];
    $user = $db['default']['username'];
    $pass = $db['default']['password'];
    $db_name = $db['default']['database'];

    $conn = new mysqli($host, $user, $pass, $db_name);

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Limpar sessões antigas (mais de 1 hora)
    $sql = "DELETE FROM ci_sessions WHERE timestamp < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL 1 HOUR))";
    $result = $conn->query($sql);

    echo "Sessões antigas removidas: " . $conn->affected_rows . " registros<br>";

    // Mostrar total de sessões restantes
    $sql = "SELECT COUNT(*) as total FROM ci_sessions";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    echo "Sessões restantes: " . $row['total'] . "<br>";

    $conn->close();

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>