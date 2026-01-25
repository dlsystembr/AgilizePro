<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * API para buscar tributação federal (CST PIS, COFINS, IPI).
 * Uso: GET index.php/tributacaofederalapi/listar?ten_id=1&ncm_id=123&tipo_operacao=saida
 */
class TributacaoFederalApi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('TributacaoFederal_model');
    }

    public function listar()
    {
        header('Content-Type: application/json');

        $tenId = (int)$this->input->get('ten_id');
        $ncmId = (int)$this->input->get('ncm_id');
        $tipoOperacao = $this->input->get('tipo_operacao'); // entrada ou saida

        // Obrigatórios: ten_id, ncm_id, tipo_operacao
        if (empty($tenId) || empty($ncmId) || empty($tipoOperacao)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Parâmetros obrigatórios: ten_id, ncm_id e tipo_operacao (entrada|saida)'
            ]);
            return;
        }

        // Validar tipo_operacao
        if (!in_array(strtolower($tipoOperacao), ['entrada', 'saida'])) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'tipo_operacao deve ser "entrada" ou "saida"'
            ]);
            return;
        }

        $tipoOperacao = strtolower($tipoOperacao);

        // Log temporário para debug
        log_message('debug', 'TributacaoFederalApi/listar - Parâmetros recebidos: tenId=' . $tenId . ', ncmId=' . $ncmId . ', tipoOperacao=' . $tipoOperacao);
        
        // Buscar tributação federal
        $tributacao = $this->TributacaoFederal_model->getByTenantAndNcm($tenId, $ncmId, $tipoOperacao);

        if (empty($tributacao)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Tributação federal não configurada para este NCM.'
            ]);
            return;
        }

        // Retornar apenas os campos solicitados
        echo json_encode([
            'sucesso' => true,
            'dados' => [
                'id' => $tributacao->id ?? null,
                'cst_ipi' => $tributacao->cst_ipi ?? null,
                'cst_pis' => $tributacao->cst_pis ?? null,
                'cst_cofins' => $tributacao->cst_cofins ?? null,
                'aliquota_ipi' => $tributacao->aliquota_ipi ?? null,
                'aliquota_pis' => $tributacao->aliquota_pis ?? null,
                'aliquota_cofins' => $tributacao->aliquota_cofins ?? null
            ]
        ]);
    }
}
