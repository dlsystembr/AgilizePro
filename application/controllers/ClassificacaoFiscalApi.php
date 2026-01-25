<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * API simples para listar classificações fiscais por tenant e operação comercial.
 * Uso: GET index.php/classificacaofiscalapi/listar?ten_id=1&opc_id=10&natureza=Contribuinte%20ICMS&destinacao=estadual&objetivo=consumo
 */
class ClassificacaoFiscalApi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ClassificacaoFiscal_model');
    }

    public function listar()
    {
        header('Content-Type: application/json');

        $tenId = (int)$this->input->get('ten_id');
        $opcId = (int)$this->input->get('opc_id');
        $destinacao = $this->input->get('destinacao');
        $ativa = $this->input->get('ativa'); // obrigatório agora
        // Garantir que ativa seja inteiro (0 ou 1)
        $ativa = ($ativa === '0' || $ativa === 0) ? 0 : (($ativa === '1' || $ativa === 1) ? 1 : null);
        $natureza = $this->input->get('natureza'); // opcional
        $objetivo = $this->input->get('objetivo'); // opcional
        $finalidade = $this->input->get('finalidade'); // opcional
        $tipo_tributacao = $this->input->get('tipo_tributacao'); // opcional
        $tipo_cliente = $this->input->get('tipo_cliente'); // opcional

        // Obrigatórios e na ordem: ten_id -> opc_id -> destinacao -> ativa
        if (empty($tenId) || empty($opcId) || empty($destinacao) || ($ativa === null || $ativa === '')) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Parâmetros obrigatórios: ten_id, opc_id, destinacao (estadual|interestadual) e ativa (1|0)'
            ]);
            return;
        }

        // Estratégia incremental:
        // 1) Sempre aplica filtros obrigatórios: ten_id, opc_id, destinacao, ativa.
        // 2) Opcionais em ordem: finalidade -> tipo_tributacao -> natureza -> objetivo -> tipo_cliente.
        //    - Se um filtro não retornar nada, ignora-o e tenta o próximo.
        //    - Se retornar 1 registro, para imediatamente.
        //    - Se o último filtro testado zerar, usar top1 do último conjunto não-vazio.

        $appliedNatureza = null;
        $appliedObjetivo = null;
        $appliedFinalidade = null;
        $appliedTipoTributacao = null;
        $appliedTipoCliente = null;
        $topOneFallback = false;

        // Log temporário para debug
        log_message('debug', 'ClassificacaoFiscalApi/listar - Parâmetros recebidos: tenId=' . $tenId . ', opcId=' . $opcId . ', destinacao=' . $destinacao . ', ativa=' . $ativa);
        
        // Base obrigatória (sem opcionais)
        $dadosBase = $this->ClassificacaoFiscal_model->getByTenantAndOperacao(
            $tenId,
            $opcId,
            null,
            $destinacao,
            null,
            null,
            null,
            null,
            $ativa
        );

        if (empty($dadosBase)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Classificação fiscal não configurada.'
            ]);
            return;
        }

        if (count($dadosBase) === 1) {
            echo json_encode([
                'sucesso' => true,
                'total' => 1,
                'dados' => $dadosBase
            ]);
            return;
        }

        $lastNonEmpty = $dadosBase;

        // Filtro 1: finalidade
        if (!empty($finalidade)) {
            $res = $this->ClassificacaoFiscal_model->getByTenantAndOperacao(
                $tenId,
                $opcId,
                null,
                $destinacao,
                null,
                $finalidade,
                null,
                null,
                $ativa
            );
            if (!empty($res)) {
                $lastNonEmpty = $res;
                $appliedFinalidade = $finalidade;
                if (count($res) === 1) {
                    echo json_encode([
                        'sucesso' => true,
                        'total' => 1,
                        'dados' => $res
                    ]);
                    return;
                }
            }
        }

        // Filtro 2: tipo_tributacao
        if (!empty($tipo_tributacao)) {
            $res = $this->ClassificacaoFiscal_model->getByTenantAndOperacao(
                $tenId,
                $opcId,
                null,
                $destinacao,
                null,
                $appliedFinalidade,
                $tipo_tributacao,
                null,
                $ativa
            );
            if (!empty($res)) {
                $lastNonEmpty = $res;
                $appliedTipoTributacao = $tipo_tributacao;
                if (count($res) === 1) {
                    echo json_encode([
                        'sucesso' => true,
                        'total' => 1,
                        'dados' => $res
                    ]);
                    return;
                }
            }
        }

        // Filtro 3: natureza
        if (!empty($natureza)) {
            $res = $this->ClassificacaoFiscal_model->getByTenantAndOperacao(
                $tenId,
                $opcId,
                $natureza,
                $destinacao,
                null,
                $appliedFinalidade,
                $appliedTipoTributacao,
                null,
                $ativa
            );
            if (!empty($res)) {
                $lastNonEmpty = $res;
                $appliedNatureza = $natureza;
                if (count($res) === 1) {
                    echo json_encode([
                        'sucesso' => true,
                        'total' => 1,
                        'dados' => $res
                    ]);
                    return;
                }
            }
        }

        // Filtro 4: objetivo
        if (!empty($objetivo)) {
            $res = $this->ClassificacaoFiscal_model->getByTenantAndOperacao(
                $tenId,
                $opcId,
                $appliedNatureza,
                $destinacao,
                $objetivo,
                $appliedFinalidade,
                $appliedTipoTributacao,
                null,
                $ativa
            );
            if (!empty($res)) {
                $lastNonEmpty = $res;
                $appliedObjetivo = $objetivo;
                if (count($res) === 1) {
                    echo json_encode([
                        'sucesso' => true,
                        'total' => 1,
                        'dados' => $res
                    ]);
                    return;
                }
            }
        }

        // Filtro 5: tipo_cliente
        if (!empty($tipo_cliente)) {
            $res = $this->ClassificacaoFiscal_model->getByTenantAndOperacao(
                $tenId,
                $opcId,
                $appliedNatureza,
                $destinacao,
                $appliedObjetivo,
                $appliedFinalidade,
                $appliedTipoTributacao,
                $tipo_cliente,
                $ativa
            );
            if (!empty($res)) {
                $lastNonEmpty = $res;
                $appliedTipoCliente = $tipo_cliente;
                if (count($res) === 1) {
                    echo json_encode([
                        'sucesso' => true,
                        'total' => 1,
                        'dados' => $res
                    ]);
                    return;
                }
            } else {
                // último filtro zerou
                $topOneFallback = true;
            }
        }

        if (empty($lastNonEmpty)) {
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Classificação fiscal não configurada.'
            ]);
            return;
        }

        // Se não caiu em nenhum retorno anterior e houve fallback, devolve top1 do último conjunto não-vazio
        if ($topOneFallback) {
            echo json_encode([
                'sucesso' => true,
                'total' => 1,
                'dados' => [ $lastNonEmpty[0] ]
            ]);
            return;
        }

        // Se nenhum filtro opcional foi aplicado e há múltiplos registros, retorna apenas o primeiro
        $nenhumFiltroOpcional = empty($appliedFinalidade) && empty($appliedTipoTributacao) && empty($appliedNatureza) && empty($appliedObjetivo) && empty($appliedTipoCliente);
        if ($nenhumFiltroOpcional && count($lastNonEmpty) > 1) {
            echo json_encode([
                'sucesso' => true,
                'total' => 1,
                'dados' => [ $lastNonEmpty[0] ]
            ]);
            return;
        }

        // Caso ainda haja múltiplos registros após aplicar filtros opcionais, devolve todos filtrados
        echo json_encode([
            'sucesso' => true,
            'total' => count($lastNonEmpty),
            'dados' => $lastNonEmpty
        ]);
    }
}

