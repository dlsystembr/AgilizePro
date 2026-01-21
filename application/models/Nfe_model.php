<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Nfe_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        return $this->db->insert('nfe_emitidas', $data);
    }

    public function getNfe($param = array())
    {
        // Se for um número, busca por ID único
        if (is_numeric($param)) {
            $this->db->select('nfe_emitidas.*, clientes.nomeCliente');
            $this->db->from('nfe_emitidas');
            $this->db->where('nfe_emitidas.ten_id', $this->session->userdata('ten_id'));
            // Verifica se a tabela nfe_emitidas tem o campo cliente_id
            $fields = $this->db->field_data('nfe_emitidas');
            $hasClienteId = false;
            foreach ($fields as $field) {
                if ($field->name == 'cliente_id') {
                    $hasClienteId = true;
                    break;
                }
            }
            if ($hasClienteId) {
                $this->db->join('clientes', 'clientes.idClientes = nfe_emitidas.cliente_id', 'left');
            } else {
                $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
                $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
            }
            $this->db->where('nfe_emitidas.id', $param);
            $query = $this->db->get();
            if (!$query) {
                log_message('error', 'Erro ao buscar NFe por ID: ' . $this->db->error()['message']);
                return null;
            }
            return $query->row();
        }

        // Se for array, busca lista
        $fields = $this->db->field_data('nfe_emitidas');
        $hasClienteId = false;
        foreach ($fields as $field) {
            if ($field->name == 'cliente_id') {
                $hasClienteId = true;
                break;
            }
        }
        $this->db->select('nfe_emitidas.*, clientes.nomeCliente');
        $this->db->from('nfe_emitidas');
        $this->db->where('nfe_emitidas.ten_id', $this->session->userdata('ten_id'));
        if ($hasClienteId) {
            $this->db->join('clientes', 'clientes.idClientes = nfe_emitidas.cliente_id', 'left');
        } else {
            $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
            $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        }
        if (!empty($param)) {
            foreach ($param as $key => $value) {
                if ($key === 'status') {
                    $this->db->where('nfe_emitidas.status', $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        }
        $this->db->order_by('nfe_emitidas.id', 'desc');
        $query = $this->db->get();
        if (!$query) {
            log_message('error', 'Erro ao buscar NFe: ' . $this->db->error()['message']);
            return [];
        }
        return $query->result();
    }

    public function getCertificate()
    {
        // Busca o certificado ativo configurado para NFe
        $this->db->select('cer.CER_ARQUIVO, cer.CER_SENHA, cer.CER_VALIDADE_FIM, cer.CER_TIPO, cer.CER_CNPJ');
        $this->db->from('configuracoes_fiscais cfg');
        $this->db->join('certificados_digitais cer', 'cer.CER_ID = cfg.CER_ID', 'inner');
        $this->db->where('cfg.ten_id', $this->session->userdata('ten_id'));
        $this->db->where('cfg.CFG_TIPO_DOCUMENTO', 'NFE');
        $this->db->where('cfg.CFG_ATIVO', 1);
        $this->db->where('cer.CER_ATIVO', 1);
        $this->db->order_by('cer.CER_DATA_UPLOAD', 'DESC');
        $this->db->limit(1);

        $result = $this->db->get()->row();

        // Se não encontrar certificado para NFe, tenta buscar qualquer certificado ativo
        if (!$result) {
            $this->db->select('CER_ARQUIVO, CER_SENHA, CER_VALIDADE_FIM, CER_TIPO, CER_CNPJ');
            $this->db->from('certificados_digitais');
            $this->db->where('CER_ATIVO', 1);
            $this->db->order_by('CER_DATA_UPLOAD', 'DESC');
            $this->db->limit(1);
            $result = $this->db->get()->row();
        }

        return $result;
    }

    public function getConfigurations()
    {
        try {
            $query = $this->db->get('configuracoes_nfe');

            if (!$query) {
                log_message('error', 'Database error in getConfigurations: ' . $this->db->error()['message']);
                return [];
            }

            $result = $query->row();

            // Se não houver configurações, cria as padrão
            if (!$result) {
                $defaultConfigs = [
                    'tipo_documento' => 'NFe',
                    'ambiente' => 2, // 1 = Produção, 2 = Homologação
                    'versao_nfe' => '4.00',
                    'tipo_impressao_danfe' => 1,
                    'orientacao_danfe' => 'P',
                    'sequencia_nota' => 1,
                    'sequencia_nfce' => 1,
                    'csc' => null,
                    'csc_id' => null
                ];

                $defaultConfigs['created_at'] = date('Y-m-d H:i:s');
                $defaultConfigs['updated_at'] = date('Y-m-d H:i:s');

                $result = $this->db->insert('configuracoes_nfe', $defaultConfigs);

                if (!$result) {
                    log_message('error', 'Database error creating default configuration: ' . $this->db->error()['message']);
                    return $defaultConfigs;
                }

                return $defaultConfigs;
            }

            // Converte o objeto para array
            $configs = (array) $result;

            // Garante que o ambiente seja um inteiro
            $configs['ambiente'] = (int) $configs['ambiente'];

            return $configs;
        } catch (Exception $e) {
            log_message('error', 'Exception in getConfigurations: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    public function updateConfigurations($id, $data)
    {
        try {
            // Inicia a transação
            $this->db->trans_start();

            // Verifica se já existe uma configuração
            $this->db->where('idConfiguracao', $id);
            $query = $this->db->get('configuracoes_nfe');

            if (!$query) {
                log_message('error', 'Database error checking configuration existence: ' . $this->db->error()['message']);
                $this->db->trans_rollback();
                return false;
            }

            $exists = $query->num_rows() > 0;

            // Prepara os dados para inserção/atualização
            $configData = [
                'tipo_documento' => $data['tipo_documento'] ?? 'NFe',
                'ambiente' => isset($data['ambiente']) ? (int) $data['ambiente'] : 2, // 1 = Produção, 2 = Homologação
                'versao_nfe' => $data['versao_nfe'] ?? '4.00',
                'tipo_impressao_danfe' => $data['tipo_impressao_danfe'] ?? 1,
                'orientacao_danfe' => $data['orientacao_danfe'] ?? 'P',
                'sequencia_nota' => $data['sequencia_nota'] ?? 1,
                'sequencia_nfce' => $data['sequencia_nfce'] ?? 1,
                'csc' => $data['csc'] ?? null,
                'csc_id' => $data['csc_id'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if ($exists) {
                // Atualiza a configuração existente
                $this->db->where('idConfiguracao', $id);
                $result = $this->db->update('configuracoes_nfe', $configData);
            } else {
                // Cria uma nova configuração
                $configData['created_at'] = date('Y-m-d H:i:s');
                $result = $this->db->insert('configuracoes_nfe', $configData);
            }

            if (!$result) {
                log_message('error', 'Database error in updateConfigurations: ' . $this->db->error()['message']);
                $this->db->trans_rollback();
                return false;
            }

            // Finaliza a transação
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Database transaction failed in updateConfigurations');
                return false;
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Exception in updateConfigurations: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            $this->db->trans_rollback();
            return false;
        }
    }

    public function saveConfigurations($data)
    {
        try {
            // Adiciona as datas de criação e atualização
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            // Verifica se já existe uma configuração
            $this->db->where('idConfiguracao', 1);
            $exists = $this->db->get('configuracoes_nfe')->row();

            if ($exists) {
                // Atualiza a configuração existente
                $this->db->where('idConfiguracao', 1);
                $result = $this->db->update('configuracoes_nfe', $data);
            } else {
                // Cria uma nova configuração
                $data['idConfiguracao'] = 1;
                $result = $this->db->insert('configuracoes_nfe', $data);
            }

            if (!$result) {
                log_message('error', 'Database error in saveConfigurations: ' . $this->db->error()['message']);
                return false;
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Exception in saveConfigurations: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('nfe_emitidas', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('nfe_emitidas');
    }

    public function updateCertificate($data)
    {
        $this->db->trans_start();

        // Remove previous configuration if exists
        $this->db->truncate('nfe_certificates');

        // Insert new configuration
        $this->db->insert('nfe_certificates', $data);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function getNextNFNumber()
    {
        try {
            $config = $this->getConfigurations();
            if (!$config || !isset($config['sequencia_nota'])) {
                return 1;
            }
            return (int) $config['sequencia_nota'];
        } catch (Exception $e) {
            log_message('error', 'Exception in getNextNFNumber: ' . $e->getMessage());
            return 1;
        }
    }

    public function incrementNFSequence()
    {
        try {
            $config = $this->getConfigurations();
            if (!$config) {
                return false;
            }

            $nextNumber = ((int) $config['sequencia_nota']) + 1;

            $data = [
                'sequencia_nota' => $nextNumber,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('idConfiguracao', 1);
            $result = $this->db->update('configuracoes_nfe', $data);

            if (!$result) {
                log_message('error', 'Database error in incrementNFSequence: ' . $this->db->error()['message']);
                return false;
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Exception in incrementNFSequence: ' . $e->getMessage());
            return false;
        }
    }

    public function buscarTributacao($venda_id)
    {
        // Get tax regime from configuration
        $this->db->select('regime_tributario');
        $this->db->from('configuracao_tributacao');
        $this->db->limit(1);
        $regime = $this->db->get()->row();

        $this->db->select('vendas.*, clientes.*, operacao_comercial.OPC_NOME as operacao_comercial,
            classificacao_fiscal.cfop, classificacao_fiscal.cst, classificacao_fiscal.csosn, 
            classificacao_fiscal.destinacao, classificacao_fiscal.objetivo_comercial');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->join('operacao_comercial', 'operacao_comercial.OPC_ID = vendas.operacao_comercial_id');
        $this->db->join('classificacao_fiscal', 'classificacao_fiscal.operacao_comercial_id = operacao_comercial.OPC_ID');
        $this->db->where('vendas.idVendas', $venda_id);

        // Check if client is Estadual or Interestadual
        $this->db->where(
            'classificacao_fiscal.destinacao',
            $this->db->case()
                ->when('clientes.estado = "SP"', 'Estadual')
                ->else('Interestadual')
                ->end()
        );

        // Check commercial objective
        $this->db->where('classificacao_fiscal.objetivo_comercial', 'clientes.objetivo_comercial');

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result = $query->row();

            // Handle tax regime
            if ($regime && $regime->regime_tributario == 'Simples Nacional') {
                $result->cst = null; // Remove CST for Simples Nacional
            } else {
                $result->csosn = null; // Remove CSOSN for Lucro Presumido/Real
            }

            return $result;
        }

        return null;
    }

    public function autoCompleteVenda($q)
    {
        $this->db->select('vendas.idVendas as id, vendas.idVendas as label');
        $this->db->from('vendas');
        $this->db->where('vendas.faturado', 1);
        $this->db->where('vendas.emitida_nfe', 0);
        $this->db->like('vendas.idVendas', $q);
        $this->db->limit(25);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = $row;
            }
            echo json_encode($row_set);
        }
    }

    public function getEmit()
    {
        $this->db->select('EMP_RAZAO_SOCIAL as nome, EMP_NOME_FANTASIA as fantasia, EMP_CNPJ as cnpj, EMP_IE as ie, EMP_LOGRADOURO as rua, EMP_NUMERO as numero, EMP_COMPLEMENTO as complemento, EMP_BAIRRO as bairro, EMP_CIDADE as cidade, EMP_UF as uf, EMP_CEP as cep, EMP_IBGE as ibge, EMP_TELEFONE as telefone, EMP_EMAIL as email, EMP_REGIME_TRIBUTARIO as regime_tributario, EMP_LOGO_PATH as url_logo');
        $this->db->from('empresas');
        // Como normalmente há apenas uma empresa emitente, buscamos a primeira
        $this->db->limit(1);
        $empresa = $this->db->get()->row();

        if (!$empresa) {
            return false; // Retorna falso se nenhuma empresa for encontrada
        }

        // Define CRT com base no regime tributário da empresa
        $crt = 3; // Padrão para Regime Normal ou Lucro Presumido
        if (isset($empresa->regime_tributario) && strtolower($empresa->regime_tributario) === 'simples nacional') {
            $crt = 1;
        }

        $emit = [
            'CNPJ' => preg_replace('/[^0-9]/', '', $empresa->cnpj),
            'xNome' => $empresa->nome,
            'xFant' => $empresa->fantasia,
            'enderEmit' => [
                'xLgr' => $empresa->rua,
                'nro' => $empresa->numero,
                'xCpl' => $empresa->complemento,
                'xBairro' => $empresa->bairro,
                'cMun' => $empresa->ibge,
                'xMun' => $empresa->cidade,
                'UF' => $empresa->uf,
                'CEP' => preg_replace('/[^0-9]/', '', $empresa->cep),
                'cPais' => '1058',
                'xPais' => 'BRASIL',
                'fone' => preg_replace('/[^0-9]/', '', $empresa->telefone)
            ],
            'IE' => $empresa->ie,
            'CRT' => $crt,
            'url_logo' => $empresa->url_logo // Adiciona a URL do logo
        ];

        return $emit;
    }

    public function emitir($id)
    {
        $this->db->select('vendas.*, clientes.*, usuarios.*, garantias.*');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = vendas.garantias_id', 'left');
        $this->db->where('vendas.idVendas', $id);
        $venda = $this->db->get()->row();

        $emit = $this->getEmit();

        // Get tax regime from configuration
        $this->db->select('regime_tributario');
        $this->db->from('configuracao_tributacao');
        $this->db->limit(1);
        $regime = $this->db->get()->row();

        // Calcula ICMS base e valor
        if ($regime && strtolower($regime->regime_tributario) === 'simples nacional') {
            $base_icms = 0;
            $aliq_icms = 0;
            $valor_icms = 0;
        } else {
            $base_icms = $venda->valorTotal;
            $aliq_icms = ($venda->estado == 'GO') ? 19 : 12; // 19% para GO, 12% para interestadual
            $valor_icms = ($base_icms * $aliq_icms) / 100;
        }

        $dest = [
            'CNPJ' => preg_replace('/[^0-9]/', '', $venda->documento),
            'xNome' => $venda->nomeCliente,
            'enderDest' => [
                'xLgr' => $venda->rua,
                'nro' => $venda->numero,
                'xCpl' => $venda->complemento,
                'xBairro' => $venda->bairro,
                'cMun' => $venda->cidade,
                'xMun' => $venda->cidade,
                'UF' => $venda->estado,
                'CEP' => preg_replace('/[^0-9]/', '', $venda->cep),
                'cPais' => '1058',
                'xPais' => 'BRASIL',
                'fone' => preg_replace('/[^0-9]/', '', $venda->telefone)
            ],
            'indIEDest' => '9',
            'IE' => $venda->ie,
            'email' => $venda->email
        ];

        $this->db->select('itens_de_vendas.*, produtos.*');
        $this->db->from('itens_de_vendas');
        $this->db->join('produtos', 'produtos.idProdutos = itens_de_vendas.produtos_id');
        $this->db->where('itens_de_vendas.vendas_id', $id);
        $produtos = $this->db->get()->result();

        $itens = [];
        foreach ($produtos as $p) {
            $item = [
                'nItem' => count($itens) + 1,
                'prod' => [
                    'cProd' => $p->codDeBarra,
                    'xProd' => $p->descricao,
                    'NCM' => $p->ncm,
                    'CFOP' => $p->cfop,
                    'uCom' => $p->unidade,
                    'qCom' => $p->quantidade,
                    'vUnCom' => number_format($p->preco, 2, '.', ''),
                    'vProd' => number_format($p->subTotal, 2, '.', ''),
                    'indTot' => 1
                ],
                'imposto' => [
                    'ICMS' => [
                        'ICMS00' => [
                            'orig' => 0,
                            'CST' => $p->cst,
                            'modBC' => 0,
                            'vBC' => number_format($base_icms, 2, '.', ''),
                            'pICMS' => number_format($aliq_icms, 2, '.', ''),
                            'vICMS' => number_format($valor_icms, 2, '.', '')
                        ]
                    ]
                ]
            ];
            $itens[] = $item;
        }
    }
}