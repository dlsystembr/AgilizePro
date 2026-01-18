<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class Tipos_pessoa_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('nome', 'asc');
        if ($where) {
            $this->db->where($where);
        }

        if ($one) {
            $method = 'row';
        } else {
            $method = 'result';
        }

        if (!is_null($perpage) && $perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        if ($array == 'array') {
            return $this->db->$method('array');
        } else {
            return $this->db->$method();
        }
    }

    public function getById($id)
    {
        $this->db->where('idTipoPessoa', $id);
        return $this->db->get('tipos_pessoa')->row();
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