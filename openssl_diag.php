<?php
echo "OPENSSL_CONF ENV: " . getenv('OPENSSL_CONF') . "\n";
echo "OPENSSL_VERSION_TEXT: " . OPENSSL_VERSION_TEXT . "\n";
echo "OPENSSL_VERSION_NUMBER: " . OPENSSL_VERSION_NUMBER . "\n";

$p = openssl_get_cert_locations();
echo "Default Cert File: " . $p['default_cert_file'] . "\n";
echo "Default Cert Dir: " . $p['default_cert_dir'] . "\n";
echo "Default Private Dir: " . $p['default_private_dir'] . "\n";
echo "Default Default Cert Area: " . $p['default_cert_area'] . "\n";
echo "Default Ini File: " . $p['ini_file'] . "\n";

// Test loading legacy? No easy way in PHP 8.2+ without specific libraries or exec
// But we can check if it fails with a dummy legacy op if we had one.
