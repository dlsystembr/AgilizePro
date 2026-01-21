<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class ConfiguracoesFiscais_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Busca todas as configurações da empresa
     */
    public function get($empId)
    {
        $this->db->select('cfg.*, cer.CER_CNPJ, cer.CER_VALIDADE_FIM, cer.CER_ATIVO as CER_ATIVO_STATUS');
        $this->db->from('configuracoes_fiscais cfg');
        $this->db->join('certificados_digitais cer', 'cer.CER_ID = cfg.CER_ID', 'left');
        $this->db->where('cfg.EMP_ID', $empId);
        $this->db->where('cfg.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('cfg.CFG_TIPO_DOCUMENTO', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Busca configuração por tipo de documento
     */
    public function getByTipo($empId, $tipoDocumento)
    {
        $this->db->select('cfg.*, cer.CER_ARQUIVO, cer.CER_SENHA, cer.CER_TIPO');
        $this->db->from('configuracoes_fiscais cfg');
        $this->db->join('certificados_digitais cer', 'cer.CER_ID = cfg.CER_ID', 'left');
        $this->db->where('cfg.EMP_ID', $empId);
        $this->db->where('cfg.CFG_TIPO_DOCUMENTO', $tipoDocumento);
        $this->db->where('cfg.ten_id', $this->session->userdata('ten_id'));

        return $this->db->get()->row();
    }

    /**
     * Busca configuração por ID
     */
    public function getById($id)
    {
        $this->db->select('cfg.*, cer.CER_CNPJ, cer.CER_VALIDADE_FIM');
        $this->db->from('configuracoes_fiscais cfg');
        $this->db->join('certificados_digitais cer', 'cer.CER_ID = cfg.CER_ID', 'left');
        $this->db->where('cfg.CFG_ID', $id);
        $this->db->where('cfg.ten_id', $this->session->userdata('ten_id'));

        return $this->db->get()->row();
    }

    /**
     * Busca dados completos para emissão (configuração + empresa + certificado)
     */
    public function getDadosEmissao($empId, $tipoDocumento)
    {
        $this->db->select('
            emp.*,
            cfg.CFG_AMBIENTE,
            cfg.CFG_SERIE,
            cfg.CFG_NUMERO_ATUAL,
            cfg.CFG_CSC_ID,
            cfg.CFG_CSC_TOKEN,
            cfg.CFG_ALIQUOTA_ISS,
            cfg.CFG_REGIME_ESPECIAL,
            cfg.CFG_FORMATO_IMPRESSAO,
            cer.CER_ARQUIVO,
            cer.CER_SENHA,
            cer.CER_TIPO
        ');
        $this->db->from('configuracoes_fiscais cfg');
        $this->db->join('empresas emp', 'emp.EMP_ID = cfg.EMP_ID');
        $this->db->join('certificados_digitais cer', 'cer.CER_ID = cfg.CER_ID', 'left');
        $this->db->where('cfg.EMP_ID', $empId);
        $this->db->where('cfg.CFG_TIPO_DOCUMENTO', $tipoDocumento);
        $this->db->where('cfg.CFG_ATIVO', 1);
        $this->db->where('cfg.ten_id', $this->session->userdata('ten_id'));

        return $this->db->get()->row();
    }

    /**
     * Adiciona nova configuração
     */
    public function add($data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert('configuracoes_fiscais', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Atualiza configuração
     */
    public function edit($id, $data)
    {
        $this->db->where('CFG_ID', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update('configuracoes_fiscais', $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Atualiza configuração por empresa e tipo
     */
    public function editByTipo($empId, $tipoDocumento, $data)
    {
        $this->db->where('EMP_ID', $empId);
        $this->db->where('CFG_TIPO_DOCUMENTO', $tipoDocumento);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update('configuracoes_fiscais', $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Exclui configuração
     */
    public function delete($id)
    {
        $this->db->where('CFG_ID', $id);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete('configuracoes_fiscais');
        return $this->db->affected_rows() == 1;
    }

    /**
     * Incrementa número da nota
     */
    public function incrementarNumero($empId, $tipoDocumento)
    {
        $this->db->set('CFG_NUMERO_ATUAL', 'CFG_NUMERO_ATUAL + 1', FALSE);
        $this->db->where('EMP_ID', $empId);
        $this->db->where('CFG_TIPO_DOCUMENTO', $tipoDocumento);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update('configuracoes_fiscais');

        return $this->db->affected_rows() >= 0;
    }

    /**
     * Verifica se configuração existe
     */
    public function existe($empId, $tipoDocumento)
    {
        $this->db->where('EMP_ID', $empId);
        $this->db->where('CFG_TIPO_DOCUMENTO', $tipoDocumento);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $count = $this->db->count_all_results('configuracoes_fiscais');

        return $count > 0;
    }

    /**
     * Cria ou atualiza configuração
     */
    public function salvar($empId, $tipoDocumento, $data)
    {
        if ($this->existe($empId, $tipoDocumento)) {
            return $this->editByTipo($empId, $tipoDocumento, $data);
        } else {
            $data['EMP_ID'] = $empId;
            $data['CFG_TIPO_DOCUMENTO'] = $tipoDocumento;
            if (!isset($data['ten_id'])) {
                $data['ten_id'] = $this->session->userdata('ten_id');
            }
            return $this->add($data);
        }
    }

    /**
     * Lista tipos de documentos disponíveis
     */
    public function getTiposDocumento()
    {
        return [
            'NFE' => 'NF-e - Nota Fiscal Eletrônica',
            'NFCE' => 'NFC-e - Nota Fiscal ao Consumidor Eletrônica',
            'NFSE' => 'NFS-e - Nota Fiscal de Serviço Eletrônica',
            'NFCOM' => 'NFCom - Nota Fiscal de Comunicação',
            'CTE' => 'CT-e - Conhecimento de Transporte Eletrônico',
            'MDFE' => 'MDF-e - Manifesto de Documentos Fiscais Eletrônico'
        ];
    }
}
