<?php
define('BASEPATH', 'dummy');
define('ENVIRONMENT', 'development');
require_once 'application/config/database.php';

$db_config = [
    'hostname' => $_ENV['DB_HOSTNAME'] ?? 'localhost',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'database' => $_ENV['DB_DATABASE'] ?? 'mapos'
];

$mysqli = new mysqli($db_config['hostname'], $db_config['username'], $db_config['password'], $db_config['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$columns = [
    "NFC_CHAVE_PIX VARCHAR(255) NULL",
    "NFC_LINHA_DIGITAVEL VARCHAR(255) NULL"
];

foreach ($columns as $column) {
    $colName = explode(' ', $column)[0];
    $check = $mysqli->query("SHOW COLUMNS FROM nfecom_capa LIKE '$colName'");
    if ($check->num_rows == 0) {
        if ($mysqli->query("ALTER TABLE nfecom_capa ADD $column")) {
            echo "Added $colName\n";
        } else {
            echo "Error adding $colName: " . $mysqli->error . "\n";
        }
    } else {
        echo "$colName already exists\n";
    }
}

$mysqli->close();
