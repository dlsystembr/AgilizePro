<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Certificados extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Certificados_model');
        $this->load->model('Empresas_model');
        $this->data['menuCertificados'] = 'certificados';
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar certificados.');
            redirect(base_url());
        }

        // Busca primeira empresa (assumindo uma empresa por instalação)
        $empresa = $this->db->limit(1)->get('empresas')->row();

        if (!$empresa) {
            $this->session->set_flashdata('error', 'Nenhuma empresa cadastrada.');
            redirect(base_url());
        }

        $this->data['empresa'] = $empresa;
        $this->data['certificados'] = $this->Certificados_model->get($empresa->emp_id, false);
        $this->data['custom_error'] = '';

        $this->data['view'] = 'certificados/index';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCertificado')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar certificados.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('cer_senha', 'Senha do Certificado', 'required|trim');
            $this->form_validation->set_rules('cer_tipo', 'Tipo', 'required');

            if ($this->form_validation->run() == false) {
                $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
            } else {
                // Busca empresa
                $empresa = $this->db->limit(1)->get('empresas')->row();

                if (!$empresa) {
                    $this->data['custom_error'] = '<div class="alert alert-danger">Nenhuma empresa cadastrada.</div>';
                } else {
                    // Processa upload do certificado
                    if (!empty($_FILES['cer_arquivo']['name'])) {
                        $arquivo = file_get_contents($_FILES['cer_arquivo']['tmp_name']);

                        if ($arquivo === false) {
                            $this->data['custom_error'] = '<div class="alert alert-danger">Erro ao ler arquivo do certificado.</div>';
                        } else {
                            // Tenta extrair informações do certificado
                            $infoCertificado = $this->extrairInfoCertificado($arquivo, $this->input->post('cer_senha'));

                            // Se houve conversão bem sucedida, usa o arquivo convertido
                            if (isset($infoCertificado['arquivo_convertido'])) {
                                $arquivo = $infoCertificado['arquivo_convertido'];
                                log_info('Certificado convertido automaticamente para formato compatível.');
                            }


                            $cnpj = (!empty($infoCertificado['cnpj'])) ? $infoCertificado['cnpj'] : null;
                            $validade = (!empty($infoCertificado['validade'])) ? $infoCertificado['validade'] : null;

                            if (empty($cnpj) || empty($validade)) {
                                log_message('error', "Falha no upload de certificado: Não foi possível extrair CNPJ ou Validade.");
                                $this->data['custom_error'] = '<div class="alert alert-danger">Não foi possível ler o certificado automaticamente. Verifique se a senha está correta e se o arquivo é válido. Tente novamente.</div>';
                            } else {
                                $data = [
                                    'emp_id' => $empresa->emp_id,
                                    'cer_arquivo' => $arquivo,
                                    'cer_senha' => $this->input->post('cer_senha'),
                                    'cer_tipo' => $this->input->post('cer_tipo'),
                                    'cer_cnpj' => $cnpj,
                                    'cer_validade_fim' => $validade,
                                    'cer_ativo' => 1
                                ];

                                if ($this->Certificados_model->add($data)) {
                                    $this->session->set_flashdata('success', 'Certificado adicionado com sucesso!');
                                    log_info('Adicionou um certificado digital manualmente.');
                                    redirect(site_url('certificados'));
                                } else {
                                    $this->data['custom_error'] = '<div class="alert alert-danger">Erro ao salvar certificado no banco de dados.</div>';
                                }
                            }
                        }
                    } else {
                        $this->data['custom_error'] = '<div class="alert alert-danger">Selecione o arquivo do certificado (.pfx).</div>';
                    }
                }
            }
        }

        $this->data['view'] = 'certificados/adicionar';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dCertificado')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir certificados.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir certificado.');
            redirect(site_url('certificados'));
        }

        // Verifica se certificado está em uso
        $this->db->where('cer_id', $id);
        $emUso = $this->db->count_all_results('configuracoes_fiscais');

        if ($emUso > 0) {
            $this->session->set_flashdata('error', 'Não é possível excluir. Certificado está em uso em configurações fiscais.');
            redirect(site_url('certificados'));
        }

        $this->Certificados_model->delete($id);
        log_info('Removeu um certificado digital. ID ' . $id);

        $this->session->set_flashdata('success', 'Certificado excluído com sucesso!');
        redirect(site_url('certificados'));
    }

    public function ativar($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aCertificado')) {
            $this->session->set_flashdata('error', 'Você não tem permissão.');
            redirect(base_url());
        }

        $certificado = $this->Certificados_model->getById($id);

        if (!$certificado) {
            $this->session->set_flashdata('error', 'Certificado não encontrado.');
            redirect(site_url('certificados'));
        }

        // Desativa outros certificados da mesma empresa
        $this->Certificados_model->desativarOutros($certificado->emp_id, $id);

        // Ativa este certificado
        $this->Certificados_model->edit($id, ['cer_ativo' => 1]);

        $this->session->set_flashdata('success', 'Certificado ativado com sucesso!');
        redirect(site_url('certificados'));
    }

    /**
     * Extrai informações do certificado .pfx
     */
    private function extrairInfoCertificado($arquivo, $senha)
    {
        $info = [];

        // Força caminhos do OpenSSL para carregar o Legacy Provider no XAMPP (Windows)
        putenv('OPENSSL_CONF=C:/xampp/php/extras/ssl/openssl.cnf');
        putenv('OPENSSL_MODULES=C:/xampp/php/extras/ssl');

        // Limpa erros anteriores do OpenSSL
        while (openssl_error_string())
            ;

        $certs = [];
        if (openssl_pkcs12_read($arquivo, $certs, $senha)) {
            $certData = openssl_x509_parse($certs['cert']);

            if ($certData) {
                // Extrai CNPJ do subject
                if (isset($certData['subject']['CN'])) {
                    $cn = $certData['subject']['CN'];
                    if (preg_match('/(\d{14})/', $cn, $matches)) {
                        $info['cnpj'] = $matches[1];
                    }
                }

                // Extrai validade
                if (isset($certData['validTo_time_t'])) {
                    $info['validade'] = date('Y-m-d', $certData['validTo_time_t']);
                }
            }
        } else {
            // Tenta converter usando binário do OpenSSL (Fallback para certificados muito antigos)
            $tmpFile = sys_get_temp_dir() . '/' . uniqid('cert_') . '.pfx';
            $tmpFileNew = sys_get_temp_dir() . '/' . uniqid('cert_new_') . '.pfx';

            file_put_contents($tmpFile, $arquivo);

            // Comando para converter/reparar o PFX usando o binário do sistema
            // Requer que o OpenSSL esteja no PATH ou caminho absoluto
            $opensslBin = 'C:\xampp\apache\bin\openssl.exe';
            if (!file_exists($opensslBin)) {
                $opensslBin = 'openssl'; // Tenta do PATH
            }

            // Exporta para novo PFX forçando algoritmos compatíveis com OpenSSL 3 default
            // -legacy para ler o antigo, e exporta normal (que será compatível)
            $cmd = "\"$opensslBin\" pkcs12 -in \"$tmpFile\" -export -out \"$tmpFileNew\" -passin pass:\"$senha\" -passout pass:\"$senha\" -legacy -nokeys";
            // Nota: -nokeys e export pode não funcionar bem juntos se queremos o PFX completo
            // Melhor: Apenas ler info? O usuário pediu para "CONVERTER E DEPOIS SUBIR"

            // Passar senha via variável de ambiente é mais seguro e evita problemas de I/O e caracteres
            putenv("CERTPASS=$senha");

            // Tenta RE-GERAR o PFX completo forçando algoritmos MODERNOS (AES)
            // Input: usa provider legacy. Output: usa algoritmos modernos.
            // O uso de 'env:CERTPASS' evita que a senha apareça na linha de comando (ps/logs) e resolve problemas de escaping
            $cmd = "\"$opensslBin\" pkcs12 -in \"$tmpFile\" -export -out \"$tmpFileNew\" -passin env:CERTPASS -passout env:CERTPASS -provider legacy -provider default -keypbe AES-256-CBC -certpbe AES-256-CBC -macalg SHA256";

            exec($cmd . " 2>&1", $output, $returnVar);

            // Tenta validar se criou. Se falhar, tenta sem os flags de criptografia (default do OpenSSL 3)
            if ($returnVar !== 0 || !file_exists($tmpFileNew)) {
                $cmd = "\"$opensslBin\" pkcs12 -in \"$tmpFile\" -export -out \"$tmpFileNew\" -passin env:CERTPASS -passout env:CERTPASS -provider legacy -provider default";
                exec($cmd . " 2>&1", $output, $returnVar);
            }

            // Limpa a variável de ambiente por segurança
            putenv("CERTPASS");

            if ($returnVar === 0 && file_exists($tmpFileNew)) {
                $novoArquivo = file_get_contents($tmpFileNew);

                // Tenta ler o novo arquivo
                // Resetando o error string
                while (openssl_error_string())
                    ;

                if (openssl_pkcs12_read($novoArquivo, $certs, $senha)) {
                    // SUCESSO!
                    $info['arquivo_convertido'] = $novoArquivo;

                    $certData = openssl_x509_parse($certs['cert']);
                    if ($certData) {
                        if (isset($certData['subject']['CN'])) {
                            $cn = $certData['subject']['CN'];
                            if (preg_match('/(\d{14})/', $cn, $matches)) {
                                $info['cnpj'] = $matches[1];
                            }
                        }
                        if (isset($certData['validTo_time_t'])) {
                            $info['validade'] = date('Y-m-d', $certData['validTo_time_t']);
                        }
                    }
                } else {
                    $sslErr = '';
                    while ($msg = openssl_error_string())
                        $sslErr .= $msg;
                    $debugParams = "OpenSSL Read Error: $sslErr";
                    file_put_contents('debug_ssl.txt', $debugParams);
                    log_message('error', 'Falha ao ler certificado convertido PHP: ' . $sslErr);
                }
            } else {
                $debugParams = "Falha Exec: Ret: $returnVar. Output: " . print_r($output, true);
                file_put_contents('debug_ssl.txt', $debugParams);

                $errorMsg = 'Falha no fallback OpenSSL (Retorno: ' . $returnVar . '): ' . implode(" ", $output);
                log_message('error', $errorMsg);
            }

            // Limpeza
            @unlink($tmpFile);
            @unlink($tmpFileNew);

            if (empty($info['cnpj'])) {
                $errorMsg = '';
                while ($msg = openssl_error_string()) {
                    $errorMsg .= $msg . ' | ';
                }
                log_message('error', 'Erro OpenSSL ao ler certificado: ' . $errorMsg);
            }
        }

        return $info;
    }
}
