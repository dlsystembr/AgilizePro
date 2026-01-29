<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!defined('SOAP_1_2')) {
    define('SOAP_1_2', 2);
}

require_once FCPATH . 'application/vendor/autoload.php';

use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\CertificateException;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Common\Complements;
use NFePHP\Common\CertificateHandler;

class FaturamentoEntrada extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('FaturamentoEntrada_model');
        $this->load->model('Produtos_model');
        $this->load->model('OperacaoComercial_model');
        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('Mapos_model');
        $this->load->model('Clientes_model');
        $this->load->model('Nfe_model');
        $this->load->model('NfeMonitoradas_model');
        $this->load->model('NfeNsu_model');
        $this->load->library('CertificateHandler');
        $this->data['menuFaturamentoEntrada'] = 'Faturamento de Entrada';
    }

    public function validate_items($str)
    {
        // If we're editing and no items were submitted, that's fine
        if ($this->uri->segment(3)) {
            // Check if this is a form submission with no changes
            $produtos = $this->input->post('produtos');
            $quantidades = $this->input->post('quantidades');
            $valores = $this->input->post('valores');
            $aliquotas = $this->input->post('aliquotas');

            // If any of these arrays are empty, it means no changes were made
            if (empty($produtos) || empty($quantidades) || empty($valores) || empty($aliquotas)) {
                return true;
            }
        }

        // For new entries or when items are being modified, validate all required fields
        $produtos = $this->input->post('produtos');
        $quantidades = $this->input->post('quantidades');
        $valores = $this->input->post('valores');
        $aliquotas = $this->input->post('aliquotas');

        if (empty($produtos) || empty($quantidades) || empty($valores) || empty($aliquotas)) {
            $this->form_validation->set_message('validate_items', 'Todos os campos de itens são obrigatórios.');
            return false;
        }

        // Validate that all numeric fields contain valid numbers
        foreach ($quantidades as $qtd) {
            $qtd = str_replace(['R$', '.', ' '], '', str_replace(',', '.', $qtd));
            if (!is_numeric($qtd)) {
                $this->form_validation->set_message('validate_items', 'As quantidades devem ser valores numéricos válidos.');
                return false;
            }
        }

        foreach ($valores as $valor) {
            $valor = str_replace(['R$', '.', ' '], '', str_replace(',', '.', $valor));
            if (!is_numeric($valor)) {
                $this->form_validation->set_message('validate_items', 'Os valores devem ser valores numéricos válidos.');
                return false;
            }
        }

        foreach ($aliquotas as $aliquota) {
            $aliquota = str_replace(['R$', '.', ' '], '', str_replace(',', '.', $aliquota));
            if (!is_numeric($aliquota)) {
                $this->form_validation->set_message('validate_items', 'As alíquotas devem ser valores numéricos válidos.');
                return false;
            }
        }

        return true;
    }

    public function index()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar faturamento de entrada.');
            redirect(base_url());
        }

        $this->load->library('pagination');
        $this->load->library('table');
        $this->load->library('form_validation');

        $config['base_url'] = base_url() . 'index.php/faturamentoEntrada/index/';
        $config['total_rows'] = $this->FaturamentoEntrada_model->count('faturamento_entrada');
        $config['per_page'] = 10;
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination pagination-centered"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><a href="#">';
        $config['cur_tag_close'] = '</a></li>';
        $config['prev_tag_open'] = '<li class="prev">';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li class="next">';
        $config['next_tag_close'] = '</li>';
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';

        $this->pagination->initialize($config);
        
        $this->data['results'] = $this->FaturamentoEntrada_model->getFaturamentoEntrada(
            'faturamento_entrada',
            'faturamento_entrada.*, clientes.nomeCliente',
            '',
            $config['per_page'],
            $this->uri->segment(3)
        );
        
        $this->data['view'] = 'faturamento_entrada/faturamento_entrada';
        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturamentoEntrada')) {
            if ($this->input->is_ajax_request()) {
                $this->output->set_status_header(403);
                echo json_encode(['success' => false, 'message' => 'Você não tem permissão para adicionar faturamento de entrada.']);
                return;
            }
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar faturamento de entrada.');
            redirect(base_url());
        }

        // Carrega o helper form para usar set_value()
        $this->load->helper('form');

        // Se for GET, exibe o formulário
        if ($this->input->method() === 'get') {
            $this->data['custom_error'] = '';
            $this->data['fornecedores'] = $this->FaturamentoEntrada_model->getFornecedores();
            $this->data['produtos'] = $this->Produtos_model->get('produtos', '*');
            $this->data['operacoes'] = $this->OperacaoComercial_model->get('operacao_comercial', '*');
            $this->data['view'] = 'faturamento_entrada/adicionarFaturamentoEntrada';
            return $this->layout();
        }

        // Se for POST, processa o formulário
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        log_message('debug', 'POST data: ' . json_encode($_POST));

        if ($this->form_validation->run('faturamento_entrada') == false) {
            log_message('error', 'Validation errors: ' . validation_errors());
            $this->output->set_status_header(400);
            echo json_encode([
                'success' => false,
                'message' => 'Erro de validação',
                'error' => validation_errors()
            ]);
            return;
        }

        try {
            log_message('debug', 'Iniciando transação...');
                $this->db->trans_begin();

                $data = array(
                    'fornecedor_id' => $this->input->post('fornecedor_id'),
                    'transportadora_id' => $this->input->post('transportadora_id'),
                    'modalidade_frete' => $this->input->post('modalidade_frete'),
                    'peso_bruto' => str_replace(',', '.', $this->input->post('peso_bruto')),
                    'peso_liquido' => str_replace(',', '.', $this->input->post('peso_liquido')),
                    'volume' => str_replace(',', '.', $this->input->post('volume')),
                    'operacao_comercial_id' => $this->input->post('operacao_comercial_id'),
                    'data_emissao' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('data_emissao')))),
                    'data_entrada' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('data_entrada')))),
                    'numero_nota' => $this->input->post('numero_nfe'),
                    'chave_acesso' => $this->input->post('chave_acesso'),
                    'valor_total' => str_replace(',', '.', str_replace('.', '', $this->input->post('total_nota'))),
                    'valor_produtos' => str_replace(',', '.', str_replace('.', '', $this->input->post('total_produtos'))),
                    'valor_icms' => str_replace(',', '.', str_replace('.', '', $this->input->post('total_icms'))),
                    'total_base_icms_st' => str_replace(',', '.', str_replace('.', '', $this->input->post('total_base_icms_st'))),
                    'total_icms_st' => str_replace(',', '.', str_replace('.', '', $this->input->post('total_icms_st'))),
                    'valor_ipi' => str_replace(',', '.', str_replace('.', '', $this->input->post('total_ipi'))),
                    'valor_frete' => str_replace(',', '.', str_replace('.', '', $this->input->post('frete'))),
                    'valor_outras_despesas' => str_replace(',', '.', str_replace('.', '', $this->input->post('despesas'))),
                    'observacoes' => $this->input->post('observacoes'),
                    'data_cadastro' => date('Y-m-d H:i:s'),
                    'data_atualizacao' => date('Y-m-d H:i:s'),
                    'usuario_id' => $this->session->userdata('id_admin'),
                    'xml_conteudo' => $this->session->userdata('xml_content')
                );

            log_message('debug', 'Dados para inserção: ' . json_encode($data));

                $nsu = $this->input->post('nsu');
                if (!empty($nsu)) {
                    $this->db->where('nsu', $nsu);
                }

            log_message('debug', 'Tentando adicionar faturamento...');
                $id = $this->FaturamentoEntrada_model->add('faturamento_entrada', $data);
                
                if ($id) {
                log_message('debug', 'Faturamento adicionado com ID: ' . $id);
                
                    // Adicionar itens
                    $produtos = $this->input->post('produtos');
                    $quantidades = $this->input->post('quantidades');
                    $valores = $this->input->post('valores');
                    $descontos = $this->input->post('descontos');
                    $bases_icms = $this->input->post('bases_icms');
                    $aliquotas = $this->input->post('aliquotas');
                    $valores_icms = $this->input->post('valores_icms');
                    $bases_icms_st = $this->input->post('bases_icms_st');
                    $aliquotas_st = $this->input->post('aliquotas_st');
                    $valores_icms_st = $this->input->post('valores_icms_st');
                    $totais = $this->input->post('totais');
                    $cst = $this->input->post('cst');
                    $valores_ipi = $this->input->post('valores_ipi');
                    
                    // Dados adicionais dos produtos (quando vem do XML)
                    $produtos_descricao = $this->input->post('produtos_descricao');
                    $produtos_codigo = $this->input->post('produtos_codigo');
                    $produtos_ncm = $this->input->post('produtos_ncm');

                log_message('debug', 'Dados dos itens: ' . json_encode([
                    'produtos' => $produtos,
                    'quantidades' => $quantidades,
                    'valores' => $valores,
                    'descontos' => $descontos,
                    'bases_icms' => $bases_icms,
                    'aliquotas' => $aliquotas,
                    'valores_icms' => $valores_icms,
                    'bases_icms_st' => $bases_icms_st,
                    'aliquotas_st' => $aliquotas_st,
                    'valores_icms_st' => $valores_icms_st,
                    'totais' => $totais,
                    'cst' => $cst,
                    'produtos_descricao' => $produtos_descricao,
                    'produtos_codigo' => $produtos_codigo,
                    'produtos_ncm' => $produtos_ncm
                ]));

                    for ($i = 0; $i < count($produtos); $i++) {
                        if (!empty($produtos[$i])) {
                            $produto_id = $produtos[$i];
                            
                            // Se o produto_id é 'new' ou não é numérico, criar o produto
                            if ($produto_id === 'new' || !is_numeric($produto_id)) {
                                // Verificar se temos dados específicos do produto
                                $descricao_produto = isset($produtos_descricao[$i]) && !empty($produtos_descricao[$i]) 
                                    ? $produtos_descricao[$i] 
                                    : null;
                                
                                // Se não há descrição, não criar o produto
                                if (empty($descricao_produto)) {
                                    log_message('error', "Produto $i - Sem descrição válida para criar produto");
                                    throw new Exception('Descrição do produto é obrigatória para criar um novo produto');
                                }
                                
                                $codigo_produto = isset($produtos_codigo[$i]) ? $produtos_codigo[$i] : '';
                                $ncm_produto = isset($produtos_ncm[$i]) ? $produtos_ncm[$i] : '';
                                $valor_unitario = isset($valores[$i]) ? str_replace(',', '.', $valores[$i]) : '0';
                                $quantidade_produto = isset($quantidades[$i]) ? str_replace(',', '.', $quantidades[$i]) : '0';
                                
                                // Log dos dados do produto para debug
                                log_message('debug', "Produto $i - Dados recebidos: " . json_encode([
                                    'descricao' => $descricao_produto,
                                    'codigo' => $codigo_produto,
                                    'ncm' => $ncm_produto,
                                    'valor_unitario' => $valor_unitario,
                                    'quantidade' => $quantidade_produto
                                ]));
                                
                                // Criar dados do produto para inserção
                                $dadosProduto = [
                                    'descricao' => $descricao_produto,
                                    'codigo' => $codigo_produto,
                                    'ncm' => $ncm_produto,
                                    'valor_unitario' => $valor_unitario,
                                    'quantidade' => $quantidade_produto
                                ];
                                
                                log_message('debug', 'Criando produto: ' . json_encode($dadosProduto));
                                $produto_id = $this->verificarOuCriarProduto($dadosProduto);
                                log_message('debug', 'Produto criado/encontrado com ID: ' . $produto_id);
                            }
                            
                            // Conversão do CFOP: trocar primeiro dígito 5->1 e 6->2
                            $cfop = isset($this->input->post('cfop')[$i]) ? $this->input->post('cfop')[$i] : '';
                            if (!empty($cfop)) {
                                $primeiro_digito = substr($cfop, 0, 1);
                                if ($primeiro_digito == '5') {
                                    $cfop = '1' . substr($cfop, 1);
                                } elseif ($primeiro_digito == '6') {
                                    $cfop = '2' . substr($cfop, 1);
                                }
                            }
                            
                            $item = [
                                'faturamento_entrada_id' => $id,
                                'produto_id' => $produto_id,
                                'quantidade' => isset($quantidades[$i]) ? str_replace(',', '.', $quantidades[$i]) : '0',
                                'valor_unitario' => isset($valores[$i]) ? str_replace(',', '.', $valores[$i]) : '0',
                                'desconto' => isset($descontos[$i]) ? str_replace(',', '.', $descontos[$i]) : '0',
                                'base_calculo_icms' => isset($bases_icms[$i]) ? str_replace(',', '.', $bases_icms[$i]) : '0',
                                'aliquota_icms' => isset($aliquotas[$i]) ? str_replace(',', '.', $aliquotas[$i]) : '0',
                                'valor_icms' => isset($valores_icms[$i]) ? str_replace(',', '.', $valores_icms[$i]) : '0',
                                'base_calculo_icms_st' => isset($bases_icms_st[$i]) ? str_replace(',', '.', $bases_icms_st[$i]) : '0',
                                'aliquota_icms_st' => isset($aliquotas_st[$i]) ? str_replace(',', '.', $aliquotas_st[$i]) : '0',
                                'valor_icms_st' => isset($valores_icms_st[$i]) ? str_replace(',', '.', $valores_icms_st[$i]) : '0',
                                'total_item' => isset($totais[$i]) ? str_replace(',', '.', $totais[$i]) : '0',
                                'cst' => isset($cst[$i]) ? $cst[$i] : '00',
                                'cfop' => $cfop,
                                'valor_ipi' => isset($valores_ipi[$i]) ? str_replace(',', '.', $valores_ipi[$i]) : '0.00'
                            ];
                        
                        log_message('debug', 'Tentando adicionar item: ' . json_encode($item));
                            
                            $result_item = $this->FaturamentoEntrada_model->add('faturamento_entrada_itens', $item);
                            if (!$result_item) {
                                $db_error = $this->db->error();
                                log_message('error', 'Erro ao adicionar item: ' . print_r($db_error, true));
                                throw new Exception('Erro ao adicionar item: ' . $db_error['message']);
                            }

                        }
                    }
                    
                    // Criar documento_faturado e itens_faturados com status ABERTO
                    // Buscar pes_id do fornecedor
                    $pes_id = null;
                    
                    // Tentar buscar pes_id através da tabela clientes (nova estrutura)
                    if ($this->db->table_exists('clientes')) {
                        $cliente_novo = $this->db->where('cln_id', $data['fornecedor_id'])->get('clientes')->row();
                        if ($cliente_novo) {
                            $pes_id = $cliente_novo->pes_id;
                        }
                    }
                    
                    // Se não encontrou, tentar pela tabela antiga clientes_
                    if (!$pes_id && $this->db->table_exists('clientes_')) {
                        $fornecedor = $this->db->where('idClientes', $data['fornecedor_id'])->get('clientes_')->row();
                        if ($fornecedor) {
                            // Tentar buscar por documento
                            $documento_limpo = preg_replace('/\D/', '', $fornecedor->documento);
                            $pessoa = $this->db->where('pes_cpfcnpj', $documento_limpo)->get('pessoas')->row();
                            if ($pessoa) {
                                $pes_id = $pessoa->pes_id;
                            }
                        }
                    }
                    
                    if ($pes_id) {
                        // Criar documento_faturado
                        $dcf_data = [
                            'orv_id' => null, // NULL para faturamento de entrada
                            'pes_id' => $pes_id,
                            'dcf_numero' => $data['numero_nota'] ?: '',
                            'dcf_serie' => '',
                            'dcf_modelo' => '55', // NFe
                            'dcf_tipo' => 'E', // ENTRADA
                            'dcf_data_emissao' => $data['data_emissao'],
                            'dcf_data_saida' => $data['data_entrada'],
                            'dcf_valor_produtos' => $data['valor_produtos'],
                            'dcf_valor_frete' => $data['valor_frete'],
                            'dcf_valor_icms' => $data['valor_icms'],
                            'dcf_valor_ipi' => $data['valor_ipi'],
                            'dcf_valor_total' => $data['valor_total'],
                            'dcf_status' => 'ABERTO',
                            'dcf_data_faturamento' => null
                        ];
                        
                        $this->db->insert('documentos_faturados', $dcf_data);
                        $dcf_id = $this->db->insert_id();
                        
                        // Criar itens_faturados
                        for ($i = 0; $i < count($produtos); $i++) {
                            if (!empty($produtos[$i])) {
                                $produto_id = $produtos[$i];
                                
                                // Buscar dados do produto
                                $produto = $this->Produtos_model->getById($produto_id);
                                if ($produto) {
                                    $quantidade_item = isset($quantidades[$i]) ? str_replace(',', '.', $quantidades[$i]) : 0;
                                    $valor_unitario = isset($valores[$i]) ? str_replace(',', '.', $valores[$i]) : 0;
                                    
                                    // Buscar classificação fiscal do produto (se houver)
                                    $clf_id = null;
                                    if (isset($produto->clf_id)) {
                                        $clf_id = $produto->clf_id;
                                    }
                                    
                                    // Buscar descrição do produto
                                    $produto_desc = $produto->pro_descricao;
                                    $produto_ncm = isset($produto->pro_ncm) ? $produto->pro_ncm : '';
                                    
                                    $itf_data = [
                                        'dcf_id' => $dcf_id,
                                        'pro_id' => $produto_id,
                                        'clf_id' => $clf_id,
                                        'itf_quantidade' => $quantidade_item,
                                        'itf_valor_unitario' => $valor_unitario,
                                        'itf_valor_total' => isset($totais[$i]) ? str_replace(',', '.', $totais[$i]) : ($quantidade_item * $valor_unitario),
                                        'itf_desconto' => isset($descontos[$i]) ? str_replace(',', '.', $descontos[$i]) : 0,
                                        'itf_unidade' => isset($produto->pro_unid_medida) ? $produto->pro_unid_medida : '',
                                        'itf_pro_descricao' => $produto_desc,
                                        'itf_pro_ncm' => $produto_ncm,
                                        'itf_cfop' => isset($cfop) ? $cfop : '',
                                        'itf_icms_cst' => isset($cst[$i]) ? $cst[$i] : '00',
                                        'itf_icms_aliquota' => isset($aliquotas[$i]) ? str_replace(',', '.', $aliquotas[$i]) : 0,
                                        'itf_icms_valor_base' => isset($bases_icms[$i]) ? str_replace(',', '.', $bases_icms[$i]) : 0,
                                        'itf_icms_valor' => isset($valores_icms[$i]) ? str_replace(',', '.', $valores_icms[$i]) : 0,
                                        'itf_ipi_valor' => isset($valores_ipi[$i]) ? str_replace(',', '.', $valores_ipi[$i]) : 0
                                    ];
                                    
                                    $this->db->insert('itens_faturados', $itf_data);
                                    $itf_id = $this->db->insert_id();
                                }
                            }
                        }
                    }

                    if ($this->db->trans_status() === FALSE) {
                    log_message('error', 'Erro na transação: ' . $this->db->error()['message']);
                        $this->db->trans_rollback();
                    throw new Exception('Erro ao salvar faturamento de entrada: ' . $this->db->error()['message']);
                    }

                    $this->db->trans_commit();
                log_message('debug', 'Transação concluída com sucesso');
                echo json_encode(['success' => true, 'message' => 'Faturamento de entrada adicionado com sucesso!']);
                return;
                } else {
                log_message('error', 'Erro ao adicionar faturamento: ' . $this->db->error()['message']);
                throw new Exception('Erro ao adicionar faturamento de entrada: ' . $this->db->error()['message']);
                }
            } catch (Exception $e) {
            log_message('error', 'Exceção capturada: ' . $e->getMessage());
                $this->db->trans_rollback();
            $this->output->set_status_header(500);
            echo json_encode([
                'success' => false,
                'message' => 'Erro ao adicionar faturamento de entrada.',
                'error' => $e->getMessage()
            ]);
            return;
        }
    }

    public function editar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturamentoEntrada')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar faturamento de entrada.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Nota fiscal não encontrada.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Processar os dados antes da validação
        $post = $this->input->post();
        
        // Remover formatação de moeda e converter para ponto decimal
        if (isset($post['despesas'])) {
            $post['despesas'] = str_replace(['R$', '.', ' '], '', $post['despesas']);
            $post['despesas'] = str_replace(',', '.', $post['despesas']);
        }
        if (isset($post['frete'])) {
            $post['frete'] = str_replace(['R$', '.', ' '], '', $post['frete']);
            $post['frete'] = str_replace(',', '.', $post['frete']);
        }
        if (isset($post['total_nota'])) {
            $post['total_nota'] = str_replace(['R$', '.', ' '], '', $post['total_nota']);
            $post['total_nota'] = str_replace(',', '.', $post['total_nota']);
        }
        if (isset($post['total_base_icms'])) {
            $post['total_base_icms'] = str_replace(['R$', '.', ' '], '', $post['total_base_icms']);
            $post['total_base_icms'] = str_replace(',', '.', $post['total_base_icms']);
        }
        if (isset($post['total_icms'])) {
            $post['total_icms'] = str_replace(['R$', '.', ' '], '', $post['total_icms']);
            $post['total_icms'] = str_replace(',', '.', $post['total_icms']);
        }
        if (isset($post['total_base_icms_st'])) {
            $post['total_base_icms_st'] = str_replace(['R$', '.', ' '], '', $post['total_base_icms_st']);
            $post['total_base_icms_st'] = str_replace(',', '.', $post['total_base_icms_st']);
        }
        if (isset($post['total_icms_st'])) {
            $post['total_icms_st'] = str_replace(['R$', '.', ' '], '', $post['total_icms_st']);
            $post['total_icms_st'] = str_replace(',', '.', $post['total_icms_st']);
        }

        // Processar arrays de valores
        if (isset($post['valores'])) {
            foreach ($post['valores'] as &$valor) {
                $valor = str_replace(['R$', '.', ' '], '', $valor);
                $valor = str_replace(',', '.', $valor);
            }
        }
        if (isset($post['descontos'])) {
            foreach ($post['descontos'] as &$desconto) {
                $desconto = str_replace(['R$', '.', ' '], '', $desconto);
                $desconto = str_replace(',', '.', $desconto);
            }
        }
        if (isset($post['bases_icms'])) {
            foreach ($post['bases_icms'] as &$base) {
                $base = str_replace(['R$', '.', ' '], '', $base);
                $base = str_replace(',', '.', $base);
            }
        }
        if (isset($post['aliquotas'])) {
            foreach ($post['aliquotas'] as &$aliquota) {
                $aliquota = str_replace(['R$', '.', ' '], '', $aliquota);
                $aliquota = str_replace(',', '.', $aliquota);
            }
        }
        if (isset($post['valores_icms'])) {
            foreach ($post['valores_icms'] as &$valor) {
                $valor = str_replace(['R$', '.', ' '], '', $valor);
                $valor = str_replace(',', '.', $valor);
            }
        }
        if (isset($post['bases_icms_st'])) {
            foreach ($post['bases_icms_st'] as &$base) {
                $base = str_replace(['R$', '.', ' '], '', $base);
                $base = str_replace(',', '.', $base);
            }
        }
        if (isset($post['aliquotas_st'])) {
            foreach ($post['aliquotas_st'] as &$aliquota) {
                $aliquota = str_replace(['R$', '.', ' '], '', $aliquota);
                $aliquota = str_replace(',', '.', $aliquota);
            }
        }
        if (isset($post['valores_icms_st'])) {
            foreach ($post['valores_icms_st'] as &$valor) {
                $valor = str_replace(['R$', '.', ' '], '', $valor);
                $valor = str_replace(',', '.', $valor);
            }
        }
        if (isset($post['totais'])) {
            foreach ($post['totais'] as &$total) {
                $total = str_replace(['R$', '.', ' '], '', $total);
                $total = str_replace(',', '.', $total);
            }
        }

        // Validar os dados processados
        $this->form_validation->set_data($post);
        if ($this->form_validation->run('faturamento_entrada') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = array(
                'operacao_comercial_id' => $post['operacao_comercial_id'],
                'fornecedor_id' => $post['fornecedor_id'],
                'chave_acesso' => $post['chave_acesso'],
                'numero_nota' => $post['numero_nfe'],
                'data_entrada' => date('Y-m-d', strtotime(str_replace('/', '-', $post['data_entrada']))),
                'data_emissao' => date('Y-m-d', strtotime(str_replace('/', '-', $post['data_emissao']))),
                'valor_outras_despesas' => str_replace(',', '.', str_replace('.', '', $post['despesas'])),
                'valor_frete' => str_replace(',', '.', str_replace('.', '', $post['frete'])),
                'total_base_icms' => str_replace(',', '.', str_replace('.', '', $post['total_base_icms'])),
                'valor_icms' => str_replace(',', '.', str_replace('.', '', $post['total_icms'])),
                'total_base_icms_st' => str_replace(',', '.', str_replace('.', '', $post['total_base_icms_st'])),
                'total_icms_st' => str_replace(',', '.', str_replace('.', '', $post['total_icms_st'])),
                'valor_total' => str_replace(',', '.', str_replace('.', '', $post['total_nota'])),
                'valor_produtos' => str_replace(',', '.', str_replace('.', '', $post['total_produtos'])),
                'valor_ipi' => str_replace(',', '.', str_replace('.', '', $post['total_ipi'])),
                'observacoes' => $post['observacoes']
            );

            // Buscar itens antigos para ajustar estoque
            $itens_antigos = $this->FaturamentoEntrada_model->getItens($id);
            
            // Remover itens antigos e ajustar estoque
            $this->FaturamentoEntrada_model->deleteItens($id);
            foreach ($itens_antigos as $item) {
                // Subtrair quantidade do estoque
                $produto = $this->Produtos_model->getById($item->produto_id);
                if ($produto) {
                    $estoque_atual = $produto->estoque - $item->quantidade;
                    $this->Produtos_model->edit('produtos', array('estoque' => $estoque_atual), 'idProdutos', $item->produto_id);
                }
            }

            // Processar novos itens
            $produtos = $post['produtos'];
            $quantidades = $post['quantidades'];
            $valores = $post['valores'];
            $descontos = $post['descontos'];
            $bases_icms = $post['bases_icms'];
            $aliquotas = $post['aliquotas'];
            $valores_icms = $post['valores_icms'];
            $bases_icms_st = $post['bases_icms_st'];
            $aliquotas_st = $post['aliquotas_st'];
            $valores_icms_st = $post['valores_icms_st'];
            $totais = $post['totais'];
            $cst = $post['cst'];
            $valores_ipi = $post['valores_ipi'];

            if ($produtos) {
                for ($i = 0; $i < count($produtos); $i++) {
                    if (empty($produtos[$i])) continue;

                    $item = array(
                        'faturamento_entrada_id' => $id,
                        'produto_id' => $produtos[$i],
                        'quantidade' => $quantidades[$i],
                        'valor_unitario' => $valores[$i],
                        'desconto' => $descontos[$i],
                        'base_calculo_icms' => $bases_icms[$i],
                        'aliquota_icms' => $aliquotas[$i],
                        'valor_icms' => $valores_icms[$i],
                        'base_calculo_icms_st' => $bases_icms_st[$i],
                        'aliquota_icms_st' => $aliquotas_st[$i],
                        'valor_icms_st' => $valores_icms_st[$i],
                        'total_item' => $totais[$i],
                        'cst' => $cst[$i],
                        'valor_ipi' => isset($valores_ipi[$i]) ? str_replace(',', '.', $valores_ipi[$i]) : '0.00'
                    );

                    $this->FaturamentoEntrada_model->add('faturamento_entrada_itens', $item);

                    // Adicionar quantidade ao estoque
                    $produto = $this->Produtos_model->getById($produtos[$i]);
                    if ($produto) {
                        $estoque_atual = $produto->estoque + $quantidades[$i];
                        $this->Produtos_model->edit('produtos', array('estoque' => $estoque_atual), 'idProdutos', $produtos[$i]);
                    }
                }
            }

            if ($this->FaturamentoEntrada_model->edit('faturamento_entrada', $data, 'id', $id) == true) {
                $this->session->set_flashdata('success', 'Nota fiscal editada com sucesso!');
                redirect(base_url() . 'index.php/faturamentoEntrada');
            } else {
                $this->data['custom_error'] = '<div class="alert alert-danger">Ocorreu um erro ao tentar editar a nota fiscal.</div>';
            }
        }

        $this->data['faturamento'] = $this->FaturamentoEntrada_model->getById($id);
        if (!$this->data['faturamento']) {
            $this->session->set_flashdata('error', 'Nota fiscal não encontrada.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
        }

        // Mapear campos para a view com valores padrão
        $faturamento = $this->data['faturamento'];
        $faturamento->numero_nfe = isset($faturamento->numero_nota) ? $faturamento->numero_nota : '';
        $faturamento->chave_acesso = isset($faturamento->chave_acesso) ? $faturamento->chave_acesso : '';
        $faturamento->data_entrada = isset($faturamento->data_entrada) ? $faturamento->data_entrada : date('Y-m-d');
        $faturamento->data_emissao = isset($faturamento->data_emissao) ? $faturamento->data_emissao : date('Y-m-d');
        $faturamento->despesas = isset($faturamento->valor_outras_despesas) ? $faturamento->valor_outras_despesas : '0.00';
        $faturamento->frete = isset($faturamento->valor_frete) ? $faturamento->valor_frete : '0.00';
        $faturamento->total_base_icms = isset($faturamento->total_base_icms) ? $faturamento->total_base_icms : '0.00';
        $faturamento->total_icms = isset($faturamento->valor_icms) ? $faturamento->valor_icms : '0.00';
        $faturamento->total_base_icms_st = isset($faturamento->total_base_icms_st) ? $faturamento->total_base_icms_st : '0.00';
        $faturamento->total_icms_st = isset($faturamento->total_icms_st) ? $faturamento->total_icms_st : '0.00';
        $faturamento->total_nota = isset($faturamento->valor_total) ? $faturamento->valor_total : '0.00';
        $faturamento->observacoes = isset($faturamento->observacoes) ? $faturamento->observacoes : '';

        $this->data['faturamento'] = $faturamento;
        $this->data['itens'] = $this->FaturamentoEntrada_model->getItens($id);
        $this->data['operacoes'] = $this->OperacaoComercial_model->get('operacao_comercial', '*');
        $this->data['fornecedores'] = $this->FaturamentoEntrada_model->getFornecedores();
        $this->data['produtos'] = $this->Produtos_model->get('produtos', '*');

        $this->data['view'] = 'faturamento_entrada/editarFaturamentoEntrada';
        return $this->layout();
    }

    public function visualizar($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar faturamento de entrada.');
            redirect(base_url());
        }

        if ($id == null) {
            $this->session->set_flashdata('error', 'Faturamento de entrada não encontrado.');
            redirect(base_url() . 'index.php/faturamentoEntrada/');
        }

        $this->data['faturamento'] = $this->FaturamentoEntrada_model->getById($id);
        
        // Buscar nome da operação comercial
        if ($this->data['faturamento'] && $this->data['faturamento']->operacao_comercial_id) {
            $operacao = $this->OperacaoComercial_model->getById($this->data['faturamento']->operacao_comercial_id);
            if ($operacao) {
                $this->data['faturamento']->nome_operacao = $operacao->nome_operacao;
            } else {
                $this->data['faturamento']->nome_operacao = 'Operação não encontrada';
            }
        } else {
            $this->data['faturamento']->nome_operacao = 'Não definida';
        }

        // Buscar nome do fornecedor
        if ($this->data['faturamento'] && $this->data['faturamento']->fornecedor_id) {
            $fornecedor = $this->Clientes_model->getById($this->data['faturamento']->fornecedor_id);
            if ($fornecedor) {
                $this->data['faturamento']->nome_fornecedor = $fornecedor->nomeCliente;
            } else {
                $this->data['faturamento']->nome_fornecedor = 'Fornecedor não encontrado';
            }
        } else {
            $this->data['faturamento']->nome_fornecedor = 'Não definido';
        }

        // Mapear campos do banco para a view
        $this->data['faturamento']->numero_nfe = $this->data['faturamento']->numero_nota ?? 'Não informado';
        $this->data['faturamento']->chave_acesso = $this->data['faturamento']->chave_acesso ?? 'Não informada';
        $this->data['faturamento']->data_entrada = $this->data['faturamento']->data_entrada ?? date('Y-m-d');
        $this->data['faturamento']->data_emissao = $this->data['faturamento']->data_emissao ?? date('Y-m-d');
        $this->data['faturamento']->total_nota = $this->data['faturamento']->valor_total ?? 0;
        $this->data['faturamento']->despesas = $this->data['faturamento']->valor_outras_despesas ?? 0;
        $this->data['faturamento']->frete = $this->data['faturamento']->valor_frete ?? 0;
        $this->data['faturamento']->total_base_icms = $this->data['faturamento']->total_base_icms ?? 0;
        $this->data['faturamento']->total_icms = $this->data['faturamento']->valor_icms ?? 0;
        $this->data['faturamento']->total_base_icms_st = $this->data['faturamento']->total_base_icms_st ?? 0;
        $this->data['faturamento']->total_icms_st = $this->data['faturamento']->total_icms_st ?? 0;

        // Buscar itens e adicionar nome do produto
        $this->data['itens'] = $this->FaturamentoEntrada_model->getItens($id);
        if ($this->data['itens']) {
            foreach ($this->data['itens'] as $item) {
                if ($item->produto_id) {
                    $produto = $this->Produtos_model->getById($item->produto_id);
                    if ($produto) {
                        $item->nome_produto = $produto->descricao;
                    } else {
                        $item->nome_produto = 'Produto não encontrado';
                    }
                } else {
                    $item->nome_produto = 'Não definido';
                }
            }
        }

        $this->data['view'] = 'faturamento_entrada/visualizarFaturamentoEntrada';
        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturamentoEntrada')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir faturamento de entrada.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir faturamento de entrada: ID não informado.');
            redirect(base_url() . 'index.php/faturamentoEntrada/');
        }

        try {
            $this->db->trans_begin();

            // Verificar se o faturamento existe
            $faturamento = $this->FaturamentoEntrada_model->getById($id);
            if (!$faturamento) {
                throw new Exception('Faturamento não encontrado com ID: ' . $id);
            }

            // Buscar itens antes de excluir
            $itens = $this->FaturamentoEntrada_model->getItens($id);
            if ($itens === false) {
                $db_error = $this->db->error();
                throw new Exception('Erro ao buscar itens do faturamento: ' . $db_error['message']);
            }

            // Primeiro excluir os itens
            $this->db->where('faturamento_entrada_id', $id);
            if (!$this->db->delete('faturamento_entrada_itens')) {
                $db_error = $this->db->error();
                throw new Exception('Erro ao excluir itens do faturamento: ' . $db_error['message'] . ' (Código: ' . $db_error['code'] . ')');
            }

            // Depois excluir o faturamento
            $this->db->where('id', $id);
            if (!$this->db->delete('faturamento_entrada')) {
                $db_error = $this->db->error();
                throw new Exception('Erro ao excluir faturamento: ' . $db_error['message'] . ' (Código: ' . $db_error['code'] . ')');
            }

            // Atualizar estoque dos produtos
            if ($itens) {
            foreach ($itens as $item) {
                if ($item->produto_id) {
                    $produto = $this->Produtos_model->getById($item->produto_id);
                    if ($produto) {
                        $estoque_atual = $produto->estoque - $item->quantidade;
                            if ($estoque_atual < 0) {
                                $estoque_atual = 0;
                            }
                            if (!$this->Produtos_model->edit('produtos', array('estoque' => $estoque_atual), 'idProdutos', $item->produto_id)) {
                                $db_error = $this->db->error();
                                throw new Exception('Erro ao atualizar estoque do produto ID ' . $item->produto_id . ': ' . $db_error['message']);
                            }
                        }
                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $db_error = $this->db->error();
                throw new Exception('Erro na transação: ' . $db_error['message'] . ' (Código: ' . $db_error['code'] . ')');
            }

            $this->db->trans_commit();
            $this->session->set_flashdata('success', 'Faturamento de entrada excluído com sucesso!');
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Erro ao excluir faturamento de entrada ID ' . $id . ': ' . $e->getMessage());
            $this->session->set_flashdata('error', 'Erro ao tentar excluir faturamento de entrada. Detalhe: ' . $e->getMessage());
        }

        redirect(base_url() . 'index.php/faturamentoEntrada/');
    }

    public function autoCompleteFornecedor()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->db->select('idClientes as id, nomeCliente as label, documento, telefone, fornecedor');
            $this->db->from('clientes');
            $this->db->where_in('fornecedor', [1, 3]);
            $this->db->group_start();
            $this->db->like('LOWER(nomeCliente)', $q);
            $this->db->or_like('LOWER(documento)', $q);
            $this->db->or_like('telefone', $q);
            $this->db->group_end();
            $this->db->limit(10);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $result = $query->result();
                foreach ($result as &$row) {
                    $tipo = $row->fornecedor == 3 ? 'Transportadora' : 'Fornecedor';
                    $row->label = $row->label . ' | ' . $tipo . ' | Documento: ' . $row->documento . ' | Telefone: ' . $row->telefone;
                }
            } else {
                $result = [['id' => null, 'label' => 'Adicionar fornecedor...']];
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }

    public function autoCompleteTransportadora()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->db->select('idClientes as id, nomeCliente as label, documento, telefone');
            $this->db->from('clientes');
            $this->db->where('fornecedor', 3); // Apenas transportadoras
            $this->db->group_start();
            $this->db->like('LOWER(nomeCliente)', $q);
            $this->db->or_like('LOWER(documento)', $q);
            $this->db->or_like('telefone', $q);
            $this->db->group_end();
            $this->db->limit(10);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $result = $query->result();
                foreach ($result as &$row) {
                    $row->label = $row->label . ' | Transportadora | Documento: ' . $row->documento . ' | Telefone: ' . $row->telefone;
                }
            } else {
                $result = [['id' => null, 'label' => 'Adicionar transportadora...']];
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }

    public function getTributacao()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar faturamento de entrada.');
            redirect(base_url());
        }

        $operacao_id = $this->input->get('operacao_id');
        $fornecedor_id = $this->input->get('fornecedor_id');

        log_message('debug', 'GET Params: ' . json_encode($_GET));

        if (!$operacao_id || !$fornecedor_id) {
            echo json_encode(['error' => 'Parâmetros inválidos']);
            return;
        }

        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('Fornecedor_model');

        $fornecedor = $this->Fornecedor_model->getById($fornecedor_id);
        if (!$fornecedor) {
            echo json_encode(['error' => 'Fornecedor não encontrado']);
            return;
        }

        $natureza_contribuinte = $fornecedor->natureza_contribuinte ?? 'nao_inscrito';
        $destinacao = $fornecedor->estado === $this->Emitente_model->get()->uf ? 'estadual' : 'interestadual';
        $objetivo_comercial = $fornecedor->objetivo_comercial ?? 'REVENDA';

        log_message('debug', 'Parâmetros de busca: ' . json_encode([
            'operacao_id' => $operacao_id,
            'natureza_contribuinte' => $natureza_contribuinte,
            'destinacao' => $destinacao,
            'objetivo_comercial' => $objetivo_comercial
        ]));

        $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
            $operacao_id,
            $natureza_contribuinte,
            $destinacao,
            $objetivo_comercial
        );

        if (!$tributacao) {
            $error = sprintf(
                'Não foi possível encontrar a tributação para esta operação/fornecedor. Parâmetros utilizados: Operação ID: %s, Natureza Contribuinte: %s, Destinação: %s, Objetivo Comercial: %s, Fornecedor: %s (%s), Emitente UF: %s. Verifique se a tributação está configurada corretamente.',
                $operacao_id,
                $natureza_contribuinte,
                $destinacao,
                $objetivo_comercial,
                $fornecedor->nome,
                $fornecedor->estado,
                $this->Emitente_model->get()->uf
            );
            echo json_encode(['error' => $error]);
            return;
        }

        $aliquota = $this->ClassificacaoFiscal_model->getAliquota(
            $tributacao->cst,
            $destinacao,
            $natureza_contribuinte
        );

        if (!$aliquota) {
            $error = sprintf(
                'Não foi possível encontrar a alíquota para CST %s, Destinação %s e Natureza Contribuinte %s',
                $tributacao->cst,
                $destinacao,
                $natureza_contribuinte
            );
            echo json_encode(['error' => $error]);
            return;
        }

        $response = [
            'cst' => $tributacao->cst,
            'cfop' => $tributacao->cfop,
            'aliquota_icms' => $aliquota->aliquota_icms,
            'aliquota_icms_st' => $aliquota->aliquota_icms_st,
            'natureza_contribuinte' => $natureza_contribuinte,
            'destinacao' => $destinacao,
            'objetivo_comercial' => $objetivo_comercial
        ];

        echo json_encode($response);
    }

    public function autoCompleteProduto()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->db->select('idProdutos as id, descricao as label, codDeBarra, precoCompra as preco, estoque, unidade, precoCompra');
            $this->db->from('produtos');
            $this->db->where('estoque >', 0);
            $this->db->group_start();
            $this->db->like('LOWER(descricao)', $q);
            $this->db->or_like('LOWER(codDeBarra)', $q);
            $this->db->or_like('LOWER(unidade)', $q);
            $this->db->group_end();
            $this->db->limit(10);
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
                $result = $query->result();
                foreach ($result as &$row) {
                    $row->label = sprintf(
                        '%s | Código: %s | Preço Compra: R$ %s | Estoque: %s %s',
                        $row->label,
                        $row->codDeBarra,
                        number_format($row->precoCompra, 2, ',', '.'),
                        $row->estoque,
                        $row->unidade
                    );
                }
            } else {
                $result = [['id' => null, 'label' => 'Nenhum produto encontrado...']];
            }
            
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }
    }

    public function monitorarNotas()
    {
        try {
            // Get search parameters
            $search = $this->input->get('search');
            $type = $this->input->get('type') ?: 'recebidas';
            $nsuEspecifico = $this->input->get('nsu');
            
            // Get last NSU from database apenas para a primeira consulta
            $lastNsu = $this->NfeNsu_model->getLastNsu();
            $ultNSU = $nsuEspecifico ?: ($lastNsu ? $lastNsu->ult_nsu : '000000000000000');

            log_message('debug', "Iniciando consulta com ultNSU inicial: {$ultNSU}");

            // Get digital certificate from database
            $certificate = $this->Nfe_model->getCertificate();
            if (!$certificate) {
                throw new Exception('Certificado digital não encontrado. Configure o certificado nas configurações do sistema.');
            }

            // Get emitente data
            $emitente = $this->Mapos_model->getEmitente();
            if (!$emitente) {
                throw new Exception('Dados do emitente não encontrados. Configure o emitente nas configurações do sistema.');
            }

            // Validar dados do emitente
            if (empty($emitente->nome) || empty($emitente->cnpj) || empty($emitente->uf)) {
                throw new Exception('Dados do emitente incompletos. Verifique se nome, CNPJ e UF estão preenchidos nas configurações do sistema.');
            }

            // Configure NFePHP
            $config = [
                'atualizacao' => date('Y-m-d H:i:s'),
                'tpAmb' => 1, // 1-Produção
                'razaosocial' => $emitente->nome,
                'cnpj' => preg_replace('/[^0-9]/', '', $emitente->cnpj),
                'siglaUF' => $emitente->uf,
                'schemes' => 'PL_009_V4',
                'versao' => '4.00',
                'tokenIBPT' => '',
                'csc' => '',
                'CSCid' => '',
                'proxyConf' => [
                    'proxyIp' => '',
                    'proxyPort' => '',
                    'proxyUser' => '',
                    'proxyPass' => ''
                ]
            ];

            $configJson = json_encode($config);

            try {
                // Load certificate using NFePHP Certificate class
                $certData = Certificate::readPfx($certificate->certificado_digital, $certificate->senha_certificado);
                log_message('debug', 'Certificado carregado com sucesso');
            } catch (Exception $e) {
                log_message('error', 'Erro ao carregar certificado: ' . $e->getMessage());
                throw new Exception('Erro ao carregar certificado digital: ' . $e->getMessage());
            }

            try {
                // Initialize NFePHP - Always use production environment
                $tools = new Tools($configJson, $certData);
                $tools->model('55');
                $tools->setEnvironment(1); // 1-Produção
                
                log_message('debug', 'NFePHP Tools inicializado com sucesso em ambiente de produção');
            } catch (\Exception $e) {
                log_message('error', 'Erro ao inicializar NFePHP Tools: ' . $e->getMessage());
                throw new Exception('Erro ao inicializar NFePHP: ' . $e->getMessage());
            }

            // Query SEFAZ based on type
            $response = null;
            $notas = [];
            $maxNSU = '000000000000000';
            $loopLimit = $nsuEspecifico ? 1 : 50; // Se for NSU específico, faz apenas uma consulta
            $iCount = 0;
            $progress = 0;
            
            try {
                do {
                    $iCount++;
                    if ($iCount >= $loopLimit) {
                        log_message('error', 'Limite de loops atingido ao consultar SEFAZ');
                        break;
                    }

                    log_message('debug', "Consultando SEFAZ - ultNSU: {$ultNSU}");
                    
                    try {
                        $resp = $tools->sefazDistDFe($ultNSU);
                        if (!$resp) {
                            throw new Exception("Resposta vazia da SEFAZ");
                        }
                        
                        $st = new Standardize();
                        $std = $st->toStd($resp);
                        
                        // Log detalhado da resposta da SEFAZ
                        log_message('debug', 'Resposta SEFAZ: ' . json_encode([
                            'cStat' => $std->cStat ?? 'não informado',
                            'xMotivo' => $std->xMotivo ?? 'não informado',
                            'ultNSU' => $std->ultNSU ?? 'não informado',
                            'maxNSU' => $std->maxNSU ?? 'não informado'
                        ]));
                        
                        if (!isset($std->cStat)) {
                            throw new Exception("Resposta inválida da SEFAZ: cStat não encontrado");
                        }

                        // Atualiza NSUs com os valores retornados pela SEFAZ
                        $maxNSU = $std->maxNSU ?? $maxNSU;
                        $ultNSU = $std->ultNSU;

                        // Calcula o progresso
                        if ($maxNSU != $lastNsu->ult_nsu) {
                            $progress = ($ultNSU - $lastNsu->ult_nsu) / ($maxNSU - $lastNsu->ult_nsu) * 100;
                            $progress = min(100, max(0, $progress)); // Garante que fique entre 0 e 100
                        } else {
                            $progress = 100; // Se já atingiu o máximo, considera 100%
                        }

                        // Save NSU information to database
                        $nsuData = [
                            'ult_nsu' => $ultNSU,
                            'max_nsu' => $maxNSU,
                            'data_consulta' => date('Y-m-d H:i:s')
                        ];
                        
                        if ($lastNsu) {
                            $this->NfeNsu_model->update($lastNsu->id, $nsuData);
                        } else {
                            $this->NfeNsu_model->add($nsuData);
                        }

                        // Verifica se há documentos apenas se o status for 138 (Documentos localizados)
                        if ($std->cStat == 138) {
                            // Verifica se existe o lote e se é um array ou objeto
                            if (!isset($std->loteDistDFeInt) || !isset($std->loteDistDFeInt->docZip)) {
                                log_message('debug', 'Nenhum documento encontrado neste lote');
                                continue;
                            }

                            // Garante que docZip seja um array
                            $docs = is_array($std->loteDistDFeInt->docZip) 
                                ? $std->loteDistDFeInt->docZip 
                                : [$std->loteDistDFeInt->docZip];
                            
                            // Process each document in the current batch
                            foreach ($docs as $doc) {
                                try {
                                    // Log do NSU do documento atual
                                    log_message('debug', 'Processando documento com NSU: ' . ($doc->NSU ?? 'não informado'));
                                    
                                    // Verifica se o documento tem o conteúdo
                                    if (!isset($doc->$content)) {
                                        log_message('error', 'Documento sem conteúdo');
                                        continue;
                                    }
                                    
                                    // Decodifica o conteúdo base64 primeiro
                                    $compressedContent = base64_decode($doc->$content);
                                    if ($compressedContent === false) {
                                        log_message('error', 'Erro ao decodificar conteúdo base64');
                                        continue;
                                    }

                                    // Tenta descompactar usando gzdecode (Gzip)
                                    $xmlContent = gzdecode($compressedContent);
                                    if ($xmlContent === false) {
                                        log_message('error', 'Erro ao descompactar conteúdo Gzip');
                                        continue;
                                    }

                                    // Verifica se o XML é válido
                                    if (!$this->isValidXML($xmlContent)) {
                                        log_message('error', 'XML inválido após descompactação');
                                        continue;
                                    }
                                    
                                    try {
                                        $nfeXml = new \SimpleXMLElement($xmlContent);
                                        
                                        // Get NFe namespace
                                        $nfeNs = $nfeXml->getNamespaces(true)[''];
                                        
                                        // Get NFe data
                                        $ide = $nfeXml->NFe->infNFe->ide;
                                        $emit = $nfeXml->NFe->infNFe->emit;
                                        $total = $nfeXml->NFe->infNFe->total;
                                        
                                        // Format date
                                        $dataEmissao = date('Y-m-d', strtotime($ide->dhEmi));
                                        
                                        // Get values
                                        $valor = isset($total->ICMSTot->vNF) ? (float)$total->ICMSTot->vNF : 0.00;
                                        $valorIpi = isset($total->ICMSTot->vIPI) ? (float)$total->ICMSTot->vIPI : 0.00;
                                        $chave = isset($nfeXml->NFe->infNFe->attributes()->Id) ? (string)$nfeXml->NFe->infNFe->attributes()->Id : '';
                                        $chave = str_replace('NFe', '', $chave);
                                        $numero = isset($ide->nNF) ? (string)$ide->nNF : '';
                                        $serie = isset($ide->serie) ? (string)$ide->serie : '';
                                        $dataEmissao = isset($ide->dhEmi) ? (string)$ide->dhEmi : '';
                                        $dataEmissao = date('d/m/Y', strtotime($dataEmissao));

                                        // Process products
                                        $produtos = [];
                                        foreach ($nfeXml->NFe->infNFe->det as $det) {
                                            $prod = $det->prod;
                                            $imposto = $det->imposto;
                                            
                                            // Get IPI value from IPITrib
                                            $valorIpiProduto = 0.00;
                                            if (isset($imposto->ipI->ipITrib)) {
                                                $valorIpiProduto = isset($imposto->ipI->ipITrib->vIPI) ? (float)$imposto->ipI->ipITrib->vIPI : 0.00;
                                            }

                                            $produtos[] = [
                                                'descricao' => (string)$prod->xProd,
                                                'quantidade' => (float)$prod->qCom,
                                                'valor_unitario' => number_format((float)$prod->vUnCom, 4, ',', '.'),
                                                'valor_total' => (float)$prod->vProd,
                                                'cfop' => (string)$prod->cfop,
                                                'cst' => isset($imposto->ICMS->ICMS00->cst) ? (string)$imposto->ICMS->ICMS00->cst : '',
                                                'base_icms' => isset($imposto->ICMS->ICMS00->vBC) ? (float)$imposto->ICMS->ICMS00->vBC : 0.00,
                                                'aliquota_icms' => isset($imposto->ICMS->ICMS00->pICMS) ? (float)$imposto->ICMS->ICMS00->pICMS : 0.00,
                                                'valor_icms' => isset($imposto->ICMS->ICMS00->vICMS) ? (float)$imposto->ICMS->ICMS00->vICMS : 0.00,
                                                'base_icms_st' => isset($imposto->ICMS->ICMS00->vBCST) ? (float)$imposto->ICMS->ICMS00->vBCST : 0.00,
                                                'aliquota_icms_st' => isset($imposto->ICMS->ICMS00->pMVAST) ? (float)$imposto->ICMS->ICMS00->pMVAST : 0.00,
                                                'valor_icms_st' => isset($imposto->ICMS->ICMS00->vICMSST) ? (float)$imposto->ICMS->ICMS00->vICMSST : 0.00,
                                                'valor_ipi' => $valorIpiProduto,
                                                'desconto' => isset($prod->vDesc) ? (float)$prod->vDesc : 0.00,
                                                'ncm' => isset($prod->NCM) ? (string)$prod->NCM : '',
                                                'codigo' => isset($prod->cProd) ? (string)$prod->cProd : ''
                                            ];
                                        }

                                        // Store new NFe data
                                        $nfeData = [
                                            'fornecedor' => [
                                                'nome' => $emitente->xNome,
                                                'documento' => $emitente->cnpj,
                                                'dados_completos' => [
                                                    'nome' => $emitente->xNome,
                                                    'cnpj' => $emitente->cnpj,
                                                    'endereco' => [
                                                        'logradouro' => $emitente->enderEmit->xLgr,
                                                        'numero' => $emitente->enderEmit->nro,
                                                        'complemento' => isset($emitente->enderEmit->xCpl) ? $emitente->enderEmit->xCpl : '',
                                                        'bairro' => $emitente->enderEmit->xBairro,
                                                        'cidade' => $emitente->enderEmit->xMun,
                                                        'uf' => $emitente->enderEmit->uf,
                                                        'cep' => $emitente->enderEmit->cep
                                                    ]
                                                ]
                                            ],
                                            'numero_nfe' => $numero,
                                            'serie' => $serie,
                                            'data_emissao' => $dataEmissao,
                                            'chave_acesso' => $chave,
                                            'valor_total' => $valor,
                                            'valor_ipi' => $valorIpi,
                                            'produtos' => $produtos
                                        ];
                                        
                                        // Check if NFe already exists in database
                                        $existingNfe = $this->NfeMonitoradas_model->getByChaveAcesso($chave);
                                        if (!$existingNfe) {
                                            // Store new NFe in database with the actual XML content
                                            $nfeData = [
                                                'chave_acesso' => $chave,
                                                'numero' => (string)$ide->nNF,
                                                'serie' => (string)$ide->serie,
                                                'fornecedor' => (string)$emit->xNome,
                                                'data_emissao' => $dataEmissao,
                                                'valor' => $valor,
                                                'valor_ipi' => $valorIpi,
                                                'xml' => $xmlContent,
                                                'processada' => 0
                                            ];
                                            
                                            $this->NfeMonitoradas_model->add($nfeData);
                                            
                                            log_message('debug', "Nova NFe armazenada: Chave {$chave}, Fornecedor {$nfeData['fornecedor']}, Número {$nfeData['numero']}");
                                        } else {
                                            log_message('debug', "NFe já existe no banco: Chave {$chave}");
                                        }
                                        
                                        // Create nota object for response
                                        $nota = [
                                            'fornecedor' => (string)$emit->xNome,
                                            'numero' => (string)$ide->nNF,
                                            'serie' => (string)$ide->serie,
                                            'dataEmissao' => date('d/m/Y', strtotime($dataEmissao)),
                                            'valor' => number_format($valor, 2, ',', '.'),
                                            'chave' => $chave,
                                            'xml' => base64_encode($xmlContent) // Keep base64 encoding for response
                                        ];
                                        
                                        // Apply search filter if provided
                                        if ($search) {
                                            $searchLower = strtolower($search);
                                            if (strpos(strtolower($nota['fornecedor']), $searchLower) === false &&
                                                strpos(strtolower($nota['numero']), $searchLower) === false &&
                                                strpos(strtolower($nota['chave']), $searchLower) === false) {
                                                continue;
                                            }
                                        }
                                        
                                        $notas[] = $nota;
                                    } catch (\Exception $e) {
                                        log_message('error', 'Erro ao processar XML: ' . $e->getMessage());
                                        continue;
                                    }
                                } catch (\Exception $e) {
                                    log_message('error', 'Erro ao processar documento: ' . $e->getMessage());
                                    continue;
                                }
                            }
                        } else if ($std->cStat == 137) { // Nenhum documento localizado
                            log_message('debug', 'Nenhum documento localizado para o NSU atual');
                            break;
                        } else {
                            throw new Exception("Erro na consulta: " . ($std->xMotivo ?? 'Motivo não informado'));
                        }
                        
                        // If we've reached the maxNSU, we're done
                        if ($ultNSU >= $maxNSU) {
                            log_message('debug', 'Consulta SEFAZ concluída - ultNSU atingiu maxNSU');
                            break;
                        }
                        
                        // Add a small delay between requests to avoid overwhelming the server
                        usleep(500000); // 500ms delay
                        
                    } catch (\Exception $e) {
                        log_message('error', 'Erro na consulta SEFAZ: ' . $e->getMessage());
                        throw $e;
                    }
                    
                } while ($ultNSU < $maxNSU && !$nsuEspecifico); // Se for NSU específico, não continua o loop
                
                log_message('debug', 'Consulta SEFAZ finalizada com sucesso');
                
            } catch (\Exception $e) {
                log_message('error', 'Erro ao consultar SEFAZ: ' . $e->getMessage());
                throw new Exception('Erro ao consultar SEFAZ: ' . $e->getMessage());
            }

            // Return response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'notas' => $notas,
                    'message' => count($notas) === 0 ? 'Consultando notas... Aguarde. Progresso: ' . number_format($progress, 1) . '%' : null,
                    'debug' => [
                        'ultNSU' => $ultNSU,
                        'maxNSU' => $maxNSU,
                        'total_notas' => count($notas),
                        'progress' => $progress,
                        'nsu_especifico' => $nsuEspecifico
                    ]
                ]));

        } catch (Exception $e) {
            log_message('error', 'Erro ao monitorar notas: ' . $e->getMessage());
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Erro ao monitorar notas: ' . $e->getMessage(),
                    'debug' => [
                        'ultNSU' => $ultNSU ?? null,
                        'maxNSU' => $maxNSU ?? null,
                        'error' => $e->getMessage()
                    ]
                ]));
        }
    }

    /**
     * Verifica se uma string é um XML válido
     * @param string $xml
     * @return bool
     */
    private function isValidXML($xml) {
        if (empty($xml)) {
            return false;
        }
        
        try {
            $doc = new \DOMDocument();
            $doc->loadXML($xml, LIBXML_NOERROR | LIBXML_NOWARNING);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFornecedorInfo()
    {
        $fornecedor_id = $this->input->get('fornecedor_id');
        
        if (!$fornecedor_id) {
            echo json_encode(['success' => false, 'message' => 'ID do fornecedor não informado']);
            return;
        }
        
        $fornecedor = $this->Clientes_model->getById($fornecedor_id);
        
        if (!$fornecedor) {
            echo json_encode(['success' => false, 'message' => 'Fornecedor não encontrado']);
            return;
        }

        // Get emitter information
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            echo json_encode(['success' => false, 'message' => 'Dados do emitente não encontrados']);
            return;
        }

        // Determine destinação based on state comparison
        $destinacao = ($fornecedor->estado === $emitente->uf) ? 'estadual' : 'interestadual';
        
        echo json_encode([
            'success' => true,
            'natureza_contribuinte' => $fornecedor->natureza_contribuinte ?? 'nao_inscrito',
            'destinacao' => $destinacao,
            'estado_fornecedor' => $fornecedor->estado,
            'uf_emitente' => $emitente->uf
        ]);
    }

    public function getFornecedorEstado($id = null)
    {
        if ($id == null) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'ID do fornecedor não informado']);
            return;
        }

        $this->load->model('Clientes_model');
        $fornecedor = $this->Clientes_model->get('clientes', '*', array('idClientes' => $id));

        if (!$fornecedor) {
            $this->output->set_status_header(404);
            echo json_encode(['success' => false, 'message' => 'Fornecedor não encontrado']);
            return;
        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'estado' => $fornecedor[0]->estado
            ]));
    }

    public function processarXML()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para importar XML.');
            redirect(base_url());
        }

        $xmlContent = $this->input->post('xml_content');
        if (empty($xmlContent)) {
            echo json_encode(['success' => false, 'message' => 'Nenhum conteúdo XML fornecido']);
            return;
        }

        // Limpar o conteúdo XML
        $xmlContent = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', '', $xmlContent);
        $xmlContent = trim($xmlContent);

        // Converter caracteres especiais
        $xmlContent = html_entity_decode($xmlContent, ENT_QUOTES | ENT_XML1, 'UTF-8');
        
        // Remover caracteres inválidos
        $xmlContent = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}\x{10000}-\x{10FFFF}]/u', '', $xmlContent);

        // Se não começar com <?xml, adicionar a declaração XML
        if (!str_starts_with($xmlContent, '<?xml')) {
            $xmlContent = '<?xml version="1.0" encoding="UTF-8"?>' . $xmlContent;
        }

        // Armazenar o XML na sessão usando set_userdata em vez de flashdata
        $this->session->set_userdata('xml_content', $xmlContent);

        // Log para debug
        log_message('debug', 'XML armazenado na sessão: ' . substr($xmlContent, 0, 100) . '...');

        try {
            // Carregar XML com tratamento de erros
            libxml_use_internal_errors(true);
            $xml = simplexml_load_string($xmlContent, 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);
            
            if ($xml === false) {
                $errors = libxml_get_errors();
                $errorMessage = 'Erro ao processar XML: ';
                foreach ($errors as $error) {
                    $errorMessage .= $error->message . ' ';
                }
                libxml_clear_errors();
                throw new Exception($errorMessage);
            }

            // Verificar se é uma NFe ou nfeProc
            if ($xml->getName() == 'nfeProc') {
                $nfeXml = $xml->NFe;
            } else if ($xml->getName() == 'NFe') {
                $nfeXml = $xml;
            } else {
                echo json_encode(['success' => false, 'message' => 'Tipo de XML não suportado. O arquivo deve ser uma NFe ou nfeProc.']);
                return;
            }

            $infNfe = $nfeXml->infNFe;
            
            // Dados do emitente (fornecedor)
            $emit = $infNfe->emit;
            $documento = isset($emit->cnpj) ? (string)$emit->cnpj : (string)$emit->cpf;
            $nomeFornecedor = (string)$emit->xNome;
            
            // Capturar dados completos do emitente para criação do fornecedor
            $dadosCompletosEmit = (object)[
                'enderEmit' => (object)[
                    'xLgr' => (string)$emit->enderEmit->xLgr,
                    'nro' => (string)$emit->enderEmit->nro,
                    'xCpl' => isset($emit->enderEmit->xCpl) ? (string)$emit->enderEmit->xCpl : '',
                    'xBairro' => (string)$emit->enderEmit->xBairro,
                    'cMun' => (string)$emit->enderEmit->cMun,
                    'xMun' => (string)$emit->enderEmit->xMun,
                    'uf' => (string)$emit->enderEmit->uf,
                    'cep' => (string)$emit->enderEmit->cep,
                    'cPais' => (string)$emit->enderEmit->cPais,
                    'xPais' => (string)$emit->enderEmit->xPais
                ],
                'fone' => (string)$emit->fone,
                'ie' => (string)$emit->ie,
                'IEST' => isset($emit->ieST) ? (string)$emit->ieST : '',
                'IM' => (string)$emit->IM,
                'CNAE' => (string)$emit->CNAE,
                'CRT' => (string)$emit->CRT,
                'cMunFG' => (string)$infNfe->ide->cMunFG
            ];
            
            // Verificar se o fornecedor existe (sem criar)
            $fornecedor = $this->verificarFornecedor($documento, $nomeFornecedor);
            
            // Adicionar dados completos ao fornecedor
            $fornecedor['dados_completos'] = $dadosCompletosEmit;
            $fornecedor['documento'] = $documento;
            $fornecedor['ibge'] = (string)$infNfe->ide->cMunFG;
            
            // Dados da NFe
            $ide = $infNfe->ide;
            $numeroNfe = (string)$ide->nNF;
            $chaveAcesso = str_replace('NFe', '', (string)$infNfe->attributes()->Id);
            $dataEmissao = date('d/m/Y', strtotime((string)$ide->dhEmi));
            
            // Valor total
            $total = $infNfe->total->ICMSTot;
            $valorTotal = isset($total->vNF) ? number_format((float)$total->vNF, 2, ',', '.') : '0,00';
            $valorProdutos = isset($total->vProd) ? number_format((float)$total->vProd, 2, ',', '.') : '0,00';
            $valorIpi = isset($total->vIPI) ? number_format((float)$total->vIPI, 2, ',', '.') : '0,00';

            // --- PESOS E VOLUMES ---
            $pesoBruto = '0.000';
            $pesoLiquido = '0.000';
            $volume = '0.000';

            // Buscar pesos e volumes no XML
            if (isset($infNfe->transp->vol)) {
                $volumes = $infNfe->transp->vol;
                if (is_array($volumes)) {
                    foreach ($volumes as $vol) {
                        if (isset($vol->pesoB)) {
                            $pesoBruto = number_format((float)$vol->pesoB, 3, ',', '.');
                        }
                        if (isset($vol->pesoL)) {
                            $pesoLiquido = number_format((float)$vol->pesoL, 3, ',', '.');
                        }
                        if (isset($vol->qVol)) {
                            $volume = number_format((float)$vol->qVol, 3, ',', '.');
                        }
                    }
                } else {
                    if (isset($volumes->pesoB)) {
                        $pesoBruto = number_format((float)$volumes->pesoB, 3, ',', '.');
                    }
                    if (isset($volumes->pesoL)) {
                        $pesoLiquido = number_format((float)$volumes->pesoL, 3, ',', '.');
                    }
                    if (isset($volumes->qVol)) {
                        $volume = number_format((float)$volumes->qVol, 3, ',', '.');
                    }
                }
            }

            // Se não encontrou os pesos no vol, tenta buscar no transp
            if ($pesoBruto == '0.000' && isset($infNfe->transp->pesoB)) {
                $pesoBruto = number_format((float)$infNfe->transp->pesoB, 3, ',', '.');
            }
            if ($pesoLiquido == '0.000' && isset($infNfe->transp->pesoL)) {
                $pesoLiquido = number_format((float)$infNfe->transp->pesoL, 3, ',', '.');
            }

            // Se não encontrou volume, tenta buscar no transp
            if ($volume == '0.000' && isset($infNfe->transp->qVol)) {
                $volume = number_format((float)$infNfe->transp->qVol, 3, ',', '.');
            }

            // Se ainda não encontrou volume, tenta buscar nos produtos
            if ($volume == '0.000') {
                $volumeTotal = 0;
                foreach ($detalhes as $det) {
                    if (isset($det->prod->qVol)) {
                        $volumeTotal += (float)$det->prod->qVol;
                    }
                }
                if ($volumeTotal > 0) {
                    $volume = number_format($volumeTotal, 3, ',', '.');
                }
            }

            // Produtos
            $produtos = [];
            $detalhes = $infNfe->det;
            
            // Garantir que $detalhes seja sempre um array
            if (!isset($detalhes)) {
                $detalhes = [];
            } elseif (!is_array($detalhes) && !($detalhes instanceof Traversable)) {
                $detalhes = [$detalhes];
            }
            
            foreach ($detalhes as $det) {
                $prod = $det->prod;
                $imposto = $det->imposto;
                
                // Conversão do CFOP: trocar primeiro dígito 5->1 e 6->2
                $cfop = (string)$prod->cfop;
                if (!empty($cfop)) {
                    $primeiro_digito = substr($cfop, 0, 1);
                    if ($primeiro_digito == '5') {
                        $cfop = '1' . substr($cfop, 1);
                    } elseif ($primeiro_digito == '6') {
                        $cfop = '2' . substr($cfop, 1);
                    }
                }
                
                $produto = [
                    'codigo' => (string)$prod->cProd,
                    'descricao' => (string)$prod->xProd,
                    'ncm' => (string)$prod->NCM,
                    'cfop' => $cfop,
                    'quantidade' => number_format((float)$prod->qCom, 4, ',', '.'),
                    'valor_unitario' => number_format((float)$prod->vUnCom, 4, ',', '.'),
                    'valor_total' => number_format((float)$prod->vProd, 2, ',', '.'),
                    'desconto' => '0,00'
                ];
                
                // Impostos ICMS
                if (isset($imposto->ICMS)) {
                    $icmsData = null;
                    
                    // Verificar diferentes tipos de ICMS
                    foreach ($imposto->ICMS->children() as $icmsNode) {
                        $icmsData = $icmsNode;
                        break; // Pegar o primeiro nó ICMS encontrado
                    }
                    
                    if ($icmsData) {
                        $produto['cst'] = isset($icmsData->cst) ? (string)$icmsData->cst : (isset($icmsData->CSOSN) ? (string)$icmsData->CSOSN : '00');
                        $produto['base_icms'] = isset($icmsData->vBC) ? number_format((float)$icmsData->vBC, 2, ',', '.') : '0,00';
                        $produto['aliquota_icms'] = isset($icmsData->pICMS) ? number_format((float)$icmsData->pICMS, 2, ',', '.') : '0,00';
                        $produto['valor_icms'] = isset($icmsData->vICMS) ? number_format((float)$icmsData->vICMS, 2, ',', '.') : '0,00';
                        $produto['base_icms_st'] = isset($icmsData->vBCST) ? number_format((float)$icmsData->vBCST, 2, ',', '.') : '0,00';
                        $produto['aliquota_icms_st'] = isset($icmsData->pICMSST) ? number_format((float)$icmsData->pICMSST, 2, ',', '.') : '0,00';
                        $produto['valor_icms_st'] = isset($icmsData->vICMSST) ? number_format((float)$icmsData->vICMSST, 2, ',', '.') : '0,00';
                    } else {
                        // Valores padrão se não encontrar dados de ICMS
                        $produto['cst'] = '00';
                        $produto['base_icms'] = '0,00';
                        $produto['aliquota_icms'] = '0,00';
                        $produto['valor_icms'] = '0,00';
                        $produto['base_icms_st'] = '0,00';
                        $produto['aliquota_icms_st'] = '0,00';
                        $produto['valor_icms_st'] = '0,00';
                    }
                } else {
                    // Valores padrão se não houver seção ICMS
                    $produto['cst'] = '00';
                    $produto['base_icms'] = '0,00';
                    $produto['aliquota_icms'] = '0,00';
                    $produto['valor_icms'] = '0,00';
                    $produto['base_icms_st'] = '0,00';
                    $produto['aliquota_icms_st'] = '0,00';
                    $produto['valor_icms_st'] = '0,00';
                }

                // Extrair valor do IPI
                if (isset($imposto->ipI->ipITrib->vIPI)) {
                    $produto['valor_ipi'] = number_format((float)$imposto->ipI->ipITrib->vIPI, 2, ',', '.');
                } else if (isset($imposto->ipI->ipITrib->vBC) && isset($imposto->ipI->ipITrib->pIPI)) {
                    // Se não tiver o valor direto, calcula baseado na base e alíquota
                    $base_ipi = (float)$imposto->ipI->ipITrib->vBC;
                    $aliquota_ipi = (float)$imposto->ipI->ipITrib->pIPI;
                    $valor_ipi = ($base_ipi * $aliquota_ipi) / 100;
                    $produto['valor_ipi'] = number_format($valor_ipi, 2, ',', '.');
                } else {
                    $produto['valor_ipi'] = '0,00';
                }
                
                $produtos[] = $produto;
            }
            
            // --- NOVO: Processar transportadora ---
            $transporta = isset($infNfe->transp->transporta) ? $infNfe->transp->transporta : null;
            $dadosTransportadora = null;
            if ($transporta) {
                $docTransp = isset($transporta->cnpj) ? (string)$transporta->cnpj : (isset($transporta->cpf) ? (string)$transporta->cpf : '');
                $nomeTransp = (string)$transporta->xNome;
                $dadosCompletosTransp = [
                    'xEnder' => isset($transporta->xEnder) ? (string)$transporta->xEnder : '',
                    'xMun' => isset($transporta->xMun) ? (string)$transporta->xMun : '',
                    'uf' => isset($transporta->uf) ? (string)$transporta->uf : '',
                    'ie' => isset($transporta->ie) ? (string)$transporta->ie : ''
                ];
                // Checar/cadastrar transportadora
                $dadosTransportadora = $this->verificarOuCriarTransportadora($docTransp, $nomeTransp, $dadosCompletosTransp);
                $dadosTransportadora['documento'] = $docTransp;
                $dadosTransportadora['dados_completos'] = $dadosCompletosTransp;
            }
            
            // --- NOVO: Modalidade do Frete e Pesos ---
            $modFrete = isset($infNfe->transp->modFrete) ? (string)$infNfe->transp->modFrete : '';
            
            $dadosXml = [
                'fornecedor' => [
                    'id' => $fornecedor['id'],
                    'nome' => $fornecedor['nome'],
                    'documento' => $documento,
                    'dados_completos' => $fornecedor['dados_completos']
                ],
                'transportadora' => $dadosTransportadora,
                'numero_nfe' => $numeroNfe,
                'chave_acesso' => $chaveAcesso,
                'data_emissao' => $dataEmissao,
                'valor_total' => $valorTotal,
                'valor_produtos' => $valorProdutos,
                'valor_ipi' => $valorIpi,
                'produtos' => $produtos,
                'modalidade_frete' => $modFrete,
                'peso_bruto' => $pesoBruto,
                'peso_liquido' => $pesoLiquido,
                'volume' => $volume
            ];
            
            echo json_encode(['success' => true, 'data' => $dadosXml]);
            
        } catch (Exception $e) {
            log_message('error', 'Erro ao processar XML: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao processar XML: ' . $e->getMessage()]);
        }
    }

    private function verificarOuCriarFornecedor($documento, $nome, $dadosEmit)
    {
        // Formatar CNPJ com máscara
        $cnpjFormatado = substr($documento, 0, 2) . '.' .
                        substr($documento, 2, 3) . '.' .
                        substr($documento, 5, 3) . '/' .
                        substr($documento, 8, 4) . '-' .
                        substr($documento, 12, 2);
        
        // Verificar se já existe um fornecedor com este documento
        $this->db->where('documento', $cnpjFormatado);
        $this->db->where('fornecedor', 1);
        $fornecedor = $this->db->get('clientes')->row();
        
        if ($fornecedor) {
            log_message('debug', 'Fornecedor encontrado: ' . $fornecedor->nomeCliente);
            return [
                'id' => $fornecedor->idClientes,
                'nome' => $fornecedor->nomeCliente
            ];
        }
        
        // Verificar se existe cliente com mesmo documento mas não é fornecedor
        $this->db->where('documento', $cnpjFormatado);
        $cliente = $this->db->get('clientes')->row();
        
        if ($cliente) {
            // Atualizar cliente existente para ser também fornecedor
            $this->db->where('idClientes', $cliente->idClientes);
            $result = $this->db->update('clientes', ['fornecedor' => 1]);
            
            if (!$result) {
                $error = $this->db->error();
                log_message('error', 'Erro ao atualizar cliente para fornecedor: ' . $error['message']);
                throw new Exception('Erro ao atualizar cliente para fornecedor: ' . $error['message']);
            }
            
            log_message('debug', 'Cliente atualizado para fornecedor: ' . $cliente->nomeCliente);
            return [
                'id' => $cliente->idClientes,
                'nome' => $cliente->nomeCliente
            ];
        }
        
        // Validar dados obrigatórios
        if (empty($nome)) {
            throw new Exception('Nome do fornecedor é obrigatório');
        }
        
        if (empty($documento)) {
            throw new Exception('Documento do fornecedor é obrigatório');
        }

        // Validar dados do endereço
        if (!$dadosEmit || !isset($dadosEmit->enderEmit)) {
            throw new Exception('Endereço do fornecedor é obrigatório');
        }

        $endereco = $dadosEmit->enderEmit;

        // Validar campos obrigatórios do endereço
        if (empty($endereco->xLgr)) {
            throw new Exception('Logradouro do fornecedor é obrigatório');
        }

        if (empty($endereco->xMun)) {
            throw new Exception('Cidade do fornecedor é obrigatória');
        }

        if (empty($endereco->uf)) {
            throw new Exception('Estado do fornecedor é obrigatório');
        }

        if (empty($endereco->cep)) {
            throw new Exception('CEP do fornecedor é obrigatório');
        }

        // Tentar obter o código IBGE
        $ibge = null;
        
        // Primeiro tenta usar o cMunFG da NFe
        if (isset($dadosEmit->cMunFG) && !empty($dadosEmit->cMunFG)) {
            $ibge = (string)$dadosEmit->cMunFG;
            log_message('debug', 'IBGE obtido do cMunFG da NFe: ' . $ibge);
        }
        
        // Se não conseguiu pelo cMunFG, tenta buscar pelo CEP
        if (empty($ibge) && isset($endereco->cep)) {
            $ibge = $this->getIBGEFromCEP($endereco->cep);
            if ($ibge) {
                log_message('debug', 'IBGE obtido da API de CEP: ' . $ibge);
            } else {
                log_message('debug', 'Não foi possível obter o IBGE da API de CEP');
            }
        }
        
        $dadosFornecedor = [
            'nomeCliente' => $nome,
            'documento' => $cnpjFormatado, // Usar CNPJ formatado
            'pessoa_fisica' => strlen($documento) == 11 ? 1 : 0,
            'fornecedor' => 1,
            'dataCadastro' => date('Y-m-d'),
            'rua' => (string)$endereco->xLgr,
            'numero' => isset($endereco->nro) ? (string)$endereco->nro : 'S/N',
            'complemento' => isset($endereco->xCpl) ? (string)$endereco->xCpl : '',
            'bairro' => isset($endereco->xBairro) ? (string)$endereco->xBairro : '',
            'cidade' => (string)$endereco->xMun,
            'estado' => (string)$endereco->uf,
            'cep' => (string)$endereco->cep,
            'objetivo_comercial' => 'REVENDA', // Definir objetivo comercial como REVENDA
            'ibge' => $ibge // Usar o IBGE obtido
        ];

        // Adicionar outros dados do emitente
        if ($dadosEmit) {
            $dadosFornecedor['telefone'] = isset($dadosEmit->fone) ? (string)$dadosEmit->fone : '';
            $dadosFornecedor['inscricao'] = isset($dadosEmit->ie) ? (string)$dadosEmit->ie : '';
            $dadosFornecedor['natureza_contribuinte'] = isset($dadosEmit->CRT) ? (string)$dadosEmit->CRT : '';
        }
        
        log_message('debug', 'Tentando criar fornecedor com dados: ' . json_encode($dadosFornecedor));
        
        // Tentar inserir o fornecedor com todos os dados
        try {
            $result = $this->db->insert('clientes', $dadosFornecedor);
        } catch (Exception $e) {
            log_message('error', 'Erro de exceção ao inserir fornecedor: ' . $e->getMessage());
            throw new Exception('Erro ao inserir fornecedor (exceção): ' . $e->getMessage());
        }
        
        if (!$result) {
            $error = $this->db->error();
            log_message('error', 'Erro ao inserir fornecedor no banco: ' . print_r($error, true));
            log_message('error', 'Dados do fornecedor: ' . json_encode($dadosFornecedor));
            throw new Exception('Erro ao inserir fornecedor: ' . $error['message'] . ' (Código: ' . $error['code'] . ')');
        }
        
        $fornecedorId = $this->db->insert_id();
        
        if (!$fornecedorId) {
            log_message('error', 'ID do fornecedor não foi gerado');
            throw new Exception('ID do fornecedor não foi gerado');
        }
        
        log_message('debug', 'Novo fornecedor criado: ' . $nome . ' (ID: ' . $fornecedorId . ')');
        return [
            'id' => $fornecedorId,
            'nome' => $nome
        ];
    }

    private function verificarOuCriarProduto($dadosProduto)
    {
        log_message('debug', 'verificarOuCriarProduto - Dados recebidos: ' . json_encode($dadosProduto));
        
        // Verificar se produto existe pelo código de barras (mais específico)
        if (!empty($dadosProduto['codigo'])) {
            $this->db->where('codDeBarra', $dadosProduto['codigo']);
            $produto = $this->db->get('produtos')->row();
            
            if ($produto) {
                log_message('debug', 'Produto encontrado por código de barras: ' . $produto->descricao . ' (ID: ' . $produto->idProdutos . ')');
                return $produto->idProdutos;
            }
        }
        
        // Verificar se produto existe por descrição exata (mais rigoroso)
        if (!empty($dadosProduto['descricao'])) {
            $this->db->where('descricao', $dadosProduto['descricao']);
            $produto = $this->db->get('produtos')->row();
            
            if ($produto) {
                log_message('debug', 'Produto encontrado por descrição exata: ' . $produto->descricao . ' (ID: ' . $produto->idProdutos . ')');
                return $produto->idProdutos;
            }
        }
        
        // Se chegou até aqui, precisa criar um novo produto
        $valorUnitario = isset($dadosProduto['valor_unitario']) ? (float)$dadosProduto['valor_unitario'] : 0;
        $quantidade = isset($dadosProduto['quantidade']) ? (float)$dadosProduto['quantidade'] : 0;
        
        // Usar a descrição fornecida do XML
        if (empty($dadosProduto['descricao'])) {
            throw new Exception('Descrição do produto é obrigatória');
        }
        
        log_message('debug', 'Criando produto com quantidade: ' . $quantidade . ' e valor unitário: ' . $valorUnitario);
        
        $dadosNovoProduto = [
            'descricao' => $dadosProduto['descricao'],
            'codDeBarra' => !empty($dadosProduto['codigo']) ? $dadosProduto['codigo'] : 'CODIGO_' . time(),
            'unidade' => 'UN',
            'precoCompra' => $valorUnitario,
            'precoVenda' => $valorUnitario > 0 ? $valorUnitario * 1.3 : 0,
            'estoque' => 0,
            'estoqueMinimo' => 0,
            'saida' => 0,
            'entrada' => 0
        ];
        
        // Adicionar NCM se disponível
        if (!empty($dadosProduto['ncm'])) {
            $dadosNovoProduto['NCMs'] = $dadosProduto['ncm'];
        }
        
        log_message('debug', 'Inserindo novo produto: ' . json_encode($dadosNovoProduto));
        
        $result = $this->db->insert('produtos', $dadosNovoProduto);
        if (!$result) {
            $db_error = $this->db->error();
            log_message('error', 'Erro ao inserir produto: ' . print_r($db_error, true));
            throw new Exception('Erro ao criar produto: ' . $db_error['message']);
        }
        
        $produtoId = $this->db->insert_id();
        
        if ($produtoId) {
            log_message('debug', 'Novo produto criado: ' . $dadosProduto['descricao'] . ' (ID: ' . $produtoId . ')');
            return $produtoId;
        } else {
            log_message('error', 'ID do produto não foi gerado');
            throw new Exception('Erro ao obter ID do produto criado');
        }
    }

    public function criarProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProdutos')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para cadastrar produtos.']);
            return;
        }

        $produto = $this->input->post('produto');
        
        if (empty($produto)) {
            echo json_encode(['success' => false, 'message' => 'Dados do produto não fornecidos']);
            return;
        }

        try {
            $produtoId = $this->verificarOuCriarProduto($produto);
            echo json_encode([
                'success' => true, 
                'produto_id' => $produtoId,
                'message' => 'Produto cadastrado com sucesso'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao criar produto via XML: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Erro ao cadastrar produto: ' . $e->getMessage()
            ]);
        }
    }

    private function verificarFornecedor($documento, $nome)
    {
        // Limpar documento para busca
        $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);
        
        // Verificar se já existe um fornecedor com este documento
        $this->db->where('documento', $documentoLimpo);
        $this->db->where('fornecedor', 1);
        $fornecedor = $this->db->get('clientes')->row();
        
        if ($fornecedor) {
            return [
                'id' => $fornecedor->idClientes,
                'nome' => $fornecedor->nomeCliente,
                'existe' => true
            ];
        }
        
        // Verificar se existe cliente com mesmo documento mas não é fornecedor
        $this->db->where('documento', $documentoLimpo);
        $cliente = $this->db->get('clientes')->row();
        
        if ($cliente) {
            return [
                'id' => $cliente->idClientes,
                'nome' => $cliente->nomeCliente,
                'existe' => true,
                'precisa_atualizar' => true
            ];
        }
        
        // Fornecedor não existe
        return [
            'id' => null,
            'nome' => $nome,
            'existe' => false,
            'documento' => $documento
        ];
    }

    public function criarFornecedor()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturamentoEntrada')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para cadastrar fornecedores.']);
            return;
        }

        $fornecedorData = $this->input->post('fornecedor');
        
        if (empty($fornecedorData)) {
            echo json_encode(['success' => false, 'message' => 'Dados do fornecedor não fornecidos']);
            return;
        }

        // Log dos dados recebidos para debug
        log_message('debug', 'Dados do fornecedor recebidos: ' . json_encode($fornecedorData));

        try {
            // Processar dados completos do fornecedor se disponíveis
            $dadosEmit = null;
            
            if (isset($fornecedorData['dados_completos'])) {
                $dadosCompletos = $fornecedorData['dados_completos'];
                
                // Criar objeto com estrutura correta
                $dadosEmit = (object)[
                    'enderEmit' => null,
                    'fone' => isset($dadosCompletos['fone']) ? $dadosCompletos['fone'] : '',
                    'ie' => isset($dadosCompletos['ie']) ? $dadosCompletos['ie'] : '',
                    'IEST' => isset($dadosCompletos['IEST']) ? $dadosCompletos['IEST'] : '',
                    'IM' => isset($dadosCompletos['IM']) ? $dadosCompletos['IM'] : '',
                    'CNAE' => isset($dadosCompletos['CNAE']) ? $dadosCompletos['CNAE'] : '',
                    'CRT' => isset($dadosCompletos['CRT']) ? $dadosCompletos['CRT'] : ''
                ];
                
                // Processar endereço se existir
                if (isset($dadosCompletos['enderEmit'])) {
                    $endereco = $dadosCompletos['enderEmit'];
                    $dadosEmit->enderEmit = (object)[
                        'xLgr' => isset($endereco['xLgr']) ? $endereco['xLgr'] : '',
                        'nro' => isset($endereco['nro']) ? $endereco['nro'] : '',
                        'xCpl' => isset($endereco['xCpl']) ? $endereco['xCpl'] : '',
                        'xBairro' => isset($endereco['xBairro']) ? $endereco['xBairro'] : '',
                        'cMun' => isset($endereco['cMun']) ? $endereco['cMun'] : '',
                        'xMun' => isset($endereco['xMun']) ? $endereco['xMun'] : '',
                        'uf' => isset($endereco['uf']) ? $endereco['uf'] : '',
                        'cep' => isset($endereco['cep']) ? $endereco['cep'] : '',
                        'cPais' => isset($endereco['cPais']) ? $endereco['cPais'] : '',
                        'xPais' => isset($endereco['xPais']) ? $endereco['xPais'] : ''
                    ];
                }
                
                log_message('debug', 'Dados do emitente processados: ' . json_encode($dadosEmit));
            }
            
            $fornecedor = $this->verificarOuCriarFornecedor(
                $fornecedorData['documento'], 
                $fornecedorData['nome'], 
                $dadosEmit
            );
            
            echo json_encode([
                'success' => true, 
                'fornecedor_id' => $fornecedor['id'],
                'fornecedor_nome' => $fornecedor['nome'],
                'message' => 'Fornecedor criado/encontrado com sucesso'
            ]);
        } catch (Exception $e) {
            log_message('error', 'Erro ao criar fornecedor via XML: ' . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Erro ao cadastrar fornecedor: ' . $e->getMessage()
            ]);
        }
    }

    public function fecharDocumento()
    {
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        if ($this->form_validation->run('faturamentoEntrada') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : false);
        } else {
            $data = $this->input->post();

            // Iniciar transação
            $this->db->trans_begin();

            try {
                // Salvar o faturamento de entrada
                $faturamentoData = array(
                    'fornecedor_id' => $data['fornecedor_id'],
                    'transportadora_id' => $data['transportadora_id'],
                    'modalidade_frete' => $data['modalidade_frete'],
                    'peso_bruto' => str_replace(',', '.', $this->input->post('peso_bruto')),
                    'peso_liquido' => str_replace(',', '.', $this->input->post('peso_liquido')),
                    'volume' => str_replace(',', '.', $this->input->post('volume')),
                    'operacao_comercial_id' => $data['operacao_comercial_id'],
                    'valor_total' => str_replace(',', '.', str_replace('.', '', $data['total_nota'])),
                    'valor_produtos' => str_replace(',', '.', str_replace('.', '', $data['total_produtos'])),
                    'valor_icms' => str_replace(',', '.', str_replace('.', '', $data['total_icms'])),
                    'total_base_icms_st' => str_replace(',', '.', str_replace('.', '', $data['total_base_icms_st'])),
                    'total_icms_st' => str_replace(',', '.', str_replace('.', '', $data['total_icms_st'])),
                    'valor_ipi' => str_replace(',', '.', str_replace('.', '', $data['total_ipi'])),
                    'valor_frete' => str_replace(',', '.', str_replace('.', '', $data['frete'])),
                    'valor_outras_despesas' => str_replace(',', '.', str_replace('.', '', $data['despesas'])),
                    'observacoes' => $data['observacoes'],
                    'data_cadastro' => date('Y-m-d H:i:s'),
                    'data_atualizacao' => date('Y-m-d H:i:s'),
                    'usuario_id' => $this->session->userdata('id_admin'),
                    'xml_conteudo' => $this->session->userdata('xml_content')
                );

                $this->db->where('id', $this->session->userdata('faturamento_entrada_id'));
                $this->db->update('faturamento_entrada', $faturamentoData);

                // Criar lançamento financeiro
                $lancamentoData = array(
                    'tipo' => 'Despesa',
                    'fornecedor_id' => $data['fornecedor_id'],
                    'descricao' => 'Faturamento de Entrada #' . $this->session->userdata('faturamento_entrada_id'),
                    'valor' => $data['valor_total'],
                    'data_vencimento' => date('Y-m-d', strtotime(str_replace('/', '-', $data['data_vencimento']))),
                    'data_pagamento' => null,
                    'baixado' => 0,
                    'cliente_fornecedor' => $data['fornecedor_nome'],
                    'forma_pgto' => $data['forma_pgto'],
                    'tipo_pgto' => 'Faturamento Entrada',
                    'observacoes' => $data['observacoes'],
                    'data' => date('Y-m-d H:i:s'),
                    'usuario_id' => $this->session->userdata('user_id')
                );

                $this->db->insert('lancamentos', $lancamentoData);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    throw new Exception('Erro ao processar o fechamento do documento.');
                }

                $this->db->trans_commit();
                $this->session->unset_userdata('faturamento_entrada_id');

                echo json_encode(array('success' => true));
                return;
            } catch (Exception $e) {
                $this->db->trans_rollback();
                echo json_encode(array('success' => false, 'message' => $e->getMessage()));
                return;
            }
        }

        echo json_encode(array('success' => false, 'message' => $this->data['custom_error']));
    }

    public function getDadosFaturamento($id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para visualizar faturamento de entrada.']);
            return;
        }

        if ($id == null) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'ID do faturamento não informado.']);
            return;
        }

        $faturamento = $this->FaturamentoEntrada_model->getById($id);
        if (!$faturamento) {
            $this->output->set_status_header(404);
            echo json_encode(['success' => false, 'message' => 'Faturamento não encontrado.']);
            return;
        }

        // Buscar dados do fornecedor
        $fornecedor = $this->Clientes_model->getById($faturamento->fornecedor_id);
        if (!$fornecedor) {
            $this->output->set_status_header(404);
            echo json_encode(['success' => false, 'message' => 'Fornecedor não encontrado.']);
            return;
        }

        // Extrair forma de pagamento do XML se disponível
        $forma_pgto = 'Cartão de Crédito'; // Valor padrão
        if (!empty($faturamento->xml_conteudo)) {
            try {
                $xml = simplexml_load_string($faturamento->xml_conteudo);
                if ($xml && isset($xml->NFe->infNFe->pag->detPag)) {
                    $pagamentos = $xml->NFe->infNFe->pag->detPag;
                    if (isset($pagamentos->tPag)) {
                        $tPag = (string)$pagamentos->tPag;
                        // Mapear códigos de pagamento da NFe para formas de pagamento do sistema
                        $formas_pagamento = [
                            '01' => 'Dinheiro',
                            '02' => 'Cartão de Crédito',
                            '03' => 'Cartão de Débito',
                            '04' => 'Cartão de Crédito',
                            '05' => 'Cartão de Débito',
                            '06' => 'Boleto',
                            '07' => 'Depósito',
                            '08' => 'Transferência DOC',
                            '09' => 'Transferência TED',
                            '10' => 'Cheque',
                            '11' => 'Cheque Pré-datado',
                            '12' => 'Pix',
                            '13' => 'Promissória'
                        ];
                        $forma_pgto = isset($formas_pagamento[$tPag]) ? $formas_pagamento[$tPag] : 'Cartão de Crédito';
                    }
                }
            } catch (Exception $e) {
                log_message('error', 'Erro ao processar XML: ' . $e->getMessage());
            }
        }

        $response = [
            'success' => true,
            'data' => [
                'total_nota' => number_format($faturamento->valor_total, 2, ',', '.'),
                'numero_nfe' => $faturamento->numero_nota,
                'fornecedor' => $fornecedor->nomeCliente,
                'fornecedor_id' => $fornecedor->idClientes,
                'data_vencimento' => date('d/m/Y', strtotime($faturamento->data_entrada)),
                'forma_pgto' => $forma_pgto
            ]
        ];

        echo json_encode($response);
    }

    public function finalizarEntrada()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturamentoEntrada')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para finalizar faturamento de entrada.']);
            return;
        }

        $id = $this->input->post('id');

        if (!$id) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'ID do faturamento não informado.']);
            return;
        }

        $this->db->trans_start();

        try {
            // Buscar faturamento_entrada
            $faturamento = $this->db->where('id', $id)->get('faturamento_entrada')->row();
            if (!$faturamento) {
                throw new Exception('Faturamento não encontrado.');
            }

            // Buscar documento_faturado relacionado
            $pes_id = null;
            
            // Tentar buscar pes_id através da tabela clientes (nova estrutura)
            if ($this->db->table_exists('clientes')) {
                $cliente_novo = $this->db->where('cln_id', $faturamento->fornecedor_id)->get('clientes')->row();
                if ($cliente_novo) {
                    $pes_id = $cliente_novo->pes_id;
                }
            }
            
            // Se não encontrou, tentar pela tabela antiga clientes_
            if (!$pes_id && $this->db->table_exists('clientes_')) {
                $fornecedor = $this->db->where('idClientes', $faturamento->fornecedor_id)->get('clientes_')->row();
                if ($fornecedor) {
                    $documento_limpo = preg_replace('/\D/', '', $fornecedor->documento);
                    $pessoa = $this->db->where('pes_cpfcnpj', $documento_limpo)->get('pessoas')->row();
                    if ($pessoa) {
                        $pes_id = $pessoa->pes_id;
                    }
                }
            }

            if ($pes_id) {
                // Buscar documento_faturado relacionado ao faturamento_entrada
                // Buscar pela data de entrada primeiro
                $dcf = $this->db->where('pes_id', $pes_id)
                                ->where('dcf_tipo', 'E')
                                ->where('dcf_status', 'ABERTO')
                                ->where('dcf_data_saida', $faturamento->data_entrada)
                                ->get('documentos_faturados')
                                ->row();
                
                // Se não encontrar, tentar pelo número da nota
                if (!$dcf && $faturamento->numero_nota) {
                    $dcf = $this->db->where('pes_id', $pes_id)
                                    ->where('dcf_tipo', 'E')
                                    ->where('dcf_numero', $faturamento->numero_nota)
                                    ->where('dcf_status', 'ABERTO')
                                    ->get('documentos_faturados')
                                    ->row();
                }

                if ($dcf) {
                    // Atualizar status para FATURADO
                    $this->db->where('dcf_id', $dcf->dcf_id);
                    $this->db->update('documentos_faturados', [
                        'dcf_status' => 'faturado',
                        'dcf_data_faturamento' => date('Y-m-d')
                    ]);

                    // Buscar itens_faturados e criar movimentações de estoque
                    $itens = $this->db->where('dcf_id', $dcf->dcf_id)->get('itens_faturados')->result();
                    
                    foreach ($itens as $item) {
                        // Criar movimentação de estoque (ENTRADA)
                        $this->Produtos_model->criarMovimentacaoEstoque($item->itf_id, $item->itf_quantidade, 'ENTRADA');
                    }
                }
            }

            // Atualizar status do faturamento_entrada
            $this->db->where('id', $id);
            $this->db->update('faturamento_entrada', [
                'status' => 'fechado',
                'data_atualizacao' => date('Y-m-d H:i:s')
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Erro na transação: ' . $this->db->error()['message']);
            }

            echo json_encode(['success' => true, 'message' => 'Entrada finalizada com sucesso!']);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao finalizar entrada: ' . $e->getMessage()]);
        }
    }

    public function atualizarStatus()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturamentoEntrada')) {
            $this->output->set_status_header(403);
            echo json_encode(['success' => false, 'message' => 'Você não tem permissão para editar faturamento de entrada.']);
            return;
        }

        $id = $this->input->post('id');
        $status = $this->input->post('status');

        if (!$id || !$status) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos.']);
            return;
        }

        try {
            $data = [
                'status' => $status,
                'data_fechamento' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $id);
            if ($this->db->update('faturamento_entrada', $data)) {
                if ($this->db->affected_rows() > 0) {
                    echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso.']);
                } else {
                    $this->output->set_status_header(404);
                    echo json_encode(['success' => false, 'message' => 'Faturamento não encontrado ou status já está atualizado.']);
                }
            } else {
                throw new Exception($this->db->error()['message']);
            }
        } catch (Exception $e) {
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar status: ' . $e->getMessage()]);
        }
    }

    private function getIBGEFromCEP($cep) {
        try {
            $cep = preg_replace('/[^0-9]/', '', $cep);
            $url = "https://viacep.com.br/ws/{$cep}/json/";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            
            if ($response) {
                $data = json_decode($response);
                if (isset($data->ibge)) {
                    return $data->ibge;
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao buscar IBGE do CEP: ' . $e->getMessage());
        }
        return null;
    }

    public function buscarFornecedores() {
        $nome = $this->input->get('nome');
        $documento = $this->input->get('documento');
        $telefone = $this->input->get('telefone');
        $limite = (int) $this->input->get('limite') ?: 50;

        $this->db->select('idClientes as id, nomeCliente as nome, documento, telefone');
        $this->db->from('clientes');
        $this->db->where('fornecedor', 1);
        if ($nome) $this->db->like('nomeCliente', $nome);
        if ($documento) $this->db->like('documento', $documento);
        if ($telefone) $this->db->like('telefone', $telefone);
        $this->db->limit($limite);
        $this->db->order_by('nomeCliente', 'asc');
        $query = $this->db->get();
        $result = $query->result();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function buscarTransportadoras() {
        $nome = $this->input->get('nome');
        $documento = $this->input->get('documento');
        $telefone = $this->input->get('telefone');
        $limite = (int) $this->input->get('limite') ?: 50;

        $this->db->select('idClientes as id, nomeCliente as nome, documento, telefone');
        $this->db->from('clientes');
        $this->db->where('fornecedor', 3);
        if ($nome) $this->db->like('nomeCliente', $nome);
        if ($documento) $this->db->like('documento', $documento);
        if ($telefone) $this->db->like('telefone', $telefone);
        $this->db->limit($limite);
        $this->db->order_by('nomeCliente', 'asc');
        $query = $this->db->get();
        $result = $query->result();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    public function buscarProdutos() {
        $nome = $this->input->get('nome');
        $codigo = $this->input->get('codigo');
        $barras = $this->input->get('barras');
        $limite = (int) $this->input->get('limite') ?: 50;

        $this->db->select('idProdutos, descricao, codDeBarra, precoVenda, estoque');
        $this->db->from('produtos');
        if ($nome) $this->db->like('descricao', $nome);
        if ($codigo) $this->db->like('idProdutos', $codigo);
        if ($barras) $this->db->like('codDeBarra', $barras);
        $this->db->limit($limite);
        $this->db->order_by('descricao', 'asc');
        $query = $this->db->get();
        $result = $query->result();
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

    private function verificarOuCriarTransportadora($documento, $nome, $dadosTransp)
    {
        $documentoLimpo = preg_replace('/[^0-9]/', '', $documento);
        // Verificar se já existe uma transportadora com este documento
        $this->db->where('documento', $documentoLimpo);
        $this->db->where('fornecedor', 3);
        $transp = $this->db->get('clientes')->row();
        if ($transp) {
            return [
                'id' => $transp->idClientes,
                'nome' => $transp->nomeCliente
            ];
        }
        // Verificar se existe cliente com mesmo documento mas não é transportadora
        $this->db->where('documento', $documentoLimpo);
        $cliente = $this->db->get('clientes')->row();
        if ($cliente) {
            $this->db->where('idClientes', $cliente->idClientes);
            $this->db->update('clientes', ['fornecedor' => 3]);
            return [
                'id' => $cliente->idClientes,
                'nome' => $cliente->nomeCliente
            ];
        }
        // Se não existe, cadastrar
        $dados = [
            'nomeCliente' => $nome,
            'documento' => $documentoLimpo,
            'fornecedor' => 3,
            'dataCadastro' => date('Y-m-d'),
            'rua' => isset($dadosTransp['xEnder']) ? $dadosTransp['xEnder'] : '',
            'cidade' => isset($dadosTransp['xMun']) ? $dadosTransp['xMun'] : '',
            'estado' => isset($dadosTransp['uf']) ? $dadosTransp['uf'] : '',
            'inscricao' => isset($dadosTransp['ie']) ? $dadosTransp['ie'] : ''
        ];
        $this->db->insert('clientes', $dados);
        $id = $this->db->insert_id();
        return [
            'id' => $id,
            'nome' => $nome
        ];
    }
} 