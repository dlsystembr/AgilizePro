<?php

class Produtos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('pro_id', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->like('pro_cod_barra', $where);
            $this->db->or_like('pro_descricao', $where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();
        
        // Calcular estoque baseado em produtos_movimentados
        if ($one) {
            $result = $this->calcularEstoque($result);
        } else {
            foreach ($result as $row) {
                $this->calcularEstoque($row);
            }
        }

        return $result;
    }
    
    /**
     * Calcula o estoque do produto baseado em produtos_movimentados
     * Considera apenas documentos com status FATURADO
     */
    private function calcularEstoque($produto)
    {
        if (!$produto) {
            return $produto;
        }
        
        // Buscar movimentações do produto através de itens_faturados
        // Soma ENTRADAs e subtrai SAIDAs
        // Apenas de documentos com status FATURADO
        $this->db->select('SUM(CASE WHEN pdm_tipo = "ENTRADA" THEN pdm_qtde ELSE -pdm_qtde END) as estoque_calculado');
        $this->db->from('produtos_movimentados');
        $this->db->join('itens_faturados', 'itens_faturados.itf_id = produtos_movimentados.itf_id');
        $this->db->join('documentos_faturados', 'documentos_faturados.dcf_id = itens_faturados.dcf_id');
        $this->db->where('itens_faturados.pro_id', $produto->pro_id);
        $this->db->where('documentos_faturados.dcf_status', 'faturado');
        $query = $this->db->get();
        $estoque_result = $query->row();
        
        // Atualizar o estoque no objeto
        $produto->pro_estoque = ($estoque_result && $estoque_result->estoque_calculado !== null) 
            ? floatval($estoque_result->estoque_calculado) 
            : 0.00;
        
        return $produto;
    }

    public function getById($id)
    {
        $this->db->where('pro_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);

        $result = $this->db->get('produtos')->row();
        
        // Calcular estoque baseado em produtos_movimentados
        return $this->calcularEstoque($result);
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
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update($table, $data);

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

    public function updateEstoque($produto, $quantidade, $operacao = '-')
    {
        $sql = "UPDATE produtos set pro_estoque = pro_estoque $operacao ? WHERE pro_id = ? AND ten_id = ?";

        return $this->db->query($sql, [$quantidade, $produto, $this->session->userdata('ten_id')]);
    }
    
    /**
     * Cria registro de movimentação de estoque em produtos_movimentados
     * @param int $itf_id ID do item faturado
     * @param decimal $quantidade Quantidade movimentada
     * @param string $tipo Tipo de movimentação: ENTRADA ou SAIDA
     * @return bool
     */
    public function criarMovimentacaoEstoque($itf_id, $quantidade, $tipo = 'ENTRADA')
    {
        if (!in_array($tipo, ['ENTRADA', 'SAIDA'])) {
            $tipo = 'ENTRADA';
        }
        
        $data = [
            'pdm_qtde' => $quantidade,
            'pdm_tipo' => $tipo,
            'itf_id' => $itf_id,
            'pdm_data' => date('Y-m-d H:i:s')
        ];
        
        return $this->db->insert('produtos_movimentados', $data);
    }
}
