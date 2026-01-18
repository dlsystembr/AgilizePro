<?php
$conn = mysqli_connect("localhost", "root", "", "mapos");
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$res = mysqli_query($conn, "DESCRIBE clientes");
if ($res) {
    echo "Colunas de clientes:\n";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "  " . $row['Field'] . "\n";
    }
} else {
    echo "Erro ao descrever clientes: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
