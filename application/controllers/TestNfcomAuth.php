<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TestNfcomAuth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('NFComMake');
        $this->load->library('NFComService');
        $this->load->model('Nfe_model');
        $this->load->model('ConfiguracoesFiscais_model');
        $this->load->model('Nfecom_model');
    }

    public function index()
    {
        echo "<h1>Teste de Emissão Manual NFCom (Homologação)</h1>";
        echo "<pre>";

        try {
            // 1. Carregar Configurações Fiscais (Certificado)
            $empresa = $this->db->limit(1)->get('empresas')->row();
            $configFiscal = $this->ConfiguracoesFiscais_model->getByTipo($empresa->EMP_ID, 'NFCOM');

            if (!$configFiscal || empty($configFiscal->CER_ARQUIVO)) {
                die("ERRO: Certificado não configurado no sistema.");
            }

            // 2. Dados do Emitente (Do Banco)
            $emitenteDb = $this->Nfe_model->getEmit();

            // 3. Dados Fixos para Teste
            $dados = [
                'chave' => '', // Será gerada
                'ide' => [
                    'cUF' => '43', // RS
                    'tpAmb' => 2, // 2 = Homologação
                    'serie' => '1',
                    'nNF' => rand(1000, 9999), // Número aleatório
                    'cNF' => rand(10000000, 99999999),
                    'cDV' => 0, // Calculado depois
                    'dhEmi' => date('c'),
                    'tpEmis' => 1,
                    'nSiteAutoriz' => 0,
                    'cMunFG' => '4314902', // Porto Alegre
                    'finNFCom' => 0,
                    'tpFat' => 0
                ],
                'emitente' => [
                    'cnpj' => $emitenteDb['CNPJ'],
                    'ie' => $emitenteDb['IE'],
                    'crt' => $emitenteDb['CRT'],
                    'razao_social' => $emitenteDb['xNome'],
                    'nome_fantasia' => $emitenteDb['xFant'] ?? $emitenteDb['xNome'],
                    'endereco' => [
                        'logradouro' => $emitenteDb['enderEmit']['xLgr'],
                        'numero' => $emitenteDb['enderEmit']['nro'],
                        'complemento' => $emitenteDb['enderEmit']['xCpl'] ?? '',
                        'bairro' => $emitenteDb['enderEmit']['xBairro'],
                        'codigo_municipio' => $emitenteDb['enderEmit']['cMun'],
                        'municipio' => $emitenteDb['enderEmit']['xMun'],
                        'cep' => $emitenteDb['enderEmit']['CEP'],
                        'uf' => $emitenteDb['enderEmit']['UF']
                    ],
                    'telefone' => $emitenteDb['enderEmit']['fone'] ?? ''
                ],
                'destinatario' => [
                    'nome' => 'NF-E EMITIDA EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',
                    'cnpj' => '02542472000100', // CNPJ de Teste (Ou o mesmo do emitente se preferir)
                    'indicador_ie' => 9, // Não Contribuinte
                    'ie' => '',
                    'endereco' => [
                        'logradouro' => 'Av Ipiranga',
                        'numero' => '123',
                        'complemento' => '',
                        'bairro' => 'Centro',
                        'codigo_municipio' => '4314902', // Porto Alegre
                        'municipio' => 'Porto Alegre',
                        'cep' => '90000000',
                        'uf' => 'RS'
                    ]
                ],
                'assinante' => [
                    'codigo' => '0001',
                    'tipo' => 1, // Comercial/Industrial
                    'tipo_servico' => 1, // Telefonia
                    'numero_contrato' => '999',
                    'data_inicio_contrato' => date('Y-m-d')
                ],
                'itens' => [
                    [
                        'nItem' => 1,
                        'codigo' => '001',
                        'descricao' => 'Servico de Internet Banda Larga',
                        'classificacao_item' => '010101', // Classificação válida
                        'cfop' => '5303',
                        'unidade' => 'UN',
                        'quantidade' => 1.0000,
                        'valor_unitario' => 100.00,
                        'valor_total' => 100.00,
                        'desconto' => 0.00,
                        'outros' => 0.00,
                        'imposto' => [
                            'icms' => [
                                'cst' => '00',
                                'vBC' => 0.00,
                                'pICMS' => 0.00,
                                'vICMS' => 0.00
                            ],
                            'pis' => [
                                'cst' => '01',
                                'vBC' => 100.00,
                                'pPIS' => 0.65,
                                'vPIS' => 0.65
                            ],
                            'cofins' => [
                                'cst' => '01',
                                'vBC' => 100.00,
                                'pCOFINS' => 3.00,
                                'vCOFINS' => 3.00
                            ]
                        ]
                    ]
                ],
                'totais' => [
                    'vProd' => 100.00,
                    'icms' => ['vBC' => 0, 'vICMS' => 0],
                    'vCOFINS' => 3.00,
                    'vPIS' => 0.65,
                    'vDesc' => 0.00,
                    'vOutro' => 0.00,
                    'vNF' => 100.00
                ],
                'faturamento' => [
                    'competencia' => date('ym'),
                    'vencimento' => date('Y-m-d'),
                    'periodo_inicio' => date('Y-m-d'),
                    'periodo_fim' => date('Y-m-d')
                ],
                'informacoes_adicionais' => [
                    'complementar' => 'Teste de emissao manual'
                ],
                'suplementar' => [
                    'qrCode' => '' // Gerado pelo Make se vazio, ou podemos ignorar por hora
                ]
            ];

            echo "Construindo XML...\n";
            $nfcomMake = new NFComMake();
            $xml = $nfcomMake->build($dados);

            echo "XML Gerado com sucesso.\n";
            // echo htmlentities($xml) . "\n\n";

            echo "Configurando Serviço...\n";
            $nfcomService = new NFComService([
                'ambiente' => 2,
                'disable_cert_validation' => true,
                'debug' => true
            ]);
            $nfcomService->setCertificate($configFiscal->CER_ARQUIVO, $configFiscal->CER_SENHA);

            echo "Assinando XML...\n";
            $xmlSigned = $nfcomService->sign($xml);
            echo "XML Assinado.\n";

            echo "Enviando para SEFAZ...\n";
            $retorno = $nfcomService->send($xmlSigned);

            echo "<h1>Retorno SEFAZ:</h1>";
            print_r($retorno);

            if (isset($retorno['xml'])) {
                echo "\nXML Retorno:\n" . htmlentities($retorno['xml']);
                file_put_contents('debug_manual_retorno.xml', $retorno['xml']);
            }

        } catch (Exception $e) {
            echo "<h1>ERRO (Exception):</h1>";
            echo $e->getMessage() . "\n";
            echo $e->getTraceAsString();
        }

        echo "</pre>";
    }
}
