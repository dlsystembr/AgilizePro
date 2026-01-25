<?php
// Teste direto de autocomplete - sem CodeIgniter
header('Content-Type: application/json');

// Conectar ao banco
$host = 'localhost';
$db = 'mapos';
$user = 'root';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $termo = isset($_GET['term']) ? $_GET['term'] : '';
    $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'cliente';

    if (empty($termo)) {
        echo json_encode([]);
        exit;
    }

    if ($tipo === 'cliente') {
        $sql = "SELECT idClientes as id, nomeCliente as label, nomeCliente as value, 
                estado, natureza_contribuinte, objetivo_comercial 
                FROM clientes 
                WHERE nomeCliente LIKE :termo OR documento LIKE :termo
                LIMIT 10";
    } else {
        $sql = "SELECT idProdutos as id, descricao as label, descricao as value, precoVenda
                FROM produtos 
                WHERE descricao LIKE :termo OR codDeBarra LIKE :termo
                LIMIT 10";
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute(['termo' => "%{$termo}%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
