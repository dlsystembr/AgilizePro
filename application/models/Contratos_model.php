<?php

class Contratos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields = '*', $search = '', $perPage = 0, $start = 0, $one = false)
    {
        $this->db->select('c.*, p.PES_NOME, p.PES_CPFCNPJ, p.PES_RAZAO_SOCIAL');
        $this->db->from('contratos c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('c.CTR_ID', 'desc');
        
        if ($perPage) {
            $this->db->limit($perPage, $start);
        }

        if ($search) {
            // Busca por número do contrato, nome do cliente ou CPF/CNPJ
            $this->db->group_start();
            $this->db->like('c.CTR_NUMERO', $search);
            $this->db->or_like('p.PES_NOME', $search);
            $this->db->or_like('p.PES_RAZAO_SOCIAL', $search);
            $this->db->or_like('p.PES_CPFCNPJ', $search);
            $this->db->group_end();
        }

        $query = $this->db->get();
        return $one ? $query->row() : $query->result();
    }

    public function getById($id)
    {
        $this->db->select('c.*, p.PES_NOME, p.PES_CPFCNPJ, p.PES_RAZAO_SOCIAL, p.PES_FISICO_JURIDICO');
        $this->db->from('contratos c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->where('c.CTR_ID', $id);
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        $this->db->limit(1);
        return $this->db->get()->row();
    }

    public function add($table, $data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id($table);
        }
        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update($table, $data);
        return $this->db->affected_rows() >= 0;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete($table);
        return $this->db->affected_rows() == 1;
    }

    public function count($table, $search = '')
    {
        $this->db->from('contratos c');
        $this->db->join('pessoas p', 'p.PES_ID = c.PES_ID', 'left');
        $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
        
        if ($search) {
            $this->db->group_start();
            $this->db->like('c.CTR_NUMERO', $search);
            $this->db->or_like('p.PES_NOME', $search);
            $this->db->or_like('p.PES_RAZAO_SOCIAL', $search);
            $this->db->or_like('p.PES_CPFCNPJ', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    public function getTiposAssinante()
    {
        return [
            1 => 'Comercial',
            2 => 'Industrial',
            3 => 'Residencia/PF',
            4 => 'Produtor Rural',
            5 => 'Orgão Público Estadual',
            6 => 'Prestador de Telecom',
            7 => 'Missões Diplomáticas',
            8 => 'Igrejas e Templos',
            99 => 'Outros'
        ];
    }

    /**
     * Busca todos os itens (serviços) de um contrato específico
     * 
     * @param int $contratoId ID do contrato
     * @return array|object Lista de itens do contrato com informações do produto/serviço
     */
    public function getItensByContratoId($contratoId)
    {
        $this->db->select('
            ci.CTI_ID,
            ci.CTR_ID,
            ci.PRO_ID,
            ci.CTI_PRECO,
            ci.CTI_QUANTIDADE,
            ci.CTI_ATIVO,
            ci.CTI_OBSERVACAO,
            ci.CTI_DATA_CADASTRO,
            ci.CTI_DATA_ATUALIZACAO,
            p.PRO_DESCRICAO,
            p.PRO_UNID_MEDIDA,
            p.PRO_TIPO,
            p.PRO_PRECO_VENDA as PRO_PRECO_VENDA_ORIGINAL
        ');
        $this->db->from('contratos_itens ci');
        $this->db->join('produtos p', 'p.PRO_ID = ci.PRO_ID', 'left');
        $this->db->where('ci.CTR_ID', $contratoId);
        $this->db->where('ci.ten_id', $this->session->userdata('ten_id'));
        $this->db->order_by('ci.CTI_ID', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Adiciona um item ao contrato
     * 
     * @param array $data Dados do item (CTR_ID, PRO_ID, CTI_PRECO, CTI_QUANTIDADE, CTI_OBSERVACAO, CTI_ATIVO)
     * @return int|false ID do item inserido ou false em caso de erro
     */
    public function addItem($data)
    {
        if (!isset($data['ten_id'])) {
            $data['ten_id'] = $this->session->userdata('ten_id');
        }
        
        // Garantir que CTI_ATIVO tenha valor padrão
        if (!isset($data['CTI_ATIVO'])) {
            $data['CTI_ATIVO'] = 1;
        }
        
        $this->db->insert('contratos_itens', $data);
        if ($this->db->affected_rows() == 1) {
            return $this->db->insert_id();
        }
        return false;
    }

    /**
     * Atualiza um item do contrato
     * 
     * @param int $itemId ID do item
     * @param array $data Dados a serem atualizados
     * @return bool
     */
    public function updateItem($itemId, $data)
    {
        $this->db->where('CTI_ID', $itemId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->update('contratos_itens', $data);
        return $this->db->affected_rows() >= 0;
    }

    /**
     * Remove um item do contrato
     * 
     * @param int $itemId ID do item
     * @return bool
     */
    public function deleteItem($itemId)
    {
        $this->db->where('CTI_ID', $itemId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete('contratos_itens');
        return $this->db->affected_rows() == 1;
    }

    /**
     * Remove todos os itens de um contrato
     * 
     * @param int $contratoId ID do contrato
     * @return bool
     */
    public function deleteItensByContratoId($contratoId)
    {
        $this->db->where('CTR_ID', $contratoId);
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        $this->db->delete('contratos_itens');
        return $this->db->affected_rows() >= 0;
    }
}
