<?php
$logFile = 'c:\\xampp\\htdocs\\mapos\\application\\logs\\log-2026-01-18.php';
$lines = file($logFile);
$count = count($lines);
$output = "";
for ($i = max(0, $count - 50); $i < $count; $i++) {
    $output .= $lines[$i];
}
file_put_contents('c:\\xampp\\htdocs\\mapos\\last_error.txt', $output);
echo "Log saved to last_error.txt";
?>