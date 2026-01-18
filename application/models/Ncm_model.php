<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ncm_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function pesquisar($termo)
    {
        $this->db->select('id, codigo, descricao');
        $this->db->from('ncms');
        
        if (!empty($termo)) {
            $this->db->group_start();
            $this->db->like('codigo', $termo);
            $this->db->or_like('descricao', $termo);
            $this->db->group_end();
        }
        
        // Filtra apenas códigos com 8 dígitos
        $this->db->where('LENGTH(codigo) = 8');
        
        $this->db->order_by('codigo', 'ASC');
        $this->db->limit(100); // Aumentei o limite para 100 resultados
        
        return $this->db->get()->result();
    }
} 