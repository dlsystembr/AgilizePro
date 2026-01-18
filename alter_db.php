<?php
$conn = mysqli_connect("localhost", "root", "", "mapos");
if (!$conn) {
    die("Falha na conexão: " . mysqli_connect_error());
}

$sql = "ALTER TABLE nfecom_capa ADD COLUMN CLN_ID INT(11) AFTER NFC_ID";
if (mysqli_query($conn, $sql)) {
    echo "Coluna CLN_ID adicionada com sucesso!\n";
} else {
    echo "Erro ao adicionar coluna: " . mysqli_error($conn) . "\n";
}

$sql_index = "ALTER TABLE nfecom_capa ADD INDEX (CLN_ID)";
if (mysqli_query($conn, $sql_index)) {
    echo "Índice para CLN_ID adicionado com sucesso!\n";
} else {
    echo "Erro ao adicionar índice: " . mysqli_error($conn) . "\n";
}

mysqli_close($conn);
