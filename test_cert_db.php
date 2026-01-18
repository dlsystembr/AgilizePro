<?php
// Load CodeIgniter framework enough to use DB
error_reporting(E_ALL);
ini_set('display_errors', 1);

$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'mapos';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Erro ao conectar ao banco: " . mysqli_connect_error() . "\n");
}

$res = mysqli_query($conn, "SELECT CER_ARQUIVO, CER_SENHA FROM configuracoes_fiscais WHERE CFG_NOME = 'NFCOM' LIMIT 1");
$config = mysqli_fetch_assoc($res);

if (!$config) {
    die("Configuração fiscal não encontrada.\n");
}

$certContent = $config['CER_ARQUIVO'];
$password = $config['CER_SENHA'];

echo "Cert length: " . strlen($certContent) . "\n";
echo "OPENSSL_VERSION_TEXT: " . OPENSSL_VERSION_TEXT . "\n";

// Try WITHOUT setting OPENSSL_CONF
$out = [];
$res = openssl_pkcs12_read($certContent, $out, $password);
if (!$res) {
    echo "FALHA SEM OPENSSL_CONF: " . openssl_error_string() . "\n";
} else {
    echo "SUCESSO SEM OPENSSL_CONF!\n";
}

// Try WITH setting OPENSSL_CONF
$confPath = 'C:\\xampp\\php\\extras\\ssl\\openssl.cnf';
if (file_exists($confPath)) {
    putenv("OPENSSL_CONF=$confPath");
    echo "Configurando OPENSSL_CONF para $confPath\n";

    $out2 = [];
    $res2 = openssl_pkcs12_read($certContent, $out2, $password);
    if (!$res2) {
        echo "FALHA COM OPENSSL_CONF: " . openssl_error_string() . "\n";
    } else {
        echo "SUCESSO COM OPENSSL_CONF!\n";
    }
} else {
    echo "Arquivo $confPath não encontrado.\n";
}

// Check if Apache conf works better
$confPath2 = 'C:\\xampp\\apache\\conf\\openssl.cnf';
if (file_exists($confPath2)) {
    putenv("OPENSSL_CONF=$confPath2");
    echo "Configurando OPENSSL_CONF para $confPath2\n";

    $out3 = [];
    $res3 = openssl_pkcs12_read($certContent, $out3, $password);
    if (!$res3) {
        echo "FALHA COM OPENSSL_CONF (Apache): " . openssl_error_string() . "\n";
    } else {
        echo "SUCESSO COM OPENSSL_CONF (Apache)!\n";
    }
}
