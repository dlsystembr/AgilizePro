<?php

class Permissoes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('idPermissao', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            if (is_array($where)) {
                $this->db->where($where);
            } else {
                $this->db->where($where);
            }
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getActive($table, $fields, $ten_id = null)
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('situacao', 1);
        if ($ten_id) {
            $this->db->where('ten_id', $ten_id);
        }
        $query = $this->db->get();

        return $query->result();

    }

    public function getById($id, $ten_id = null)
    {
        $this->db->where('idPermissao', $id);
        if ($ten_id) {
            $this->db->where('ten_id', $ten_id);
        }
        $this->db->limit(1);

        return $this->db->get('permissoes')->row();
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

/* End of file permissoes_model.php */
/* Location: ./application/models/permissoes_model.php */
