<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Usuarios_super_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($perpage = 0, $start = 0, $one = false)
    {
        $this->db->from('usuarios_super');
        $this->db->select('usuarios_super.*');
        $this->db->limit($perpage, $start);
        $this->db->order_by('USS_NOME', 'ASC');

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->where('USS_ID', $id);
        $this->db->limit(1);

        return $this->db->get('usuarios_super')->row();
    }

    public function getByEmail($email)
    {
        $this->db->where('USS_EMAIL', $email);
        $this->db->where('USS_SITUACAO', 1);
        $this->db->limit(1);

        return $this->db->get('usuarios_super')->row();
    }

    public function getAll()
    {
        $this->db->order_by('USS_NOME', 'ASC');
        return $this->db->get('usuarios_super')->result();
    }

    public function add($data)
    {
        $this->db->insert('usuarios_super', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function edit($data, $id)
    {
        $this->db->where('USS_ID', $id);
        $this->db->update('usuarios_super', $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($id)
    {
        $this->db->where('USS_ID', $id);
        $this->db->delete('usuarios_super');

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function count()
    {
        return $this->db->count_all_results('usuarios_super');
    }
}

