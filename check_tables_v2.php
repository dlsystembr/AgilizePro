<?php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=mapos;charset=utf8", "root", "");

    foreach (['emitente', 'empresas'] as $table) {
        echo "Table: $table\n";
        $stmt = $pdo->query("DESCRIBE $table");
        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "- {$row['Field']} ({$row['Type']})\n";
            }
        } else {
            echo "Could not describe table $table\n";
        }
        echo "\n";
    }
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
