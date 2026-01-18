<?php
$conn = mysqli_connect("localhost", "root", "", "mapos");
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$res = mysqli_query($conn, "DESCRIBE nfecom_itens");
if ($res) {
    echo "Colunas de nfecom_itens:\n";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "  " . $row['Field'] . "\n";
    }
} else {
    echo "Erro ao descrever nfecom_itens: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
