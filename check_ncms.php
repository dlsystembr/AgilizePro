<?php
// Configuração do banco de dados
$config = require('application/config/database.php');

$conn = new mysqli(
    $config['default']['hostname'],
    $config['default']['username'],
    $config['default']['password'],
    $config['default']['database']
);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Verifica total de NCMs
$result = $conn->query('SELECT COUNT(*) as total FROM ncms');
$row = $result->fetch_assoc();
echo 'Total de NCMs na base: ' . $row['total'] . PHP_EOL;

// Mostra os primeiros 5 NCMs
$result = $conn->query('SELECT NCM_CODIGO, NCM_DESCRICAO FROM ncms LIMIT 5');
echo 'Primeiros 5 NCMs:' . PHP_EOL;
while ($row = $result->fetch_assoc()) {
    echo $row['NCM_CODIGO'] . ' - ' . substr($row['NCM_DESCRICAO'], 0, 50) . '...' . PHP_EOL;
}

$conn->close();
?>
