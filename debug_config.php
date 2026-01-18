<?php
// Script de debug para verificar configurações
echo "<h1>Debug de Configuração Map-OS</h1>";
echo "<h2>Variáveis do Servidor:</h2>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "<br>";
echo "HTTPS: " . ($_SERVER['HTTPS'] ?? 'N/A') . "<br>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "<br>";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "<br>";

echo "<h2>Configuração Calculada:</h2>";
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$base_path = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
$base_path = $base_path === '/' ? '' : $base_path;

$base_url = $protocol . '://' . $host . $base_path . '/';
$cookie_domain = isset($_SERVER['HTTP_HOST']) ? '.' . $_SERVER['HTTP_HOST'] : '';

echo "Base URL calculada: $base_url<br>";
echo "Cookie Domain calculado: $cookie_domain<br>";

echo "<h2>Cookies Atuais:</h2>";
foreach ($_COOKIE as $name => $value) {
    echo "$name: $value<br>";
}

echo "<h2>Sessão Atual:</h2>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . session_status() . "<br>";
?>