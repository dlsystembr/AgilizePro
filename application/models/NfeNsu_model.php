<?php

defined('BASEPATH') or exit('No direct script access allowed');

class NfeNsu_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getLastNsu()
    {
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('nfe_nsu');
        return $query->row();
    }

    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('nfe_nsu', $data);
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update('nfe_nsu', $data);
    }
} 