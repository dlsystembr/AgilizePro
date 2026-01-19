<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mapos;charset=utf8", "root", "");

    foreach (['emitente', 'empresas'] as $table) {
        echo "Table: $table\n";
        $stmt = $pdo->query("SELECT * FROM $table LIMIT 1");
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                print_r($row);
            } else {
                echo "Table is empty\n";
            }
        } else {
            echo "Could not query table $table\n";
        }
        echo "\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
