<?php
// Standalone NFCom Test Script
// Run via CLI: php test_manual_nfcom.php
// Or via Browser: http://localhost/mapos/test_manual_nfcom.php

// 1. Setup Environment
define('BASEPATH', __DIR__ . '/system/'); // Fake BASEPATH for library check
date_default_timezone_set('America/Sao_Paulo');

require 'application/vendor/autoload.php';
require 'application/libraries/NFComMake.php';

use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;

// 2. Database Connection (Standard XAMPP)
$mysqli = new mysqli('localhost', 'root', '', 'mapos');
if ($mysqli->connect_error) {
    die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// 3. Get Certificate
echo "Fetching Certificate...\n";
// Join to find the cert used for NFCOM or just pick the first active one
$result = $mysqli->query("SELECT CER_ARQUIVO, CER_SENHA FROM certificados_digitais WHERE CER_ATIVO = 1 LIMIT 1");
if (!$result || $result->num_rows === 0) {
    die("Certificate not found in DB.\n");
}
$row = $result->fetch_assoc();
$pfxContent = $row['CER_ARQUIVO']; // Assuming RAW BINARY in BLOB
$password = $row['CER_SENHA'];

try {
    // Check Config for OpenSSL Legacy
    if (file_exists('C:/xampp/php/extras/ssl/openssl.cnf')) {
        putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
        putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');
    }
    $certificate = Certificate::readPfx($pfxContent, $password);
} catch (Exception $e) {
    die("Certificate Error: " . $e->getMessage() . "\n");
}

// 4. Manual Data (Homologation)
$dados = [
    'chave' => '', // Will be generated if empty, but here we construct it manually inside make? No, Make needs data.
    // We construct a fake ID for now or let formatting happen.
    // NFComMake builds ID based on 'chave'. We need to pre-calc or let it be.
    'ide' => [
        'cUF' => '43', // GO
        'tpAmb' => '2', // Homologacao
        'serie' => '1',
        'nNF' => '100', // Unique number
        'cNF' => str_pad((string) rand(0, 9999999), 7, '0', STR_PAD_LEFT),
        'cDV' => '0', // Should calculate
        'dhEmi' => date('Y-m-d\TH:i:sP'),
        'tpEmis' => '1', // Normal
        'nSiteAutoriz' => '0',
        'cMunFG' => '5217403', // Pirenopolis/GO e.g. or user's: 5218300 (Posse)
        'finNFCom' => '0', // Normal
        'tpFat' => '0', // Fatura na emissao
    ],
    'emitente' => [ // VIVA EMPREENDIMENTOS
        'cnpj' => '54314807000128',
        'ie' => '201299879',
        'crt' => '3',
        'razao_social' => 'VIVA EMPREENDIMENTOS LTDA',
        'nome_fantasia' => 'VIVA FM',
        'endereco' => [
            'logradouro' => 'AV JUSCELINO KUBITSCHEK DE OLIVEIRA',
            'numero' => 'S/N',
            'complemento' => 'QD 29 LT 22',
            'bairro' => 'GUARANI',
            'codigo_municipio' => '5218300', // Posse - GO
            'municipio' => 'POSSE',
            'cep' => '73906102',
            'uf' => 'GO',
        ],
        'telefone' => '6299999999'
    ],
    'destinatario' => [ // NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO
        'nome' => 'NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',
        'cnpj' => '02542472000100', // One of the valid test CNPJs
        'indicador_ie' => '9', // Nao contribuinte
        'endereco' => [
            'logradouro' => 'Rua Teste',
            'numero' => '123',
            'bairro' => 'Centro',
            'codigo_municipio' => '4314902', // Porto Alegre
            'municipio' => 'Porto Alegre',
            'cep' => '90000000',
            'uf' => 'RS',
        ]
    ],
    'assinante' => [
        'codigo' => '001',
        'tipo' => '1', // Comercial
        'tipo_servico' => '1', // Telefonia/Internet
        'numero_contrato' => '999',
        'data_inicio_contrato' => date('Y-m-d'),
    ],
    'itens' => [
        [
            'codigo' => '001',
            'descricao' => 'Servico Teste',
            'classificacao_item' => '010101',
            'cfop' => '5303',
            'unidade' => 'UN',
            'quantidade' => 1.0,
            'valor_unitario' => 10.00,
            'valor_total' => 10.00,
            'imposto' => [
                'icms' => ['cst' => '00', 'vBC' => 0.00, 'pICMS' => 0.00, 'vICMS' => 0.00],
                'pis' => ['cst' => '01', 'vBC' => 10.00, 'pPIS' => 0.65, 'vPIS' => 0.07],
                'cofins' => ['cst' => '01', 'vBC' => 10.00, 'pCOFINS' => 3.00, 'vCOFINS' => 0.30],
            ]
        ]
    ],
    'totais' => [
        'vProd' => 10.00,
        'icms' => ['vBC' => 0, 'vICMS' => 0, 'vICMSDeson' => 0, 'vFCP' => 0],
        'vCOFINS' => 0.30,
        'vPIS' => 0.07,
        'vDesc' => 0,
        'vOutro' => 0,
        'vNF' => 10.37 // Sum
    ],
    'faturamento' => [
        'competencia' => date('Ym'),
        'vencimento' => date('Y-m-d'),
        'periodo_inicio' => date('Y-m-d'),
        'periodo_fim' => date('Y-m-d')
    ],
    'informacoes_adicionais' => [
        'complementar' => 'Teste Emissao Manual'
    ],
    'suplementar' => [
        'qrCode' => 'https://fake-qrcode-url.com' // Should generate real one but keeping simple
    ]
];

// 5. Generate XML
echo "Generating XML...\n";
$make = new NFComMake();
// Mock chave for ID attribute (normally calculated)
$dados['chave'] = '52260154314807000128620010000001001' . rand(10000000, 99999999);
$xml = $make->build($dados);

// 6. Sign XML
echo "Signing XML...\n";
try {
    $signedXml = Signer::sign($certificate, $xml, 'infNFCom');
} catch (Exception $e) {
    die("Sign Error: " . $e->getMessage() . "\n");
}

// 7. Prepare SOAP
// Prepare payload: keep full signed XML (including declaration)
$payloadXml = $signedXml; // no removal of XML declaration
// Create ZIP in memory and encode in Base64 (required by SEFAZ)
$zip = new ZipArchive();
$tempZip = tempnam(sys_get_temp_dir(), 'nfcom_zip_');
if ($zip->open($tempZip, ZipArchive::CREATE) !== true) {
    die("Não foi possível criar o arquivo ZIP.\n");
}
$zip->addFromString('NFCom.xml', $payloadXml, ZipArchive::CM_STORE); // no compression
$zip->close();
// Save a copy for inspection
copy($tempZip, __DIR__ . '/debug_nfcom.zip');
$zipData = file_get_contents($tempZip);
unlink($tempZip);
$content = base64_encode($zipData);
echo "Testing WSDL Namespace + GZIP...\n";

$namespace = 'http://www.portalfiscal.inf.br/nfcom/wsdl/NFComRecepcao'; // WSDL Namespace
$schemaNs = 'http://www.portalfiscal.inf.br/nfcom'; // Schema namespace for payload
$action = 'http://www.portalfiscal.inf.br/nfcom/wsdl/NFComRecepcao/nfcomRecepcao';
// Use Uppercase WS
$url = 'https://nfcom-homologacao.svrs.rs.gov.br/WS/NFComRecepcao/NFComRecepcao.asmx';

$soapRequest = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://www.w3.org/2003/05/soap-envelope" xmlns:nfcom="' . $namespace . '" xmlns:nfcomData="' . $schemaNs . '">
    <soap:Header>
        <nfcom:nfcomCabecMsg>
            <nfcom:cUF>43</nfcom:cUF>
            <nfcom:versaoDados>1.00</nfcom:versaoDados>
        </nfcom:nfcomCabecMsg>
    </soap:Header>
    <soap:Body>
        <nfcomData:nfcomDadosMsg>' . $content . '</nfcomData:nfcomDadosMsg>
    </soap:Body>
</soap:Envelope>';

file_put_contents('test_manual_request.xml', $soapRequest);

// 8. Send cURL
echo "Sending to SEFAZ ($url)...\n";

$tempCert = tempnam(sys_get_temp_dir(), 'cert_');
$tempKey = tempnam(sys_get_temp_dir(), 'key_');
file_put_contents($tempCert, $certificate->publicKey);
file_put_contents($tempKey, $certificate->privateKey);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $soapRequest);
// Set proper SOAP headers
$headers = [
    "Content-Type: application/soap+xml; charset=utf-8; action=\"{$action}\"",
    "User-Agent: Mozilla/5.0",
    "Expect:"
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
// Disable Verify for Test
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

curl_setopt($ch, CURLOPT_SSLCERT, $tempCert);
curl_setopt($ch, CURLOPT_SSLKEY, $tempKey);
curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');

// Debug
$verboseInd = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, $verboseInd);

// Headers (SOAP 1.2)
$headers = [
    'Content-Type: application/soap+xml; charset=utf-8; action="' . $action . '"',
    'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
    'Expect:', // Disable 100-continue
    'Content-Length: ' . strlen($soapRequest)
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

rewind($verboseInd);
$verboseLog = stream_get_contents($verboseInd);
file_put_contents('test_manual_debug.log', $verboseLog);
echo "Debug log saved to test_manual_debug.log\n";

@unlink($tempCert);
@unlink($tempKey);

echo "HTTP Code: $httpCode\n";
if ($error) {
    echo "cURL Error: $error\n";
}

file_put_contents('test_manual_response.xml', $response);
echo "Response saved to test_manual_response.xml\n";
echo "Response Preview:\n" . substr($response, 0, 500) . "\n";

?>