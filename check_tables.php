<?php
define('BASEPATH', 'true');
require_once 'c:/xampp/htdocs/mapos/application/config/database.php';
$db_config = $db['default'];

try {
    $dsn = "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset={$db_config['char_set']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password']);

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
