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
            'classificacao_fiscal.clf_id as id,
             classificacao_fiscal.opc_id as operacao_comercial_id,
             classificacao_fiscal.clf_cst as cst,
             classificacao_fiscal.clf_csosn as csosn,
             classificacao_fiscal.clf_natureza_contribuinte as natureza_contribuinte,
             classificacao_fiscal.clf_cfop as cfop,
             classificacao_fiscal.clf_destinacao as destinacao,
             classificacao_fiscal.clf_objetivo_comercial as objetivo_comercial,
             classificacao_fiscal.clf_finalidade as finalidade,
             classificacao_fiscal.clf_tipo_tributacao as tipo_icms,
             classificacao_fiscal.clf_cclasstrib as cClassTrib,
             classificacao_fiscal.clf_mensagem as mensagem_fiscal,
             classificacao_fiscal.clf_data_inclusao as created_at,
             classificacao_fiscal.clf_data_alteracao as updated_at,
             classificacao_fiscal.tpc_id as tipo_cliente_id,
             (SELECT oc.opc_nome FROM operacao_comercial oc WHERE oc.opc_id = classificacao_fiscal.opc_id) as nome_operacao,
             (SELECT tc.tpc_nome FROM tipos_clientes tc WHERE tc.tpc_id = classificacao_fiscal.tpc_id) as nome_tipo_cliente'
        );
        $this->db->from('classificacao_fiscal');
        $this->db->where('classificacao_fiscal.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('classificacao_fiscal.clf_id', 'DESC');

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
            'classificacao_fiscal.clf_id as id,
             classificacao_fiscal.opc_id as operacao_comercial_id,
             classificacao_fiscal.clf_cst as cst,
             classificacao_fiscal.clf_csosn as csosn,
             classificacao_fiscal.clf_natureza_contribuinte as natureza_contribuinte,
             classificacao_fiscal.clf_cfop as cfop,
             classificacao_fiscal.clf_destinacao as destinacao,
             classificacao_fiscal.clf_objetivo_comercial as objetivo_comercial,
             classificacao_fiscal.clf_finalidade as finalidade,
             classificacao_fiscal.clf_tipo_tributacao as tipo_icms,
             classificacao_fiscal.clf_cclasstrib as cClassTrib,
             classificacao_fiscal.clf_mensagem as mensagem_fiscal,
             classificacao_fiscal.clf_data_inclusao as created_at,
             classificacao_fiscal.clf_data_alteracao as updated_at,
             classificacao_fiscal.tpc_id as tipo_cliente_id,
             (SELECT oc.opc_nome FROM operacao_comercial oc WHERE oc.opc_id = classificacao_fiscal.opc_id) as nome_operacao,
             (SELECT tc.tpc_nome FROM tipos_clientes tc WHERE tc.tpc_id = classificacao_fiscal.tpc_id) as nome_tipo_cliente'
        );
        $this->db->from('classificacao_fiscal');
        $this->db->where('classificacao_fiscal.clf_id', $id);
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
            // Obter campos da tabela em caixa baixa para comparação
            $rawFields = $this->db->field_data('classificacao_fiscal');
            $tableFields = array_map(function ($f) {
                return strtolower($f->name);
            }, $rawFields);

            $map = [
                'operacao_comercial_id' => ['opc_id', 'operacao_comercial_id'],
                'tipo_cliente_id' => ['tpc_id', 'tipo_cliente_id'],
                'cst' => ['clf_cst', 'cst'],
                'csosn' => ['clf_csosn', 'csosn'],
                'natureza_contribuinte' => ['clf_natureza_contribuinte', 'natureza_contribuinte'],
                'cfop' => ['clf_cfop', 'cfop'],
                'destinacao' => ['clf_destinacao', 'destinacao'],
                'objetivo_comercial' => ['clf_objetivo_comercial', 'objetivo_comercial'],
                'finalidade' => ['clf_finalidade', 'finalidade'],
                'tipo_icms' => ['clf_tipo_tributacao', 'tipo_icms'],
                'cClassTrib' => ['clf_cclasstrib', 'cClassTrib'],
                'mensagem_fiscal' => ['clf_mensagem', 'mensagem_fiscal'],
                'created_at' => ['clf_data_inclusao', 'created_at'],
                'updated_at' => ['clf_data_alteracao', 'updated_at'],
            ];

            $insert = [];
            foreach ($map as $logical => $candidates) {
                if (!array_key_exists($logical, $data)) {
                    continue;
                }
                foreach ($candidates as $dbKey) {
                    if (in_array(strtolower($dbKey), $tableFields, true)) {
                        $insert[$dbKey] = $data[$logical];
                        break;
                    }
                }
            }

            // Adicionar ten_id
            if (in_array('ten_id', $tableFields, true) && !isset($insert['ten_id'])) {
                $insert['ten_id'] = $this->session->userdata('ten_id');
            }

            // Definir datas se existirem as colunas e não vierem no payload
            if (in_array('clf_data_inclusao', $tableFields, true) && !isset($insert['clf_data_inclusao'])) {
                $insert['clf_data_inclusao'] = date('Y-m-d H:i:s');
            }
            if (in_array('clf_data_alteracao', $tableFields, true) && !isset($insert['clf_data_alteracao'])) {
                $insert['clf_data_alteracao'] = date('Y-m-d H:i:s');
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
            // Obter campos da tabela em caixa baixa para comparação
            $rawFields = $this->db->field_data('classificacao_fiscal');
            $tableFields = array_map(function ($f) {
                return strtolower($f->name);
            }, $rawFields);

            $map = [
                'operacao_comercial_id' => ['opc_id', 'operacao_comercial_id'],
                'tipo_cliente_id' => ['tpc_id', 'tipo_cliente_id'],
                'cst' => ['clf_cst', 'cst'],
                'csosn' => ['clf_csosn', 'csosn'],
                'natureza_contribuinte' => ['clf_natureza_contribuinte', 'natureza_contribuinte'],
                'cfop' => ['clf_cfop', 'cfop'],
                'destinacao' => ['clf_destinacao', 'destinacao'],
                'objetivo_comercial' => ['clf_objetivo_comercial', 'objetivo_comercial'],
                'finalidade' => ['clf_finalidade', 'finalidade'],
                'tipo_icms' => ['clf_tipo_tributacao', 'tipo_icms'],
                'cClassTrib' => ['clf_cclasstrib', 'cClassTrib'],
                'mensagem_fiscal' => ['clf_mensagem', 'mensagem_fiscal'],
                'updated_at' => ['clf_data_alteracao', 'updated_at'],
            ];

            $update = [];
            foreach ($map as $logical => $candidates) {
                if (!array_key_exists($logical, $data)) {
                    continue;
                }
                foreach ($candidates as $dbKey) {
                    if (in_array(strtolower($dbKey), $tableFields, true)) {
                        $update[$dbKey] = $data[$logical];
                        break;
                    }
                }
            }

            // Atualizar data de alteração se a coluna existir
            if (in_array('clf_data_alteracao', $tableFields, true) && !isset($update['clf_data_alteracao'])) {
                $update['clf_data_alteracao'] = date('Y-m-d H:i:s');
            }

            // Determinar a chave primária correta
            $pk = in_array('clf_id', $tableFields, true) ? 'clf_id' : $fieldID;

            $this->db->where($pk, $ID);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $this->db->update('classificacao_fiscal', $update);
        } else {
            $this->db->where($fieldID, $ID);
            $this->db->update($table, $data);
        }

        // Em CodeIgniter, affected_rows >= 0 indica sucesso (zero se nada mudou)
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
                'clf_id as id,
                 opc_id as operacao_comercial_id,
                 clf_cst as cst,
                 clf_csosn as csosn,
                 clf_natureza_contribuinte as natureza_contribuinte,
                 clf_cfop as cfop,
                 clf_destinacao as destinacao,
                 clf_objetivo_comercial as objetivo_comercial,
                 clf_tipo_tributacao as tipo_icms'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('opc_id', $operacao_id);
            $this->db->where('clf_natureza_contribuinte', $natureza_contribuinte);
            $this->db->where('clf_destinacao', $destinacao);
            $this->db->where('clf_objetivo_comercial', $objetivo_comercial);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));

            // Log da query antes da execução
            $query = $this->db->get();
            log_message('debug', 'SQL Query: ' . $this->db->last_query());

            // Se não encontrou nenhum resultado, vamos logar os dados existentes para debug
            if ($query->num_rows() == 0) {
                // Consulta para verificar todas as classificações fiscais desta operação
                $this->db->select('clf_id, opc_id, clf_natureza_contribuinte, clf_destinacao, clf_objetivo_comercial, clf_cst, clf_cfop');
                $this->db->from('classificacao_fiscal');
                $this->db->where('opc_id', $operacao_id);
                $this->db->where('ten_id', $this->session->userdata('ten_id'));
                $check_query = $this->db->get();

                log_message('debug', 'Verificando classificações existentes para operação ' . $operacao_id);
                log_message('debug', 'Total de classificações encontradas: ' . $check_query->num_rows());

                if ($check_query->num_rows() > 0) {
                    foreach ($check_query->result() as $row) {
                        log_message('debug', 'Classificação encontrada: ' . json_encode([
                            'id' => $row->clf_id,
                            'operacao_comercial_id' => $row->opc_id,
                            'natureza_contribuinte' => $row->clf_natureza_contribuinte,
                            'destinacao' => $row->clf_destinacao,
                            'objetivo_comercial' => $row->clf_objetivo_comercial,
                            'cst' => $row->clf_cst,
                            'cfop' => $row->clf_cfop
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
                'clf_id as id,
                 opc_id as operacao_comercial_id,
                 clf_cst as cst,
                 clf_csosn as csosn,
                 clf_natureza_contribuinte as natureza_contribuinte,
                 clf_cfop as cfop,
                 clf_destinacao as destinacao,
                 clf_objetivo_comercial as objetivo_comercial,
                 clf_tipo_tributacao as tipo_icms'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('opc_id', $operacao_id);
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
     * Busca classificações fiscais filtrando por tenant (ten_id) e operação comercial (opc_id).
     * Parâmetros adicionais opcionais: natureza_contribuinte, destinacao, objetivo_comercial.
     */
    // Ordem obrigatória: ten_id, opc_id, destinacao, ativa
    // Opcionais: natureza_contribuinte, objetivo_comercial, finalidade, tipo_tributacao, tipo_cliente
    public function getByTenantAndOperacao($tenId, $operacaoId, $natureza = null, $destinacao = null, $objetivo = null, $finalidade = null, $tipo_tributacao = null, $tipo_cliente = null, $ativa = null)
    {
        try {
            $this->db->select(
                'clf_id as id,
                 clf_cst as cst,
                 clf_csosn as csosn,
                 clf_cfop as cfop'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('ten_id', $tenId);
            $this->db->where('opc_id', $operacaoId);

            if (!empty($natureza)) {
                $this->db->where('clf_natureza_contribuinte', $natureza);
            }
            // destinacao é obrigatório
            $this->db->where('clf_destinacao', $destinacao);
            if (!empty($objetivo)) {
                $this->db->where('clf_objetivo_comercial', $objetivo);
            }
            if (!empty($finalidade)) {
                $this->db->where('clf_finalidade', $finalidade);
            }
            if (!empty($tipo_tributacao)) {
                $this->db->where('clf_tipo_tributacao', $tipo_tributacao);
            }
            if (!empty($tipo_cliente)) {
                $tipoClienteInt = (int) $tipo_cliente;
                $this->db->where('tpc_id', $tipoClienteInt);
            }
            // ativa é obrigatório - garantir que seja inteiro
            $ativaInt = ($ativa === '0' || $ativa === 0) ? 0 : (($ativa === '1' || $ativa === 1) ? 1 : (int) $ativa);
            $this->db->where('clf_situacao', $ativaInt);

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