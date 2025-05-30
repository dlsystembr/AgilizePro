<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ClassificacaoFiscal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table = 'classificacao_fiscal', $fields = '*')
    {
        $this->db->select('classificacao_fiscal.*, operacao_comercial.nome as nome_operacao');
        $this->db->from('classificacao_fiscal');
        $this->db->join('operacao_comercial', 'operacao_comercial.id = classificacao_fiscal.operacao_comercial_id');
        $this->db->order_by('classificacao_fiscal.id', 'DESC');
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->select('classificacao_fiscal.*, operacao_comercial.nome as nome_operacao');
        $this->db->from('classificacao_fiscal');
        $this->db->join('operacao_comercial', 'operacao_comercial.id = classificacao_fiscal.operacao_comercial_id');
        $this->db->where('classificacao_fiscal.id', $id);
        return $this->db->get()->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);
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

            // Construir a query conforme o exemplo fornecido
            $this->db->select('*');
            $this->db->from('classificacao_fiscal');
            $this->db->where('operacao_comercial_id', $operacao_id);
            $this->db->where('natureza_contribuinte', $natureza_contribuinte);
            $this->db->where('destinacao', $destinacao);
            $this->db->where('objetivo_comercial', $objetivo_comercial);
            
            // Log da query antes da execução
            $query = $this->db->get();
            log_message('debug', 'SQL Query: ' . $this->db->last_query());
            
            // Se não encontrou nenhum resultado, vamos logar os dados existentes para debug
            if ($query->num_rows() == 0) {
                // Consulta para verificar todas as classificações fiscais desta operação
                $this->db->select('*');
                $this->db->from('classificacao_fiscal');
                $this->db->where('operacao_comercial_id', $operacao_id);
                $check_query = $this->db->get();
                
                log_message('debug', 'Verificando classificações existentes para operação ' . $operacao_id);
                log_message('debug', 'Total de classificações encontradas: ' . $check_query->num_rows());
                
                if ($check_query->num_rows() > 0) {
                    foreach ($check_query->result() as $row) {
                        log_message('debug', 'Classificação encontrada: ' . json_encode([
                            'id' => $row->id,
                            'operacao_comercial_id' => $row->operacao_comercial_id,
                            'natureza_contribuinte' => $row->natureza_contribuinte,
                            'destinacao' => $row->destinacao,
                            'objetivo_comercial' => $row->objetivo_comercial,
                            'cst' => $row->cst,
                            'cfop' => $row->cfop
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

            $this->db->select('*');
            $this->db->from('classificacao_fiscal');
            $this->db->where('operacao_comercial_id', $operacao_id);
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