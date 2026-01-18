<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

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
             classificacao_fiscal.CLF_NATUREZA_CONTRIB as natureza_contribuinte,
             classificacao_fiscal.CLF_CFOP as cfop,
             classificacao_fiscal.CLF_DESTINACAO as destinacao,
             classificacao_fiscal.CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
             classificacao_fiscal.CLF_TIPO_ICMS as tipo_icms,
             classificacao_fiscal.CLF_DATA_INCLUSAO as created_at,
             classificacao_fiscal.CLF_DATA_ALTERACAO as updated_at,
             (SELECT oc.OPC_NOME FROM operacao_comercial oc WHERE oc.OPC_ID = classificacao_fiscal.OPC_ID) as nome_operacao'
        );
        $this->db->from('classificacao_fiscal');
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
             classificacao_fiscal.CLF_NATUREZA_CONTRIB as natureza_contribuinte,
             classificacao_fiscal.CLF_CFOP as cfop,
             classificacao_fiscal.CLF_DESTINACAO as destinacao,
             classificacao_fiscal.CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
             classificacao_fiscal.CLF_TIPO_ICMS as tipo_icms,
             classificacao_fiscal.CLF_DATA_INCLUSAO as created_at,
             classificacao_fiscal.CLF_DATA_ALTERACAO as updated_at,
             (SELECT oc.OPC_NOME FROM operacao_comercial oc WHERE oc.OPC_ID = classificacao_fiscal.OPC_ID) as nome_operacao'
        );
        $this->db->from('classificacao_fiscal');
        $this->db->where('classificacao_fiscal.CLF_ID', $id);
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
            // Mapear chaves lógicas -> colunas reais existentes na tabela
            $tableFields = array_map(function ($f) { return $f->name; }, $this->db->field_data('classificacao_fiscal'));

            $map = [
                'operacao_comercial_id' => ['OPC_ID', 'OPC_ID', 'operacao_comercial_id'],
                'cst' => ['CLF_CST', 'cst'],
                'csosn' => ['CLF_CSOSN', 'csosn'],
                'natureza_contribuinte' => ['CLF_NATUREZA_CONTRIB', 'natureza_contribuinte'],
                'cfop' => ['CLF_CFOP', 'cfop'],
                'destinacao' => ['CLF_DESTINACAO', 'destinacao'],
                'objetivo_comercial' => ['CLF_OBJETIVO_COMERCIAL', 'objetivo_comercial'],
                'tipo_icms' => ['CLF_TIPO_ICMS', 'tipo_icms'],
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
            $tableFields = array_map(function ($f) { return $f->name; }, $this->db->field_data('classificacao_fiscal'));
            $map = [
                'operacao_comercial_id' => ['OPC_ID', 'OPC_ID', 'operacao_comercial_id'],
                'cst' => ['CLF_CST', 'cst'],
                'csosn' => ['CLF_CSOSN', 'csosn'],
                'natureza_contribuinte' => ['CLF_NATUREZA_CONTRIB', 'natureza_contribuinte'],
                'cfop' => ['CLF_CFOP', 'cfop'],
                'destinacao' => ['CLF_DESTINACAO', 'destinacao'],
                'objetivo_comercial' => ['CLF_OBJETIVO_COMERCIAL', 'objetivo_comercial'],
                'tipo_icms' => ['CLF_TIPO_ICMS', 'tipo_icms'],
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
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
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

            // Construir a query mapeando colunas CLF_* para nomes antigos
            $this->db->select(
                'COALESCE(CLF_ID, id) as id,
                 COALESCE(OPC_ID, OPC_ID, operacao_comercial_id) as operacao_comercial_id,
                 COALESCE(CLF_CST, cst) as cst,
                 COALESCE(CLF_CSOSN, csosn) as csosn,
                 COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte) as natureza_contribuinte,
                 COALESCE(CLF_CFOP, cfop) as cfop,
                 COALESCE(CLF_DESTINACAO, destinacao) as destinacao,
                 COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial) as objetivo_comercial,
                 COALESCE(CLF_TIPO_ICMS, NULL) as tipo_icms'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('COALESCE(OPC_ID, OPC_ID, operacao_comercial_id)', $operacao_id);
            $this->db->where('COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte)', $natureza_contribuinte);
            $this->db->where('COALESCE(CLF_DESTINACAO, destinacao)', $destinacao);
            $this->db->where('COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial)', $objetivo_comercial);
            
            // Log da query antes da execução
            $query = $this->db->get();
            log_message('debug', 'SQL Query: ' . $this->db->last_query());
            
            // Se não encontrou nenhum resultado, vamos logar os dados existentes para debug
            if ($query->num_rows() == 0) {
                // Consulta para verificar todas as classificações fiscais desta operação
                $this->db->select('COALESCE(CLF_ID, id) as CLF_ID, COALESCE(OPC_ID, OPC_ID, operacao_comercial_id) as OPC_ID, COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte) as CLF_NATUREZA_CONTRIB, COALESCE(CLF_DESTINACAO, destinacao) as CLF_DESTINACAO, COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial) as CLF_OBJETIVO_COMERCIAL, COALESCE(CLF_CST, cst) as CLF_CST, COALESCE(CLF_CFOP, cfop) as CLF_CFOP');
                $this->db->from('classificacao_fiscal');
                $this->db->where('COALESCE(OPC_ID, OPC_ID, operacao_comercial_id)', $operacao_id);
                $check_query = $this->db->get();
                
                log_message('debug', 'Verificando classificações existentes para operação ' . $operacao_id);
                log_message('debug', 'Total de classificações encontradas: ' . $check_query->num_rows());
                
                if ($check_query->num_rows() > 0) {
                    foreach ($check_query->result() as $row) {
                        log_message('debug', 'Classificação encontrada: ' . json_encode([
                            'id' => $row->CLF_ID,
                            'operacao_comercial_id' => $row->OPC_ID,
                            'natureza_contribuinte' => $row->CLF_NATUREZA_CONTRIB,
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
                'COALESCE(CLF_ID, id) as id,
                 COALESCE(OPC_ID, OPC_ID, operacao_comercial_id) as operacao_comercial_id,
                 COALESCE(CLF_CST, cst) as cst,
                 COALESCE(CLF_CSOSN, csosn) as csosn,
                 COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte) as natureza_contribuinte,
                 COALESCE(CLF_CFOP, cfop) as cfop,
                 COALESCE(CLF_DESTINACAO, destinacao) as destinacao,
                 COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial) as objetivo_comercial,
                 COALESCE(CLF_TIPO_ICMS, NULL) as tipo_icms'
            );
            $this->db->from('classificacao_fiscal');
            $this->db->where('COALESCE(OPC_ID, OPC_ID, operacao_comercial_id)', $operacao_id);
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
} 