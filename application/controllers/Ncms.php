<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ncms extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ((!session_id()) || (!$this->session->userdata('logado'))) {
            redirect('login');
        }
        $this->load->model('ncms_model');
        $this->load->library('form_validation');
        $this->data['menuConfiguracoes'] = 'Configurações';
        $this->data['menuNcms'] = 'NCMs';
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNcm')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NCMs.');
            redirect(base_url());
        }

        $this->data['view'] = 'ncms/ncms';
        
        // Configuração da paginação
        $this->load->library('pagination');
        
        // Parâmetros de pesquisa
        $search = $this->input->get('pesquisa');
        $tipo = $this->input->get('tipo');
        $per_page = 20; // Número fixo de registros por página
        $page = $this->input->get('page') ? $this->input->get('page') : 1;
        $start = ($page - 1) * $per_page;

        // Configuração da paginação
        $this->data['configuration']['base_url'] = base_url('index.php/ncms/index');
        $this->data['configuration']['total_rows'] = $this->ncms_model->count($search, $tipo);
        $this->data['configuration']['per_page'] = $per_page;
        $this->data['configuration']['use_page_numbers'] = TRUE;
        $this->data['configuration']['page_query_string'] = TRUE;
        $this->data['configuration']['query_string_segment'] = 'page';
        $this->data['configuration']['reuse_query_string'] = TRUE;
        $this->data['configuration']['num_links'] = 2;

        $this->pagination->initialize($this->data['configuration']);

        // Busca os NCMs com paginação
        $this->data['ncms'] = $this->ncms_model->get($search, $per_page, $start, $tipo);
        $this->data['pagination'] = $this->pagination->create_links();
        $this->data['search'] = $search;
        $this->data['tipo'] = $tipo;
        $this->data['total'] = $this->data['configuration']['total_rows'];
        $this->data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        // Busca informações de tributação para cada NCM
        foreach ($this->data['ncms'] as $ncm) {
            // Verifica tributação federal
            $tributacao_federal = $this->ncms_model->verificarDadosInseridos($ncm->NCM_ID);
            $ncm->tributacao_federal = !empty($tributacao_federal) && 
                                     ($tributacao_federal->tbf_cst_ipi_entrada != '' || 
                                      $tributacao_federal->tbf_cst_pis_cofins_entrada != '' || 
                                      $tributacao_federal->tbf_cst_ipi_saida != '' || 
                                      $tributacao_federal->tbf_cst_pis_cofins_saida != '');
            
            // Verifica tributação estadual
            $tributacao_estadual = $this->ncms_model->getTributacaoEstadual($ncm->NCM_ID);
            $ncm->tributacao_estadual = !empty($tributacao_estadual);
        }

        return $this->layout();
    }

    public function buscar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNcm')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NCMs.');
            redirect(base_url());
        }

        $termo = $this->input->get('termo');
        $pagina = $this->input->get('pagina') ? $this->input->get('pagina') : 1;
        $limite = $this->input->get('limite') ? $this->input->get('limite') : 25;
        $start = ($pagina - 1) * $limite;

        // Se for uma requisição AJAX, retorna JSON
        if ($this->input->is_ajax_request()) {
            if (!$this->input->get('termo')) {
                // Carregar primeiros NCMs sem termo de busca
                log_message('debug', 'Buscando NCMs sem termo - limite: ' . $limite . ', start: ' . $start);
                $ncms = $this->ncms_model->get(null, $limite, $start);
                $total_registros = $this->ncms_model->count();
                $total_paginas = ceil($total_registros / $limite);

                log_message('debug', 'Encontrados ' . count($ncms) . ' NCMs de ' . $total_registros . ' total');

                $this->output->set_content_type('application/json')
                            ->set_output(json_encode(array(
                                'resultados' => $ncms,
                                'total_registros' => $total_registros,
                                'total_paginas' => $total_paginas,
                                'pagina_atual' => $pagina
                            )));
            } else {
                // Busca com termo
                log_message('debug', 'Controller Ncms - Buscando por termo: ' . $termo . ', limite: ' . $limite . ', start: ' . $start);
                $ncms = $this->ncms_model->buscar($termo, $limite, $start);
                log_message('debug', 'Controller Ncms - Resultados encontrados: ' . count($ncms));

                $total_registros = count($this->ncms_model->buscar($termo)); // Contagem total sem limite
                log_message('debug', 'Controller Ncms - Total de registros: ' . $total_registros);

                $total_paginas = ceil($total_registros / $limite);

                $response = array(
                    'resultados' => $ncms,
                    'total_registros' => $total_registros,
                    'total_paginas' => $total_paginas,
                    'pagina_atual' => $pagina,
                    'termo_pesquisado' => $termo
                );

                log_message('debug', 'Controller Ncms - Resposta final: ' . json_encode($response));

                $this->output->set_content_type('application/json')
                            ->set_output(json_encode($response));
            }
            return;
        }

        $this->data['view'] = 'ncms/ncms';
        $this->data['ncms'] = $this->ncms_model->buscar($termo);
        $this->data['termo'] = $termo;
        $this->data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        return $this->layout();
    }

    public function visualizar($id)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNcm')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar NCMs.');
            redirect(base_url());
        }

        $this->data['view'] = 'ncms/visualizar';
        $this->data['ncm'] = $this->ncms_model->getById($id);
        $this->data['tributacao_federal'] = $this->ncms_model->verificarDadosInseridos($id);
        $this->data['tributacao_estadual'] = $this->ncms_model->getTributacaoEstadual($id);
        return $this->layout();
    }

    public function importar()
    {
        // Aumenta os limites do PHP para arquivos grandes
        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', 300);
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');

        // Verifica se é uma requisição SSE
        $isSSE = $this->input->get('sse') === 'true';
        
        if ($isSSE) {
            // Configura os headers para SSE
            header('Content-Type: text/event-stream');
            header('Cache-Control: no-cache');
            header('Connection: keep-alive');
            header('X-Accel-Buffering: no'); // Desativa o buffering do Nginx
            
            // Envia um comentário para manter a conexão viva
            echo ": keepalive\n\n";
            flush();
            
            // Verifica se o arquivo foi enviado
            if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
                $this->sendSSEMessage([
                    'success' => false,
                    'message' => 'Nenhum arquivo foi enviado ou ocorreu um erro no upload.'
                ]);
                return;
            }
        } else {
        // Verifica se o arquivo foi enviado
        if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
            $this->output->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'Nenhum arquivo foi enviado ou ocorreu um erro no upload.'
                        ]));
            return;
            }
        }

        // Verifica se o usuário está logado
        if (!$this->session->userdata('logado')) {
            if ($isSSE) {
                $this->sendSSEMessage([
                    'success' => false,
                    'message' => 'Sessão expirada. Por favor, faça login novamente.'
                ]);
            } else {
            $this->output->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'Sessão expirada. Por favor, faça login novamente.'
                        ]));
            }
            return;
        }

        // Verifica permissão
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aNcm')) {
            if ($isSSE) {
                $this->sendSSEMessage([
                    'success' => false,
                    'message' => 'Você não tem permissão para importar NCMs.'
                ]);
            } else {
            $this->output->set_content_type('application/json')
                        ->set_output(json_encode([
                            'success' => false,
                            'message' => 'Você não tem permissão para importar NCMs.'
                        ]));
            }
            return;
        }

        try {
            // Verifica se o diretório de upload existe
            $upload_path = './uploads/';
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            $config['upload_path'] = $upload_path;
            $config['allowed_types'] = '*';
            $config['file_ext_tolower'] = true;
            $config['max_size'] = 102400; // 100MB
            $config['file_name'] = 'ncms_' . date('YmdHis');
            $config['overwrite'] = true;
            $config['detect_mime'] = true;

            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('arquivo')) {
                throw new Exception($this->upload->display_errors());
            }

            $upload_data = $this->upload->data();
            $json_content = file_get_contents($upload_data['full_path']);
            
            if ($json_content === false) {
                throw new Exception('Erro ao ler o arquivo JSON');
            }

            // Limpa possíveis caracteres inválidos
            $json_content = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $json_content);
            
            // Remove BOM se existir
            $json_content = preg_replace('/^\xEF\xBB\xBF/', '', $json_content);
            
            // Remove espaços em branco extras
            $json_content = trim($json_content);

            // Tenta decodificar o JSON
            $json_data = json_decode($json_content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $error_msg = json_last_error_msg();
                $error_pos = json_last_error() === JSON_ERROR_SYNTAX ? $this->findJsonErrorPosition($json_content) : null;
                
                $error_details = "Erro ao decodificar o arquivo JSON: " . $error_msg;
                if ($error_pos !== null) {
                    $error_details .= "\nPosição do erro: " . $error_pos;
                    $error_details .= "\nLinha aproximada: " . $this->getJsonErrorLine($json_content, $error_pos);
                }
                
                unlink($upload_data['full_path']);
                throw new Exception($error_details);
            }

            // Verifica se o JSON tem a estrutura esperada
            if (!isset($json_data['Nomenclaturas']) || !is_array($json_data['Nomenclaturas'])) {
                unlink($upload_data['full_path']);
                throw new Exception('O arquivo JSON deve conter um array "Nomenclaturas" com os dados dos NCMs.');
            }

            $total = count($json_data['Nomenclaturas']);
            $imported = 0;
            $errors = 0;
            $error_messages = [];
            $batch_size = 1000; // Processa 1000 registros por vez

            // Envia resposta inicial
            if ($isSSE) {
                $this->sendSSEMessage([
                    'success' => true,
                    'progress' => 0,
                    'current' => 0,
                    'total' => $total,
                    'imported' => 0,
                    'errors' => 0
                ]);
            } else {
                $this->sendJsonResponse([
                            'success' => true,
                            'progress' => 0,
                            'current' => 0,
                            'total' => $total,
                            'imported' => 0,
                            'errors' => 0
                ]);
            }

            // Processa em lotes
            for ($i = 0; $i < $total; $i += $batch_size) {
                $batch = array_slice($json_data['Nomenclaturas'], $i, $batch_size);
                $batch_data = [];

                foreach ($batch as $ncm) {
                    try {
                        // Verifica se o NCM tem os campos necessários
                        if (!isset($ncm['Codigo']) || !isset($ncm['Descricao'])) {
                            throw new Exception('NCM inválido: campos obrigatórios ausentes');
                        }
                        
                        // Remove a máscara do código NCM (remove pontos e traços)
                        $codigo = preg_replace('/[^0-9]/', '', $ncm['Codigo']);
                        
                        // Converte as datas para o formato do banco
                        $data_inicio = !empty($ncm['Data_Inicio']) ? date('Y-m-d', strtotime(str_replace('/', '-', $ncm['Data_Inicio']))) : null;
                        $data_fim = !empty($ncm['Data_Fim']) ? date('Y-m-d', strtotime(str_replace('/', '-', $ncm['Data_Fim']))) : null;
                        
                        $batch_data[] = array(
                            'codigo' => $codigo,
                            'descricao' => $ncm['Descricao'],
                            'data_inicio' => $data_inicio,
                            'data_fim' => $data_fim,
                            'tipo_ato' => $ncm['Tipo_Ato_Ini'] ?? null,
                            'numero_ato' => $ncm['Numero_Ato_Ini'] ?? null,
                            'ano_ato' => $ncm['Ano_Ato_Ini'] ?? null,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        );
                    } catch (Exception $e) {
                        $errors++;
                        $error_messages[] = "Erro ao processar NCM {$ncm['Codigo']}: " . $e->getMessage();
                    }
                }

                if (!empty($batch_data)) {
                    $this->db->insert_batch('ncms', $batch_data);
                    $imported += count($batch_data);
                }

                // Envia atualização de progresso
                $progress = round(($i + count($batch)) / $total * 100);
                $response = [
                    'success' => true,
                    'progress' => $progress,
                    'current' => $i + count($batch),
                    'total' => $total,
                    'imported' => $imported,
                    'errors' => $errors
                ];

                if ($isSSE) {
                    $this->sendSSEMessage($response);
                } else {
                    $this->sendJsonResponse($response);
                }
            }

            unlink($upload_data['full_path']);

            $message = $errors > 0 
                ? "Importação concluída com $errors erros. $imported de $total NCMs importados com sucesso."
                : "$imported de $total NCMs importados com sucesso.";

            if (!empty($error_messages)) {
                log_message('error', 'Erros durante a importação: ' . implode(', ', $error_messages));
            }

            $final_response = [
                            'success' => true,
                            'message' => $message,
                            'details' => $error_messages,
                            'progress' => 100,
                            'current' => $total,
                            'total' => $total,
                            'imported' => $imported,
                            'errors' => $errors
            ];

            if ($isSSE) {
                $this->sendSSEMessage($final_response);
            } else {
                $this->sendJsonResponse($final_response);
            }

        } catch (Exception $e) {
            log_message('error', 'Erro na importação: ' . $e->getMessage());
            $error_response = [
                            'success' => false,
                            'message' => $e->getMessage()
            ];
            
            if ($isSSE) {
                $this->sendSSEMessage($error_response);
            } else {
                $this->sendJsonResponse($error_response);
            }
        }
    }

    private function findJsonErrorPosition($json) {
        $json = trim($json);
        $length = strlen($json);
        $pos = 0;
        $stack = [];
        
        for ($i = 0; $i < $length; $i++) {
            $char = $json[$i];
            
            if ($char === '{' || $char === '[') {
                array_push($stack, $char);
            } elseif ($char === '}' || $char === ']') {
                if (empty($stack)) {
                    return $i;
                }
                $last = array_pop($stack);
                if (($char === '}' && $last !== '{') || ($char === ']' && $last !== '[')) {
                    return $i;
                }
            } elseif ($char === '"') {
                $i++;
                while ($i < $length && $json[$i] !== '"') {
                    if ($json[$i] === '\\') {
                        $i++;
                    }
                    $i++;
                }
                if ($i >= $length) {
                    return $i;
                }
            }
        }
        
        return $length;
    }
    
    private function getJsonErrorLine($json, $position) {
        $lines = explode("\n", substr($json, 0, $position));
        return count($lines);
    }

    private function sendJsonResponse($data) {
        // Limpa qualquer saída anterior
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Envia a resposta
        $this->output->set_content_type('application/json')
                    ->set_output(json_encode($data));
        $this->output->_display();
        flush();
    }

    private function sendSSEMessage($data) {
        // Limpa qualquer saída anterior
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        // Envia a mensagem SSE
        echo "data: " . json_encode($data) . "\n\n";
        flush();
    }

    public function tributacao($ncm_id = null)
    {
        if (!$ncm_id) {
            $this->session->set_flashdata('error', 'NCM ID não informado.');
            redirect(base_url() . 'index.php/ncms');
            return;
        }

        // Verifica se o NCM existe
        $ncm = $this->ncms_model->getById($ncm_id);
        log_message('debug', 'NCM no controlador após getById: ' . json_encode($ncm));
        
        if (!$ncm) {
            $this->session->set_flashdata('error', 'NCM não encontrado.');
            redirect(base_url() . 'index.php/ncms');
            return;
        }

        // Carrega os dados da tributação federal
        $tributacao_federal = $this->ncms_model->verificarDadosInseridos($ncm_id);
        
        // Carrega os dados da tributação estadual
        $tributacao_estadual = $this->ncms_model->getTributacaoEstadual($ncm_id);

        // Lista de estados
        $estados = [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins'
        ];

        // Tipos de tributação
        $tipos_tributacao = [
            'ICMS Normal' => 'ICMS Normal',
            'ST' => 'Substituição Tributária',
            'Serviço' => 'Serviço'
        ];

        // CSTs IPI Entrada
        $this->data['cst_ipi_entrada'] = [
            '00' => 'Entrada com Recuperação de Crédito',
            '01' => 'Entrada Tributada com Alíquota Zero',
            '02' => 'Entrada Isenta',
            '03' => 'Entrada não Tributada',
            '04' => 'Entrada Imune',
            '05' => 'Entrada com Suspensão',
            '49' => 'Outras Entradas'
        ];

        // CSTs IPI Saída
        $this->data['cst_ipi_saida'] = [
            '50' => 'Saída Tributada',
            '51' => 'Saída Tributada com a Alíquota Zero',
            '52' => 'Saída Isenta',
            '53' => 'Saída não Tributada',
            '54' => 'Saída Imune',
            '55' => 'Saída com suspensão',
            '99' => 'Outras saídas'
        ];

        // CSTs PIS/COFINS Entrada
        $this->data['cst_pis_cofins_entrada'] = [
            '50' => 'Operação com Direito a Crédito – Vinculado Exclusivamente a Receita Tributada no Mercado Interno',
            '51' => 'Operação com Direito a Crédito – Vinculado Exclusivamente a Receita Não Tributada no Mercado Interno',
            '52' => 'Operação com Direito a Crédito – Vinculado Exclusivamente a Receita de Exportação',
            '53' => 'Operação com Direito a Crédito – Vinculado a Receitas Tributadas e Não-Tributadas no Mercado Interno',
            '54' => 'Operação com Direito a Crédito – Vinculado a Receitas Tributadas no Mercado Interno e de Exportação',
            '55' => 'Operação com Direito a Crédito – Vinculado a Receitas Não-Tributadas no Mercado Interno e de Exportação',
            '56' => 'Operação com Direito a Crédito – Vinculado a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação',
            '60' => 'Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Tributada no Mercado Interno',
            '61' => 'Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita Não Tributada no Mercado Interno',
            '62' => 'Crédito Presumido – Operação de Aquisição Vinculada Exclusivamente a Receita de Exportação',
            '63' => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno',
            '64' => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas no Mercado Interno e de Exportação',
            '65' => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Não-Tributadas no Mercado Interno e de Exportação',
            '66' => 'Crédito Presumido – Operação de Aquisição Vinculada a Receitas Tributadas e Não-Tributadas no Mercado Interno, e de Exportação',
            '67' => 'Crédito Presumido – Outras Operações',
            '70' => 'Operação de Aquisição sem Direito a Crédito',
            '71' => 'Operação de Aquisição com Isenção',
            '72' => 'Operação de Aquisição com Suspensão',
            '73' => 'Operação de Aquisição a Alíquota Zero',
            '74' => 'Operação de Aquisição sem Incidência da Contribuição',
            '75' => 'Operação de Aquisição por Substituição Tributária',
            '98' => 'Outras Operações de Entrada',
            '99' => 'Outras Operações'
        ];

        // CSTs PIS/COFINS Saída
        $this->data['cst_pis_cofins_saida'] = [
            '01' => 'Operação Tributável com Alíquota Básica',
            '02' => 'Operação Tributável com Alíquota Diferenciada',
            '03' => 'Operação Tributável com Alíquota por Unidade de Medida de Produto',
            '04' => 'Operação Tributável Monofásica – Revenda a Alíquota Zero',
            '05' => 'Operação Tributável por Substituição Tributária',
            '06' => 'Operação Tributável a Alíquota Zero',
            '07' => 'Operação Isenta da Contribuição',
            '08' => 'Operação sem Incidência da Contribuição',
            '09' => 'Operação com Suspensão da Contribuição',
            '49' => 'Outras Operações de Saída',
            '99' => 'Outras Operações'
        ];
        
        $this->data['ncm'] = $ncm;
        $this->data['tributacao_federal'] = $tributacao_federal;
        $this->data['tributacao_estadual'] = $tributacao_estadual;
        $this->data['estados'] = $estados;
        $this->data['tipos_tributacao'] = $tipos_tributacao;
        $this->data['custom_error'] = '';
        $this->data['view'] = 'ncms/tributacao';
        return $this->layout();
    }

    public function salvarTributacao()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Validações específicas para campos obrigatórios
        $this->form_validation->set_rules('tbf_aliquota_ipi_entrada', 'Alíquota IPI Entrada', 'trim');
        $this->form_validation->set_rules('tbf_aliquota_pis_entrada', 'Alíquota PIS Entrada', 'trim');
        $this->form_validation->set_rules('tbf_aliquota_cofins_entrada', 'Alíquota COFINS Entrada', 'trim');
        $this->form_validation->set_rules('tbf_aliquota_ipi_saida', 'Alíquota IPI Saída', 'trim');
        $this->form_validation->set_rules('tbf_aliquota_pis_saida', 'Alíquota PIS Saída', 'trim');
        $this->form_validation->set_rules('tbf_aliquota_cofins_saida', 'Alíquota COFINS Saída', 'trim');

        if ($this->form_validation->run() == false) {
            log_message('error', '=== ERRO DE VALIDAÇÃO TRIBUTAÇÃO FEDERAL ===');
            log_message('error', 'Erros: ' . validation_errors());
            $this->data['custom_error'] = '<div class="alert alert-danger">' . validation_errors() . '</div>';
            $this->session->set_flashdata('error', 'Por favor, preencha todos os campos obrigatórios.');
            redirect(site_url() . '/ncms/tributacao/' . $this->input->post('ncm_id'));
            return;
        }

        $ncm_id = $this->input->post('ncm_id');
        
        // Função auxiliar para tratar valores vazios como 0
        $getNumericValue = function($value) {
            if (empty($value) || $value == '') {
                return '0.00';
            }
            return str_replace(',', '.', $value);
        };

        // Dados da tributação federal
        $data_federal = [
            'ncm_id' => $ncm_id,
            'tbf_cst_ipi_entrada' => $this->input->post('tbf_cst_ipi_entrada'),
            'tbf_aliquota_ipi_entrada' => $getNumericValue($this->input->post('tbf_aliquota_ipi_entrada')),
            'tbf_cst_pis_cofins_entrada' => $this->input->post('tbf_cst_pis_cofins_entrada'),
            'tbf_aliquota_pis_entrada' => $getNumericValue($this->input->post('tbf_aliquota_pis_entrada')),
            'tbf_aliquota_cofins_entrada' => $getNumericValue($this->input->post('tbf_aliquota_cofins_entrada')),
            'tbf_cst_ipi_saida' => $this->input->post('tbf_cst_ipi_saida'),
            'tbf_aliquota_ipi_saida' => $getNumericValue($this->input->post('tbf_aliquota_ipi_saida')),
            'tbf_cst_pis_cofins_saida' => $this->input->post('tbf_cst_pis_cofins_saida'),
            'tbf_aliquota_pis_saida' => $getNumericValue($this->input->post('tbf_aliquota_pis_saida')),
            'tbf_aliquota_cofins_saida' => $getNumericValue($this->input->post('tbf_aliquota_cofins_saida'))
        ];

        // Lista de todos os estados do Brasil
        $estados = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];

        // Log dos dados processados
        log_message('debug', '=== DADOS PROCESSADOS TRIBUTAÇÃO FEDERAL ===');
        log_message('debug', 'Dados a serem salvos (federal): ' . json_encode($data_federal));

        // Verifica se as tabelas existem
        if (!$this->db->table_exists('tributacao_federal')) {
            log_message('error', 'Tabela tributacao_federal não existe');
            $this->session->set_flashdata('error', 'Erro: Tabela de tributação federal não existe.');
            redirect(site_url() . '/ncms/tributacao/' . $ncm_id);
            return;
        }

        if (!$this->db->table_exists('tributacao_estadual')) {
            log_message('error', 'Tabela tributacao_estadual não existe');
            $this->session->set_flashdata('error', 'Erro: Tabela de tributação estadual não existe.');
            redirect(site_url() . '/ncms/tributacao/' . $ncm_id);
            return;
        }

        // Tenta salvar a tributação federal
        $result_federal = $this->ncms_model->saveTributacaoFederal($ncm_id, $data_federal);
        log_message('debug', 'Resultado do salvamento federal: ' . ($result_federal ? 'Sucesso' : 'Falha'));

        // Tenta salvar a tributação estadual para todos os estados
        $this->db->trans_start();
        $result_estadual = true;
        $tributacao_estadual = $this->input->post('tributacao_estadual');
        
        foreach ($estados as $uf) {
            $data_estadual = [
                'ncm_id' => $ncm_id,
                'tbe_uf' => $uf,
                'tbe_tipo_tributacao' => isset($tributacao_estadual[$uf]['tipo_tributacao']) ? $tributacao_estadual[$uf]['tipo_tributacao'] : $this->input->post('tipo_tributacao_todos'),
                'tbe_aliquota_icms' => isset($tributacao_estadual[$uf]['aliquota_icms']) ? $getNumericValue($tributacao_estadual[$uf]['aliquota_icms']) : $getNumericValue($this->input->post('aliquota_icms_todos')),
                'tbe_mva' => isset($tributacao_estadual[$uf]['mva']) ? $getNumericValue($tributacao_estadual[$uf]['mva']) : $getNumericValue($this->input->post('mva_todos')),
                'tbe_aliquota_icms_st' => isset($tributacao_estadual[$uf]['aliquota_icms_st']) ? $getNumericValue($tributacao_estadual[$uf]['aliquota_icms_st']) : $getNumericValue($this->input->post('aliquota_icms_st_todos')),
                'tbe_percentual_reducao_icms' => isset($tributacao_estadual[$uf]['percentual_reducao_icms']) ? $getNumericValue($tributacao_estadual[$uf]['percentual_reducao_icms']) : $getNumericValue($this->input->post('percentual_reducao_icms_todos')),
                'tbe_percentual_reducao_st' => isset($tributacao_estadual[$uf]['percentual_reducao_st']) ? $getNumericValue($tributacao_estadual[$uf]['percentual_reducao_st']) : $getNumericValue($this->input->post('percentual_reducao_st_todos')),
                'tbe_aliquota_fcp' => isset($tributacao_estadual[$uf]['aliquota_fcp']) ? $getNumericValue($tributacao_estadual[$uf]['aliquota_fcp']) : $getNumericValue($this->input->post('aliquota_fcp_todos')),
                'tbe_data_cadastro' => date('Y-m-d H:i:s'),
                'tbe_data_alteracao' => date('Y-m-d H:i:s')
            ];

            $this->db->where('ncm_id', $ncm_id);
            $this->db->where('tbe_uf', $uf);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $existing = $this->db->get('tributacao_estadual')->row();
            if ($existing) {
                $this->db->where('tbe_id', $existing->tbe_id);
                $this->db->where('ten_id', $this->session->userdata('ten_id'));
                $result = $this->db->update('tributacao_estadual', $data_estadual);
            } else {
                $data_estadual['ten_id'] = $this->session->userdata('ten_id');
                $result = $this->db->insert('tributacao_estadual', $data_estadual);
            }
            if ($result === false) {
                $result_estadual = false;
                break;
            }
        }
        $this->db->trans_complete();
        log_message('debug', 'Resultado do salvamento estadual: ' . ($result_estadual ? 'Sucesso' : 'Falha'));

        if ($result_federal && $result_estadual) {
            log_message('debug', 'Ambas as tributações salvas com sucesso');
            $this->session->set_flashdata('success', 'Tributação salva com sucesso para todos os estados!');
            redirect(site_url() . '/ncms/visualizarTributacao/' . $ncm_id);
        } else {
            log_message('error', 'Erro ao salvar tributação');
            if (!$result_federal) {
                log_message('error', 'Falha ao salvar tributação federal');
            }
            if (!$result_estadual) {
                log_message('error', 'Falha ao salvar tributação estadual');
            }
            $this->session->set_flashdata('error', 'Ocorreu um erro ao tentar salvar a tributação.');
            redirect(site_url() . '/ncms/tributacao/' . $ncm_id);
        }
    }

    public function verificarEstrutura()
    {
        $this->load->model('ncms_model');
        
        // Verifica a estrutura da tabela
        $result = $this->ncms_model->verificarEstruturaTabela();
        
        if ($result) {
            $this->session->set_flashdata('success', 'Estrutura da tabela verificada com sucesso. Verifique os logs para mais detalhes.');
        } else {
            $this->session->set_flashdata('error', 'Erro ao verificar estrutura da tabela. Verifique os logs para mais detalhes.');
        }
        
        redirect(base_url() . 'index.php/ncms');
    }

    public function verificarDados($ncm_id)
    {
        $this->load->model('ncms_model');
        
        // Verifica os dados inseridos
        $dados = $this->ncms_model->verificarDadosInseridos($ncm_id);
        
        if ($dados) {
            $this->session->set_flashdata('success', 'Dados encontrados com sucesso. Verifique os logs para mais detalhes.');
        } else {
            $this->session->set_flashdata('error', 'Nenhum dado encontrado para este NCM. Verifique os logs para mais detalhes.');
        }
        
        redirect(base_url() . 'index.php/ncms/tributacao/' . $ncm_id);
    }

    public function visualizarTributacao($ncm_id = null)
    {
        if (!$ncm_id) {
            $this->session->set_flashdata('error', 'NCM ID não informado.');
            redirect(base_url() . 'index.php/ncms');
            return;
        }

        // Verifica se o NCM existe
        $ncm = $this->ncms_model->getById($ncm_id);
        if (!$ncm) {
            $this->session->set_flashdata('error', 'NCM não encontrado.');
            redirect(base_url() . 'index.php/ncms');
            return;
        }

        // Carrega os dados da tributação federal
        $tributacao = $this->ncms_model->verificarDadosInseridos($ncm_id);
        if (!$tributacao) {
            $this->session->set_flashdata('error', 'Tributação não encontrada.');
            redirect(base_url() . 'index.php/ncms');
            return;
        }

        // Carrega os dados da tributação estadual
        $tributacao_estadual = $this->ncms_model->getTributacaoEstadual($ncm_id);

        $this->data['ncm'] = $ncm;
        $this->data['tributacao'] = $tributacao;
        $this->data['tributacao_estadual'] = $tributacao_estadual;
        $this->data['view'] = 'ncms/visualizarTributacao';
        return $this->layout();
    }
} 