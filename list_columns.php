<?php
$mysqli = new mysqli("localhost", "root", "", "mapos");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$result = $mysqli->query("SHOW COLUMNS FROM nfecom_capa");
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
$mysqli->close();
