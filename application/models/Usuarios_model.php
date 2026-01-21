<?php

class Usuarios_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($perpage = 0, $start = 0, $one = false)
    {
        $this->db->from('usuarios');
        $this->db->select('usuarios.*, permissoes.nome as permissao');
        $this->db->limit($perpage, $start);
        $this->db->join('permissoes', 'usuarios.permissoes_id = permissoes.idPermissao', 'left');
        $this->db->where('usuarios.ten_id', $this->session->userdata('ten_id'));

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getAllTipos()
    {
        $this->db->where('situacao', 1);

        return $this->db->get('tiposUsuario')->result();
    }

    public function getById($id)
    {
        $this->db->where('idUsuarios', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getAll()
    {
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->get('usuarios')->result();
    }

    public function add($table, $data)
    {
        // Garantir que ten_id seja incluído se não estiver presente
        if ($table === 'usuarios' && !isset($data['ten_id'])) {
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
        if ($table != 'permissoes') {
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
        }
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        if ($table != 'permissoes') {
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
        }
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        if ($table != 'permissoes') {
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
        }
        return $this->db->count_all_results($table);
    }
}
