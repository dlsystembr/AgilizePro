<?php

class Contratos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields = '*', $search = '', $perPage = 0, $start = 0, $one = false)
    {
        $this->db->select('c.*, p.PES_NOME, p.PES_CPFCNPJ, p.PES_RAZAO_SOCIAL');
        $this->db->from('contratos c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('c.CTR_ID', 'desc');
        
        if ($perPage) {
            $this->db->limit($perPage, $start);
        }

        if ($search) {
            // Busca por número do contrato, nome do cliente ou CPF/CNPJ
            $this->db->group_start();
            $this->db->like('c.CTR_NUMERO', $search);
            $this->db->or_like('p.PES_NOME', $search);
            $this->db->or_like('p.PES_RAZAO_SOCIAL', $search);
            $this->db->or_like('p.PES_CPFCNPJ', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $one ? $query->row() : $query->result();
    }

    public function getById($id)
    {
        $this->db->select('c.*, p.PES_NOME, p.PES_CPFCNPJ, p.PES_RAZAO_SOCIAL, p.PES_FISICO_JURIDICO');
        $this->db->from('contratos c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->where('c.CTR_ID', $id);
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function add($table, $data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id($table);
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update($table, $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete($table);
        return $this->db->affected_rows() == 1;
    }

    public function count($table, $search = '')
    {
        $this->db->from('contratos c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('c.CTR_NUMERO', $search);
            $this->db->or_like('p.PES_NOME', $search);
            $this->db->or_like('p.PES_RAZAO_SOCIAL', $search);
            $this->db->or_like('p.PES_CPFCNPJ', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function getTiposAssinante()
    {
        return [
            1 => 'Comercial',
            2 => 'Industrial',
            3 => 'Residencia/PF',
            4 => 'Produtor Rural',
            5 => 'Orgão Público Estadual',
            6 => 'Prestador de Telecom',
            7 => 'Missões Diplomáticas',
            8 => 'Igrejas e Templos',
            99 => 'Outros'
        ];
    }
}
