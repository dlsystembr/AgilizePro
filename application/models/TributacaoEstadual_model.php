<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class TributacaoEstadual_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Busca tributação estadual por tenant (ten_id), NCM (ncm_id) e UF.
     */
    public function getByTenantAndNcmAndUf($tenId, $ncmId, $uf)
    {
        try {
            // Verificar se a tabela tem o campo ten_id
            $fields = $this->db->field_data('tributacao_estadual');
            $hasTenId = false;
            foreach ($fields as $field) {
                if ($field->name === 'ten_id') {
                    $hasTenId = true;
                    break;
                }
            }

            $this->db->select(
                'tbe_id as id,
                 tbe_uf as uf,
                 tbe_tipo_tributacao as tipo_tributacao,
                 tbe_aliquota_icms as aliquota_icms,
                 tbe_mva as mva,
                 tbe_aliquota_icms_st as aliquota_icms_st,
                 tbe_percentual_reducao_icms as percentual_reducao_icms,
                 tbe_percentual_reducao_st as percentual_reducao_st,
                 tbe_aliquota_fcp as aliquota_fcp'
            );
            $this->db->from('tributacao_estadual');
            
            // Aplicar filtro ten_id apenas se o campo existir
            if ($hasTenId) {
                $this->db->where('ten_id', $tenId);
            }
            
            $this->db->where('ncm_id', $ncmId);
            $this->db->where('tbe_uf', strtoupper($uf));
            $this->db->limit(1);

            $query = $this->db->get();
            if (!$query) {
                log_message('error', 'Erro em getByTenantAndNcmAndUf: ' . $this->db->error()['message']);
                return null;
            }
            
            $result = $query->row();
            log_message('debug', 'getByTenantAndNcmAndUf - Parâmetros: tenId=' . $tenId . ', ncmId=' . $ncmId . ', uf=' . $uf . ' | Resultado: ' . ($result ? 'encontrado' : 'não encontrado'));
            
            return $result;
        } catch (Exception $e) {
            log_message('error', 'Erro em getByTenantAndNcmAndUf: ' . $e->getMessage());
            return null;
        }
    }
}
