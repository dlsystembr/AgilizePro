<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ncms_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($search = null, $per_page = null, $start = null, $tipo = null)
    {
        $this->db->select('ncms.*');
        $this->db->from('ncms');
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('codigo', $search);
            $this->db->or_like('descricao', $search);
            $this->db->group_end();
        }

        if ($tipo) {
            if ($tipo == 'analitico') {
                $this->db->where('LENGTH(codigo) = 8');
            } else if ($tipo == 'sintetico') {
                $this->db->where('LENGTH(codigo) < 8');
            } else if ($tipo == 'configurados') {
                // Subconsulta para NCMs com tributação federal configurada
                $this->db->where("EXISTS (
                    SELECT 1 FROM tributacao_federal tf 
                    WHERE tf.ncm_id = ncms.ncm_id 
                    AND (tf.tbf_cst_ipi_entrada != '' 
                         OR tf.tbf_cst_pis_cofins_entrada != '' 
                         OR tf.tbf_cst_ipi_saida != '' 
                         OR tf.tbf_cst_pis_cofins_saida != '')
                )");
                
                // Subconsulta para NCMs com tributação estadual configurada
                $this->db->or_where("EXISTS (
                    SELECT 1 FROM tributacao_estadual te 
                    WHERE te.ncm_id = ncms.ncm_id
                )");
            }
        }
        
        $this->db->order_by('codigo', 'ASC');
        
        if ($per_page && $start !== null) {
            $this->db->limit($per_page, $start);
        }

        return $this->db->get()->result();
    }

    public function count($search = null, $tipo = null)
    {
        $this->db->select('COUNT(*) as total');
        $this->db->from('ncms');
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('codigo', $search);
            $this->db->or_like('descricao', $search);
            $this->db->group_end();
        }

        if ($tipo) {
            if ($tipo == 'analitico') {
                $this->db->where('LENGTH(codigo) = 8');
            } else if ($tipo == 'sintetico') {
                $this->db->where('LENGTH(codigo) < 8');
            } else if ($tipo == 'configurados') {
                // Subconsulta para NCMs com tributação federal configurada
                $this->db->where("EXISTS (
                    SELECT 1 FROM tributacao_federal tf 
                    WHERE tf.ncm_id = ncms.ncm_id 
                    AND (tf.tbf_cst_ipi_entrada != '' 
                         OR tf.tbf_cst_pis_cofins_entrada != '' 
                         OR tf.tbf_cst_ipi_saida != '' 
                         OR tf.tbf_cst_pis_cofins_saida != '')
                )");
                
                // Subconsulta para NCMs com tributação estadual configurada
                $this->db->or_where("EXISTS (
                    SELECT 1 FROM tributacao_estadual te 
                    WHERE te.ncm_id = ncms.ncm_id
                )");
            }
        }
        
        $result = $this->db->get()->row();
        return $result ? $result->total : 0;
    }

    public function getById($id)
    {
        $this->db->select('ncm_id, codigo as ncm_codigo, descricao as ncm_descricao, data_inicio, data_fim, tipo_ato, numero_ato, ano_ato');
        $this->db->from('ncms');
        $this->db->where('ncm_id', $id);
        $ncm = $this->db->get()->row();
        log_message('debug', 'NCM encontrado: ' . json_encode($ncm));
        return $ncm;
    }

    public function add($data)
    {
        $this->db->insert('ncms', $data);
        return $this->db->insert_id();
    }

    public function edit($data, $id)
    {
        $this->db->where('ncm_id', $id);
        return $this->db->update('ncms', $data);
    }

    public function delete($id)
    {
        $this->db->where('ncm_id', $id);
        return $this->db->delete('ncms');
    }

    public function buscar($termo)
    {
        // Primeiro tenta encontrar uma correspondência exata do código
        $this->db->select('*');
        $this->db->from('ncms');
        $this->db->where('codigo', $termo);
        $result = $this->db->get()->result();
        
        // Se não encontrar correspondência exata, busca por similaridade
        if (empty($result)) {
            $this->db->select('*');
            $this->db->from('ncms');
            $this->db->group_start();
            $this->db->like('codigo', $termo);
            $this->db->or_like('descricao', $termo);
            $this->db->group_end();
            $this->db->order_by('codigo', 'asc');
            $result = $this->db->get()->result();
        }
        
        return $result;
    }

    public function getTributacao($ncm_id)
    {
        $this->db->where('ncm_id', $ncm_id);
        return $this->db->get('tributacao_federal')->row();
    }

    public function verificarEstruturaTabela()
    {
        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_federal')) {
                log_message('error', 'Tabela tributacao_federal não existe');
                return false;
            }

            // Obtém a estrutura da tabela
            $fields = $this->db->field_data('tributacao_federal');
            
            // Log da estrutura
            log_message('debug', 'Estrutura da tabela tributacao_federal:');
            foreach ($fields as $field) {
                log_message('debug', "Campo: {$field->name}, Tipo: {$field->type}, Tamanho: {$field->max_length}, Null: {$field->null}, Default: {$field->default}");
            }

            // Obtém as chaves
            $keys = $this->db->query("SHOW KEYS FROM tributacao_federal")->result();
            log_message('debug', 'Chaves da tabela tributacao_federal:');
            foreach ($keys as $key) {
                log_message('debug', "Chave: {$key->Key_name}, Coluna: {$key->Column_name}, Tipo: {$key->Index_type}");
            }

            return true;
        } catch (Exception $e) {
            log_message('error', 'Erro ao verificar estrutura da tabela: ' . $e->getMessage());
            return false;
        }
    }

    public function verificarDadosInseridos($ncm_id)
    {
        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_federal')) {
                log_message('error', 'Tabela tributacao_federal não existe');
                return (object)[
                    'tbf_cst_ipi_entrada' => '',
                    'tbf_aliquota_ipi_entrada' => '0.00',
                    'tbf_cst_pis_cofins_entrada' => '',
                    'tbf_aliquota_pis_entrada' => '0.00',
                    'tbf_aliquota_cofins_entrada' => '0.00',
                    'tbf_cst_ipi_saida' => '',
                    'tbf_aliquota_ipi_saida' => '0.00',
                    'tbf_cst_pis_cofins_saida' => '',
                    'tbf_aliquota_pis_saida' => '0.00',
                    'tbf_aliquota_cofins_saida' => '0.00'
                ];
            }

            // Busca os dados do NCM
            $this->db->where('ncm_id', $ncm_id);
            $dados = $this->db->get('tributacao_federal')->row();

            if ($dados) {
                log_message('debug', 'Dados encontrados para NCM ID ' . $ncm_id . ':');
                log_message('debug', 'CST IPI Entrada: ' . $dados->tbf_cst_ipi_entrada);
                log_message('debug', 'Alíquota IPI Entrada: ' . $dados->tbf_aliquota_ipi_entrada);
                log_message('debug', 'CST PIS/COFINS Entrada: ' . $dados->tbf_cst_pis_cofins_entrada);
                log_message('debug', 'Alíquota PIS Entrada: ' . $dados->tbf_aliquota_pis_entrada);
                log_message('debug', 'Alíquota COFINS Entrada: ' . $dados->tbf_aliquota_cofins_entrada);
                log_message('debug', 'CST IPI Saída: ' . $dados->tbf_cst_ipi_saida);
                log_message('debug', 'Alíquota IPI Saída: ' . $dados->tbf_aliquota_ipi_saida);
                log_message('debug', 'CST PIS/COFINS Saída: ' . $dados->tbf_cst_pis_cofins_saida);
                log_message('debug', 'Alíquota PIS Saída: ' . $dados->tbf_aliquota_pis_saida);
                log_message('debug', 'Alíquota COFINS Saída: ' . $dados->tbf_aliquota_cofins_saida);
                return $dados;
            } else {
                log_message('error', 'Nenhum dado encontrado para NCM ID ' . $ncm_id);
                return (object)[
                    'tbf_cst_ipi_entrada' => '',
                    'tbf_aliquota_ipi_entrada' => '0.00',
                    'tbf_cst_pis_cofins_entrada' => '',
                    'tbf_aliquota_pis_entrada' => '0.00',
                    'tbf_aliquota_cofins_entrada' => '0.00',
                    'tbf_cst_ipi_saida' => '',
                    'tbf_aliquota_ipi_saida' => '0.00',
                    'tbf_cst_pis_cofins_saida' => '',
                    'tbf_aliquota_pis_saida' => '0.00',
                    'tbf_aliquota_cofins_saida' => '0.00'
                ];
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao verificar dados inseridos: ' . $e->getMessage());
            return (object)[
                'tbf_cst_ipi_entrada' => '',
                'tbf_aliquota_ipi_entrada' => '0.00',
                'tbf_cst_pis_cofins_entrada' => '',
                'tbf_aliquota_pis_entrada' => '0.00',
                'tbf_aliquota_cofins_entrada' => '0.00',
                'tbf_cst_ipi_saida' => '',
                'tbf_aliquota_ipi_saida' => '0.00',
                'tbf_cst_pis_cofins_saida' => '',
                'tbf_aliquota_pis_saida' => '0.00',
                'tbf_aliquota_cofins_saida' => '0.00'
            ];
        }
    }

    public function saveTributacao($data)
    {
        log_message('debug', '=== INÍCIO DO MÉTODO SAVE TRIBUTAÇÃO ===');
        log_message('debug', 'Dados recebidos: ' . json_encode($data));

        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_federal')) {
                log_message('error', 'Tabela tributacao_federal não existe');
                // Cria a tabela se não existir
                $this->db->query("CREATE TABLE IF NOT EXISTS `tributacao_federal` (
                    `tbf_id` int(11) NOT NULL AUTO_INCREMENT,
                    `ncm_id` int(11) NOT NULL,
                    `tbf_cst_ipi_entrada` varchar(2) NOT NULL,
                    `tbf_aliquota_ipi_entrada` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_cst_pis_cofins_entrada` varchar(2) NOT NULL,
                    `tbf_aliquota_pis_entrada` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_aliquota_cofins_entrada` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_cst_ipi_saida` varchar(2) NOT NULL,
                    `tbf_aliquota_ipi_saida` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_cst_pis_cofins_saida` varchar(2) NOT NULL,
                    `tbf_aliquota_pis_saida` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_aliquota_cofins_saida` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_aliquota_ii` decimal(10,2) NOT NULL DEFAULT '0.00',
                    `tbf_data_cadastro` datetime NOT NULL,
                    `tbf_data_alteracao` datetime NOT NULL,
                    PRIMARY KEY (`tbf_id`),
                    KEY `ncm_id` (`ncm_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            }

            // Debug dos dados recebidos
            log_message('debug', '=== DADOS RECEBIDOS NO MODELO ===');
            log_message('debug', json_encode($data));

            // Verifica se já existe tributação
            $this->db->where('ncm_id', $data['ncm_id']);
            $tributacao = $this->db->get('tributacao_federal')->row();
            log_message('debug', 'Tributação existente: ' . json_encode($tributacao));

            // Prepara dados para salvar com todos os campos
            $saveData = [
                'ncm_id' => $data['ncm_id'],
                'tbf_cst_ipi_entrada' => $data['tbf_cst_ipi_entrada'],
                'tbf_aliquota_ipi_entrada' => $data['tbf_aliquota_ipi_entrada'],
                'tbf_cst_pis_cofins_entrada' => $data['tbf_cst_pis_cofins_entrada'],
                'tbf_aliquota_pis_entrada' => $data['tbf_aliquota_pis_entrada'],
                'tbf_aliquota_cofins_entrada' => $data['tbf_aliquota_cofins_entrada'],
                'tbf_cst_ipi_saida' => $data['tbf_cst_ipi_saida'],
                'tbf_aliquota_ipi_saida' => $data['tbf_aliquota_ipi_saida'],
                'tbf_cst_pis_cofins_saida' => $data['tbf_cst_pis_cofins_saida'],
                'tbf_aliquota_pis_saida' => $data['tbf_aliquota_pis_saida'],
                'tbf_aliquota_cofins_saida' => $data['tbf_aliquota_cofins_saida'],
                'tbf_aliquota_ii' => '0.00',
                'tbf_data_cadastro' => date('Y-m-d H:i:s'),
                'tbf_data_alteracao' => date('Y-m-d H:i:s')
            ];

            // Debug dos dados a serem salvos
            log_message('debug', '=== DADOS A SEREM SALVOS ===');
            log_message('debug', json_encode($saveData));

            if ($tributacao) {
                // Atualiza
                $sql = "UPDATE tributacao_federal SET 
                    tbf_cst_ipi_entrada = ?,
                    tbf_aliquota_ipi_entrada = ?,
                    tbf_cst_pis_cofins_entrada = ?,
                    tbf_aliquota_pis_entrada = ?,
                    tbf_aliquota_cofins_entrada = ?,
                    tbf_cst_ipi_saida = ?,
                    tbf_aliquota_ipi_saida = ?,
                    tbf_cst_pis_cofins_saida = ?,
                    tbf_aliquota_pis_saida = ?,
                    tbf_aliquota_cofins_saida = ?,
                    tbf_data_alteracao = ?
                WHERE ncm_id = ?";

                $this->db->query($sql, [
                    $saveData['tbf_cst_ipi_entrada'],
                    $saveData['tbf_aliquota_ipi_entrada'],
                    $saveData['tbf_cst_pis_cofins_entrada'],
                    $saveData['tbf_aliquota_pis_entrada'],
                    $saveData['tbf_aliquota_cofins_entrada'],
                    $saveData['tbf_cst_ipi_saida'],
                    $saveData['tbf_aliquota_ipi_saida'],
                    $saveData['tbf_cst_pis_cofins_saida'],
                    $saveData['tbf_aliquota_pis_saida'],
                    $saveData['tbf_aliquota_cofins_saida'],
                    $saveData['tbf_data_alteracao'],
                    $saveData['ncm_id']
                ]);

                $error = $this->db->error();
                log_message('debug', 'Query de atualização: ' . $this->db->last_query());
                if ($error['code'] != 0) {
                    log_message('error', 'Erro na atualização: ' . json_encode($error));
                    return false;
                }
            } else {
                // Insere
                log_message('debug', '=== TENTANDO INSERIR DADOS ===');
                
                $sql = "INSERT INTO tributacao_federal (
                    ncm_id,
                    tbf_cst_ipi_entrada,
                    tbf_aliquota_ipi_entrada,
                    tbf_cst_pis_cofins_entrada,
                    tbf_aliquota_pis_entrada,
                    tbf_aliquota_cofins_entrada,
                    tbf_cst_ipi_saida,
                    tbf_aliquota_ipi_saida,
                    tbf_cst_pis_cofins_saida,
                    tbf_aliquota_pis_saida,
                    tbf_aliquota_cofins_saida,
                    tbf_aliquota_ii,
                    tbf_data_cadastro,
                    tbf_data_alteracao
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                $this->db->query($sql, [
                    $saveData['ncm_id'],
                    $saveData['tbf_cst_ipi_entrada'],
                    $saveData['tbf_aliquota_ipi_entrada'],
                    $saveData['tbf_cst_pis_cofins_entrada'],
                    $saveData['tbf_aliquota_pis_entrada'],
                    $saveData['tbf_aliquota_cofins_entrada'],
                    $saveData['tbf_cst_ipi_saida'],
                    $saveData['tbf_aliquota_ipi_saida'],
                    $saveData['tbf_cst_pis_cofins_saida'],
                    $saveData['tbf_aliquota_pis_saida'],
                    $saveData['tbf_aliquota_cofins_saida'],
                    $saveData['tbf_aliquota_ii'],
                    $saveData['tbf_data_cadastro'],
                    $saveData['tbf_data_alteracao']
                ]);

                $error = $this->db->error();
                log_message('debug', 'Query de inserção: ' . $this->db->last_query());
                if ($error['code'] != 0) {
                    log_message('error', 'Erro na inserção: ' . json_encode($error));
                    return false;
                }
                log_message('debug', 'Dados inseridos com sucesso. ID: ' . $this->db->insert_id());
            }

            // Verifica se os dados foram salvos
            $this->db->where('ncm_id', $data['ncm_id']);
            $dados_salvos = $this->db->get('tributacao_federal')->row();
            log_message('debug', '=== DADOS SALVOS NO BANCO ===');
            log_message('debug', json_encode($dados_salvos));

            if ($dados_salvos) {
                return true;
            } else {
                log_message('error', 'Dados não foram salvos corretamente');
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Exceção no modelo: ' . $e->getMessage());
        return false;
        }
    }

    public function getByCodigo($codigo)
    {
        $this->db->select('*');
        $this->db->from('ncms');
        $this->db->where('codigo', $codigo);
        return $this->db->get()->row();
    }

    public function getTributacaoEstadual($ncm_id)
    {
        $this->db->where('ncm_id', $ncm_id);
        return $this->db->get('tributacao_estadual')->result();
    }

    public function insertTributacaoEstadual($data)
    {
        log_message('debug', '=== INÍCIO DO MÉTODO INSERT TRIBUTAÇÃO ESTADUAL ===');
        log_message('debug', 'Dados recebidos: ' . json_encode($data));

        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_estadual')) {
                log_message('error', 'Tabela tributacao_estadual não existe');
                return false;
            }

            // Insere nova tributação
            $data['tbe_data_cadastro'] = date('Y-m-d H:i:s');
            $data['tbe_data_alteracao'] = date('Y-m-d H:i:s');
            $result = $this->db->insert('tributacao_estadual', $data);
            log_message('debug', 'SQL Insert: ' . $this->db->last_query());

            if ($result === false) {
                log_message('error', 'Erro na operação: ' . $this->db->error()['message']);
                return false;
            }

            log_message('debug', 'Resultado da operação: ' . ($result ? 'Sucesso' : 'Falha'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao inserir tributação estadual: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function updateTributacaoEstadual($id, $data)
    {
        log_message('debug', '=== INÍCIO DO MÉTODO UPDATE TRIBUTAÇÃO ESTADUAL ===');
        log_message('debug', 'ID: ' . $id);
        log_message('debug', 'Dados recebidos: ' . json_encode($data));

        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_estadual')) {
                log_message('error', 'Tabela tributacao_estadual não existe');
                return false;
            }

            // Atualiza a tributação
            $this->db->where('tbe_id', $id);
            $data['tbe_data_alteracao'] = date('Y-m-d H:i:s');
            $result = $this->db->update('tributacao_estadual', $data);
            log_message('debug', 'SQL Update: ' . $this->db->last_query());

            if ($result === false) {
                log_message('error', 'Erro na operação: ' . $this->db->error()['message']);
                return false;
            }

            log_message('debug', 'Resultado da operação: ' . ($result ? 'Sucesso' : 'Falha'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao atualizar tributação estadual: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function saveTributacaoEstadual($data)
    {
        log_message('debug', '=== INÍCIO DO MÉTODO SAVE TRIBUTAÇÃO ESTADUAL ===');
        log_message('debug', 'Dados recebidos: ' . json_encode($data));

        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_estadual')) {
                log_message('error', 'Tabela tributacao_estadual não existe');
                return false;
            }

            // Verifica se já existe tributação para este NCM e UF
            $this->db->where('ncm_id', $data['ncm_id']);
            $this->db->where('tbe_uf', $data['tbe_uf']);
            $existing = $this->db->get('tributacao_estadual')->row();
            log_message('debug', 'Verificando tributação existente: ' . ($existing ? 'Encontrada' : 'Não encontrada'));

            if ($existing) {
                // Atualiza a tributação existente
                $this->db->where('tbe_id', $existing->tbe_id);
                $data['tbe_data_alteracao'] = date('Y-m-d H:i:s');
                $result = $this->db->update('tributacao_estadual', $data);
                log_message('debug', 'SQL Update: ' . $this->db->last_query());
            } else {
                // Insere nova tributação
                $data['tbe_data_cadastro'] = date('Y-m-d H:i:s');
                $data['tbe_data_alteracao'] = date('Y-m-d H:i:s');
                $result = $this->db->insert('tributacao_estadual', $data);
                log_message('debug', 'SQL Insert: ' . $this->db->last_query());
            }

            if ($result === false) {
                log_message('error', 'Erro na operação: ' . $this->db->error()['message']);
                return false;
            }

            log_message('debug', 'Resultado da operação: ' . ($result ? 'Sucesso' : 'Falha'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao salvar tributação estadual: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function saveAllTributacaoEstadual($ncm_id, $tributacoes)
    {
        log_message('debug', '=== INÍCIO DO MÉTODO SAVE ALL TRIBUTAÇÃO ESTADUAL ===');
        log_message('debug', 'NCM ID: ' . $ncm_id);
        log_message('debug', 'Tributações: ' . json_encode($tributacoes));

        try {
            $this->db->trans_start();

            // Para cada tributação
            foreach ($tributacoes as $uf => $tributacao) {
                // Valida o tipo de tributação
                if (!in_array($tributacao['tipo_tributacao'], ['ICMS Normal', 'ST', 'Serviço'])) {
                    throw new Exception('Tipo de tributação inválido para o estado ' . $uf);
                }

                // Valida a alíquota ICMS
                if (!is_numeric($tributacao['aliquota_icms']) || $tributacao['aliquota_icms'] < 0 || $tributacao['aliquota_icms'] > 100) {
                    throw new Exception('Alíquota ICMS inválida para o estado ' . $uf);
                }

                // Se for ST, valida MVA e alíquota ICMS ST
                if ($tributacao['tipo_tributacao'] === 'ST') {
                    if (!is_numeric($tributacao['mva']) || $tributacao['mva'] < 0) {
                        throw new Exception('MVA inválida para o estado ' . $uf);
                    }
                    if (!is_numeric($tributacao['aliquota_icms_st']) || $tributacao['aliquota_icms_st'] < 0 || $tributacao['aliquota_icms_st'] > 100) {
                        throw new Exception('Alíquota ICMS ST inválida para o estado ' . $uf);
                    }
                }

                $data = [
                    'ncm_id' => $ncm_id,
                    'tbe_uf' => $uf,
                    'tbe_tipo_tributacao' => $tributacao['tipo_tributacao'],
                    'tbe_aliquota_icms' => $tributacao['aliquota_icms'],
                    'tbe_mva' => $tributacao['mva'],
                    'tbe_aliquota_icms_st' => $tributacao['aliquota_icms_st'],
                    'tbe_percentual_reducao_icms' => $tributacao['percentual_reducao_icms'],
                    'tbe_percentual_reducao_st' => $tributacao['percentual_reducao_st'],
                    'tbe_aliquota_fcp' => $tributacao['aliquota_fcp']
                ];

                // Verifica se já existe tributação para este NCM e UF
                $this->db->where('ncm_id', $ncm_id);
                $this->db->where('tbe_uf', $uf);
                $existing = $this->db->get('tributacao_estadual')->row();

                if ($existing) {
                    // Atualiza a tributação existente
                    $this->db->where('tbe_id', $existing->tbe_id);
                    $data['tbe_data_alteracao'] = date('Y-m-d H:i:s');
                    $this->db->update('tributacao_estadual', $data);
                } else {
                    // Insere nova tributação
                    $data['tbe_data_cadastro'] = date('Y-m-d H:i:s');
                    $data['tbe_data_alteracao'] = date('Y-m-d H:i:s');
                    $this->db->insert('tributacao_estadual', $data);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Erro na transação de salvar todas as tributações estaduais');
                return false;
            }

            log_message('debug', 'Todas as tributações estaduais salvas com sucesso');
            return true;
        } catch (Exception $e) {
            log_message('error', 'Erro ao salvar todas as tributações estaduais: ' . $e->getMessage());
            return false;
        }
    }

    public function saveTributacaoFederal($ncm_id, $data)
    {
        try {
            log_message('debug', '=== INÍCIO DO MÉTODO SAVE TRIBUTAÇÃO FEDERAL ===');
            log_message('debug', 'NCM ID: ' . $ncm_id);
            log_message('debug', 'Dados recebidos: ' . json_encode($data));

            // Validação dos dados
            if (empty($ncm_id)) {
                log_message('error', 'NCM ID não informado');
                return false;
            }

            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_federal')) {
                log_message('error', 'Tabela tributacao_federal não existe');
                return false;
            }

            // Validação dos campos obrigatórios
            $required_fields = [
                'tbf_aliquota_ipi_entrada',
                'tbf_aliquota_pis_entrada',
                'tbf_aliquota_cofins_entrada',
                'tbf_aliquota_ipi_saida',
                'tbf_aliquota_pis_saida',
                'tbf_aliquota_cofins_saida'
            ];

            foreach ($required_fields as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    log_message('error', 'Campo obrigatório não preenchido: ' . $field);
                    return false;
                }
            }

            $this->db->trans_start();

            // Verifica se já existe tributação para este NCM
            $this->db->where('ncm_id', $ncm_id);
            $existing = $this->db->get('tributacao_federal')->row();
            log_message('debug', 'Verificando tributação existente: ' . ($existing ? 'Encontrada' : 'Não encontrada'));

            if ($existing) {
                // Atualiza a tributação existente
                $this->db->where('tbf_id', $existing->tbf_id);
                $data['tbf_data_alteracao'] = date('Y-m-d H:i:s');
                $update_result = $this->db->update('tributacao_federal', $data);
                log_message('debug', 'Resultado da atualização: ' . ($update_result ? 'Sucesso' : 'Falha'));
                log_message('debug', 'SQL Update: ' . $this->db->last_query());
            } else {
                // Insere nova tributação
                $data['tbf_data_cadastro'] = date('Y-m-d H:i:s');
                $data['tbf_data_alteracao'] = date('Y-m-d H:i:s');
                $insert_result = $this->db->insert('tributacao_federal', $data);
                log_message('debug', 'Resultado da inserção: ' . ($insert_result ? 'Sucesso' : 'Falha'));
                log_message('debug', 'SQL Insert: ' . $this->db->last_query());
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Erro na transação de salvar tributação federal');
                log_message('error', 'Erro DB: ' . $this->db->error()['message']);
                return false;
            }

            log_message('debug', 'Tributação federal salva com sucesso');
            return true;
        } catch (Exception $e) {
            log_message('error', 'Erro ao salvar tributação federal: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function saveTributacaoEstadualTodosEstados($data)
    {
        log_message('debug', '=== INÍCIO DO MÉTODO SAVE TRIBUTAÇÃO ESTADUAL TODOS ESTADOS ===');
        log_message('debug', 'Dados recebidos: ' . json_encode($data));

        try {
            // Verifica se a tabela existe
            if (!$this->db->table_exists('tributacao_estadual')) {
                log_message('error', 'Tabela tributacao_estadual não existe');
                return false;
            }

            // Lista de todos os estados do Brasil
            $estados = [
                'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG',
                'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
            ];

            $this->db->trans_start();
            $result = true;

            foreach ($estados as $uf) {
                // Dados da tributação estadual para cada UF
                $data_estadual = $data;
                $data_estadual['tbe_uf'] = $uf;

                // Verifica se já existe tributação para este NCM e UF
                $this->db->where('ncm_id', $data['ncm_id']);
                $this->db->where('tbe_uf', $uf);
                $existing = $this->db->get('tributacao_estadual')->row();
                log_message('debug', 'Verificando tributação existente para ' . $uf . ': ' . ($existing ? 'Encontrada' : 'Não encontrada'));

                if ($existing) {
                    // Atualiza a tributação existente
                    $this->db->where('tbe_id', $existing->tbe_id);
                    $data_estadual['tbe_data_alteracao'] = date('Y-m-d H:i:s');
                    $result_uf = $this->db->update('tributacao_estadual', $data_estadual);
                    log_message('debug', 'SQL Update para ' . $uf . ': ' . $this->db->last_query());
                } else {
                    // Insere nova tributação
                    $data_estadual['tbe_data_cadastro'] = date('Y-m-d H:i:s');
                    $data_estadual['tbe_data_alteracao'] = date('Y-m-d H:i:s');
                    $result_uf = $this->db->insert('tributacao_estadual', $data_estadual);
                    log_message('debug', 'SQL Insert para ' . $uf . ': ' . $this->db->last_query());
                }

                if ($result_uf === false) {
                    $result = false;
                    log_message('error', 'Erro ao salvar tributação estadual para ' . $uf . ': ' . $this->db->error()['message']);
                    break;
                }
            }

            $this->db->trans_complete();
            log_message('debug', 'Resultado da operação: ' . ($result ? 'Sucesso' : 'Falha'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao salvar tributação estadual: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return false;
        }
    }
} 