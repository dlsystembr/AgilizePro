<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * API para buscar tributação estadual (ICMS, MVA, ST, FCP).
 * Uso: GET index.php/tributacaoestadualapi/listar?ten_id=1&ncm_id=123&uf=SP
 */
class TributacaoEstadualApi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('TributacaoEstadual_model');
    }

    public function listar()
    {
        header('Content-Type: application/json');

        $tenId = (int)$this->input->get('ten_id');
        $ncmId = (int)$this->input->get('ncm_id');
        $uf = $this->input->get('uf'); // UF do estado (ex: SP, RJ, MG)

        // Obrigatórios: ten_id, ncm_id, uf
        if (empty($tenId) || empty($ncmId) || empty($uf)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Parâmetros obrigatórios: ten_id, ncm_id e uf (ex: SP, RJ, MG)'
            ]);
            return;
        }

        // Validar UF (deve ter 2 caracteres)
        $uf = strtoupper(trim($uf));
        if (strlen($uf) !== 2) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'UF deve ter 2 caracteres (ex: SP, RJ, MG)'
            ]);
            return;
        }

        // Log temporário para debug
        log_message('debug', 'TributacaoEstadualApi/listar - Parâmetros recebidos: tenId=' . $tenId . ', ncmId=' . $ncmId . ', uf=' . $uf);
        
        // Buscar tributação estadual
        $tributacao = $this->TributacaoEstadual_model->getByTenantAndNcmAndUf($tenId, $ncmId, $uf);

        if (empty($tributacao)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Tributação estadual não configurada para este NCM e UF.'
            ]);
            return;
        }

        // Retornar apenas os campos solicitados
        echo json_encode([
            'sucesso' => true,
            'dados' => [
                'id' => $tributacao->id ?? null,
                'uf' => $tributacao->uf ?? null,
                'tipo_tributacao' => $tributacao->tipo_tributacao ?? null,
                'aliquota_icms' => $tributacao->aliquota_icms ?? null,
                'mva' => $tributacao->mva ?? null,
                'aliquota_icms_st' => $tributacao->aliquota_icms_st ?? null,
                'percentual_reducao_icms' => $tributacao->percentual_reducao_icms ?? null,
                'percentual_reducao_st' => $tributacao->percentual_reducao_st ?? null,
                'aliquota_fcp' => $tributacao->aliquota_fcp ?? null
            ]
        ]);
    }
}
