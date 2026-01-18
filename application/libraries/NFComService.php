<?php
defined('BASEPATH') or exit('No direct script access allowed');

use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;

class NFComService
{
    private $certificate;
    private $password;
    private $config;
    private $pfxContent;

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function setCertificate($content, $password)
    {
        // Fix for OpenSSL 3 legacy certificates
        if (file_exists('C:/xampp/php/extras/ssl/openssl.cnf')) {
            putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
            putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');
        }
        $this->pfxContent = $content;
        $this->certificate = Certificate::readPfx($content, $password);
        $this->password = $password;
    }

    public function sign($xml)
    {
        return Signer::sign(
            $this->certificate,
            $xml,
            'infNFCom',
            'Id',
            OPENSSL_ALGO_SHA1,
            [true, false, null, null]
        );
    }

    public function send($xmlSigned)
    {
        try {
            $ambiente = 2; // FORÇADO PARA HOMOLOGAÇÃO
            $url = $this->getEndpoint($ambiente, 'Recepcao');

            // Extrair o conteúdo do NFCom (remover <?xml e obter apenas o elemento NFCom)
            $dom = new DOMDocument();
            @$dom->loadXML($xmlSigned);

            // Buscar o elemento NFCom
            $xpath = new DOMXPath($dom);
            $xpath->registerNamespace('nfcom', 'http://www.portalfiscal.inf.br/nfcom');

            $nfcomNode = null;
            $nodes = $xpath->query('//nfcom:NFCom');
            if ($nodes->length > 0) {
                $nfcomNode = $nodes->item(0);
            } else {
                $nodes = $dom->getElementsByTagName('NFCom');
                if ($nodes->length > 0) {
                    $nfcomNode = $nodes->item(0);
                }
            }

            if (!$nfcomNode) {
                throw new \Exception('Elemento NFCom não encontrado no XML assinado');
            }

            // Converter para XML string e remover declaração
            $nfcomXml = $dom->saveXML($nfcomNode);
            $nfcomXml = preg_replace('/<\?xml[^>]*\?>/', '', $nfcomXml);

            // Comprimir com GZIP e converter para Base64 (como funciona no NFECom)
            $compressed = gzencode($nfcomXml);
            $encodedBody = trim(chunk_split(base64_encode($compressed), 76, "\n"));

            // Construir envelope SOAP exato como no NFECom (com Header obrigatório)
            $soapEnvelope = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            $soapEnvelope .= '<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">' . "\n";
            $soapEnvelope .= '  <soap12:Header>' . "\n";
            $soapEnvelope .= '    <nfcomCabecMsg xmlns="http://www.portalfiscal.inf.br/nfcom/wsdl/NFComRecepcao">' . "\n";
            $soapEnvelope .= '      <cUF>52</cUF>' . "\n"; // Código da UF do emitente (GO = 52)
            $soapEnvelope .= '      <versaoDados>1.00</versaoDados>' . "\n";
            $soapEnvelope .= '    </nfcomCabecMsg>' . "\n";
            $soapEnvelope .= '  </soap12:Header>' . "\n";
            $soapEnvelope .= '  <soap12:Body>' . "\n";
            $soapEnvelope .= '    <nfcomDadosMsg xmlns="http://www.portalfiscal.inf.br/nfcom/wsdl/NFComRecepcao">' . $encodedBody . '</nfcomDadosMsg>' . "\n";
            $soapEnvelope .= '  </soap12:Body>' . "\n";
            $soapEnvelope .= '</soap12:Envelope>';

            // Salvar envelope SOAP para debug
            file_put_contents('debug_lote_nfcom.xml', $soapEnvelope);

            // Enviar via método modificado que usa o envelope SOAP completo
            $response = $this->sendSoapEnvelope($url, $soapEnvelope);

            if (empty($response) || trim($response) === '') {
                log_message('error', 'Resposta vazia do SEFAZ após sendSoapEnvelope. URL: ' . $url);
                return ['error' => 'Resposta vazia do SEFAZ. Verifique: 1) Conexão com internet, 2) Certificado válido, 3) URL do serviço correta'];
            }

            return $this->parseResponse($response);

        } catch (\Exception $e) {
            log_message('error', 'NFCom Send Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return ['error' => 'Erro ao enviar NFCom: ' . $e->getMessage()];
        }
    }

    public function consult($chave, $ambiente = 2)
    {
        $ambiente = 2; // FORÇADO PARA HOMOLOGAÇÃO
        $url = $this->getEndpoint($ambiente, 'ConsultaProtocolo');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<consSitNFCom xmlns="http://www.portalfiscal.inf.br/nfcom" versao="1.00">
    <tpAmb>' . $ambiente . '</tpAmb>
    <chNFCom>' . $chave . '</chNFCom>
</consSitNFCom>';

        try {
            $response = $this->executeCurl($url, $xml, 'nfcomConsulta');

            if (empty($response) || trim($response) === '') {
                log_message('error', 'Resposta vazia do SEFAZ na consulta. URL: ' . $url);
                return ['error' => 'Resposta vazia do SEFAZ. Verifique: 1) Conexão com internet, 2) Certificado válido, 3) URL do serviço correta'];
            }

            return $this->parseConsultResponse($response);

        } catch (\Exception $e) {
            log_message('error', 'NFCom Consult Error: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return ['error' => 'Erro ao consultar NFCom: ' . $e->getMessage()];
        }
    }

    private function sendSoapEnvelope($url, $soapEnvelope)
    {
        // Headers conforme padrão SEFAZ para NFCom
        $headers = [
            "Content-type: application/soap+xml;charset=utf-8;action=\"http://www.portalfiscal.inf.br/nfcom/wsdl/NFComRecepcao/nfcomRecepcao\"",
            "Content-length: " . strlen($soapEnvelope),
        ];

        // Converter PFX para PEM para uso no cURL
        $certFile = tempnam(sys_get_temp_dir(), 'cert_');
        $pemFile = tempnam(sys_get_temp_dir(), 'pem_');
        $useP12 = false;

        try {
            // Fix for OpenSSL 3 legacy certificates
            if (file_exists('C:/xampp/php/extras/ssl/openssl.cnf')) {
                putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
                putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');
            }

            // Método 1: Tentar usar openssl_pkcs12_read do PHP
            $certs = [];
            if (openssl_pkcs12_read($this->pfxContent, $certs, $this->password)) {
                // Extrair certificado e chave privada
                $certPem = $certs['cert'] ?? '';
                $keyPem = $certs['pkey'] ?? '';

                if (!empty($certPem) && !empty($keyPem)) {
                    // Combinar certificado e chave em um arquivo PEM
                    $pemContent = $certPem . "\n" . $keyPem;
                    file_put_contents($pemFile, $pemContent);

                    if (file_exists($pemFile) && filesize($pemFile) > 0) {
                        log_message('debug', 'Certificado convertido para PEM via openssl_pkcs12_read');
                    } else {
                        throw new \Exception('Falha ao escrever arquivo PEM');
                    }
                } else {
                    throw new \Exception('Certificado ou chave privada vazios');
                }
            } else {
                throw new \Exception('Falha ao ler PFX com openssl_pkcs12_read');
            }
        } catch (\Exception $e) {
            log_message('warning', 'Falha ao converter certificado via PHP: ' . $e->getMessage() . '. Tentando OpenSSL CLI.');

            // Método 2: Tentar usar OpenSSL via linha de comando
            try {
                file_put_contents($certFile, $this->pfxContent);

                // Verificar se OpenSSL está disponível
                $opensslCheck = @shell_exec('openssl version 2>&1');
                if (empty($opensslCheck) || strpos($opensslCheck, 'OpenSSL') === false) {
                    throw new \Exception('OpenSSL não encontrado no sistema');
                }

                // Converter PFX para PEM
                $opensslCmd = "openssl pkcs12 -in \"{$certFile}\" -out \"{$pemFile}\" -nodes -passin pass:\"{$this->password}\" 2>&1";
                $output = [];
                $returnVar = 0;
                exec($opensslCmd, $output, $returnVar);

                if ($returnVar !== 0 || !file_exists($pemFile) || filesize($pemFile) == 0) {
                    throw new \Exception('Falha na conversão OpenSSL: ' . implode("\n", $output));
                }

                log_message('debug', 'Certificado convertido para PEM via OpenSSL CLI');
            } catch (\Exception $e2) {
                log_message('error', 'Erro ao converter certificado: ' . $e2->getMessage() . '. Tentando usar PFX diretamente.');
                // Último recurso: tentar usar PFX diretamente (alguns sistemas podem suportar)
                $pemFile = $certFile;
                $useP12 = true;
            }
        }

        // Configuração cURL exatamente como no NFECom
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soapEnvelope);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

        // Configuração do certificado - usar o mesmo método que no NFECom
        if ($useP12) {
            curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->password);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, "P12");
        } else {
            curl_setopt($ch, CURLOPT_SSLCERT, $pemFile);
            curl_setopt($ch, CURLOPT_SSLKEY, $pemFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->password);
        }

