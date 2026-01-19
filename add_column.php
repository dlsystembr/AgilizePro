<?php
$mysqli = new mysqli("localhost", "root", "", "mapos");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$query = "ALTER TABLE nfecom_capa ADD COLUMN NFC_D_CONTRATO_FIM DATE NULL AFTER NFC_D_CONTRATO_INI";
if ($mysqli->query($query)) {
    echo "Column NFC_D_CONTRATO_FIM added successfully.\n";
} else {
    echo "Error adding column: " . $mysqli->error . "\n";
}
$mysqli->close();
