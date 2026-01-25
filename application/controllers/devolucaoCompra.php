<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!defined('SOAP_1_2')) {
    define('SOAP_1_2', 2);
}

require_once FCPATH . 'application/vendor/autoload.php';

use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\CertificateException;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Common\Complements;
use NFePHP\Common\CertificateHandler;

class DevolucaoCompra extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('FaturamentoEntrada_model');
        $this->load->model('Clientes_model');
        $this->load->model('Mapos_model');
        $this->load->model('Produtos_model');
        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('OperacaoComercial_model');
        $this->load->model('TributacaoProduto_model');
        $this->load->model('Aliquotas_model');

        // Busca o regime tributário
        $this->db->select('valor');
        $this->db->from('configuracoes');
        $this->db->where('config', 'regime_tributario');
        $this->db->limit(1);
        $regime = $this->db->get()->row();
        $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;
    }

    public function devolucaoCompra($entrada_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
            return;
        }

        // Se não recebeu o ID da entrada como parâmetro, tenta pegar do POST ou GET
        if (empty($entrada_id)) {
            $entrada_id = $this->input->post('entrada_id') ? $this->input->post('entrada_id') : $this->input->get('entrada_id');
        }

        $devolver_todos = $this->input->post('devolver_todos');
        $itens_selecionados = $this->input->post('itens_selecionados');
        $quantidades = $this->input->post('quantidades');

        if (empty($entrada_id)) {
            $this->session->set_flashdata('error', 'Entrada não informada.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Busca os dados da entrada
        $entrada = $this->FaturamentoEntrada_model->getById($entrada_id);
        if (!$entrada) {
            $this->session->set_flashdata('error', 'Entrada não encontrada.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Busca a NFe original da entrada
        $this->db->where('id', $entrada->id);
        $nfe_original = $this->db->get('faturamento_entrada')->row();
        if (!$nfe_original) {
            $this->session->set_flashdata('error', 'NFe da entrada não encontrada.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Carrega dados do fornecedor
        $fornecedor = $this->Clientes_model->getById($entrada->fornecedor_id);
        if (!$fornecedor) {
            $this->session->set_flashdata('error', 'Fornecedor não encontrado.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Emitente não configurado.');
            redirect(base_url() . 'index.php/nfe');
            return;
        }

        // Carrega produtos da entrada
        $produtos = $this->FaturamentoEntrada_model->getItens($entrada_id);
        if (empty($produtos)) {
            $this->session->set_flashdata('error', 'Entrada sem produtos.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Se for devolver todos, define os itens selecionados como todos os produtos
        if ($devolver_todos === 'true') {
            $itens_selecionados = array();
            $quantidades = array();
            foreach ($produtos as $produto) {
                $itens_selecionados[] = $produto->produto_id;
                $quantidades[$produto->produto_id] = $produto->quantidade;
            }
            $totalProdutos = $entrada->valor_produtos;
        } else if (empty($itens_selecionados)) {
            // Se não for devolver todos e não houver itens selecionados, redireciona para a página de seleção
            $this->data['entrada'] = $entrada;
            $this->data['produtos'] = $produtos;
            
            // Busca informações da transportadora
            $this->db->select('transportadora_id, modalidade_frete, peso_bruto, peso_liquido, volume');
            $this->db->from('faturamento_entrada');
            $this->db->where('id', $entrada_id);
            $transportadora = $this->db->get()->row();
            
            if ($transportadora) {
                $entrada->transportadora_id = $transportadora->transportadora_id;
                $entrada->modalidade_frete = $transportadora->modalidade_frete;
                $entrada->peso_bruto = $transportadora->peso_bruto;
                $entrada->peso_liquido = $transportadora->peso_liquido;
                $entrada->volume = $transportadora->volume;
            }
            
            $this->data['view'] = 'faturamento_entrada/selecionarItensDevolucao';
            return $this->layout();
        }

        // Carrega configurações da NFe
        $this->db->where('idConfiguracao', 1);
        $configNFe = $this->db->get('configuracoes_nfe')->row();
        if (!$configNFe) {
            $this->session->set_flashdata('error', 'Configurações de NFe não encontradas. Configure as configurações de NFe primeiro.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFe');
            return;
        }

        try {
            // Cria objeto NFe
            $nfe = new Make();

            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf);
            $std->cNF = rand(10000000, 99999999);
            $std->natOp = 'Devolução Compra';
            $std->mod = 55;
            $std->serie = 1;
            $std->nNF = $configNFe->sequencia_nota;
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 1; // 1 = Saída
            $std->idDest = ($fornecedor->estado != $emitente->uf) ? '2' : '1';
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = $configNFe->tipo_impressao_danfe;
            $std->tpEmis = 1;
            $std->cDV = 0;
            $std->tpAmb = $configNFe->ambiente;
            $std->finNFe = 4; // 4 = Devolução
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = $configNFe->versao_nfe;

            // Adiciona referência à NFe original
            if (!empty($nfe_original->chave_acesso)) {
                $std->NFref = new \stdClass();
                $std->NFref->refNFe = $nfe_original->chave_acesso;
                $nfe->tagrefNFe($std->NFref);
            } else {
                throw new Exception('Chave da NFe original não encontrada. Não é possível emitir NFe de devolução sem referência.');
            }

            // Gera o código numérico da NFe
            $cUF = $this->get_cUF($emitente->uf);
            $ano = date('y');
            $mes = date('m');
            $cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $mod = '55';
            $serie = str_pad('1', 3, '0', STR_PAD_LEFT);
            $numero = str_pad($configNFe->sequencia_nota, 9, '0', STR_PAD_LEFT);
            $tpEmis = '1';
            $codigo = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis;
            
            // Calcula o DV
            $dv = $this->calculaDV($codigo);
            $codigo .= $dv;
            
            // Define o código numérico
            $std->cNF = substr($codigo, -8);
            $std->cDV = $dv;
            
            $nfe->tagide($std);

            $this->db->select('valor');
            $this->db->from('configuracoes');
            $this->db->where('config', 'regime_tributario');
            $this->db->limit(1);
            $regime = $this->db->get()->row();
            $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->IE = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->CNPJ = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->UF = $emitente->uf;
            $std->CEP = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);


            // [dest]
            $std = new \stdClass();
            $std->xNome = $fornecedor->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $fornecedor->documento)) == 11) {
                $std->CPF = preg_replace('/[^0-9]/', '', $fornecedor->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                $std->CNPJ = preg_replace('/[^0-9]/', '', $fornecedor->documento);
                if (!empty($fornecedor->inscricao)) {
                    $std->IE = $fornecedor->inscricao;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $fornecedor->rua;
            $std->nro = $fornecedor->numero;
            if (!empty($fornecedor->complemento)) {
                $std->xCpl = $fornecedor->complemento;
            }
            $std->xBairro = $fornecedor->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($fornecedor->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $fornecedor->ibge;
            $std->xMun = $fornecedor->cidade;
            $std->UF = $fornecedor->estado;
            $std->CEP = preg_replace('/[^0-9]/', '', $fornecedor->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($fornecedor->telefone)) {
                $std->fone = preg_replace('/[^0-9]/', '', $fornecedor->telefone);
            }
            $nfe->tagenderDest($std);

            // Adiciona infCpl (informação complementar) conforme solicitado
            $std = new \stdClass();
            $std->infAdFisco = '';
            
            // Inicializa o valor total do IPI
            $valor_ipi = 0;
            
            // Soma o IPI de todos os itens selecionados
            foreach ($produtos as $produto) {
                if ($devolver_todos === 'true' || in_array($produto->idProdutos, $itens_selecionados)) {
                    // Busca o item da entrada para pegar o IPI
                    $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                           ->where('produto_id', $produto->idProdutos)
                                           ->get('faturamento_entrada_itens')
                                           ->row();
                    
                    if ($item_entrada) {
                        $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                                    (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
                        
                        // Calcula o IPI proporcional à quantidade
                        $valor_ipi += ($item_entrada->valor_ipi / $item_entrada->quantidade) * $quantidade;
                    }
                }
            }
            
            if ($devolver_todos === 'true') {
                $std->infCpl = 'Devolução de Compra referente a nota ' . $entrada->numero_nota . ' com chave: ' . $entrada->chave_acesso . "  \nIPI de devolução: R$ " . number_format($valor_ipi, 2, ',', '.');
            } else {
                $std->infCpl = 'Devolução de Compra Parcial referente a nota ' . $entrada->numero_nota . ' com chave: ' . $entrada->chave_acesso . "  \nIPI de devolução: R$ " . number_format($valor_ipi, 2, ',', '.');
            }
            $nfe->taginfAdic($std);

            // Produtos
            $i = 1;
            $totalProdutos = 0;
            $totalICMS = 0; // Inicializa o total de ICMS

            foreach ($produtos as $produto) {
                // Se não for devolver todos, verifica se o item está selecionado
                if ($devolver_todos !== 'true' && !in_array($produto->idProdutos, $itens_selecionados)) {
                    continue;
                }

                // Se houver quantidades específicas, usa a quantidade informada
                $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                             (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
                                                 // Primeiro tenta pegar do POST (selecionarItensDevolucao.php)
                    $base_icms = $this->input->post('base_icms')[$produto->idProdutos] ?? null;
                    $aliquota_icms = $this->input->post('aliquota_icms')[$produto->idProdutos] ?? null;
                    $valor_icms = $this->input->post('valor_icms')[$produto->idProdutos] ?? null;

                $emitente = $this->Mapos_model->getEmitente();
                $cliente = $this->Clientes_model->getById($entrada->fornecedor_id);
                
                // Busca os dados de ICMS do item da entrada
                $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                       ->where('produto_id', $produto->idProdutos)
                                       ->get('faturamento_entrada_itens')
                                       ->row();
                
                if (!$item_entrada) {
                    $this->session->set_flashdata('error', 'Dados de ICMS do item da entrada não encontrados.');
                    redirect(base_url() . 'index.php/vendas');
                    return;
                }
                    
                // Usa os valores do item da entrada
                $aliq = $item_entrada->aliquota_icms;
                // Calcula a base de cálculo proporcional à quantidade
                $base_icms = ($item_entrada->base_calculo_icms / $item_entrada->quantidade) * $quantidade;
                $valor_icms = ($item_entrada->valor_icms / $item_entrada->quantidade) * $quantidade;
                $cst = $item_entrada->cst;
                // Define CSOSN 900 para devolução
                $csosn = '900';

                // Inicializa valores padrão
                $cst_pis = '01';
                $aliq_pis = 0;
                $cst_cofins = '01';
                $aliq_cofins = 0;
                $valor_ipi_item = 0;

                // Tenta obter valores do XML
                if (!empty($entrada->xml_conteudo)) {
                    $dom = new \DOMDocument();
                    $dom->loadXML($entrada->xml_conteudo);
                    
                    // Procura pelo item específico no XML
                    $items = $dom->getElementsByTagName('det');
                    foreach ($items as $item) {
                        $prod = $item->getElementsByTagName('prod')->item(0);
                        if ($prod && $prod->getElementsByTagName('cProd')->item(0)->nodeValue == $produto->idProdutos) {
                            // PIS
                            $pis = $item->getElementsByTagName('PIS')->item(0);
                            if ($pis) {
                                $cst_pis = $pis->getElementsByTagName('CST')->item(0) ? $pis->getElementsByTagName('CST')->item(0)->nodeValue : '01';
                                $aliq_pis = $pis->getElementsByTagName('pPIS')->item(0) ? $pis->getElementsByTagName('pPIS')->item(0)->nodeValue : 0;
                            }
                            
                            // COFINS
                            $cofins = $item->getElementsByTagName('COFINS')->item(0);
                            if ($cofins) {
                                $cst_cofins = $cofins->getElementsByTagName('CST')->item(0) ? $cofins->getElementsByTagName('CST')->item(0)->nodeValue : '01';
                                $aliq_cofins = $cofins->getElementsByTagName('pCOFINS')->item(0) ? $cofins->getElementsByTagName('pCOFINS')->item(0)->nodeValue : 0;
                            }
                            
                            // IPI
                            $ipi = $item->getElementsByTagName('IPI')->item(0);
                            if ($ipi) {
                                $valor_ipi_item = $ipi->getElementsByTagName('vIPI')->item(0) ? $ipi->getElementsByTagName('vIPI')->item(0)->nodeValue : 0;
                            }
                            break;
                        }
                    }
                }

                // Define o CFOP baseado na localização do fornecedor
                $cfop = ($fornecedor->estado == $emitente->uf) ? '5202' : '6202';

                // [prod]
                $std = new \stdClass();
                $std->item = $i;
                $std->cProd = $produto->idProdutos;
                $std->cEAN = 'SEM GTIN';
                $std->cEANTrib = 'SEM GTIN';
                $std->xProd = $produto->descricao;
                $std->NCM = $produto->NCMs;
                $std->CFOP = $cfop;
                $std->uCom = 'UN';
                $std->qCom = $quantidade;

                // Calcula a proporção para devolução parcial
                $proporcao = $quantidade / $item_entrada->quantidade;
                
                // Calcula o valor unitário proporcional
                $valor_unitario = $item_entrada->valor_unitario;
                $std->vUnCom = number_format($valor_unitario, 2, '.', '');
                
                // Calcula o valor total do produto proporcional
                $valorProduto = $valor_unitario * $quantidade;
                $std->vProd = number_format($valorProduto, 2, '.', '');
                
                $std->uTrib = 'UN';
                $std->qTrib = $quantidade;
                $std->vUnTrib = number_format($valor_unitario, 2, '.', '');
                $std->indTot = 1;
                $std->cEAN = 'SEM GTIN';
                $std->cEANTrib = 'SEM GTIN';
                
                // Calcula o valor do IPI proporcional
                $valor_ipi_item = ($item_entrada->valor_ipi / $item_entrada->quantidade) * $quantidade;
                $std->vIPIDevol = number_format($valor_ipi_item, 2, '.', '');
                
                $nfe->tagprod($std);

                // [imposto]
                $std = new \stdClass();
                $std->item = $i;
                $nfe->tagimposto($std);
                
                // ICMS
                $std = new \stdClass();
                $std->item = $i;
                $std->orig = $produto->origem;
                
                // Calcula base ICMS e valor ICMS proporcionais
                $base_icms = ($item_entrada->base_calculo_icms / $item_entrada->quantidade) * $quantidade;
                $valor_icms = ($item_entrada->valor_icms / $item_entrada->quantidade) * $quantidade;
                
                if ($this->crt == 1) {
                    // Simples Nacional - Devolução
                    $std->CSOSN = $csosn;
                    $std->modBC = 3;
                    $std->vBC = number_format($base_icms, 2, '.', '');
                    $std->pICMS = number_format($aliq, 2, '.', '');   
                    $std->vICMS = number_format($valor_icms, 2, '.', '');
                    
                    // Busca a alíquota de crédito ICMS das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'aliq_cred_icms');
                    $this->db->limit(1);
                    $aliq_cred = $this->db->get()->row();
                    $pCredSN = $aliq_cred ? str_replace(',', '.', $aliq_cred->valor) : 3.00;
                    
                    $std->pCredSN = number_format($pCredSN, 2, '.', '');
                    $std->vCredICMSSN = number_format(($base_icms * $pCredSN) / 100, 2, '.', '');
                    
                    $nfe->tagICMSSN($std);
                } else {
                    // Lucro Real ou Presumido
                    $std->CST = $cst;
                    $std->modBC = 3;
                    $std->vBC = number_format($base_icms, 2, '.', '');
                    $std->pICMS = number_format($aliq, 2, '.', '');   
                    $std->vICMS = number_format($valor_icms, 2, '.', '');
                    $nfe->tagICMS($std);
                }
                
                // PIS
                $std = new \stdClass();
                $std->item = $i;
                $std->CST = $cst_pis;
                $base_calculo = $valorProduto; // Usa o valor proporcional do produto
                $std->vBC = number_format($base_calculo, 2, '.', '');
                $std->pPIS = $aliq_pis;
                $std->vPIS = number_format(($base_calculo * $aliq_pis) / 100, 2, '.', '');
                $nfe->tagPIS($std);

                // COFINS
                $std = new \stdClass();
                $std->item = $i;
                $std->CST = $cst_cofins;
                $std->vBC = number_format($base_calculo, 2, '.', '');
                $std->pCOFINS = $aliq_cofins;
                $std->vCOFINS = number_format(($base_calculo * $aliq_cofins) / 100, 2, '.', '');
                $nfe->tagCOFINS($std);

                // [impostoDevol]
                $std = new \stdClass();
                $std->item = $i;
                $std->pDevol = '100.00'; // Percentual de devolução (100%)
                $std->vIPIDevol = number_format($valor_ipi_item, 2, '.', '');
                $nfe->tagimpostoDevol($std);

                // Atualiza totais
                $totalProdutos += $valorProduto;
                $totalICMS += $valor_icms;
                $i++;
            }

            // Adiciona todas as mensagens DIFAL no infCpl geral da nota
            if (!empty($this->mensagensDIFAL)) {
                $std = new \stdClass();
                $std->infAdFisco = '';
                $std->infCpl = "DADOS ADICIONAIS\n\n" . implode("\n", $this->mensagensDIFAL);
                $nfe->taginfAdic($std);
            } else {
                // Adiciona mensagem padrão sobre crédito ICMS para Simples Nacional apenas se for contribuinte
                if ($this->crt == 1 && isset($dest->indIEDest) && $dest->indIEDest == 1) {
                    // Busca a mensagem do Simples Nacional das configurações
                    $this->db->select('valor');
                    $this->db->from('configuracoes');
                    $this->db->where('config', 'mensagem_simples_nacional');
                    $this->db->limit(1);
                    $mensagem = $this->db->get()->row();
                    
                    $std = new \stdClass();
                    $std->infAdFisco = '';
                    
                    // Calcula o valor do crédito ICMS usando o total
                    $total = number_format($produto->valor_unitario * $quantidade, 2, '.', '');
                    $vCredICMSSN = number_format(($total * $pCredSN) / 100, 2, '.', '');
                    
                    $mensagemTexto = $mensagem ? $mensagem->valor : "CRÉDITO ICMS SIMPLES NACIONAL\nConforme Art. 23 da LC 123/2006, o valor do crédito de ICMS corresponde a [percentual]% da base de cálculo do Simples Nacional.";
                    
                    // Substitui os placeholders na mensagem
                    $mensagemTexto = str_replace('[percentual]', number_format($pCredSN, 2, ',', '.'), $mensagemTexto);
                    $mensagemTexto = str_replace('[valor]', number_format($vCredICMSSN, 2, ',', '.'), $mensagemTexto);
                    
                    $std->infCpl = "DADOS ADICIONAIS\n\n" . $mensagemTexto;
                    $nfe->taginfAdic($std);
                }
            }

            // [ICMSTot]
            $std = new \stdClass();
            
            // Calcula o valor total dos produtos proporcionalmente aos itens selecionados
            $totalProdutos = 0;
            $totalBaseICMS = 0;
            $valorTotalIPI = $this->input->post('total_ipi') ? floatval($this->input->post('total_ipi')) : 0;
            $totalICMS = 0;

            foreach ($produtos as $produto) {
                if ($devolver_todos === 'true' || in_array($produto->idProdutos, $itens_selecionados)) {
                    $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                                (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
                    
                    // Busca o item da entrada
                    $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                           ->where('produto_id', $produto->idProdutos)
                                           ->get('faturamento_entrada_itens')
                                           ->row();
                    
                    if ($item_entrada) {
                        // Calcula os valores proporcionais à quantidade
                        $proporcao = $quantidade / $item_entrada->quantidade;
                        
                        $base_icms = ($item_entrada->base_calculo_icms / $item_entrada->quantidade) * $quantidade;
                        $valor_icms = ($item_entrada->valor_icms / $item_entrada->quantidade) * $quantidade;
                        
                        // Atualiza os totais
                        $totalBaseICMS += $base_icms;
                        $totalICMS += $valor_icms;
                        
                        // Calcula o valor total proporcional à quantidade
                        $valorItem = $item_entrada->valor_unitario * $quantidade;
                        $totalProdutos += $valorItem;
                    }
                }
            }

            // Calcula o valor total da nota (vNF) como soma dos produtos + IPI
            $valorTotalNota = $totalProdutos + $valorTotalIPI;
            
            $std->vBC = number_format($totalBaseICMS, 2, '.', '');
            $std->vICMS = number_format($totalICMS, 2, '.', '');
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vNF = number_format($valorTotalNota, 2, '.', ''); // Soma dos produtos + IPI
            $std->vIPIDevol = number_format($valorTotalIPI, 2, '.', '');
            $nfe->tagICMSTot($std);

// 1 = Por conta do destinatário (FOB)
if (empty($entrada->transportadora_id)) {
    throw new Exception('Transportadora é obrigatória quando a modalidade de frete é diferente de 9 (Sem Frete)');
}

// Busca informações completas da transportadora
$this->db->select('*');
$this->db->from('clientes');
$this->db->where('idClientes', $entrada->transportadora_id);
$transportadora = $this->db->get()->row();

if (!$transportadora) {
    throw new Exception('Transportadora não encontrada');
}

// Define o tipo de frete
$std = new \stdClass();

// Primeiro tenta pegar do POST (selecionarItensDevolucao.php)
$modalidade_frete = $this->input->post('modalidade_frete');

// Se não encontrou no POST, tenta pegar da entrada
if ($modalidade_frete === null || $modalidade_frete === '') {
    $modalidade_frete = $entrada->modalidade_frete ?? '1';
}

$std->modFrete = $modalidade_frete;
$nfe->tagtransp($std);

// Informações da transportadora
$stdTransp = new \stdClass();
$stdTransp->CNPJ = preg_replace('/[^0-9]/', '', $transportadora->documento);
$stdTransp->xNome = $transportadora->nomeCliente;
$stdTransp->IE = !empty($transportadora->inscricao) ? $transportadora->inscricao : 'ISENTO';

// Endereço formatado
$endereco = $transportadora->rua;
if (!empty($transportadora->numero)) {
    $endereco .= ', ' . $transportadora->numero;
}
if (!empty($transportadora->complemento)) {
    $endereco .= ' - ' . $transportadora->complemento;
}
$stdTransp->xEnder = $endereco;
$stdTransp->xMun = $transportadora->cidade;
$stdTransp->UF = $transportadora->estado;

$nfe->tagtransporta($stdTransp);

// Busca informações de peso e volume
$peso_liquido = null;
$peso_bruto = null;
$volume = null;
$especie = null;

// Primeiro tenta pegar do POST (selecionarItensDevolucao.php)
if ($this->input->post('peso_liquido')) {
    $peso_liquido = $this->input->post('peso_liquido');
}
if ($this->input->post('peso_bruto')) {
    $peso_bruto = $this->input->post('peso_bruto');
}
if ($this->input->post('volume')) {
    $volume = $this->input->post('volume');
}
if ($this->input->post('especie')) {
    $especie = $this->input->post('especie');
}

// Se não encontrou no POST, tenta pegar da entrada
if ($peso_liquido === null && !empty($entrada->peso_liquido)) {
    $peso_liquido = $entrada->peso_liquido;
}
if ($peso_bruto === null && !empty($entrada->peso_bruto)) {
    $peso_bruto = $entrada->peso_bruto;
}
if ($volume === null && !empty($entrada->volume)) {
    $volume = $entrada->volume;
}
if ($especie === null && !empty($entrada->especie)) {
    $especie = $entrada->especie;
}

// Volume
$stdVol = new \stdClass();
$stdVol->qVol = !empty($volume) ? $volume : '1';
$stdVol->esp = !empty($especie) ? $especie : 'VOLUME';

// Só adiciona os pesos se existirem
if ($peso_liquido !== null) {
    $stdVol->pesoL = number_format($peso_liquido, 3, '.', '');
}
if ($peso_bruto !== null) {
    $stdVol->pesoB = number_format($peso_bruto, 3, '.', '');
}

$nfe->tagvol($stdVol);


            // [pag]
            $std = new \stdClass();
            $std->indPag = 0; // 0 = pagamento à vista
            $std->tPag = '90'; // 90 = sem Pagamento
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagpag($std);

            // [detPag]
            $std = new \stdClass();
            $std->tPag = '90'; // 90 = sem Pagamento
            $std->vPag = number_format($totalProdutos, 2, '.', '');
            $nfe->tagdetPag($std);

            try {
                $xml = $nfe->getXML();
                
                // Obtém o objeto Tools com o certificado
                $tools = $this->getTools();

                // Verifica se há erros nas tags antes de prosseguir
                $errors = $nfe->getErrors();
                if (!empty($errors)) {
                    $errorMessage = "Erros encontrados no XML da NFC-e:\n";
                    foreach ($errors as $error) {
                        $errorMessage .= "- " . str_replace('"', '', $error) . "\n";
                    }
                    throw new Exception($errorMessage);
                }

                // Assina o XML
                $signed = $tools->signNFe($xml);

                // Envia para a SEFAZ
                $idLote = str_pad(100, 15, '0', STR_PAD_LEFT); // Identificador do lote
                $response = $tools->sefazEnviaLote([$signed], $idLote);
                $st = new \NFePHP\NFe\Common\Standardize($response);
                $std = $st->toStd();

                if ($std->cStat != 103) {
                    throw new Exception("Erro ao enviar NFe: " . $std->xMotivo);
                }

                $recibo = $std->infRec->nRec;
                
                // Aguarda o processamento do lote
                $tentativas = 0;
                $maxTentativas = 10;
                $status = 0;
                $chave_retorno_evento = '';
                
                while ($tentativas < $maxTentativas) {
                    // Consulta recibo
                    $protocolo = $tools->sefazConsultaRecibo($recibo);
                    
                    // Log da resposta bruta
                    log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                    
                    $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                    $std = $st->toStd();
                    
                    // Log do objeto padronizado
                    log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                    
                    // Verifica se o lote ainda está em processamento
                    if (isset($std->cStat) && $std->cStat == '105') {
                        // Lote em processamento, aguarda e tenta novamente
                        sleep(2);
                        $tentativas++;
                        continue;
                    }
                    
                    // Extrai o status real da NFe do protocolo
                    $dom = new \DOMDocument();
                    $dom->loadXML($protocolo);
                    
                    // Procura por diferentes tags possíveis
                    $infProt = $dom->getElementsByTagName('infProt')->item(0);
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                    }
                    if (!$infProt) {
                        $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                    }
                    
                    if (!$infProt) {
                        // Tenta extrair informações diretamente do objeto padronizado
                        if (isset($std->cStat)) {
                            $status = ($std->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsReciNFe->cStat)) {
                            $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                        } else if (isset($std->retConsSitNFe->cStat)) {
                            $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                            $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                        } else {
                            throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                        }
                    } else {
                        $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                        
                        if ($cStat && $cStat->nodeValue == '100') {
                            $status = 1;
                            $chave_retorno_evento = "Autorizado o uso da NF-e";
                        } else {
                            $status = 0;
                            $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                        }
                    }
                    
                    // Se encontrou o status, sai do loop
                    if ($status != 0 || $chave_retorno_evento != '') {
                        break;
                    }
                    
                    // Aguarda antes da próxima tentativa
                    sleep(2);
                    $tentativas++;
                }
                
                if ($tentativas >= $maxTentativas) {
                    throw new Exception("Tempo limite excedido ao aguardar processamento do lote");
                }

                // Log da resposta bruta
                log_message('debug', 'Resposta bruta da SEFAZ: ' . $protocolo);
                
                $st = new \NFePHP\NFe\Common\Standardize($protocolo);
                $std = $st->toStd();
                
                // Log do objeto padronizado
                log_message('debug', 'Resposta padronizada: ' . json_encode($std));
                
                // Extrai o status real da NFe do protocolo
                $dom = new \DOMDocument();
                $dom->loadXML($protocolo);
                
                // Procura por diferentes tags possíveis
                $infProt = $dom->getElementsByTagName('infProt')->item(0);
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsSitNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('retConsReciNFe')->item(0);
                }
                if (!$infProt) {
                    $infProt = $dom->getElementsByTagName('protNFe')->item(0);
                }
                
                if (!$infProt) {
                    // Tenta extrair informações diretamente do objeto padronizado
                    if (isset($std->cStat)) {
                        $status = ($std->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->xMotivo) ? $std->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsReciNFe->cStat)) {
                        $status = ($std->retConsReciNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsReciNFe->xMotivo) ? $std->retConsReciNFe->xMotivo : 'Motivo não informado';
                    } else if (isset($std->retConsSitNFe->cStat)) {
                        $status = ($std->retConsSitNFe->cStat == '100') ? 1 : 0;
                        $chave_retorno_evento = isset($std->retConsSitNFe->xMotivo) ? $std->retConsSitNFe->xMotivo : 'Motivo não informado';
                    } else {
                        throw new Exception("Não foi possível processar a resposta da SEFAZ. Estrutura desconhecida: " . json_encode($std));
                    }
                } else {
                    $cStat = $infProt->getElementsByTagName('cStat')->item(0);
                    $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0);
                    
                    if ($cStat && $cStat->nodeValue == '100') {
                        $status = 1;
                        $chave_retorno_evento = "Autorizado o uso da NF-e";
                    } else {
                        $status = 0;
                        $chave_retorno_evento = $xMotivo ? $xMotivo->nodeValue : 'Motivo não informado';
                    }
                }

                // Extrai o número da nota e chave do XML
                $dom = new \DOMDocument();
                $dom->loadXML($xml);
                $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
                if (!$infNFe) {
                    throw new Exception("Erro ao extrair informações da NFe: tag infNFe não encontrada");
                }
                
                $ide = $infNFe->getElementsByTagName('ide')->item(0);
                if (!$ide) {
                    throw new Exception("Erro ao extrair informações da NFe: tag ide não encontrada");
                }
                
                $nNF = $ide->getElementsByTagName('nNF')->item(0);
                if (!$nNF) {
                    throw new Exception("Erro ao extrair informações da NFe: tag nNF não encontrada");
                }
                $numero_nfe = $nNF->nodeValue;

                // Extrai a chave da NFe
                $chNFe = $infNFe->getAttribute('Id');
                if ($chNFe) {
                    $chNFe = str_replace('NFe', '', $chNFe); // Remove o prefixo 'NFe'
                }

                // Mostra a resposta da SEFAZ para análise
                log_message('debug', 'Resposta da SEFAZ: ' . $protocolo);

                // Salva NFe emitida
                $nfeData = [
                    'entrada_id' => (int)$entrada->id,
                    'modelo' => 55, // Modelo NFe
                    'numero_nfe' => (string)$numero_nfe,
                    'chave_nfe' => (string)$chNFe,
                    'xml' => (string)$signed,
                    'xml_protocolo' => (string)$protocolo,
                    'status' => $status,
                    'chave_retorno_evento' => $chave_retorno_evento,
                    'protocolo' => '', // Será preenchido posteriormente
                    'valor_total' => $totalProdutos, // Adiciona o valor total dos itens devolvidos
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Verifica se a tabela nfe_emitidas existe
                if (!$this->db->table_exists('nfe_emitidas')) {
                    // Cria a tabela se não existir
                    $this->db->query("CREATE TABLE IF NOT EXISTS `nfe_emitidas` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `entrada_id` int(11) DEFAULT NULL,
                        `modelo` int(11) DEFAULT NULL,
                        `numero_nfe` varchar(20) DEFAULT NULL,
                        `chave_nfe` varchar(44) DEFAULT NULL,
                        `xml` text DEFAULT NULL,
                        `xml_protocolo` text DEFAULT NULL,
                        `status` tinyint(1) DEFAULT NULL,
                        `chave_retorno_evento` text DEFAULT NULL,
                        `protocolo` varchar(20) DEFAULT NULL,
                        `valor_total` decimal(10,2) DEFAULT NULL,
                        `created_at` datetime DEFAULT NULL,
                        `updated_at` datetime DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        KEY `entrada_id` (`entrada_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
                }

                // Insere na tabela nfe_emitidas
                $this->db->insert('nfe_emitidas', $nfeData);

                if ($this->db->affected_rows() == 0) {
                    throw new Exception('Erro ao salvar NFe no banco de dados');
                }

                // Atualiza o campo emitida_nfe na tabela faturamento_entrada
                $this->db->where('id', $entrada->id);
                $this->db->update('faturamento_entrada', ['emitida_nfe' => true]);

                // Atualiza a sequência da nota
                $this->db->where('idConfiguracao', 1);
                $this->db->update('configuracoes_nfe', [
                    'sequencia_nota' => $configNFe->sequencia_nota + 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Prepara dados para o modal
                $modalData = [
                    'status' => ($status == 1) ? 'Autorizada' : 'Rejeitada',
                    'motivo' => $chave_retorno_evento,
                    'protocolo' => $protocolo,
                    'xml' => $signed,
                    'chave_nfe' => $chNFe,
                    'numero_nfe' => $numero_nfe,
                    'modelo' => 55 // Modelo NFe
                ];
                
                $configNFe = $this->db->get('configuracoes_nfe')->row();
                if ($configNFe && isset($configNFe->preview_nfe) && $configNFe->preview_nfe) {
                    $nfe_emitida = $this->db->order_by('id', 'desc')->get('nfe_emitidas')->row();
                    if ($nfe_emitida) {
                        $this->session->set_flashdata('preview_nfe_id', $nfe_emitida->id);
                    }
                } else {
                    $this->session->set_flashdata('nfe_modal', $modalData);
                    $this->session->set_flashdata('success', 'Nota fiscal emitida com sucesso!');
                }
                redirect(base_url() . 'index.php/nfe/gerenciar');

            } catch (Exception $e) {
                $errors = $nfe->getErrors();
                $errorMessage = $e->getMessage();
                
                if (!empty($errors)) {
                    $errorMessage .= " - Erros nas tags: " . implode(", ", array_map(function($error) {
                        return str_replace('"', '', $error);
                    }, $errors));
                }
                
                // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
                $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage);
                $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8');
                $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
                redirect(base_url() . 'index.php/nfe');
                return;
            }

        } catch (Exception $e) {
            $errors = $nfe->getErrors();
            $errorMessage = $e->getMessage();
            
            if (!empty($errors)) {
                $errorMessage .= " - Erros nas tags: " . implode(", ", array_map(function($error) {
                    return str_replace('"', '', $error);
                }, $errors));
            }
            
            // Limpa a mensagem de erro para evitar caracteres inválidos em JS/HTML
            $errorMessage = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $errorMessage);
            $errorMessage = htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8');
            $this->session->set_flashdata('error', 'Erro ao gerar NFe: ' . $errorMessage);
            redirect(base_url() . 'index.php/nfe');
            return;
        }
    }

    public function reemitirDevolucao($nfe_id = null)
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para emitir NFe.');
            redirect(base_url());
            return;
        }

        // Se não recebeu o ID da NFe como parâmetro, tenta pegar do POST
        if (empty($nfe_id)) {
            $nfe_id = $this->input->post('nfe_id');
        }

        if (empty($nfe_id)) {
            $this->session->set_flashdata('error', 'NFe não informada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
            return;
        }

        // Busca a NFe original
        $this->db->where('id', $nfe_id);
        $nfe_original = $this->db->get('nfe_emitidas')->row();
        if (!$nfe_original) {
            $this->session->set_flashdata('error', 'NFe original não encontrada.');
            redirect(base_url() . 'index.php/nfe/gerenciar');
            return;
        }

        // Busca a entrada original
        $entrada = $this->FaturamentoEntrada_model->getById($nfe_original->entrada_id);
        if (!$entrada) {
            $this->session->set_flashdata('error', 'Entrada original não encontrada.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Carrega dados do fornecedor
        $fornecedor = $this->Clientes_model->getById($entrada->fornecedor_id);
        if (!$fornecedor) {
            $this->session->set_flashdata('error', 'Fornecedor não encontrado.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            $this->session->set_flashdata('error', 'Emitente não configurado.');
            redirect(base_url() . 'index.php/nfe');
            return;
        }

        // Carrega produtos da entrada
        $produtos = $this->FaturamentoEntrada_model->getItens($entrada->id);
        if (empty($produtos)) {
            $this->session->set_flashdata('error', 'Entrada sem produtos.');
            redirect(base_url() . 'index.php/faturamentoEntrada');
            return;
        }

        // Carrega configurações da NFe
        $this->db->where('idConfiguracao', 1);
        $configNFe = $this->db->get('configuracoes_nfe')->row();
        if (!$configNFe) {
            $this->session->set_flashdata('error', 'Configurações de NFe não encontradas.');
            redirect(base_url() . 'index.php/nfe/configuracoesNFe');
            return;
        }

        // Busca o regime tributário
        $this->db->select('valor');
        $this->db->from('configuracoes');
        $this->db->where('config', 'regime_tributario');
        $this->db->limit(1);
        $regime = $this->db->get()->row();
        $this->crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? 1 : 3;

        try {
            // Cria objeto NFe
            $nfe = new Make();

            // [infNFe]
            $std = new \stdClass();
            $std->versao = '4.00';
            $nfe->taginfNFe($std);

            // [ide]
            $std = new \stdClass();
            $std->cUF = $this->get_cUF($emitente->uf);
            $std->cNF = rand(10000000, 99999999);
            $std->natOp = 'Devolução Compra';
            $std->mod = 55;
            $std->serie = 1;
            $std->nNF = $configNFe->sequencia_nota;
            $std->dhEmi = date('Y-m-d\TH:i:sP');
            $std->dhSaiEnt = date('Y-m-d\TH:i:sP');
            $std->tpNF = 1; // 1 = Saída
            $std->idDest = ($fornecedor->estado != $emitente->uf) ? '2' : '1';
            $std->cMunFG = $emitente->ibge;
            $std->tpImp = $configNFe->tipo_impressao_danfe;
            $std->tpEmis = 1;
            $std->cDV = 0;
            $std->tpAmb = $configNFe->ambiente;
            $std->finNFe = 4; // 4 = Devolução
            $std->indFinal = 1;
            $std->indPres = 1;
            $std->procEmi = 0;
            $std->verProc = $configNFe->versao_nfe;

            // Adiciona referência à NFe original
            if (!empty($nfe_original->chave_nfe)) {
                $std->NFref = new \stdClass();
                $std->NFref->refNFe = $nfe_original->chave_nfe;
                $nfe->tagrefNFe($std->NFref);
            } else {
                throw new Exception('Chave da NFe original não encontrada.');
            }

            // Gera o código numérico da NFe
            $cUF = $this->get_cUF($emitente->uf);
            $ano = date('y');
            $mes = date('m');
            $cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $mod = '55';
            $serie = str_pad('1', 3, '0', STR_PAD_LEFT);
            $numero = str_pad($configNFe->sequencia_nota, 9, '0', STR_PAD_LEFT);
            $tpEmis = '1';
            $codigo = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis;
            
            // Calcula o DV
            $dv = $this->calculaDV($codigo);
            $codigo .= $dv;
            
            // Define o código numérico
            $std->cNF = substr($codigo, -8);
            $std->cDV = $dv;
            
            $nfe->tagide($std);

            // [emit]
            $std = new \stdClass();
            $std->xNome = $emitente->nome;
            $std->xFant = $emitente->nome;
            $std->IE = !empty($emitente->ie) ? $emitente->ie : 'ISENTO';
            $std->CRT = $this->crt;
            $std->cnpj = preg_replace('/[^0-9]/', '', $emitente->cnpj);
            $nfe->tagemit($std);

            // [enderEmit]
            $std = new \stdClass();
            $std->xLgr = $emitente->rua;
            $std->nro = $emitente->numero;
            if (!empty($emitente->complemento)) {
                $std->xCpl = $emitente->complemento;
            }
            $std->xBairro = $emitente->bairro;
            $std->cMun = $emitente->ibge;
            $std->xMun = $emitente->cidade;
            $std->UF = $emitente->uf;
            $std->CEP = preg_replace('/[^0-9]/', '', $emitente->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            $std->fone = preg_replace('/[^0-9]/', '', $emitente->telefone);
            $nfe->tagenderEmit($std);

            // [transp]
            $std = new \stdClass();
            $std->modFrete = $entrada->modalidade_frete ?? '1'; // 1 = Por conta do destinatário (FOB)

            // Verifica se existe transportadora
            if (empty($entrada->transportadora_id)) {
                throw new Exception('Transportadora é obrigatória quando a modalidade de frete é diferente de 9 (Sem Frete)');
            }

            // Busca informações completas da transportadora
            $this->db->select('*');
            $this->db->from('clientes');
            $this->db->where('idClientes', $entrada->transportadora_id);
            $transportadora = $this->db->get()->row();

            if (!$transportadora) {
                throw new Exception('Transportadora não encontrada');
            }

            // Adiciona informações da transportadora
            $std->transporta = new \stdClass();
            $std->transporta->CNPJ = preg_replace('/[^0-9]/', '', $transportadora->documento);
            $std->transporta->xNome = $transportadora->nomeCliente;
            $std->transporta->IE = !empty($transportadora->inscricao) ? $transportadora->inscricao : 'ISENTO';
            
            // Monta o endereço completo
            $endereco = $transportadora->rua;
            if (!empty($transportadora->numero)) {
                $endereco .= ', ' . $transportadora->numero;
            }
            if (!empty($transportadora->complemento)) {
                $endereco .= ' - ' . $transportadora->complemento;
            }
            $std->transporta->xEnder = $endereco;
            
            $std->transporta->xMun = $transportadora->cidade;
            $std->transporta->UF = $transportadora->estado;

            // Adiciona informações de volume (obrigatório quando modFrete != 9)
            if (empty($entrada->peso_bruto) || empty($entrada->peso_liquido)) {
                throw new Exception('Peso bruto e peso líquido são obrigatórios quando a modalidade de frete é diferente de 9 (Sem Frete)');
            }

            $std->vol = new \stdClass();
            $std->vol->qVol = '1'; // Quantidade de volumes
            $std->vol->esp = 'VOLUME'; // Espécie dos volumes
            $std->vol->pesoL = number_format($entrada->peso_liquido, 3, '.', '');
            $std->vol->pesoB = number_format($entrada->peso_bruto, 3, '.', '');

            $nfe->tagtransp($std);

            // [dest]
            $std = new \stdClass();
            $std->xNome = $fornecedor->nomeCliente;
            if (strlen(preg_replace('/[^0-9]/', '', $fornecedor->documento)) == 11) {
                $std->CPF = preg_replace('/[^0-9]/', '', $fornecedor->documento);
                $std->indIEDest = 9; // Contribuinte Isento
            } else {
                $std->CNPJ = preg_replace('/[^0-9]/', '', $fornecedor->documento);
                if (!empty($fornecedor->inscricao)) {
                    $std->IE = $fornecedor->inscricao;
                    $std->indIEDest = 1; // Contribuinte
                } else {
                    $std->indIEDest = 9; // Contribuinte Isento
                }
            }
            $nfe->tagdest($std);

            // [enderDest]
            $std = new \stdClass();
            $std->xLgr = $fornecedor->rua;
            $std->nro = $fornecedor->numero;
            if (!empty($fornecedor->complemento)) {
                $std->xCpl = $fornecedor->complemento;
            }
            $std->xBairro = $fornecedor->bairro;
            
            // Garante que o código do município (IBGE) está presente
            if (empty($fornecedor->ibge)) {
                throw new Exception('Código IBGE do município do cliente não encontrado. Por favor, verifique o cadastro do cliente.');
            }
            
            $std->cMun = $fornecedor->ibge;
            $std->xMun = $fornecedor->cidade;
            $std->UF = $fornecedor->estado;
            $std->CEP = preg_replace('/[^0-9]/', '', $fornecedor->cep);
            $std->cPais = '1058';
            $std->xPais = 'BRASIL';
            if (!empty($fornecedor->telefone)) {
                $std->fone = preg_replace('/[^0-9]/', '', $fornecedor->telefone);
            }
            $nfe->tagenderDest($std);

            // Adiciona infCpl (informação complementar) conforme solicitado
            $std = new \stdClass();
            $std->infAdFisco = '';
            
            // Inicializa o valor total do IPI
            $valor_ipi = 0;
            
            // Soma o IPI de todos os itens selecionados
            foreach ($produtos as $produto) {
                if ($devolver_todos === 'true' || in_array($produto->idProdutos, $itens_selecionados)) {
                    // Busca o item da entrada para pegar o IPI
                    $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                           ->where('produto_id', $produto->idProdutos)
                                           ->get('faturamento_entrada_itens')
                                           ->row();
                    
                    if ($item_entrada) {
                        $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                                    (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
                        
                        // Calcula o IPI proporcional à quantidade
                        $valor_ipi += ($item_entrada->valor_ipi / $item_entrada->quantidade) * $quantidade;
                    }
                }
            }
            
            if ($devolver_todos === 'true') {
                $std->infCpl = 'Devolução de Compra referente a nota ' . $entrada->numero_nota . ' com chave: ' . $entrada->chave_acesso . "\nIPI de devolução: R$ " . number_format($valor_ipi, 2, ',', '.');
            } else {
                $std->infCpl = 'Devolução de Compra Parcial referente a nota ' . $entrada->numero_nota . ' com chave: ' . $entrada->chave_acesso . "\nIPI de devolução: R$ " . number_format($valor_ipi, 2, ',', '.');
            }
            $nfe->taginfAdic($std);

            // Produtos
            $i = 1;
            $totalProdutos = 0;
            $totalICMS = 0;

            foreach ($produtos as $produto) {
                // Busca os dados de ICMS do item da entrada
                $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                       ->where('produto_id', $produto->produto_id)
                                       ->get('faturamento_entrada_itens')
                                       ->row();
                
                if (!$item_entrada) {
                    throw new Exception('Dados de ICMS do item da entrada não encontrados.');
                }

                // Usa os valores do item da entrada
                $aliq = $item_entrada->aliquota_icms;
                $base_icms = $item_entrada->base_calculo_icms;
                $valor_icms = $item_entrada->valor_icms;
                $cst = $item_entrada->cst;
                $csosn = '900'; // CSOSN 900 para devolução

                $std = new \stdClass();
                $std->item = $i;
                $std->cProd = $produto->codDeBarra;
                $std->xProd = $produto->descricao;
                $std->NCM = $produto->ncm;
                $std->CFOP = '1202'; // CFOP para devolução de compra
                $std->uCom = $produto->unidade;
                $std->qCom = $produto->quantidade;
                $std->vUnCom = $produto->valor_unitario;
                $std->vProd = $produto->quantidade * $produto->valor_unitario;
                $std->cEAN = 'SEM GTIN';
                $std->cEANTrib = 'SEM GTIN';
                $std->uTrib = $produto->unidade;
                $std->qTrib = $produto->quantidade;
                $std->vUnTrib = $produto->valor_unitario;
                $std->indTot = 1;
                $nfe->tagprod($std);

                // [imposto]
                $std = new \stdClass();
                $std->item = $i;
                $nfe->tagimposto($std);
                
                // ICMS
                $std = new \stdClass();
                $std->item = $i;
                $std->ICMS = new \stdClass();
                $std->ICMS->item = $i;
                $std->ICMS->CST = $cst;
                $std->ICMS->CSOSN = $csosn;
                $std->ICMS->vBC = number_format($base_icms, 2, '.', '');
                $std->ICMS->pICMS = number_format($aliq, 2, '.', '');
                $std->ICMS->vICMS = number_format($valor_icms, 2, '.', '');
                $nfe->tagimposto($std);

                $totalProdutos += $produto->quantidade * $produto->valor_unitario;
                $totalICMS += $valor_icms;
                $i++;
            }

            // [ICMSTot]
            $std = new \stdClass();
            
            // Calcula o valor total dos produtos proporcionalmente aos itens selecionados
            $totalProdutos = 0;
            $totalBaseICMS = 0;
            $valorTotalIPI = $this->input->post('total_ipi') ? floatval($this->input->post('total_ipi')) : 0;
            $totalICMS = 0;

            foreach ($produtos as $produto) {
                if ($devolver_todos === 'true' || in_array($produto->idProdutos, $itens_selecionados)) {
                    $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                                (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
                    
                    // Busca o item da entrada
                    $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                           ->where('produto_id', $produto->idProdutos)
                                           ->get('faturamento_entrada_itens')
                                           ->row();
                    
                    if ($item_entrada) {
                        // Calcula os valores proporcionais à quantidade
                        $proporcao = $quantidade / $item_entrada->quantidade;
                        
                        $base_icms = ($item_entrada->base_calculo_icms / $item_entrada->quantidade) * $quantidade;
                        $valor_icms = ($item_entrada->valor_icms / $item_entrada->quantidade) * $quantidade;
                        
                        // Atualiza os totais
                        $totalBaseICMS += $base_icms;
                        $totalICMS += $valor_icms;
                        
                        // Calcula o valor total proporcional à quantidade
                        $valorItem = $item_entrada->valor_unitario * $quantidade;
                        $totalProdutos += $valorItem;
                    }
                }
            }

            // Calcula o valor total da nota (vNF) como soma dos produtos + IPI
            $valorTotalNota = $totalProdutos + $valorTotalIPI;
            
            $std->vBC = number_format($totalBaseICMS, 2, '.', '');
            $std->vICMS = number_format($totalICMS, 2, '.', '');
            $std->vProd = number_format($totalProdutos, 2, '.', '');
            $std->vNF = number_format($valorTotalNota, 2, '.', ''); // Soma dos produtos + IPI
            $std->vIPIDevol = number_format($valorTotalIPI, 2, '.', '');
            $nfe->tagICMSTot($std);

            // [infAdic]
            $std = new \stdClass();
            $std->infAdFisco = '';
            
            // Inicializa o valor total do IPI
            $valor_ipi = 0;
            
            // Soma o IPI de todos os itens selecionados
            foreach ($produtos as $produto) {
                if ($devolver_todos === 'true' || in_array($produto->idProdutos, $itens_selecionados)) {
                    // Busca o item da entrada para pegar o IPI
                    $item_entrada = $this->db->where('faturamento_entrada_id', $entrada->id)
                                           ->where('produto_id', $produto->idProdutos)
                                           ->get('faturamento_entrada_itens')
                                           ->row();
                    
                    if ($item_entrada) {
                        $quantidade = $devolver_todos === 'true' ? $produto->quantidade : 
                                    (isset($quantidades[$produto->idProdutos]) ? $quantidades[$produto->idProdutos] : $produto->quantidade);
                        
                        // Calcula o IPI proporcional à quantidade
                        $valor_ipi += ($item_entrada->valor_ipi / $item_entrada->quantidade) * $quantidade;
                    }
                }
            }
            
            if ($devolver_todos === 'true') {
                $std->infCpl = 'Devolução de Compra referente a nota ' . $entrada->numero_nota . ' com chave: ' . $entrada->chave_acesso . "\nIPI de devolução: R$ " . number_format($valor_ipi, 2, ',', '.');
            } else {
                $std->infCpl = 'Devolução de Compra Parcial referente a nota ' . $entrada->numero_nota . ' com chave: ' . $entrada->chave_acesso . "\nIPI de devolução: R$ " . number_format($valor_ipi, 2, ',', '.');
            }
            $nfe->taginfAdic($std);

            // Monta o XML
            $xml = $nfe->getXML();

            // Assina o XML
            $tools = $this->getTools();
            $signed = $tools->signNFe($xml);

            // Envia para a SEFAZ
            $response = $tools->sefazEnviaLote([$signed], $configNFe->sequencia_lote);

            // Processa a resposta
            $st = new Standardize($response);
            $std = $st->toStd();
            $json = json_encode($std, JSON_PRETTY_PRINT);

            // Verifica se foi autorizada
            if ($std->cStat != 104) {
                throw new Exception("Erro ao enviar NFe: " . $std->xMotivo);
            }

            // Consulta o protocolo
            $protocolo = $tools->sefazConsultaRecibo($std->infRec->nRec);

            // Processa a resposta do protocolo
            $st = new Standardize($protocolo);
            $std = $st->toStd();
            $json = json_encode($std, JSON_PRETTY_PRINT);

            // Verifica se foi autorizada
            if ($std->prot->infProt->cStat != 100) {
                throw new Exception("NFe não autorizada: " . $std->prot->infProt->xMotivo);
            }

            // Extrai informações da NFe
            $dom = new \DOMDocument();
            $dom->loadXML($signed);
            $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
            if (!$infNFe) {
                throw new Exception("Erro ao extrair informações da NFe: tag infNFe não encontrada");
            }

            $ide = $infNFe->getElementsByTagName('ide')->item(0);
            if (!$ide) {
                throw new Exception("Erro ao extrair informações da NFe: tag ide não encontrada");
            }

            $nNF = $ide->getElementsByTagName('nNF')->item(0);
            if (!$nNF) {
                throw new Exception("Erro ao extrair informações da NFe: tag nNF não encontrada");
            }
            $numero_nfe = $nNF->nodeValue;

            // Extrai a chave da NFe
            $chNFe = $infNFe->getAttribute('Id');
            if ($chNFe) {
                $chNFe = str_replace('NFe', '', $chNFe);
            }

            // Salva NFe emitida
            $nfeData = [
                'entrada_id' => (int)$entrada->id,
                'modelo' => 55,
                'numero_nfe' => (string)$numero_nfe,
                'chave_nfe' => (string)$chNFe,
                'xml' => (string)$signed,
                'xml_protocolo' => (string)$protocolo,
                'status' => 1,
                'chave_retorno_evento' => $std->prot->infProt->xMotivo,
                'protocolo' => $std->prot->infProt->nProt,
                'valor_total' => $totalProdutos,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Insere na tabela nfe_emitidas
            $this->db->insert('nfe_emitidas', $nfeData);

            if ($this->db->affected_rows() == 0) {
                throw new Exception('Erro ao salvar NFe no banco de dados');
            }

            // Atualiza a sequência da nota
            $this->db->where('idConfiguracao', 1);
            $this->db->update('configuracoes_nfe', [
                'sequencia_nota' => $configNFe->sequencia_nota + 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Prepara dados para o modal
            $modalData = [
                'status' => 'Autorizada',
                'motivo' => $std->prot->infProt->xMotivo,
                'protocolo' => $std->prot->infProt->nProt,
                'xml' => $signed,
                'chave_nfe' => $chNFe,
                'numero_nfe' => $numero_nfe
            ];

            $this->session->set_flashdata('nfe_modal', $modalData);
            $this->session->set_flashdata('success', 'NFe reemitida com sucesso!');
            redirect(base_url() . 'index.php/nfe/gerenciar');

        } catch (Exception $e) {
            log_message('error', 'Erro ao reemitir NFe: ' . $e->getMessage());
            
            // Prepara dados para o modal em caso de exceção
            $modalData = [
                'status' => 'Rejeitada',
                'motivo' => $e->getMessage(),
                'protocolo' => '',
                'xml' => isset($signed) ? $signed : '',
                'chave_nfe' => '',
                'numero_nfe' => ''
            ];
            
            $this->session->set_flashdata('nfe_modal', $modalData);
            $this->session->set_flashdata('error', $e->getMessage());
            redirect(base_url() . 'index.php/nfe/gerenciar');
        }
    }

    private function get_cUF($uf)
    {
        $ufs = array(
            'AC' => '12', 'AL' => '27', 'AM' => '13', 'AP' => '16', 'BA' => '29',
            'CE' => '23', 'DF' => '53', 'ES' => '32', 'GO' => '52', 'MA' => '21',
            'MG' => '31', 'MS' => '50', 'MT' => '51', 'PA' => '15', 'PB' => '25',
            'PE' => '26', 'PI' => '22', 'PR' => '41', 'RJ' => '33', 'RN' => '24',
            'RO' => '11', 'RR' => '14', 'RS' => '43', 'SC' => '42', 'SE' => '28',
            'SP' => '35', 'TO' => '17'
        );
        return isset($ufs[$uf]) ? $ufs[$uf] : '35';
    }

    private function calculaDV($codigo)
    {
        $pesos = array(2, 3, 4, 5, 6, 7, 8, 9);
        $soma = 0;
        $j = 0;
        for ($i = strlen($codigo) - 1; $i >= 0; $i--) {
            $soma += $codigo[$i] * $pesos[$j];
            $j = ($j + 1) % 8;
        }
        $resto = $soma % 11;
        if ($resto == 0 || $resto == 1) {
            return 0;
        }
        return 11 - $resto;
    }

    private function getTools()
    {
        // Carrega configurações da NFe
        $this->db->where('idConfiguracao', 1);
        $configNFe = $this->db->get('configuracoes_nfe')->row();
        if (!$configNFe) {
            throw new Exception('Configurações de NFe não encontradas.');
        }

        // Carrega dados do emitente
        $emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            throw new Exception('Emitente não configurado.');
        }

        // Prepara configuração em JSON
        $config = [
            'atualizacao' => date('Y-m-d H:i:s'),
            'tpAmb' => (int)$configNFe->ambiente,
            'razaosocial' => $emitente->nome,
            'siglaUF' => $emitente->uf,
            'cnpj' => preg_replace('/[^0-9]/', '', $emitente->cnpj),
            'schemes' => 'PL_009_V4',
            'versao' => '4.00',
            'tokenIBPT' => '',
            'CSC' => $configNFe->csc,
            'CSCid' => $configNFe->csc_id
        ];

        // Carrega o certificado da tabela nfe_certificates
        $this->load->model('Nfe_model');
        $certificate = $this->Nfe_model->getCertificate();
        if (!$certificate) {
            throw new Exception('Certificado digital não encontrado. Configure o certificado nas configurações do sistema.');
        }

        // Verifica se o certificado está vencido
        $dataValidade = new DateTime($certificate->data_validade);
        $hoje = new DateTime();
        if ($hoje > $dataValidade) {
            throw new Exception('O certificado digital está vencido. Por favor, atualize o certificado nas configurações do sistema.');
        }

        // Carrega o certificado
        try {
            $cert = Certificate::readPfx($certificate->certificado_digital, $certificate->senha_certificado);
        } catch (Exception $e) {
            throw new Exception('Erro ao ler o certificado digital: ' . $e->getMessage());
        }

        // Retorna instância do Tools com configuração e certificado
        return new \NFePHP\NFe\Tools(json_encode($config), $cert);
    }
} 
