<?php

class Pessoas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields = '*', $search = '', $perPage = 0, $start = 0, $one = false)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('PES_ID', 'desc');
        if ($perPage) {
            $this->db->limit($perPage, $start);
        }

        if ($search) {
            // Busca por nome, razão social e documento
            $this->db->group_start();
            $this->db->like('PES_NOME', $search);
            $this->db->or_like('PES_RAZAO_SOCIAL', $search);
            $this->db->or_like('PES_CPFCNPJ', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $one ? $query->row() : $query->result();
    }

    public function getById($id)
    {
        $this->db->where('PES_ID', $id);
        $this->db->limit(1);
        return $this->db->get('pessoas')->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id($table);
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        return $this->db->affected_rows() == 1;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }
}