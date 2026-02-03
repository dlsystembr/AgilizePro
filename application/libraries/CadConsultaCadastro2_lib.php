<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Biblioteca para consulta ao Cadastro de Contribuinte (IE/CNPJ) via Web Service CadConsultaCadastro2.
 * Usado para testar se a IE do destinatário está cadastrada na UF (evitar rejeição 428 na NFCom).
 */
class CadConsultaCadastro2_lib
{
    private $ci;
    private $urls;
    private $cuf;
    /** @var string|null Caminho do arquivo PEM com certificado (e chave) para cURL */
    private $certPemFile = null;
    /** @var string|null Caminho do arquivo PEM com apenas a chave privada (alternativo) */
    private $keyPemFile = null;
    private $certPassword = null;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->config->load('consulta_cadastro_uf');
        $this->urls = $this->ci->config->item('consulta_cadastro_uf') ?: [];
        $this->cuf  = $this->ci->config->item('consulta_cadastro_cuf') ?: [];
    }

    /**
     * Define o certificado digital (PFX) para autenticação na SEFAZ.
     * Obrigatório para a maioria das UFs (evita erro "certificate unknown").
     * @param string $certPathOrContent Caminho do arquivo .pfx ou conteúdo binário (ex.: blob do banco)
     * @param string $password Senha do certificado
     */
    public function setCertificate($certPathOrContent, $password)
    {
        $pfxContent = $certPathOrContent;
        if (is_resource($certPathOrContent)) {
            $pfxContent = stream_get_contents($certPathOrContent);
        } elseif (is_string($certPathOrContent)) {
            $isPath = strlen($certPathOrContent) < 2048 && strpos($certPathOrContent, "\0") === false && @file_exists($certPathOrContent);
            if ($isPath) {
                $pfxContent = @file_get_contents($certPathOrContent);
            }
        }

        if (!is_string($pfxContent) || $pfxContent === '' || !is_string($password)) {
            return;
        }

        if (file_exists('C:/xampp/php/extras/ssl/openssl.cnf')) {
            putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
            putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');
        }

        $certs = [];
        if (!openssl_pkcs12_read($pfxContent, $certs, $password)) {
            log_message('error', 'CadConsultaCadastro2: Falha ao ler certificado PFX. Verifique o arquivo e a senha.');
            return;
        }

        $certPem = $certs['cert'] ?? '';
        $keyPem  = $certs['pkey'] ?? '';
        if (empty($certPem) || empty($keyPem)) {
            log_message('error', 'CadConsultaCadastro2: Certificado ou chave vazios após leitura do PFX.');
            return;
        }

        $tmpDir = sys_get_temp_dir();
        $this->certPemFile = tempnam($tmpDir, 'cad_cert_');
        $this->keyPemFile  = tempnam($tmpDir, 'cad_key_');
        $this->certPassword = $password;
        file_put_contents($this->certPemFile, $certPem);
        file_put_contents($this->keyPemFile, $keyPem);
    }

    /**
     * Consulta cadastro por CNPJ e/ou IE na UF.
     * @param string $uf Sigla da UF (ex: GO)
     * @param string|null $cnpj Apenas dígitos (14)
     * @param string|null $ie Apenas dígitos (opcional)
     * @return array ['success' => bool, 'data' => array, 'raw' => string, 'error' => string]
     */
    public function consultar($uf, $cnpj = null, $ie = null)
    {
        $uf = strtoupper(trim($uf));
        $cnpj = $cnpj ? preg_replace('/\D/', '', $cnpj) : '';
        $ie   = $ie   ? preg_replace('/\D/', '', $ie)   : '';

        if (strlen($cnpj) !== 14 && $ie === '') {
            return ['success' => false, 'error' => 'Informe CNPJ (14 dígitos) e/ou IE.'];
        }

        if (empty($this->urls[$uf])) {
            return ['success' => false, 'error' => 'UF não configurada para consulta: ' . $uf];
        }

        $cUF = isset($this->cuf[$uf]) ? $this->cuf[$uf] : '';
        $url = $this->urls[$uf];

        $infCons = '<infCons xmlns="http://www.portalfiscal.inf.br/nfe">';
        $infCons .= '<xServ>CONSULTAR CADASTRO</xServ>';
        $infCons .= '<UF>' . $uf . '</UF>';
        if ($cnpj !== '') {
            $infCons .= '<CNPJ>' . $cnpj . '</CNPJ>';
        }
        if ($ie !== '') {
            $infCons .= '<IE>' . $ie . '</IE>';
        }
        $infCons .= '</infCons>';

        // consCad sem declaração XML (fica dentro do Body; declaração no meio do documento causa erro 500 na SEFAZ)
        $consCad = '<consCad xmlns="http://www.portalfiscal.inf.br/nfe" versao="2.00">';
        $consCad .= $infCons;
        $consCad .= '</consCad>';

        $soapBody = '<?xml version="1.0" encoding="UTF-8"?>';
        $soapBody .= '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">';
        $soapBody .= '<soap:Header>';
        $soapBody .= '<nfeCabecMsg xmlns="http://www.portalfiscal.inf.br/nfe/wsdl/CadConsultaCadastro2">';
        $soapBody .= '<versaoDados>2.00</versaoDados>';
        $soapBody .= '<cUF>' . $cUF . '</cUF>';
        $soapBody .= '</nfeCabecMsg>';
        $soapBody .= '</soap:Header>';
        $soapBody .= '<soap:Body>';
        $soapBody .= '<nfeDadosMsg xmlns="http://www.portalfiscal.inf.br/nfe/wsdl/CadConsultaCadastro2">';
        $soapBody .= $consCad;
        $soapBody .= '</nfeDadosMsg>';
        $soapBody .= '</soap:Body>';
        $soapBody .= '</soap:Envelope>';

        if (!$this->certPemFile || !file_exists($this->certPemFile)) {
            return ['success' => false, 'error' => 'Certificado digital não configurado. Configure o certificado da NFCom em Configurações Fiscais e tente novamente.', 'raw' => ''];
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $soapBody,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://www.portalfiscal.inf.br/nfe/wsdl/CadConsultaCadastro2/consultaCadastro2"',
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        curl_setopt($ch, CURLOPT_SSLCERT, $this->certPemFile);
        curl_setopt($ch, CURLOPT_SSLKEY, $this->keyPemFile && file_exists($this->keyPemFile) ? $this->keyPemFile : $this->certPemFile);
        curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err      = curl_error($ch);
        curl_close($ch);

        if ($this->certPemFile && file_exists($this->certPemFile)) {
            @unlink($this->certPemFile);
            $this->certPemFile = null;
        }
        if ($this->keyPemFile && file_exists($this->keyPemFile)) {
            @unlink($this->keyPemFile);
            $this->keyPemFile = null;
        }

        if ($err) {
            return ['success' => false, 'error' => 'cURL: ' . $err . (strpos($err, 'certificate') !== false ? ' Use o certificado digital da NFCom (Configurações Fiscais).' : ''), 'raw' => '', 'server_message' => ''];
        }
        if ($httpCode !== 200) {
            $serverMsg = $this->extractFaultFromResponse($response);
            $msg = $httpCode === 500
                ? 'A SEFAZ retornou erro 500 (servidor). Pode ser instabilidade do webservice ou rejeição. Tente mais tarde ou confira o certificado e os dados.'
                : 'A SEFAZ retornou HTTP ' . $httpCode . '.';
            if ($serverMsg !== '') {
                $msg .= ' Detalhe: ' . $serverMsg;
            }
            return ['success' => false, 'error' => $msg, 'raw' => $response, 'server_message' => $serverMsg];
        }

        $result = $this->parseResponse($response);
        $result['raw'] = $response;
        return $result;
    }

    /**
     * Extrai mensagem de falha SOAP/XML da resposta (faultstring, detail, etc.).
     * @param string $response Corpo da resposta HTTP
     * @return string Mensagem extraída ou vazio
     */
    private function extractFaultFromResponse($response)
    {
        if (!is_string($response) || $response === '') {
            return '';
        }
        $out = [];
        if (preg_match('/<faultstring[^>]*>(.*?)<\/faultstring>/si', $response, $m)) {
            $out[] = trim(strip_tags(html_entity_decode($m[1])));
        }
        if (preg_match('/<faultcode[^>]*>(.*?)<\/faultcode>/si', $response, $m)) {
            $out[] = 'Código: ' . trim(strip_tags($m[1]));
        }
        if (preg_match('/<message[^>]*>(.*?)<\/message>/si', $response, $m)) {
            $out[] = trim(strip_tags(html_entity_decode($m[1])));
        }
        if (preg_match('/<xMotivo[^>]*>(.*?)<\/xMotivo>/si', $response, $m)) {
            $out[] = trim(strip_tags(html_entity_decode($m[1])));
        }
        return implode(' — ', $out);
    }

    private function parseResponse($xml)
    {
        $out = ['success' => false, 'data' => [], 'error' => '', 'contribuintes' => []];

        if (preg_match('/<consultaCadastro2Result[^>]*>(.*)<\/consultaCadastro2Result>/s', $xml, $m)) {
            $inner = trim(html_entity_decode($m[1]));
            $inner = preg_replace('/^<!\[CDATA\[|\]\]>$/', '', $inner);
        } else {
            if (preg_match('/<faultstring[^>]*>(.*?)<\/faultstring>/s', $xml, $f)) {
                $out['error'] = trim(strip_tags($f[1]));
            } else {
                $out['error'] = 'Resposta sem consultaCadastro2Result.';
            }
            return $out;
        }

        $retConsCad = @simplexml_load_string($inner);
        if ($retConsCad === false) {
            $out['error'] = 'XML de retorno inválido.';
            $out['data']['xml_retorno'] = $inner;
            return $out;
        }

        $retConsCad->registerXPathNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');
        $infCons = $retConsCad->xpath('//nfe:infCons');
        if (empty($infCons)) {
            $infCons = $retConsCad->xpath('//infCons');
        }

        if (!empty($infCons)) {
            $ic = $infCons[0];
            $out['data']['cStat']    = (string) ($ic->cStat ?? '');
            $out['data']['xMotivo']  = (string) ($ic->xMotivo ?? '');
            $out['data']['UF']       = (string) ($ic->UF ?? '');
            $out['data']['dhCons']   = (string) ($ic->dhCons ?? '');
            $out['success']          = ((string) ($ic->cStat ?? '')) === '111';
        }

        $contribuintes = $retConsCad->xpath('//nfe:infCad') ?: $retConsCad->xpath('//infCad');
        foreach ($contribuintes as $cad) {
            $out['contribuintes'][] = [
                'IE'             => (string) ($cad->IE ?? ''),
                'CNPJ'           => (string) ($cad->CNPJ ?? ''),
                'xNome'          => (string) ($cad->xNome ?? ''),
                'UF'             => (string) ($cad->UF ?? ''),
                'cSit'           => (string) ($cad->cSit ?? ''),
                'indCredNFe'     => (string) ($cad->indCredNFe ?? ''),
                'indCredNFCe'    => (string) ($cad->indCredNFCe ?? ''),
            ];
        }

        if (!$out['success'] && empty($out['error']) && !empty($out['data']['xMotivo'])) {
            $out['error'] = $out['data']['xMotivo'];
        }

        return $out;
    }

    /** Retorna lista de UFs configuradas para o dropdown */
    public function getUfsDisponiveis()
    {
        return array_keys($this->urls ?: []);
    }
}
