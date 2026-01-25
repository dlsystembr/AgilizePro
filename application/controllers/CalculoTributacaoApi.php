<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * API para calcular todos os impostos (ICMS, IPI, PIS, COFINS) de uma operação.
 * Uso: GET index.php/calculotributacaoapi/calcular?ten_id=1&produto_id=123&cliente_id=456&operacao_id=1&valor=100.00&quantidade=2
 */
class CalculoTributacaoApi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Não usar MY_Controller para evitar verificação de sessão
        // Esta API recebe ten_id como parâmetro e não precisa de sessão
        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('TributacaoFederal_model');
        $this->load->model('TributacaoEstadual_model');
        $this->load->model('Aliquotas_model');
        $this->load->model('produtos_model', 'Produtos_model');
    }

    public function calcular()
    {
        header('Content-Type: application/json');

        $tenId = (int)$this->input->get('ten_id');
        $produtoId = (int)$this->input->get('produto_id');
        $clienteId = (int)$this->input->get('cliente_id');
        $operacaoId = (int)$this->input->get('operacao_id');
        $valor = $this->input->get('valor'); // Valor unitário do produto
        $quantidade = (int)$this->input->get('quantidade', 1);
        $tipoOperacao = $this->input->get('tipo_operacao', 'saida'); // entrada ou saida
        $enderecoId = $this->input->get('endereco_id'); // ID do endereço selecionado (opcional)

        // Obrigatórios: ten_id, produto_id, cliente_id, operacao_id, valor
        if (empty($tenId) || empty($produtoId) || empty($clienteId) || empty($operacaoId) || empty($valor)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Parâmetros obrigatórios: ten_id, produto_id, cliente_id, operacao_id e valor'
            ]);
            return;
        }

        // Converter valor para float
        // O valor vem formatado como "1.00" (ponto decimal) do number_format($valor, 2, '.', '')
        // IMPORTANTE: number_format sempre retorna com ponto decimal, sem separador de milhar
        // Exemplos válidos: "1.00", "100.50", "1000.00"
        $valorOriginal = $valor;
        
        // Se tiver vírgula: pode ser formato brasileiro (1,00) ou formato com milhar (1.000,50)
        if (strpos($valor, ',') !== false) {
            // Se também tiver ponto, é formato brasileiro com milhar (1.000,50)
            if (strpos($valor, '.') !== false) {
                $valor = str_replace('.', '', $valor); // Remove separador de milhar
                $valor = str_replace(',', '.', $valor); // Converte vírgula decimal para ponto
            } else {
                // Só tem vírgula: formato brasileiro simples (1,00)
                $valor = str_replace(',', '.', $valor);
            }
        }
        // Se só tiver ponto (sem vírgula): é formato americano (1.00)
        // Como vem do number_format, sempre terá 2 casas decimais após o ponto
        // Não precisa fazer nada, floatval() já converte corretamente "1.00" para 1.0
        
        $valor = floatval($valor);
        
        // Validação: se o valor convertido for muito grande, pode ter havido erro na conversão
        if ($valor > 1000000) {
            log_message('error', 'CalculoTributacaoApi - Valor convertido muito alto: ' . $valor . ' (original: ' . $valorOriginal . ')');
        }
        
        log_message('debug', 'CalculoTributacaoApi - Valor recebido: "' . $valorOriginal . '" | Valor convertido: ' . $valor . ' | Quantidade: ' . $quantidade . ' | ValorBase será: ' . ($valor * $quantidade));

        // Validar tipo_operacao
        $tipoOperacao = strtolower($tipoOperacao);
        if (!in_array($tipoOperacao, ['entrada', 'saida'])) {
            $tipoOperacao = 'saida';
        }

        try {
            log_message('debug', 'CalculoTributacaoApi - Iniciando cálculo - tenId: ' . $tenId . ', produtoId: ' . $produtoId . ', clienteId: ' . $clienteId . ', operacaoId: ' . $operacaoId);
            
            // 1. Buscar dados do produto (para pegar NCM)
            // Buscar diretamente do banco, pois getById depende de sessão
            $this->db->select('*');
            $this->db->from('produtos');
            $this->db->where('PRO_ID', $produtoId);
            $this->db->where('ten_id', $tenId);
            $this->db->limit(1);
            $produto = $this->db->get()->row();
            if (!$produto) {
                log_message('error', 'CalculoTributacaoApi - Produto não encontrado: ' . $produtoId);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Produto não encontrado.'
                ]);
                return;
            }

            $ncmId = isset($produto->NCM_ID) ? $produto->NCM_ID : null;
            if (!$ncmId) {
                log_message('error', 'CalculoTributacaoApi - Produto sem NCM: ' . $produtoId);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Produto não possui NCM configurado.'
                ]);
                return;
            }
            
            log_message('debug', 'CalculoTributacaoApi - Produto encontrado - NCM ID: ' . $ncmId);

            // Origem do produto (0=Nacional, 1=Estrangeira, 2=Estrangeira com adquirente, etc.)
            $origemProduto = isset($produto->PRO_ORIGEM) ? (int)$produto->PRO_ORIGEM : (isset($produto->origem) ? (int)$produto->origem : 0);

            // 2. Buscar dados do cliente (para pegar UF e natureza)
            // Se endereco_id for fornecido, usar esse endereço específico
            // Caso contrário, usar o endereço padrão
            $this->db->select('c.CLN_ID, p.PES_NOME, p.PES_CPFCNPJ, 
                              est.EST_UF as uf_cliente,
                              doc.DOC_NATUREZA_CONTRIBUINTE as natureza_contribuinte,
                              c.CLN_OBJETIVO_COMERCIAL as objetivo_comercial');
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'c.PES_ID = p.PES_ID', 'left');
            $this->db->join('enderecos end', 'p.PES_ID = end.PES_ID', 'left');
            $this->db->join('municipios mun', 'end.MUN_ID = mun.MUN_ID', 'left');
            $this->db->join('estados est', 'mun.EST_ID = est.EST_ID', 'left');
            $this->db->join('documentos doc', 'p.PES_ID = doc.PES_ID', 'left');
            $this->db->where('c.CLN_ID', $clienteId);
            
            // Filtrar por endereço selecionado ou usar padrão
            if (!empty($enderecoId)) {
                $this->db->where('end.END_ID', (int)$enderecoId);
            } else {
                // Se não houver endereço selecionado, usar o padrão
                // Se não houver padrão, pegar o primeiro endereço disponível
                $this->db->where('(end.END_PADRAO = 1 OR end.END_ID IS NOT NULL)', null, false);
                $this->db->order_by('end.END_PADRAO', 'DESC'); // Priorizar endereço padrão
                $this->db->order_by('end.END_ID', 'ASC'); // Se não houver padrão, pegar o primeiro
            }
            
            $cliente = $this->db->get()->row();

            if (!$cliente) {
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Cliente não encontrado.'
                ]);
                return;
            }

            $ufCliente = $cliente->uf_cliente ?? null;
            $natureza = $cliente->natureza_contribuinte ?? 'Contribuinte ICMS';
            $objetivo = $cliente->objetivo_comercial ?? 'consumo';

            // 3. Determinar destinação (estadual ou interestadual)
            // Buscar UF da empresa (tabela empresas normalmente não possui ten_id)
            $this->db->select('EMP_UF');
            $this->db->from('empresas');
            $this->db->limit(1);
            $empresa = $this->db->get()->row();
            $ufEmpresa = $empresa ? $empresa->EMP_UF : null;

            $destinacao = ($ufCliente && $ufEmpresa && $ufCliente === $ufEmpresa) ? 'estadual' : 'interestadual';

            // 4. Buscar classificação fiscal (usar query direta pois getTributacao depende de sessão)
            $this->db->select('CLF_ID as id, CLF_CST as cst, CLF_CSOSN as csosn, CLF_CFOP as cfop, 
                              CLF_TIPO_TRIBUTACAO as tipo_tributacao, CLF_NATUREZA_CONTRIBUINTE as natureza_contribuinte');
            $this->db->from('classificacao_fiscal');
            $this->db->where('OPC_ID', $operacaoId);
            $this->db->where('CLF_NATUREZA_CONTRIBUINTE', $natureza);
            $this->db->where('CLF_DESTINACAO', $destinacao);
            $this->db->where('CLF_OBJETIVO_COMERCIAL', $objetivo);
            $this->db->where('ten_id', $tenId);
            $this->db->where('CLF_SITUACAO', 'ativa');
            $this->db->limit(1);
            $classificacaoFiscal = $this->db->get()->row();
            
            log_message('debug', 'CalculoTributacaoApi - Classificação fiscal: ' . ($classificacaoFiscal ? 'encontrada' : 'não encontrada'));

            // 5. Buscar tributação federal
            $tributacaoFederal = null;
            if ($ncmId) {
                $tributacaoFederal = $this->TributacaoFederal_model->getByTenantAndNcm($tenId, $ncmId, $tipoOperacao);
            }

            // 6. Buscar tributação estadual
            $tributacaoEstadual = null;
            if ($ncmId && $ufCliente) {
                $tributacaoEstadual = $this->TributacaoEstadual_model->getByTenantAndNcmAndUf($tenId, $ncmId, $ufCliente);
            }

            // 7. Calcular valores
            // $valor já está convertido para float (ex: 1.00 vira 1.0)
            // $quantidade é int (ex: 1)
            $valorBase = $valor * $quantidade;
            
            log_message('debug', 'CalculoTributacaoApi - Cálculo base: valor=' . $valor . ' × quantidade=' . $quantidade . ' = valorBase=' . $valorBase);

            // Impostos Federais - Inicializar variáveis
            $valorIpi = 0;
            $valorPis = 0;
            $valorCofins = 0;
            $aliqIpi = 0;
            $aliqPis = 0;
            $aliqCofins = 0;
            $baseIpi = 0;
            $basePis = 0;
            $baseCofins = 0;

            // Verificar CSTs dos impostos federais
            $cstIpi = $tributacaoFederal ? ($tributacaoFederal->cst_ipi ?? null) : null;
            $cstPis = $tributacaoFederal ? ($tributacaoFederal->cst_pis ?? null) : null;
            $cstCofins = $tributacaoFederal ? ($tributacaoFederal->cst_cofins ?? null) : null;
            
            // CSTs IPI que CALCULAM: 00 (entrada com recuperação), 50 (saída tributada)
            // CSTs IPI que NÃO calculam: 01-05, 49 (entrada), 51-55, 99 (saída)
            $cstsIpiQueCalculam = ['00', '50'];
            $calculaIpi = $cstIpi && in_array($cstIpi, $cstsIpiQueCalculam);
            
            // CSTs PIS/COFINS que CALCULAM: 01 (Operação Tributável - Base de Cálculo = Valor da Operação Alíquota Normal), 02 (Operação Tributável - Base de Cálculo = Valor da Operação Alíquota Diferenciada)
            // CSTs PIS/COFINS que NÃO calculam: 03-09, 49, 50-99 (isento, não tributado, suspenso, etc.)
            $cstsPisCofinsQueCalculam = ['01', '02'];
            $calculaPis = $cstPis && in_array($cstPis, $cstsPisCofinsQueCalculam);
            $calculaCofins = $cstCofins && in_array($cstCofins, $cstsPisCofinsQueCalculam);

            if ($tributacaoFederal) {
                // IPI - só calcula se CST permitir
                if ($calculaIpi) {
                    $aliqIpi = floatval($tributacaoFederal->aliquota_ipi ?? 0);
                    $baseIpi = $valorBase;
                    $valorIpi = ($baseIpi * $aliqIpi) / 100;
                } else {
                    // Mesmo sem calcular, pode ter alíquota zero para referência
                    $aliqIpi = floatval($tributacaoFederal->aliquota_ipi ?? 0);
                    $baseIpi = 0;
                    $valorIpi = 0;
                }
                
                // PIS - só calcula se CST permitir
                if ($calculaPis) {
                    $aliqPis = floatval($tributacaoFederal->aliquota_pis ?? 0);
                    $basePis = $valorBase; // Base = valor unitário × quantidade
                    $valorPis = ($basePis * $aliqPis) / 100;
                    log_message('debug', 'CalculoTributacaoApi - PIS: base=' . $basePis . ', aliq=' . $aliqPis . '%, valor=' . $valorPis);
                } else {
                    $aliqPis = floatval($tributacaoFederal->aliquota_pis ?? 0);
                    $basePis = 0;
                    $valorPis = 0;
                }
                
                // COFINS - só calcula se CST permitir
                if ($calculaCofins) {
                    $aliqCofins = floatval($tributacaoFederal->aliquota_cofins ?? 0);
                    $baseCofins = $valorBase; // Base = valor unitário × quantidade
                    $valorCofins = ($baseCofins * $aliqCofins) / 100;
                    log_message('debug', 'CalculoTributacaoApi - COFINS: base=' . $baseCofins . ', aliq=' . $aliqCofins . '%, valor=' . $valorCofins);
                } else {
                    $aliqCofins = floatval($tributacaoFederal->aliquota_cofins ?? 0);
                    $baseCofins = 0;
                    $valorCofins = 0;
                }
            }

            // Impostos Estaduais - Inicializar variáveis
            $valorIcms = 0;
            $valorIcmsSt = 0;
            $valorFcp = 0;
            $aliqIcms = 0;
            $aliqIcmsSt = 0;
            $aliqFcp = 0;
            $mva = 0;
            $percentualReducaoIcms = 0;
            $percentualReducaoSt = 0;
            $baseIcms = 0;
            $baseSt = 0;
            $baseFcp = 0;

            // Verificar se deve calcular ICMS baseado no tipo de tributação e CST
            $tipoTributacaoEstadual = $tributacaoEstadual ? $tributacaoEstadual->tipo_tributacao : null;
            $cst = $classificacaoFiscal ? ($classificacaoFiscal->cst ?? null) : null;
            
            // Função auxiliar para verificar se calcula ICMS baseado no CST
            // CSTs que CALCULAM ICMS: 00, 10, 20, 30, 70 (com regras específicas)
            // CSTs que NÃO calculam ICMS: 40 (Isenta), 41 (Não tributada), 50 (Suspensão), 51 (Diferimento), 60 (ICMS cobrado anteriormente por ST)
            $cstsQueCalculamIcms = ['00', '10', '20', '30', '70'];
            $cstsSemIcms = ['40', '41', '50', '51', '60'];
            
            // Verificar se é serviço (não calcula ICMS)
            // Verificar tanto na tributação estadual quanto na classificação fiscal
            $tipoTributacaoClassificacao = $classificacaoFiscal ? ($classificacaoFiscal->tipo_tributacao ?? null) : null;
            $naoCalculaIcms = ($tipoTributacaoEstadual === 'Serviço' || $tipoTributacaoClassificacao === 'Serviço');
            
            // Se tiver CST, verificar se calcula ou não
            if ($cst) {
                if (in_array($cst, $cstsSemIcms)) {
                    $naoCalculaIcms = true; // Isento, não tributado, suspenso, diferido, etc.
                } elseif (!in_array($cst, $cstsQueCalculamIcms)) {
                    // Se o CST não está na lista de calcula nem na de não calcula, considerar como não calcula por segurança
                    $naoCalculaIcms = true;
                }
            }

            // Inicializar valores da tributação estadual
            if ($tributacaoEstadual) {
                $aliqIcms = floatval($tributacaoEstadual->aliquota_icms ?? 0);
                $aliqIcmsSt = floatval($tributacaoEstadual->aliquota_icms_st ?? 0);
                $aliqFcp = floatval($tributacaoEstadual->aliquota_fcp ?? 0);
                $mva = floatval($tributacaoEstadual->mva ?? 0);
                $percentualReducaoIcms = floatval($tributacaoEstadual->percentual_reducao_icms ?? 0);
                $percentualReducaoSt = floatval($tributacaoEstadual->percentual_reducao_st ?? 0);
            } else {
                // Se não houver tributação estadual, inicializar com zero
                $aliqIcms = 0;
                $aliqIcmsSt = 0;
                $aliqFcp = 0;
                $mva = 0;
                $percentualReducaoIcms = 0;
                $percentualReducaoSt = 0;
            }

            // Calcular ICMS baseado no CST
            // CST 00: Tributada integralmente - calcula normalmente
            // CST 10: Tributada com ST - calcula ICMS normal e pode ter ST
            // CST 20: Tributada com redução de base - calcula com redução
            // CST 30: Isenta ou não tributada com ST - não calcula ICMS, mas pode ter ST
            // CST 70: Tributada com redução de base e ST - calcula com redução e pode ter ST
            
            if (!$naoCalculaIcms) {
                // Verificar se precisa buscar alíquota da tabela se estiver zero
                // Só busca se realmente vai calcular (não é CST isento/não tributado)
                if ($aliqIcms == 0 && $ufEmpresa && $ufCliente) {
                    // Buscar alíquota da tabela usando ten_id
                    $this->db->select('*');
                    $this->db->from('aliquotas');
                    $this->db->where('uf_origem', $ufEmpresa);
                    $this->db->where('uf_destino', $ufCliente);
                    $this->db->where('ten_id', $tenId);
                    $aliquotaTabela = $this->db->get()->row();
                    
                    if ($aliquotaTabela) {
                        // Se for estadual, usa aliquota_origem; se for interestadual, usa aliquota_destino
                        $isEstadual = ($ufCliente === $ufEmpresa);
                        $aliqIcms = $isEstadual ? floatval($aliquotaTabela->aliquota_origem ?? 0) : floatval($aliquotaTabela->aliquota_destino ?? 0);
                    }
                }
                
                // Calcular ICMS apenas se NÃO for serviço/CST isento E tiver alíquota
                if (!$naoCalculaIcms && $aliqIcms > 0) {
                    // CST 20 e 70 têm redução de base
                    $temReducaoBase = in_array($cst, ['20', '70']);
                    
                    // Base de cálculo ICMS = Valor da operação + IPI (quando aplicável)
                    // IPI integra a base do ICMS quando:
                    // - NÃO for entre contribuintes, OU
                    // - NÃO for destinado à industrialização/comercialização, OU
                    // - NÃO configurar fato gerador de ambos os impostos
                    $baseIcms = $valorBase;
                    
                    // Verificar se deve incluir IPI na base de cálculo
                    // IPI NÃO integra quando TODAS as condições são verdadeiras:
                    // 1. Operação entre contribuintes (cliente é contribuinte)
                    // 2. Destinado à industrialização ou comercialização (objetivo = revenda/industrialização)
                    // 3. Configura fato gerador de ambos os impostos (tem IPI e ICMS)
                    $ehContribuinte = ($natureza === 'Contribuinte ICMS' || strpos(strtolower($natureza), 'contribuinte') !== false);
                    $ehRevendaOuIndustrializacao = (in_array(strtolower($objetivo), ['revenda', 'industrialização', 'industrializacao']));
                    $temIpiEICMS = ($valorIpi > 0 && $aliqIcms > 0);
                    
                    // Se NÃO for entre contribuintes OU NÃO for revenda/industrialização OU não tem ambos impostos, incluir IPI
                    $incluirIpiNaBase = !($ehContribuinte && $ehRevendaOuIndustrializacao && $temIpiEICMS);
                    
                    if ($incluirIpiNaBase && $valorIpi > 0) {
                        $baseIcms = $baseIcms + $valorIpi;
                    }
                    
                    // Aplicar redução de base (CST 20 e 70, ou se configurado)
                    if ($temReducaoBase && $percentualReducaoIcms > 0) {
                        $baseIcms = $baseIcms * (1 - ($percentualReducaoIcms / 100));
                    } elseif (!$temReducaoBase && $percentualReducaoIcms > 0) {
                        // Mesmo que não seja CST 20/70, se tiver redução configurada, aplicar
                        $baseIcms = $baseIcms * (1 - ($percentualReducaoIcms / 100));
                    }
                    
                    $valorIcms = ($baseIcms * $aliqIcms) / 100;
                } else {
                    // Não calcula ICMS (CST isento/não tributado/suspenso OU sem alíquota)
                    // Manter base e valor em zero
                    $baseIcms = 0;
                    $valorIcms = 0;
                }
                
                // ICMS ST - calcula para CST 10, 30, 70
                $cstsComSt = ['10', '30', '70'];
                if (in_array($cst, $cstsComSt) && $mva > 0 && $aliqIcmsSt > 0) {
                    // Base ST = Valor Base × (1 + MVA/100)
                    $baseSt = $valorBase * (1 + ($mva / 100));
                    if ($percentualReducaoSt > 0) {
                        $baseSt = $baseSt * (1 - ($percentualReducaoSt / 100));
                    }
                    // ICMS ST = (Base ST × Alíquota ST) - ICMS Normal
                    $icmsStCalculado = ($baseSt * $aliqIcmsSt) / 100;
                    $valorIcmsSt = $icmsStCalculado - $valorIcms;
                    if ($valorIcmsSt < 0) {
                        $valorIcmsSt = 0; // Não pode ser negativo
                    }
                } else {
                    $baseSt = 0;
                    $valorIcmsSt = 0;
                }

                // FCP - Base de cálculo FCP é a mesma base do ICMS (quando calcula ICMS)
                if ($aliqFcp > 0 && $baseIcms > 0) {
                    $baseFcp = $baseIcms;
                    $valorFcp = ($baseFcp * $aliqFcp) / 100;
                } else {
                    $baseFcp = 0;
                    $valorFcp = 0;
                }
            } else {
                // Não calcula ICMS (CST isento, não tributado, etc.)
                $baseIcms = 0;
                $baseSt = 0;
                $baseFcp = 0;
                $valorIcms = 0;
                $valorIcmsSt = 0;
                $valorFcp = 0;
            }

            // Valor total
            $valorTotal = $valorBase + $valorIpi;
            $totalImpostos = $valorIpi + $valorPis + $valorCofins + $valorIcms + $valorIcmsSt + $valorFcp;

            // 8. Montar resposta
            echo json_encode([
                'sucesso' => true,
                'dados' => [
                    // Classificação Fiscal
                    'classificacao_fiscal' => [
                        'id' => $classificacaoFiscal->id ?? null,
                        'cst' => $classificacaoFiscal->cst ?? null,
                        'csosn' => $classificacaoFiscal->csosn ?? null,
                        'cfop' => $classificacaoFiscal->cfop ?? null
                    ],
                    // Tributação Federal
                    'tributacao_federal' => [
                        'cst_ipi' => $tributacaoFederal->cst_ipi ?? null,
                        'cst_pis' => $tributacaoFederal->cst_pis ?? null,
                        'cst_cofins' => $tributacaoFederal->cst_cofins ?? null
                    ],
                    // Tributação Estadual
                    'tributacao_estadual' => [
                        'uf' => $tributacaoEstadual->uf ?? null,
                        'tipo_tributacao' => $tributacaoEstadual->tipo_tributacao ?? null
                    ],
                    // Valores
                    'valores' => [
                        'quantidade' => $quantidade,
                        'valor_unitario' => number_format($valor, 2, '.', ''),
                        'valor_base' => number_format($valorBase, 2, '.', ''),
                        'valor_total' => number_format($valorTotal, 2, '.', ''),
                        'total_impostos' => number_format($totalImpostos, 2, '.', '')
                    ],
                    // Impostos Federais
                    'impostos_federais' => [
                        'ipi' => [
                            'cst' => $tributacaoFederal->cst_ipi ?? null,
                            'base_calculo' => number_format($baseIpi, 2, '.', ''),
                            'aliquota' => number_format($aliqIpi, 2, '.', ''),
                            'valor' => number_format($valorIpi, 2, '.', '')
                        ],
                        'pis' => [
                            'cst' => $tributacaoFederal->cst_pis ?? null,
                            'base_calculo' => number_format($basePis, 2, '.', ''),
                            'aliquota' => number_format($aliqPis, 2, '.', ''),
                            'valor' => number_format($valorPis, 2, '.', '')
                        ],
                        'cofins' => [
                            'cst' => $tributacaoFederal->cst_cofins ?? null,
                            'base_calculo' => number_format($baseCofins, 2, '.', ''),
                            'aliquota' => number_format($aliqCofins, 2, '.', ''),
                            'valor' => number_format($valorCofins, 2, '.', '')
                        ]
                    ],
                    // Impostos Estaduais
                    'impostos_estaduais' => [
                        'icms' => [
                            'origem' => $origemProduto,
                            'modalidade_bc' => 3, // 0=Margem de valor agregado, 1=Pauta, 2=Preço tabelado, 3=Valor da operação
                            'base_calculo' => number_format($baseIcms, 2, '.', ''),
                            'aliquota' => number_format($aliqIcms, 2, '.', ''),
                            'percentual_reducao' => number_format($percentualReducaoIcms, 2, '.', ''),
                            'valor' => number_format($valorIcms, 2, '.', '')
                        ],
                        'icms_st' => [
                            'modalidade_bc_st' => 4, // 0=Preço tabelado ou máximo sugerido, 4=Margem de valor agregado, 5=Preço tabelado ou máximo sugerido
                            'base_calculo' => number_format($baseSt, 2, '.', ''),
                            'mva' => number_format($mva, 2, '.', ''),
                            'aliquota' => number_format($aliqIcmsSt, 2, '.', ''),
                            'percentual_reducao' => number_format($percentualReducaoSt, 2, '.', ''),
                            'valor' => number_format($valorIcmsSt, 2, '.', '')
                        ],
                        'fcp' => [
                            'base_calculo' => number_format($baseFcp, 2, '.', ''),
                            'aliquota' => number_format($aliqFcp, 2, '.', ''),
                            'valor' => number_format($valorFcp, 2, '.', '')
                        ]
                    ]
                ]
            ]);

        } catch (Exception $e) {
            log_message('error', 'Erro em CalculoTributacaoApi: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro ao calcular tributação: ' . $e->getMessage()
            ]);
        } catch (Error $e) {
            log_message('error', 'Erro fatal em CalculoTributacaoApi: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro fatal ao calcular tributação: ' . $e->getMessage()
            ]);
        }
    }
}
