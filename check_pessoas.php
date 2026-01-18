<?php
$conn = mysqli_connect("localhost", "root", "", "mapos");
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$res = mysqli_query($conn, "DESCRIBE pessoas");
if ($res) {
    echo "Colunas de pessoas:\n";
    while ($row = mysqli_fetch_assoc($res)) {
        echo "  " . $row['Field'] . "\n";
    }
} else {
    echo "Erro ao descrever pessoas: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
