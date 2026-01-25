<?php
// diag.php
define('BASEPATH', 'diag');
require_once 'application/config/database.php';

$config = $db['default'];
$conn = new mysqli($config['hostname'], $config['username'], $config['password'], $config['database']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$tables = ['clientes', 'os', 'usuarios', 'pessoas'];
foreach ($tables as $table) {
    echo "Table: $table\n";
    $result = $conn->query("SHOW COLUMNS FROM $table");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo " - " . $row['Field'] . "\n";
        }
    } else {
        echo " Error: " . $conn->error . "\n";
    }
    echo "\n";
}
$conn->close();
