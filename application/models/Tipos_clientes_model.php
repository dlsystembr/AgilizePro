<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tipos_clientes_model extends CI_Model
{
    /**
     * @param string $table
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'object', $order_field = 'tpc_nome', $order_direction = 'asc')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));

        $order_field = $order_field ?: 'tpc_nome';
        $order_direction = $order_direction ?: 'asc';
        $this->db->order_by($order_field, $order_direction);

        // Só aplica limit se perpage for maior que 0
        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();
        if ($query === false) {
            log_message('error', 'Tipos_clientes_model->get falhou. Tabela: ' . $table . ' | erro: ' . $this->db->error()['message']);
            return $one ? null : [];
        }

        $result = !$one ? $query->result($array === 'object' || $array === 'array' ? $array : 'object') : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->where('tpc_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);

        return $this->db->get('tipos_clientes')->row();
    }

    public function add($table, $data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete($table);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->count_all_results($table);
    }
}
