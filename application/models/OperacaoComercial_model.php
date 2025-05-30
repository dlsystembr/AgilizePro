<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class OperacaoComercial_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $this->db->select('id, nome as nome_operacao, mensagem_nota');
        return $this->db->get('operacao_comercial')->result();
    }

    public function getById($id)
    {
        $this->db->select('id, nome as nome_operacao, mensagem_nota');
        $this->db->where('id', $id);
        $query = $this->db->get('operacao_comercial');
        if ($query && $query->num_rows() > 0) {
            return $query->row();
        }
        return null;
    }

    public function add($data)
    {
        $this->db->insert('operacao_comercial', $data);
        return $this->db->insert_id();
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('operacao_comercial', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('operacao_comercial');
    }

    public function get($table = 'operacao_comercial', $fields = '*')
    {
        $this->db->select('id, nome as nome_operacao, mensagem_nota');
        $this->db->from('operacao_comercial');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        if ($query) {
            return $query->result();
        }
        return array();
    }
} 