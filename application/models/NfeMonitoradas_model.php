<?php

defined('BASEPATH') or exit('No direct script access allowed');

class NfeMonitoradas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function add($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('nfe_monitoradas', $data);
    }

    public function getByChaveAcesso($chave)
    {
        return $this->db->where('chave_acesso', $chave)
                        ->get('nfe_monitoradas')
                        ->row();
    }

    public function getAll($where = [], $limit = null, $offset = null)
    {
        if (!empty($where)) {
            $this->db->where($where);
        }
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->order_by('data_emissao', 'DESC')
                        ->get('nfe_monitoradas')
                        ->result();
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)
                        ->update('nfe_monitoradas', $data);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)
                        ->delete('nfe_monitoradas');
    }
} 