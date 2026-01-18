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

        if ($searchTerm) {
            $this->db->group_start();
            $this->db->like('EMP_RAZAO_SOCIAL', $searchTerm);
            $this->db->or_like('EMP_CNPJ', $searchTerm);
            $this->db->or_like('EMP_CODIGO', $searchTerm);
            $this->db->or_like('EMP_NOME_FANTASIA', $searchTerm);
            $this->db->group_end();
        }

        $this->db->order_by('EMP_ID', 'desc');
        $this->db->limit($perpage, $start);

        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->where('EMP_ID', $id);
        $this->db->limit(1);
        return $this->db->get('empresas')->row();
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
}
