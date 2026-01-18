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
        $this->db->select('*');
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
                $this->db->where('ncm_id', $ncm_id);
                $data['tbf_data_alteracao'] = date('Y-m-d H:i:s');
                $result = $this->db->update('tributacao_federal', $data);
                log_message('debug', 'SQL Update: ' . $this->db->last_query());
            } else {
                // Insere nova tributação
                $data['ncm_id'] = $ncm_id;
                $data['tbf_data_cadastro'] = date('Y-m-d H:i:s');
                $data['tbf_data_alteracao'] = date('Y-m-d H:i:s');
                $result = $this->db->insert('tributacao_federal', $data);
                log_message('debug', 'SQL Insert: ' . $this->db->last_query());
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Erro na transação: ' . $this->db->error()['message']);
                return false;
            }

            log_message('debug', 'Resultado da operação: ' . ($result ? 'Sucesso' : 'Falha'));
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro ao salvar tributação federal: ' . $e->getMessage());
            return false;
        }
    }
} 