<?php
libxml_use_internal_errors(true);
$xml = file_get_contents('c:\\xampp\\htdocs\\mapos\\debug_nfcom_generated.xml');

$dom = new DOMDocument();
if (!$dom->loadXML($xml)) {
    echo "XML INVALIDO:\n";
    foreach (libxml_get_errors() as $error) {
        echo $error->message . "\n";
    }
} else {
    echo "XML VALIDO";
}
?>