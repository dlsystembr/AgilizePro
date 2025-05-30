<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class TributacaoProduto_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table = 'tributacao_produto', $fields = '*')
    {
        $this->db->select($fields);
        $this->db->from($table);
        return $this->db->get()->result();
    }

    public function getById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get('tributacao_produto')->row();
    }

    public function add($table, $data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
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