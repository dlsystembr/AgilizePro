<?php
$url = "https://nfcom-homologacao.svrs.rs.gov.br/ws/NFComRecepcao/NFComRecepcao.asmx?wsdl";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response Length: " . strlen($response) . "\n";
if ($httpCode != 200) {
    echo "Response: " . substr($response, 0, 500);
} else {
    echo "WSDL Found.";
}
?>