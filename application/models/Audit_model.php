<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Audit_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('idLogs', 'desc');
        $this->db->limit($perpage, $start);
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function add($data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert('logs', $data);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->count_all_results('logs');
    }

    public function clean()
    {
        $this->db->where('data <', date('Y-m-d', strtotime('- 30 days')));
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete('logs');

        if ($this->db->affected_rows()) {
            return true;
        }

        return false;
    }
}

/* End of file Log_model.php */
/* Location: ./application/models/Log_model.php */
