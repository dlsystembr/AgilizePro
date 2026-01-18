<?php

defined('BASEPATH') or exit('No direct script access allowed');

class CertificateHandler
{
    private $certificate;
    private $password;
    private $certInfo;

    public function __construct($certificatePath = null, $password = null)
    {
        log_message('debug', 'CertificateHandler: Iniciando construtor');
        if ($certificatePath && $password) {
            $this->loadCertificate($certificatePath, $password);
        }
    }

    public function loadCertificate($certificatePath, $password)
    {
        log_message('debug', 'CertificateHandler: Tentando carregar certificado de ' . $certificatePath);
        
        if (!file_exists($certificatePath)) {
            log_message('error', 'CertificateHandler: Arquivo do certificado não encontrado em ' . $certificatePath);
            throw new Exception('Arquivo do certificado não encontrado');
        }

        $certContent = file_get_contents($certificatePath);
        $this->password = $password;

        if (!openssl_pkcs12_read($certContent, $this->certificate, $password)) {
            log_message('error', 'CertificateHandler: Falha ao ler o certificado. Verifique a senha.');
            throw new Exception('Falha ao ler o certificado. Verifique a senha.');
        }

        log_message('debug', 'CertificateHandler: Certificado carregado com sucesso');
        $this->extractCertificateInfo();
    }

    private function extractCertificateInfo()
    {
        log_message('debug', 'CertificateHandler: Extraindo informações do certificado');
        $cert = openssl_x509_parse($this->certificate['cert']);
        
        $this->certInfo = [
            'commonName' => $cert['subject']['CN'] ?? '',
            'issuerName' => $cert['issuer']['CN'] ?? '',
            'ownerName' => $cert['subject']['O'] ?? '',
            'cnpj' => $this->extractCNPJ($cert),
            'validFrom' => date('Y-m-d H:i:s', $cert['validFrom_time_t']),
            'validTo' => date('Y-m-d H:i:s', $cert['validTo_time_t'])
        ];
        
        log_message('debug', 'CertificateHandler: Informações extraídas: ' . json_encode($this->certInfo));
    }

    private function extractCNPJ($cert)
    {
        if (isset($cert['subject']['OU'])) {
            foreach ($cert['subject']['OU'] as $ou) {
                if (preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $ou)) {
                    return $ou;
                }
            }
        }
        return '';
    }

    public function getCertificate()
    {
        return $this->certificate;
    }

    public function getCertificateInfo()
    {
        return $this->certInfo;
    }

    public function isValid()
    {
        $isValid = !empty($this->certificate) && !empty($this->certInfo);
        log_message('debug', 'CertificateHandler: Validação do certificado: ' . ($isValid ? 'válido' : 'inválido'));
        return $isValid;
    }

    public function getCommonName()
    {
        return $this->certInfo['commonName'] ?? '';
    }

    public function getIssuerName()
    {
        return $this->certInfo['issuerName'] ?? '';
    }

    public function getOwnerName()
    {
        return $this->certInfo['ownerName'] ?? '';
    }

    public function getCNPJ()
    {
        return $this->certInfo['cnpj'] ?? '';
    }

    public function getValidFrom()
    {
        return $this->certInfo['validFrom'] ?? '';
    }

    public function getValidTo()
    {
        return $this->certInfo['validTo'] ?? '';
    }
} 