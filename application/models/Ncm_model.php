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
        $this->db->select('id, ncm_codigo, ncm_descricao');
        $this->db->from('ncms');
        // NCMs são compartilhados entre todos os tenants
        
        if (!empty($termo)) {
            $this->db->group_start();
            $this->db->like('ncm_codigo', $termo);
            $this->db->or_like('ncm_descricao', $termo);
            $this->db->group_end();
        }
        
        // Filtra apenas códigos com 8 dígitos
        $this->db->where('LENGTH(ncm_codigo) = 8');
        
        $this->db->order_by('ncm_codigo', 'ASC');
        $this->db->limit(100); // Aumentei o limite para 100 resultados
        
        return $this->db->get()->result();
    }
} 