<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Tentar conectar sem banco primeiro para listar bancos
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$db_res = mysqli_query($conn, "SHOW DATABASES");
echo "Bancos de dados encontrados:\n";
$target_db = "";
while ($row = mysqli_fetch_array($db_res)) {
    echo "- " . $row[0] . "\n";
    if (strpos($row[0], 'agilizepro') !== false || $row[0] == 'mapos') {
        $target_db = $row[0];
    }
}

if ($target_db) {
    echo "\nUsando banco: $target_db\n";
    mysqli_select_db($conn, $target_db);
    $res = mysqli_query($conn, "DESCRIBE nfecom_capa");
    if ($res) {
        echo "Colunas de nfecom_capa:\n";
        while ($row = mysqli_fetch_assoc($res)) {
            echo "  " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Erro ao descrever nfecom_capa: " . mysqli_error($conn) . "\n";
    }
} else {
    echo "\nNenhum banco alvo encontrado.\n";
}

mysqli_close($conn);
