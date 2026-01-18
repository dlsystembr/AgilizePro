<?php

class TiposPessoa_model extends CI_Model
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
            $this->db->like('nome', $where);
            $this->db->or_like('descricao', $where);
        }
        $this->db->limit($perpage, $start);
        
        if ($one) {
            $method = 'row';
        } else {
            $method = 'result';
        }

        if (!is_null($where)) {
            $this->db->where($where);
        }

        return $this->db->$method();
    }

    public function getById($id)
    {
        $this->db->where('id', $id);
        $this->db->limit(1);
        return $this->db->get('tipos_pessoa')->row();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id();
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

    public function getTiposByPessoa($pessoa_id)
    {
        $this->db->select('tipos_pessoa.*');
        $this->db->from('tipos_pessoa');
        $this->db->join('pessoa_tipos', 'pessoa_tipos.tipo_id = tipos_pessoa.id');
        $this->db->where('pessoa_tipos.pessoa_id', $pessoa_id);
        return $this->db->get()->result();
    }

    public function addTipoToPessoa($pessoa_id, $tipo_id)
    {
        $data = [
            'pessoa_id' => $pessoa_id,
            'tipo_id' => $tipo_id
        ];
        return $this->add('pessoa_tipos', $data);
    }

    public function removeTipoFromPessoa($pessoa_id, $tipo_id)
    {
        $this->db->where('pessoa_id', $pessoa_id);
        $this->db->where('tipo_id', $tipo_id);
        $this->db->delete('pessoa_tipos');
        return $this->db->affected_rows() > 0;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }
} 