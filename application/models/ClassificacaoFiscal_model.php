<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class ClassificacaoFiscal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table = 'classificacao_fiscal', $fields = '*')
    {
        $this->db->select(
            'classificacao_fiscal.CLF_ID as id,
             classificacao_fiscal.OPC_ID as operacao_comercial_id,
             classificacao_fiscal.CLF_CST as cst,
             classificacao_fiscal.CLF_CSOSN as csosn,
             classificacao_fiscal.CLF_NATUREZA_CONTRIBUINTE as natureza_contribuinte,
             classificacao_fiscal.CLF_CFOP as cfop,
             classificacao_fiscal.CLF_DESTINACAO as destinacao,
             classificacao_fiscal.CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
             classificacao_fiscal.CLF_FINALIDADE as finalidade,
             classificacao_fiscal.CLF_TIPO_TRIBUTACAO as tipo_icms,
             classificacao_fiscal.CLF_CCLASSTRIB as cClassTrib,
             classificacao_fiscal.CLF_MENSAGEM as mensagem_fiscal,
             classificacao_fiscal.CLF_DATA_INCLUSAO as created_at,
             classificacao_fiscal.CLF_DATA_ALTERACAO as updated_at,
             classificacao_fiscal.TPC_ID as tipo_cliente_id,
             (SELECT oc.OPC_NOME FROM operacao_comercial oc WHERE oc.OPC_ID = classificacao_fiscal.OPC_ID) as nome_operacao,
             (SELECT tc.TPC_NOME FROM tipos_clientes tc WHERE tc.TPC_ID = classificacao_fiscal.TPC_ID) as nome_tipo_cliente'
        );
        $this->db->from('classificacao_fiscal');
        $this->db->where('classificacao_fiscal.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('classificacao_fiscal.CLF_ID', 'DESC');

        $query = $this->db->get();
        if (!$query) {
            log_message('error', 'Erro ao buscar classificacao_fiscal: ' . $this->db->error()['message']);
            return [];
        }
        log_message('debug', 'ClassificacaoFiscal_model::get SQL: ' . $this->db->last_query());
        log_message('debug', 'ClassificacaoFiscal_model::get rows: ' . $query->num_rows());
        return $query->result();
    }

    public function getById($id)
    {
        $this->db->select(
            'classificacao_fiscal.CLF_ID as id,
             classificacao_fiscal.OPC_ID as operacao_comercial_id,
             classificacao_fiscal.CLF_CST as cst,
             classificacao_fiscal.CLF_CSOSN as csosn,
             classificacao_fiscal.CLF_NATUREZA_CONTRIBUINTE as natureza_contribuinte,
             classificacao_fiscal.CLF_CFOP as cfop,
             classificacao_fiscal.CLF_DESTINACAO as destinacao,
             classificacao_fiscal.CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
             classificacao_fiscal.CLF_FINALIDADE as finalidade,
             classificacao_fiscal.CLF_TIPO_TRIBUTACAO as tipo_icms,
             classificacao_fiscal.CLF_CCLASSTRIB as cClassTrib,
             classificacao_fiscal.CLF_MENSAGEM as mensagem_fiscal,
             classificacao_fiscal.CLF_DATA_INCLUSAO as created_at,
             classificacao_fiscal.CLF_DATA_ALTERACAO as updated_at,
             classificacao_fiscal.TPC_ID as tipo_cliente_id,
             (SELECT oc.OPC_NOME FROM operacao_comercial oc WHERE oc.OPC_ID = classificacao_fiscal.OPC_ID) as nome_operacao,
             (SELECT tc.TPC_NOME FROM tipos_clientes tc WHERE tc.TPC_ID = classificacao_fiscal.TPC_ID) as nome_tipo_cliente'
        );
        $this->db->from('classificacao_fiscal');
        $this->db->where('classificacao_fiscal.CLF_ID', $id);
        $this->db->where('classificacao_fiscal.ten_id', $this->session->userdata('ten_id'));
        $query = $this->db->get();
        if (!$query) {
            log_message('error', 'Erro ao buscar classificacao_fiscal por ID: ' . $this->db->error()['message']);
            return null;
        }
        return $query->row();
    }

    public function add($table, $data)
    {
        if ($table === 'classificacao_fiscal') {
            if (!isset($insert['TEN_ID']) && in_array('TEN_ID', $tableFields, true)) {
                $insert['TEN_ID'] = $this->session->userdata('ten_id');
            }
            // Mapear chaves lógicas -> colunas reais existentes na tabela
            $tableFields = array_map(function ($f) {
                return strtoupper($f->name);
            }, $this->db->field_data('classificacao_fiscal'));

            $map = [
                'operacao_comercial_id' => ['OPC_ID', 'OPC_ID', 'operacao_comercial_id'],
                'tipo_cliente_id' => ['TPC_ID', 'tipo_cliente_id'],
                'cst' => ['CLF_CST', 'cst'],
                'csosn' => ['CLF_CSOSN', 'csosn'],
                'natureza_contribuinte' => ['CLF_NATUREZA_CONTRIBUINTE', 'natureza_contribuinte'],
                'cfop' => ['CLF_CFOP', 'cfop'],
                'destinacao' => ['CLF_DESTINACAO', 'destinacao'],
                'objetivo_comercial' => ['CLF_OBJETIVO_COMERCIAL', 'objetivo_comercial'],
                'finalidade' => ['CLF_FINALIDADE', 'finalidade'],
                'tipo_icms' => ['CLF_TIPO_TRIBUTACAO', 'tipo_icms'],
                'cClassTrib' => ['CLF_CCLASSTRIB', 'cClassTrib'],
                'mensagem_fiscal' => ['CLF_MENSAGEM', 'mensagem_fiscal'],
                'created_at' => ['CLF_DATA_INCLUSAO', 'created_at'],
                'updated_at' => ['CLF_DATA_ALTERACAO', 'updated_at'],
            ];

            $insert = [];
            foreach ($map as $logical => $candidates) {
                if (!array_key_exists($logical, $data)) {
                    continue;
                }
                foreach ($candidates as $dbKey) {
                    if (in_array($dbKey, $tableFields, true)) {
                        $insert[$dbKey] = $data[$logical];
                        break;
                    }
                }
            }

            // Definir datas se existirem as colunas e não vierem no payload
            if (in_array('CLF_DATA_INCLUSAO', $tableFields, true) && !isset($insert['CLF_DATA_INCLUSAO'])) {
                $insert['CLF_DATA_INCLUSAO'] = date('Y-m-d H:i:s');
            }
            if (in_array('created_at', $tableFields, true) && !isset($insert['created_at'])) {
                $insert['created_at'] = date('Y-m-d H:i:s');
            }

            $this->db->insert('classificacao_fiscal', $insert);
        } else {
            $this->db->insert($table, $data);
        }
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        if ($table === 'classificacao_fiscal') {
            $tableFields = array_map(function ($f) {
                return strtoupper($f->name);
            }, $this->db->field_data('classificacao_fiscal'));

            $map = [
                'operacao_comercial_id' => ['OPC_ID', 'OPC_ID', 'operacao_comercial_id'],
                'tipo_cliente_id' => ['TPC_ID', 'tipo_cliente_id'],
                'cst' => ['CLF_CST', 'cst'],
                'csosn' => ['CLF_CSOSN', 'csosn'],
                'natureza_contribuinte' => ['CLF_NATUREZA_CONTRIBUINTE', 'natureza_contribuinte'],
                'cfop' => ['CLF_CFOP', 'cfop'],
                'destinacao' => ['CLF_DESTINACAO', 'destinacao'],
                'objetivo_comercial' => ['CLF_OBJETIVO_COMERCIAL', 'objetivo_comercial'],
                'finalidade' => ['CLF_FINALIDADE', 'finalidade'],
                'tipo_icms' => ['CLF_TIPO_TRIBUTACAO', 'tipo_icms'],
                'cClassTrib' => ['CLF_CCLASSTRIB', 'cClassTrib'],
                'mensagem_fiscal' => ['CLF_MENSAGEM', 'mensagem_fiscal'],
                'updated_at' => ['CLF_DATA_ALTERACAO', 'updated_at'],
            ];
            $update = [];
            foreach ($map as $logical => $candidates) {
                if (!array_key_exists($logical, $data)) {
                    continue;
                }
                foreach ($candidates as $dbKey) {
                    if (in_array($dbKey, $tableFields, true)) {
                        $update[$dbKey] = $data[$logical];
                        break;
                    }
                }
            }
            if (in_array('CLF_DATA_ALTERACAO', $tableFields, true) && !isset($update['CLF_DATA_ALTERACAO'])) {
                $update['CLF_DATA_ALTERACAO'] = date('Y-m-d H:i:s');
            }
            $pk = in_array('CLF_ID', $tableFields, true) ? 'CLF_ID' : $fieldID;
            $this->db->where($pk, $ID);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $this->db->update('classificacao_fiscal', $update);
        } else {
            $this->db->where($fieldID, $ID);
            $this->db->update($table, $data);
        }
        if ($this->db->affected_rows() >= 0) {
            return true;
        }
        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    public function count($table)
    {
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->count_all_results($table);
    }

    public function getTributacao($operacao_id, $natureza_contribuinte, $destinacao, $objetivo_comercial)
    {
        try {
            log_message('debug', 'Iniciando getTributacao no modelo');
            log_message('debug', 'Parâmetros recebidos: ' . json_encode([
                'operacao_id' => $operacao_id,
                'natureza_contribuinte' => $natureza_contribuinte,
                'destinacao' => $destinacao,
                'objetivo_comercial' => $objetivo_comercial
            ]));

            // Verificar se a tabela existe
            if (!$this->db->table_exists('classificacao_fiscal')) {
                log_message('error', 'Tabela classificacao_fiscal não existe');
                return null;
            }

            // Query usando apenas as colunas que existem na tabela
            $this->db->select(
                'CLF_ID as id,
                 OPC_ID as operacao_comercial_id,
                 CLF_CST as cst,
                 CLF_CSOSN as csosn,
                 CLF_NATUREZA_CONTRIBUINTE as natureza_contribuinte,
                 CLF_CFOP as cfop,
                 CLF_DESTINACAO as destinacao,
                 CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
                 CLF_TIPO_TRIBUTACAO as tipo_icms'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('OPC_ID', $operacao_id);
            $this->db->where('CLF_NATUREZA_CONTRIBUINTE', $natureza_contribuinte);
            $this->db->where('CLF_DESTINACAO', $destinacao);
            $this->db->where('CLF_OBJETIVO_COMERCIAL', $objetivo_comercial);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));

            // Log da query antes da execução
            $query = $this->db->get();
            log_message('debug', 'SQL Query: ' . $this->db->last_query());

            // Se não encontrou nenhum resultado, vamos logar os dados existentes para debug
            if ($query->num_rows() == 0) {
                // Consulta para verificar todas as classificações fiscais desta operação
                $this->db->select('CLF_ID, OPC_ID, CLF_NATUREZA_CONTRIBUINTE, CLF_DESTINACAO, CLF_OBJETIVO_COMERCIAL, CLF_CST, CLF_CFOP');
                $this->db->from('classificacao_fiscal');
            $this->db->where('OPC_ID', $operacao_id);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
                $check_query = $this->db->get();

                log_message('debug', 'Verificando classificações existentes para operação ' . $operacao_id);
                log_message('debug', 'Total de classificações encontradas: ' . $check_query->num_rows());

                if ($check_query->num_rows() > 0) {
                    foreach ($check_query->result() as $row) {
                        log_message('debug', 'Classificação encontrada: ' . json_encode([
                            'id' => $row->CLF_ID,
                            'operacao_comercial_id' => $row->OPC_ID,
                            'natureza_contribuinte' => $row->CLF_NATUREZA_CONTRIBUINTE,
                            'destinacao' => $row->CLF_DESTINACAO,
                            'objetivo_comercial' => $row->CLF_OBJETIVO_COMERCIAL,
                            'cst' => $row->CLF_CST,
                            'cfop' => $row->CLF_CFOP
                        ]));
                    }
                } else {
                    log_message('debug', 'Nenhuma classificação fiscal encontrada para a operação ' . $operacao_id);
                }

                return null;
            }

            // Retorna o primeiro resultado encontrado
            return $query->row();

        } catch (Exception $e) {
            log_message('error', 'Erro em getTributacao: ' . $e->getMessage());
            return null;
        }
    }

    public function getByOperacao($operacao_id)
    {
        try {
            log_message('debug', 'Iniciando getByOperacao no modelo');
            log_message('debug', 'Operação ID: ' . $operacao_id);

            // Verificar se a tabela existe
            if (!$this->db->table_exists('classificacao_fiscal')) {
                log_message('error', 'Tabela classificacao_fiscal não existe');
                return [];
            }

            $this->db->select(
                'CLF_ID as id,
                 OPC_ID as operacao_comercial_id,
                 CLF_CST as cst,
                 CLF_CSOSN as csosn,
                 CLF_NATUREZA_CONTRIBUINTE as natureza_contribuinte,
                 CLF_CFOP as cfop,
                 CLF_DESTINACAO as destinacao,
                 CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
                 CLF_TIPO_TRIBUTACAO as tipo_icms'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('OPC_ID', $operacao_id);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $query = $this->db->get();

            log_message('debug', 'SQL Query getByOperacao: ' . $this->db->last_query());
            log_message('debug', 'Registros encontrados: ' . $query->num_rows());

            return $query->result();
        } catch (Exception $e) {
            log_message('error', 'Erro em getByOperacao: ' . $e->getMessage());
            return [];
        }
    }

    public function getAliquota($cst, $destinacao, $natureza_contribuinte)
    {
        log_message('debug', 'ClassificacaoFiscal_model::getAliquota - Iniciando busca de alíquota');
        log_message('debug', 'Parâmetros: CST=' . $cst . ', Destinação=' . $destinacao . ', Natureza Contribuinte=' . $natureza_contribuinte);

        if (!$this->db->table_exists('aliquotas')) {
            log_message('error', 'Tabela aliquotas não existe');
            return null;
        }

        $this->db->select('*');
        $this->db->from('aliquotas');
        $this->db->where('cst', $cst);
        $this->db->where('destinacao', $destinacao);
        $this->db->where('natureza_contribuinte', $natureza_contribuinte);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $query = $this->db->get();

        log_message('debug', 'SQL: ' . $this->db->last_query());

        if ($query->num_rows() > 0) {
            $result = $query->row();
            log_message('debug', 'Alíquota encontrada: ' . json_encode($result));
            return $result;
        }

        log_message('debug', 'Nenhuma alíquota encontrada para os parâmetros informados');
        return null;
    }

    /**
     * Busca classificações fiscais filtrando por tenant (ten_id) e operação comercial (OPC_ID).
     * Parâmetros adicionais opcionais: natureza_contribuinte, destinacao, objetivo_comercial.
     */
    // Ordem obrigatória: ten_id, opc_id, destinacao, ativa
    // Opcionais: natureza_contribuinte, objetivo_comercial, finalidade, tipo_tributacao, tipo_cliente
    public function getByTenantAndOperacao($tenId, $operacaoId, $natureza = null, $destinacao = null, $objetivo = null, $finalidade = null, $tipo_tributacao = null, $tipo_cliente = null, $ativa = null)
    {
        try {
            $this->db->select(
                'CLF_ID as id,
                 CLF_CST as cst,
                 CLF_CSOSN as csosn,
                 CLF_CFOP as cfop'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('ten_id', $tenId);
            $this->db->where('OPC_ID', $operacaoId);

            if (!empty($natureza)) {
                $this->db->where('CLF_NATUREZA_CONTRIBUINTE', $natureza);
            }
            // destinacao é obrigatório
            $this->db->where('CLF_DESTINACAO', $destinacao);
            if (!empty($objetivo)) {
                $this->db->where('CLF_OBJETIVO_COMERCIAL', $objetivo);
            }
            if (!empty($finalidade)) {
                $this->db->where('CLF_FINALIDADE', $finalidade);
            }
            if (!empty($tipo_tributacao)) {
                $this->db->where('CLF_TIPO_TRIBUTACAO', $tipo_tributacao);
            }
            if (!empty($tipo_cliente)) {
                $tipoClienteInt = (int)$tipo_cliente;
                $this->db->where('TPC_ID', $tipoClienteInt);
            }
            // ativa é obrigatório - garantir que seja inteiro
            $ativaInt = ($ativa === '0' || $ativa === 0) ? 0 : (($ativa === '1' || $ativa === 1) ? 1 : (int)$ativa);
            $this->db->where('CLF_SITUACAO', $ativaInt);

            $query = $this->db->get();
            if (!$query) {
                log_message('error', 'Erro em getByTenantAndOperacao: ' . $this->db->error()['message']);
                return [];
            }
            $result = $query->result();
            // Log temporário para debug
            log_message('debug', 'getByTenantAndOperacao - Parâmetros: tenId=' . $tenId . ', opcId=' . $operacaoId . ', destinacao=' . $destinacao . ', ativa=' . $ativa . ' | Resultados: ' . count($result));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro em getByTenantAndOperacao: ' . $e->getMessage());
            return [];
        }
    }
}