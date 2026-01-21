<?php

class Servicos_model extends CI_Model
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
        $this->db->order_by('idServicos', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->like('nome', $where);
            $this->db->or_like('descricao', $where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->where('idServicos', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);

        return $this->db->get('servicos')->row();
    }

    public function add($table, $data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
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
}
