<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Aliquotas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('uf_origem', 'ASC');
        if ($where) {
            $this->db->where($where);
        }

        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        if ($one) {
            $method = 'row';
        } else {
            $method = 'result';
        }

        return $this->db->get()->$method();
    }

    public function getById($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('aliquotas')->row();
    }

    public function add($data)
    {
        $this->db->insert('aliquotas', $data);
        return $this->db->insert_id();
    }

    public function edit($data, $id)
    {
        $this->db->where('id', $id);
        return $this->db->update('aliquotas', $data);
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('aliquotas');
    }

    public function getAliquota($uf_origem, $uf_destino)
    {
        $this->db->where('uf_origem', $uf_origem);
        $this->db->where('uf_destino', $uf_destino);
        return $this->db->get('aliquotas')->row();
    }

    public function getAliquotaExceto($uf_origem, $uf_destino, $id)
    {
        $this->db->where('uf_origem', $uf_origem);
        $this->db->where('uf_destino', $uf_destino);
        $this->db->where('id !=', $id);
        return $this->db->get('aliquotas')->row();
    }

    public function getUFs()
    {
        return [
            'AC' => 'Acre',
            'AL' => 'Alagoas',
            'AP' => 'Amapá',
            'AM' => 'Amazonas',
            'BA' => 'Bahia',
            'CE' => 'Ceará',
            'DF' => 'Distrito Federal',
            'ES' => 'Espírito Santo',
            'GO' => 'Goiás',
            'MA' => 'Maranhão',
            'MT' => 'Mato Grosso',
            'MS' => 'Mato Grosso do Sul',
            'MG' => 'Minas Gerais',
            'PA' => 'Pará',
            'PB' => 'Paraíba',
            'PR' => 'Paraná',
            'PE' => 'Pernambuco',
            'PI' => 'Piauí',
            'RJ' => 'Rio de Janeiro',
            'RN' => 'Rio Grande do Norte',
            'RS' => 'Rio Grande do Sul',
            'RO' => 'Rondônia',
            'RR' => 'Roraima',
            'SC' => 'Santa Catarina',
            'SP' => 'São Paulo',
            'SE' => 'Sergipe',
            'TO' => 'Tocantins'
        ];
    }
} 