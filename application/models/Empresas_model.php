<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Empresas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $searchTerm = '', $perpage = 0, $start = 0)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));

        if ($searchTerm) {
            $this->db->group_start();
            $this->db->like('emp_razao_social', $searchTerm);
            $this->db->or_like('emp_cnpj', $searchTerm);
            $this->db->or_like('EMP_CODIGO', $searchTerm);
            $this->db->or_like('emp_nome_fantasia', $searchTerm);
            $this->db->group_end();
        }

        $this->db->order_by('emp_id', 'desc');
        $this->db->limit($perpage, $start);

        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->where('emp_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);
        return $this->db->get('empresas')->row();
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
