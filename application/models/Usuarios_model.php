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
        $this->db->select('usuarios.*');
        if ($this->db->table_exists('grupo_usuario_empresa') && $this->db->table_exists('grupo_usuario')) {
            $this->db->select('grupo_usuario.gpu_nome as permissao');
            $emp_id = (int) $this->session->userdata('emp_id');
            $this->db->join('grupo_usuario_empresa', 'grupo_usuario_empresa.usu_id = usuarios.usu_id AND grupo_usuario_empresa.emp_id = ' . $emp_id, 'left');
            $this->db->join('grupo_usuario', 'grupo_usuario.gpu_id = grupo_usuario_empresa.gpu_id', 'left');
        }
        $this->db->limit($perpage, $start);
        $this->db->where('usuarios.gre_id', $this->session->userdata('ten_id'));

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
        $this->db->where('usu_id', $id);
        if ($this->db->field_exists('gre_id', 'usuarios')) {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
        } elseif ($this->db->field_exists('ten_id', 'usuarios')) {
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
        }
        $this->db->limit(1);

        return $this->db->get('usuarios')->row();
    }

    public function getAll()
    {
        $col = $this->db->field_exists('gre_id', 'usuarios') ? 'gre_id' : 'ten_id';
        $this->db->where($col, $this->session->userdata('ten_id'));
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
        if ($table === 'usuarios' && $this->db->field_exists('gre_id', 'usuarios')) {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
        } elseif ($table !== 'usuarios') {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
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
        if ($table === 'usuarios' && $this->db->field_exists('gre_id', 'usuarios')) {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
        } elseif ($table !== 'usuarios') {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
        }
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        if ($table === 'usuarios' && $this->db->field_exists('gre_id', 'usuarios')) {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
        } elseif ($table !== 'usuarios') {
            $this->db->where('gre_id', $this->session->userdata('ten_id'));
        }
        return $this->db->count_all_results($table);
    }
}
