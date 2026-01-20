<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TiposClientes_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll()
    {
        $this->db->select('*');
        $this->db->from('tipos_clientes');
        $this->db->order_by('TPC_NOME', 'ASC');
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return [];
    }
}
