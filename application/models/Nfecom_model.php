<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Nfecom_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('nfc_id', 'desc');

        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        // Condicionais da pesquisa
        if ($where) {
            // Condicional de pesquisa (chave, cliente, etc)
            if (array_key_exists('pesquisa', $where)) {
                $this->db->group_start();
                $this->db->like('nfc_ch_nfcom', $where['pesquisa']);
                $this->db->or_like('nfc_x_nome_dest', $where['pesquisa']);
                $this->db->or_like('nfc_nnf', $where['pesquisa']);
                $this->db->group_end();
            }

            // Condicional de status
            if (array_key_exists('status', $where)) {
                $this->db->where_in('nfc_status', $where['status']);
            }

            // Condicional data emissão
            if (array_key_exists('de', $where)) {
                $this->db->where('DATE(nfc_dhemi) >=', $where['de']);
            }
            // Condicional data final
            if (array_key_exists('ate', $where)) {
                $this->db->where('DATE(nfc_dhemi) <=', $where['ate']);
            }
        }

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('*');
        $this->db->from('nfecom_capa');
        $this->db->where('nfc_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $query = $this->db->get();

        return $query->row();
    }

    public function getByIdWithOperation($id)
    {
        $this->db->select('nfecom_capa.*, operacao_comercial.opc_nome as operacao_nome');
        $this->db->from('nfecom_capa');
        $this->db->join('operacao_comercial', 'operacao_comercial.opc_id = nfecom_capa.opc_id', 'left');
        $this->db->where('nfecom_capa.nfc_id', $id);
        $this->db->where('nfecom_capa.ten_id', $this->session->userdata('ten_id'));
        $query = $this->db->get();

        return $query->row();
    }

    public function getItens($nfecomId)
    {
        // Verificar se o campo clf_id existe na tabela
        $fields = $this->db->list_fields('nfecom_itens');
        $selectFields = '*';
        if (in_array('clf_id', $fields)) {
            $selectFields = '*, clf_id';
        } elseif (in_array('clf_id', $fields)) {
            $selectFields = '*, clf_id';
        }
        
        $this->db->select($selectFields);
        $this->db->from('nfecom_itens');
        $this->db->where('nfc_id', $nfecomId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('nfi_n_item', 'asc');
        $query = $this->db->get();

        return $query->result();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id();
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);

        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function getNextNumero()
    {
        $this->db->select_max('nfc_nnf');
        $query = $this->db->get('nfecom_capa');
        $result = $query->row();

        return ($result->nfc_nnf ?? 0) + 1;
    }

    public function updateStatus($id, $data)
    {
        $this->db->where('nfc_id', $id);
        $this->db->update('nfecom_capa', $data);

        return $this->db->affected_rows() > 0;
    }

    public function getStatusDescription($status)
    {
        $statuses = [
            0 => 'Rascunho',
            1 => 'Salvo',
            2 => 'Enviado',
            3 => 'Autorizado',
            4 => 'Rejeitado'
        ];

        return $statuses[$status] ?? 'Desconhecido';
    }

    public function getTotalValor($id)
    {
        $this->db->select_sum('nfi_v_prod');
        $this->db->from('nfecom_itens');
        $this->db->where('nfc_id', $id);
        $query = $this->db->get();

        return $query->row()->nfi_v_prod ?? 0;
    }

    public function getNfecomByChave($chave)
    {
        $this->db->select('*');
        $this->db->from('nfecom_capa');
        $this->db->where('nfc_ch_nfcom', $chave);
        $query = $this->db->get();

        return $query->row();
    }
}