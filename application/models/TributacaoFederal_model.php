<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TributacaoFederal_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Busca tributação federal por tenant (ten_id), NCM (ncm_id) e tipo de operação (entrada/saida).
     */
    public function getByTenantAndNcm($tenId, $ncmId, $tipoOperacao = 'saida')
    {
        try {
            // Verificar se a tabela tem o campo ten_id
            $fields = $this->db->field_data('tributacao_federal');
            $hasTenId = false;
            foreach ($fields as $field) {
                if ($field->name === 'ten_id') {
                    $hasTenId = true;
                    break;
                }
            }

            $this->db->select(
                'tbf_id as id,
                 tbf_cst_ipi_' . $tipoOperacao . ' as cst_ipi,
                 tbf_aliquota_ipi_' . $tipoOperacao . ' as aliquota_ipi,
                 tbf_cst_pis_cofins_' . $tipoOperacao . ' as cst_pis,
                 tbf_aliquota_pis_' . $tipoOperacao . ' as aliquota_pis,
                 tbf_cst_pis_cofins_' . $tipoOperacao . ' as cst_cofins,
                 tbf_aliquota_cofins_' . $tipoOperacao . ' as aliquota_cofins'
            );
            $this->db->from('tributacao_federal');
            
            // Aplicar filtro ten_id apenas se o campo existir
            if ($hasTenId) {
                $this->db->where('ten_id', $tenId);
            }
            
            $this->db->where('ncm_id', $ncmId);
            $this->db->limit(1);

            $query = $this->db->get();
            if (!$query) {
                log_message('error', 'Erro em getByTenantAndNcm: ' . $this->db->error()['message']);
                return null;
            }
            
            $result = $query->row();
            log_message('debug', 'getByTenantAndNcm - Parâmetros: tenId=' . $tenId . ', ncmId=' . $ncmId . ', tipoOperacao=' . $tipoOperacao . ' | Resultado: ' . ($result ? 'encontrado' : 'não encontrado'));
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro em getByTenantAndNcm: ' . $e->getMessage());
            return null;
        }
    }
}
