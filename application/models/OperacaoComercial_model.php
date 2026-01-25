<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class OperacaoComercial_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $this->db->select('opc_id, opc_sigla, opc_nome, opc_natureza_operacao, opc_tipo_movimento, opc_afeta_custo, opc_fato_fiscal, opc_gera_financeiro, opc_movimenta_estoque, opc_situacao, opc_finalidade_nfe');
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->get('operacao_comercial')->result();
    }

    public function getById($id)
    {
        $this->db->select('opc_id, opc_sigla, opc_nome, opc_natureza_operacao, opc_tipo_movimento, opc_afeta_custo, opc_fato_fiscal, opc_gera_financeiro, opc_movimenta_estoque, opc_situacao, opc_finalidade_nfe');
        $this->db->where('opc_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $query = $this->db->get('operacao_comercial');
        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function add($data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert('operacao_comercial', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('opc_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->update('operacao_comercial', $data);
    }

    public function delete($id)
    {
        $this->db->where('opc_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->delete('operacao_comercial');
    }

    public function get($search = null, $situacao = null, $per_page = null, $start = null)
    {
        $this->db->select('opc_id, opc_sigla, opc_nome, opc_natureza_operacao, opc_tipo_movimento, opc_afeta_custo, opc_fato_fiscal, opc_gera_financeiro, opc_movimenta_estoque, opc_situacao, opc_finalidade_nfe');
        $this->db->from('operacao_comercial');
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('opc_sigla', $search, 'both');
            $this->db->or_like('opc_nome', $search, 'both');
            $this->db->group_end();
        }
        
        if ($situacao !== null && $situacao !== '') {
            $this->db->where('opc_situacao', $situacao);
        }
        
        $this->db->order_by('opc_id', 'DESC');
        
        if ($per_page && $start !== null) {
            $this->db->limit($per_page, $start);
        }
        
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        }
        return array();
    }

    public function count($search = null, $situacao = null)
    {
        $this->db->from('operacao_comercial');
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('opc_sigla', $search, 'both');
            $this->db->or_like('opc_nome', $search, 'both');
            $this->db->group_end();
        }
        
        if ($situacao !== null && $situacao !== '') {
            $this->db->where('opc_situacao', $situacao);
        }
        
        return $this->db->count_all_results();
    }
} 