        // Debug info
        file_put_contents('soap_request_debug.xml', $soapEnvelope);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);

        // Limpar arquivos temporários
        @unlink($certFile);
        if ($pemFile != $certFile && file_exists($pemFile)) {
            @unlink($pemFile);
        }

        // Log detalhado para debug
        log_message('debug', 'cURL Info: HTTP Code=' . $httpCode . ', Error=' . ($error ?: 'Nenhum') . ', Response Length=' . strlen($response ?: ''));

        if ($error) {
            log_message('error', 'Erro cURL: ' . $error . ' | HTTP Code: ' . $httpCode);
            throw new \Exception("Erro cURL: " . $error . " (HTTP Code: " . $httpCode . ")");
        }

        if (empty($response)) {
            log_message('error', 'Resposta vazia do SEFAZ. HTTP Code: ' . $httpCode . ' | URL: ' . $url);
            throw new \Exception("Resposta vazia do SEFAZ. HTTP Code: " . $httpCode . ". Verifique a conexão e o certificado.");
        }

        // Salvar resposta para debug mesmo se houver erro
        file_put_contents('soap_response_debug.xml', $response);

        if ($httpCode != 200) {
            log_message('error', 'HTTP Code diferente de 200: ' . $httpCode . ' | Response: ' . substr($response, 0, 1000));

            // Tentar extrair mensagem de erro do SOAP Fault se houver
            $errorMsg = "Erro HTTP " . $httpCode . " do SEFAZ";
            if (!empty($response)) {
                $dom = new DOMDocument();
                @$dom->loadXML($response);
                $fault = $dom->getElementsByTagName('Fault')->item(0);
                if ($fault) {
                    $faultString = $fault->getElementsByTagName('reason')->item(0);
                    if (!$faultString) {
                        $faultString = $fault->getElementsByTagName('faultstring')->item(0);
                    }
                    if ($faultString) {
                        $errorMsg .= ": " . $faultString->nodeValue;
                    }
                } else {
                    // Tentar extrair qualquer mensagem de erro do XML
                    $errorMsg .= ": " . substr(strip_tags($response), 0, 200);
                }
            }

            throw new \Exception($errorMsg);
        }

        curl_close($ch);
        return $response;
    }

    private function executeCurl($url, $xmlContent, $soapAction)
    {
        // Determinar namespace e service name baseado na URL
        $isConsulta = strpos($url, 'ConsultaProtocolo') !== false;
        $serviceName = $isConsulta ? 'NFComConsulta' : 'NFComRecepcao';
        $namespaceMsg = 'http://www.portalfiscal.inf.br/nfcom/wsdl/' . $serviceName;

        // Prepara conteúdo da mensagem - remover declaração XML
        $cleanXml = preg_replace('/<\?xml[^>]*\?>/', '', $xmlContent);
        $cleanXml = trim($cleanXml);

        // Validar XML antes de enviar
        $domTest = new DOMDocument();
        libxml_use_internal_errors(true);
        $xmlValid = @$domTest->loadXML($cleanXml);
        if (!$xmlValid) {
            $errors = libxml_get_errors();
            $errorMsg = 'XML inválido: ';
            foreach ($errors as $error) {
                $errorMsg .= trim($error->message) . ' ';
            }
            libxml_clear_errors();
            throw new \Exception($errorMsg);
        }
        libxml_use_internal_errors(false);

        // Converter XML para string sem formatação (como NFePHP faz)
        $domTest->formatOutput = false;
        $domTest->preserveWhiteSpace = false;
        $cleanXml = $domTest->saveXML($domTest->documentElement);

        // Envelope SOAP 1.2 - formato exato como NFePHP usa
        // NFePHP usa SOAP 1.2 com estrutura específica
        $soapEnvelope = '<?xml version="1.0" encoding="UTF-8"?>';
        $soapEnvelope .= '<soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">';
        $soapEnvelope .= '<soap12:Body>';
        $soapEnvelope .= '<nfcomDadosMsg xmlns="' . $namespaceMsg . '">';
        // XML direto sem quebras de linha extras (como NFePHP faz)
        $soapEnvelope .= $cleanXml;
        $soapEnvelope .= '</nfcomDadosMsg>';
        $soapEnvelope .= '</soap12:Body>';
        $soapEnvelope .= '</soap12:Envelope>';

        // Headers exatamente como NFePHP usa
        $soapActionValue = $namespaceMsg . '/' . $soapAction;

        $headers = [
            'Content-Type: application/soap+xml; charset=utf-8; action="' . $soapActionValue . '"',
            'SOAPAction: "' . $soapActionValue . '"',
        ];

        // Converter PFX para PEM para uso no cURL
        $certFile = tempnam(sys_get_temp_dir(), 'cert_');
        $pemFile = tempnam(sys_get_temp_dir(), 'pem_');
        $useP12 = false;

        try {
            // Fix for OpenSSL 3 legacy certificates
            if (file_exists('C:/xampp/php/extras/ssl/openssl.cnf')) {
                putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
                putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');
            }

            // Método 1: Tentar usar openssl_pkcs12_read do PHP
            $certs = [];
            if (openssl_pkcs12_read($this->pfxContent, $certs, $this->password)) {
                // Extrair certificado e chave privada
                $certPem = $certs['cert'] ?? '';
                $keyPem = $certs['pkey'] ?? '';

                if (!empty($certPem) && !empty($keyPem)) {
                    // Combinar certificado e chave em um arquivo PEM
                    $pemContent = $certPem . "\n" . $keyPem;
                    file_put_contents($pemFile, $pemContent);

                    if (file_exists($pemFile) && filesize($pemFile) > 0) {
                        log_message('debug', 'Certificado convertido para PEM via openssl_pkcs12_read');
                    } else {
                        throw new \Exception('Falha ao escrever arquivo PEM');
                    }
                } else {
                    throw new \Exception('Certificado ou chave privada vazios');
                }
            } else {
                throw new \Exception('Falha ao ler PFX com openssl_pkcs12_read');
            }
        } catch (\Exception $e) {
            log_message('warning', 'Falha ao converter certificado via PHP: ' . $e->getMessage() . '. Tentando OpenSSL CLI.');

            // Método 2: Tentar usar OpenSSL via linha de comando
            try {
                file_put_contents($certFile, $this->pfxContent);

                // Verificar se OpenSSL está disponível
                $opensslCheck = @shell_exec('openssl version 2>&1');
                if (empty($opensslCheck) || strpos($opensslCheck, 'OpenSSL') === false) {
                    throw new \Exception('OpenSSL não encontrado no sistema');
                }

                // Converter PFX para PEM
                $opensslCmd = "openssl pkcs12 -in \"{$certFile}\" -out \"{$pemFile}\" -nodes -passin pass:\"{$this->password}\" 2>&1";
                $output = [];
                $returnVar = 0;
                exec($opensslCmd, $output, $returnVar);

                if ($returnVar !== 0 || !file_exists($pemFile) || filesize($pemFile) == 0) {
                    throw new \Exception('Falha na conversão OpenSSL: ' . implode("\n", $output));
                }

                log_message('debug', 'Certificado convertido para PEM via OpenSSL CLI');
            } catch (\Exception $e2) {
                log_message('error', 'Erro ao converter certificado: ' . $e2->getMessage() . '. Tentando usar PFX diretamente.');
                // Último recurso: tentar usar PFX diretamente (alguns sistemas podem suportar)
                $pemFile = $certFile;
                $useP12 = true;
            }
        }

        // Configuração cURL exatamente como NFePHP faz
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soapEnvelope);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NFePHP NFCom');

        // Configuração do certificado - usar o mesmo método que NFePHP
        if ($useP12) {
            // Tentar usar PFX diretamente (alguns sistemas suportam)
            curl_setopt($ch, CURLOPT_SSLCERT, $certFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->password);
            curl_setopt($ch, CURLOPT_SSLCERTTYPE, "P12");
        } else {
            // Usar PEM (formato padrão)
            curl_setopt($ch, CURLOPT_SSLCERT, $pemFile);
            curl_setopt($ch, CURLOPT_SSLKEY, $pemFile);
            curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $this->password);
        }

        // Debug info
        file_put_contents('soap_request_debug.xml', implode("\n", $headers) . "\n\n" . $soapEnvelope);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);

        curl_close($ch);

        // Limpar arquivos temporários
        @unlink($certFile);
        if ($pemFile != $certFile && file_exists($pemFile)) {
            @unlink($pemFile);
        }

        // Log detalhado para debug
        log_message('debug', 'cURL Info: HTTP Code=' . $httpCode . ', Error=' . ($error ?: 'Nenhum') . ', Response Length=' . strlen($response ?: ''));

        if ($error) {
            log_message('error', 'Erro cURL: ' . $error . ' | HTTP Code: ' . $httpCode);
            throw new \Exception("Erro cURL: " . $error . " (HTTP Code: " . $httpCode . ")");
        }

        if (empty($response)) {
            log_message('error', 'Resposta vazia do SEFAZ. HTTP Code: ' . $httpCode . ' | URL: ' . $url);
            throw new \Exception("Resposta vazia do SEFAZ. HTTP Code: " . $httpCode . ". Verifique a conexão e o certificado.");
        }

        // Salvar resposta para debug mesmo se houver erro
        file_put_contents('soap_response_debug.xml', $response);

        if ($httpCode != 200) {
            log_message('error', 'HTTP Code diferente de 200: ' . $httpCode . ' | Response: ' . substr($response, 0, 1000));

            // Tentar extrair mensagem de erro do SOAP Fault se houver
            $errorMsg = "Erro HTTP " . $httpCode . " do SEFAZ";
            if (!empty($response)) {
                $dom = new DOMDocument();
                @$dom->loadXML($response);
                $fault = $dom->getElementsByTagName('Fault')->item(0);
                if ($fault) {
                    $faultString = $fault->getElementsByTagName('reason')->item(0);
                    if (!$faultString) {
                        $faultString = $fault->getElementsByTagName('faultstring')->item(0);
                    }
                    if ($faultString) {
                        $errorMsg .= ": " . $faultString->nodeValue;
                    }
                } else {
                    // Tentar extrair qualquer mensagem de erro do XML
                    $errorMsg .= ": " . substr(strip_tags($response), 0, 200);
                }
            }

            throw new \Exception($errorMsg);
        }

        file_put_contents('soap_response_debug.xml', $response);

        return $response;
    }

    private function getEndpoint($ambiente, $service)
    {
        // For NFCom, SVRS uses NFComRecepcao for authorization
        if ($ambiente == 1) { // Produção
            return "https://nfcom.svrs.rs.gov.br/WS/NFCom{$service}/NFCom{$service}.asmx";
        } else { // Homologação
            return "https://nfcom-homologacao.svrs.rs.gov.br/WS/NFCom{$service}/NFCom{$service}.asmx";
        }
    }

    private function parseResponse($response)
    {
        // Remove namespaces for easier parsing
        $xml = preg_replace('/(<\/?)(\\w+):([^>]*>)/', '$1$3', $response);

        $dom = new DOMDocument();
        @$dom->loadXML($xml);

        // SOAP Fault
        $fault = $dom->getElementsByTagName('Fault')->item(0);
        if ($fault) {
            $faultString = $fault->getElementsByTagName('reason')->item(0)->nodeValue ??
                $fault->getElementsByTagName('faultstring')->item(0)->nodeValue ??
                'Erro SOAP desconhecido';
            return ['error' => 'Erro SOAP: ' . $faultString, 'raw' => $response];
        }

        $ret = $dom->getElementsByTagName('retNFCom')->item(0);
        if (!$ret) {
            return ['error' => 'Resposta do SEFAZ inválida (retNFCom não encontrado).', 'raw' => substr($response, 0, 1000)];
        }

        $cStat = $ret->getElementsByTagName('cStat')->item(0)->nodeValue ?? '999';
        $xMotivo = $ret->getElementsByTagName('xMotivo')->item(0)->nodeValue ?? 'Erro desconhecido';

        $protocolo = null;
        if ($cStat == '100' || $cStat == '101') {
            $prot = $ret->getElementsByTagName('infProt')->item(0);
            if ($prot) {
                $protocolo = [
                    'nProt' => $prot->getElementsByTagName('nProt')->item(0)->nodeValue ?? '',
                    'dhRecbto' => $prot->getElementsByTagName('dhRecbto')->item(0)->nodeValue ?? '',
                    'chNFCom' => $prot->getElementsByTagName('chNFCom')->item(0)->nodeValue ?? ''
                ];
            }
        }

        return [
            'cStat' => $cStat,
            'xMotivo' => $xMotivo,
            'protocolo' => $protocolo,
            'xml' => $response
        ];
    }

    private function parseConsultResponse($response)
    {
        $xml = preg_replace('/(<\/?)(\\w+):([^>]*>)/', '$1$3', $response);

        $dom = new DOMDocument();
        @$dom->loadXML($xml);

        $ret = $dom->getElementsByTagName('retConsSitNFCom')->item(0);
        if (!$ret) {
            return ['error' => 'Resposta de consulta inválida'];
        }

        $cStat = $ret->getElementsByTagName('cStat')->item(0)->nodeValue ?? '999';
        $xMotivo = $ret->getElementsByTagName('xMotivo')->item(0)->nodeValue ?? 'Erro desconhecido';

        $protocolo = null;
        if ($cStat == '100') {
            $prot = $ret->getElementsByTagName('protNFCom')->item(0);
            if ($prot) {
                $infProt = $prot->getElementsByTagName('infProt')->item(0);
                if ($infProt) {
                    $protocolo = [
                        'nProt' => $infProt->getElementsByTagName('nProt')->item(0)->nodeValue ?? '',
                        'dhRecbto' => $infProt->getElementsByTagName('dhRecbto')->item(0)->nodeValue ?? '',
                        'chNFCom' => $infProt->getElementsByTagName('chNFCom')->item(0)->nodeValue ?? ''
                    ];
                }
            }
        }

        return [
            'cStat' => $cStat,
            'xMotivo' => $xMotivo,
            'protocolo' => $protocolo,
            'xml' => $response
        ];
    }
}
