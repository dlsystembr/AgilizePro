<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Certificados_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Busca certificados da empresa
     */
    public function get($empId, $apenasAtivos = true)
    {
        $this->db->select('*');
        $this->db->from('certificados_digitais');
        $this->db->where('emp_id', $empId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));

        if ($apenasAtivos) {
            $this->db->where('cer_ativo', 1);
        }

        $this->db->order_by('cer_data_upload', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Busca certificado por ID
     */
    public function getById($id)
    {
        $this->db->where('cer_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->get('certificados_digitais')->row();
    }

    /**
     * Adiciona novo certificado
     */
    public function add($data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert('certificados_digitais', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Atualiza certificado
     */
    public function edit($id, $data)
    {
        $this->db->where('cer_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update('certificados_digitais', $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Exclui certificado
     */
    public function delete($id)
    {
        $this->db->where('cer_id', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete('certificados_digitais');
        return $this->db->affected_rows() == 1;
    }

    /**
     * Busca certificados válidos da empresa
     */
    public function getCertificadosValidos($empId)
    {
        $this->db->select('*');
        $this->db->from('certificados_digitais');
        $this->db->where('emp_id', $empId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->where('cer_ativo', 1);
        $this->db->where('cer_validade_fim >=', date('Y-m-d'));
        $this->db->order_by('cer_validade_fim', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Verifica se certificado está vencido
     */
    public function isVencido($certificado)
    {
        if (!$certificado)
            return true;

        $validade = is_object($certificado) ? $certificado->cer_validade_fim : $certificado['cer_validade_fim'];
        return strtotime($validade) < strtotime(date('Y-m-d'));
    }

    /**
     * Conta dias para vencer
     */
    public function diasParaVencer($certificado)
    {
        if (!$certificado)
            return 0;

        $validade = is_object($certificado) ? $certificado->cer_validade_fim : $certificado['cer_validade_fim'];
        $diff = strtotime($validade) - strtotime(date('Y-m-d'));
        return floor($diff / (60 * 60 * 24));
    }

    /**
     * Desativa outros certificados da empresa
     */
    public function desativarOutros($empId, $excetoCerId = null)
    {
        $this->db->where('emp_id', $empId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        if ($excetoCerId) {
            $this->db->where('cer_id !=', $excetoCerId);
        }
        $this->db->update('certificados_digitais', ['cer_ativo' => 0]);
        return true;
    }

    /**
     * Conta certificados da empresa
     */
    public function count($empId)
    {
        $this->db->where('emp_id', $empId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->count_all_results('certificados_digitais');
    }
}
