<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FiscalClassificationService
 * 
 * Service responsável por determinar a Classificação Fiscal correta
 * de um item durante a emissão de uma NFCom ou NF-e.
 * 
 * Implementa um "funil de decisão" que aplica filtros sequenciais
 * para encontrar a classificação fiscal mais adequada.
 * 
 * @author Sistema MAPOS
 * @version 1.0
 */
class FiscalClassificationService
{
    private $CI;
    private $db;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
    }

    /**
     * Método principal para encontrar a classificação fiscal
     * 
     * @param int $operacaoComercialId ID da Operação Comercial (OPC_ID)
     * @param int $clienteId ID do Cliente (PES_ID)
     * @param int $produtoId ID do Produto (PRO_ID)
     * @param int $empresaId ID da Empresa (EMP_ID)
     * @return array|null Retorna array com CFOP, CST e cClassTrib ou null se não encontrar
     * @throws Exception Se não houver tributação configurada para a destinação
     */
    public function findClassification($operacaoComercialId, $clienteId, $produtoId, $empresaId)
    {
        log_message('debug', '=== INÍCIO findClassification ===');
        log_message('debug', "Parâmetros: OPC_ID={$operacaoComercialId}, Cliente={$clienteId}, Produto={$produtoId}, Empresa={$empresaId}");

        // PASSO 1: Carga Inicial - Buscar todas as classificações da operação comercial
        $classificacoes = $this->carregarClassificacoesIniciais($operacaoComercialId);
        
        if (empty($classificacoes) || !is_array($classificacoes)) {
            log_message('error', 'Nenhuma classificação fiscal encontrada para a operação comercial: ' . $operacaoComercialId);
            return null;
        }

        // Garantir que é um array indexado numericamente
        $classificacoes = array_values($classificacoes);
        log_message('debug', 'Classificações iniciais encontradas: ' . count($classificacoes));

        // PASSO 2: Filtro de Destinação (Crítico/Obrigatório)
        $destinacao = $this->determinarDestinacao($empresaId, $clienteId);
        log_message('debug', "Destinação determinada: {$destinacao}");
        
        $classificacoes = $this->filtrarPorDestinacao($classificacoes, $destinacao);
        
        if (empty($classificacoes)) {
            throw new Exception("Não há tributação configurada para esta destinação ({$destinacao}).");
        }

        // Se restou apenas 1, retornar
        if (count($classificacoes) === 1) {
            $first = reset($classificacoes);
            return $first ? $this->formatarResultado($first) : null;
        }

        log_message('debug', 'Após filtro de destinação: ' . count($classificacoes) . ' registros');

        // PASSO 3: Filtro de Tipo de Tributação (ICMS)
        $tipoTributacao = $this->obterTipoTributacao($produtoId, $clienteId);
        log_message('debug', "Tipo de tributação obtido: {$tipoTributacao}");
        
        if ($tipoTributacao) {
            $classificacoesAntes = $classificacoes;
            $classificacoes = $this->filtrarPorTipoTributacao($classificacoes, $tipoTributacao);
            
            if (empty($classificacoes)) {
                // Se não encontrou, manter a lista anterior
                $classificacoes = $classificacoesAntes;
                log_message('debug', 'Filtro de tipo tributação não encontrou resultados, mantendo lista anterior');
            } else {
                log_message('debug', 'Após filtro de tipo tributação: ' . count($classificacoes) . ' registros');
            }
        }

        // Se restou apenas 1, retornar
        if (count($classificacoes) === 1) {
            $first = reset($classificacoes);
            return $first ? $this->formatarResultado($first) : null;
        }

        // PASSO 4: Filtro de Tipo de Cliente (Opcional/Soft)
        $tipoClienteId = $this->obterTipoCliente($clienteId);
        log_message('debug', "Tipo de cliente obtido: {$tipoClienteId}");
        
        if ($tipoClienteId) {
            $classificacoesAntes = $classificacoes;
            $classificacoes = $this->filtrarPorTipoCliente($classificacoes, $tipoClienteId);
            
            if (empty($classificacoes)) {
                // Se não encontrou, ignorar filtro e manter lista anterior
                $classificacoes = $classificacoesAntes;
                log_message('debug', 'Filtro de tipo cliente não encontrou resultados, ignorando filtro');
            } else {
                log_message('debug', 'Após filtro de tipo cliente: ' . count($classificacoes) . ' registros');
            }
        }

        // Se restou apenas 1, retornar
        if (count($classificacoes) === 1) {
            $first = reset($classificacoes);
            return $first ? $this->formatarResultado($first) : null;
        }

        // PASSO 5: Filtro de Natureza do Contribuinte
        $naturezaContribuinte = $this->obterNaturezaContribuinte($clienteId);
        log_message('debug', "Natureza do contribuinte obtida: {$naturezaContribuinte}");
        
        if ($naturezaContribuinte) {
            $classificacoesAntes = $classificacoes;
            $classificacoes = $this->filtrarPorNaturezaContribuinte($classificacoes, $naturezaContribuinte);
            
            if (empty($classificacoes)) {
                // Fallback: usar TOP 1 da lista anterior
                $top1 = reset($classificacoesAntes);
                if ($top1) {
                    $classificacoes = [$top1];
                    log_message('debug', 'Filtro de natureza não encontrou resultados, usando TOP 1 anterior');
                } else {
                    log_message('warning', 'Lista anterior está vazia, não é possível usar fallback');
                }
            } else {
                log_message('debug', 'Após filtro de natureza: ' . count($classificacoes) . ' registros');
            }
        }

        // Se restou apenas 1, retornar
        if (count($classificacoes) === 1) {
            $first = reset($classificacoes);
            return $first ? $this->formatarResultado($first) : null;
        }

        // PASSO 6: Filtro de Objetivo Comercial
        $objetivoComercial = $this->obterObjetivoComercial($clienteId);
        log_message('debug', "Objetivo comercial obtido: {$objetivoComercial}");
        
        if ($objetivoComercial) {
            $classificacoesAntes = $classificacoes;
            $classificacoes = $this->filtrarPorObjetivoComercial($classificacoes, $objetivoComercial);
            
            if (empty($classificacoes)) {
                // Fallback: usar TOP 1 da lista anterior
                $top1 = reset($classificacoesAntes);
                if ($top1) {
                    $classificacoes = [$top1];
                    log_message('debug', 'Filtro de objetivo comercial não encontrou resultados, usando TOP 1 anterior');
                } else {
                    log_message('warning', 'Lista anterior está vazia, não é possível usar fallback');
                }
            } else {
                log_message('debug', 'Após filtro de objetivo comercial: ' . count($classificacoes) . ' registros');
            }
        }

        // Se ainda restar mais de 1, pegar o primeiro (TOP 1)
        if (empty($classificacoes)) {
            log_message('error', 'Nenhuma classificação fiscal encontrada após todos os filtros');
            return null;
        }
        
        $resultado = reset($classificacoes);
        log_message('debug', '=== FIM findClassification - Retornando resultado ===');
        
        if (!$resultado) {
            log_message('error', 'Erro ao obter resultado final - array vazio');
            return null;
        }
        
        return $this->formatarResultado($resultado);
    }

    /**
     * PASSO 1: Carrega todas as classificações fiscais da operação comercial
     * 
     * @param int $operacaoComercialId
     * @return array
     */
    private function carregarClassificacoesIniciais($operacaoComercialId)
    {
        $this->db->select('
            CLF_ID as id,
            OPC_ID as operacao_comercial_id,
            CLF_CST as cst,
            CLF_CSOSN as csosn,
            CLF_CFOP as cfop,
            CLF_DESTINACAO as destinacao,
            CLF_TIPO_TRIBUTACAO as tipo_icms,
            CLF_NATUREZA_CONTRIBUINTE as natureza_contribuinte,
            CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
            CLF_CCLASSTRIB as cClassTrib,
            CLF_MENSAGEM as mensagem_fiscal,
            TPC_ID as tipo_cliente_id
        ');
        $this->db->from('classificacao_fiscal');
        $this->db->where('OPC_ID', $operacaoComercialId);
        // Filtrar apenas classificações ativas (CLF_SITUACAO = 1)
        // Se o campo não existir, a query falhará, mas isso é esperado
        $this->db->where('CLF_SITUACAO', 1);
        // Filtrar por ten_id
        $this->db->where('ten_id', $this->CI->session->userdata('ten_id'));
        
        $query = $this->db->get();
        
        if (!$query) {
            log_message('error', 'Erro na query de classificação fiscal: ' . $this->db->error()['message']);
            return [];
        }
        
        return $query->result();
    }

    /**
     * PASSO 2: Determina se a operação é Estadual ou Interestadual
     * 
     * @param int $empresaId
     * @param int $clienteId
     * @return string 'Estadual' ou 'Interestadual'
     */
    private function determinarDestinacao($empresaId, $clienteId)
    {
        try {
            // Buscar UF da empresa
            $this->db->select('EMP_UF');
            $this->db->from('empresas');
            $this->db->where('EMP_ID', $empresaId);
            $this->db->where('ten_id', $this->CI->session->userdata('ten_id'));
            $empresaQuery = $this->db->get();
            
            if ($empresaQuery->num_rows() == 0) {
                log_message('warning', 'Empresa não encontrada: ' . $empresaId);
                return 'Interestadual'; // Default
            }
            
            $empresaUf = $empresaQuery->row()->EMP_UF;
            
            // Buscar UF do cliente
            $this->db->select('E3.EST_UF');
            $this->db->from('pessoas P');
            $this->db->join('enderecos E2', 'P.PES_ID = E2.PES_ID', 'left');
            $this->db->join('municipios M', 'E2.MUN_ID = M.MUN_ID', 'left');
            $this->db->join('estados E3', 'M.EST_ID = E3.EST_ID', 'left');
            $this->db->where('P.PES_ID', $clienteId);
            $this->db->where('P.ten_id', $this->CI->session->userdata('ten_id'));
            $this->db->limit(1);
            
            $clienteQuery = $this->db->get();
            
            if ($clienteQuery->num_rows() == 0) {
                log_message('warning', 'Cliente ou endereço não encontrado: ' . $clienteId);
                return 'Interestadual'; // Default
            }
            
            $clienteUf = $clienteQuery->row()->EST_UF;
            
            // Comparar UFs
            if ($empresaUf && $clienteUf && $empresaUf == $clienteUf) {
                return 'Estadual';
            } else {
                return 'Interestadual';
            }
        } catch (Exception $e) {
            log_message('error', 'Erro ao determinar destinação: ' . $e->getMessage());
            return 'Interestadual'; // Default em caso de erro
        }
    }

    /**
     * Filtra classificações por destinação
     * 
     * @param array $classificacoes
     * @param string $destinacao
     * @return array
     */
    private function filtrarPorDestinacao($classificacoes, $destinacao)
    {
        $filtradas = array_filter($classificacoes, function($cf) use ($destinacao) {
            // Normalizar valores para comparação
            $cfDestinacao = $cf->destinacao ?? '';
            $cfDestinacao = ucfirst(strtolower($cfDestinacao));
            $destinacaoNormalizada = ucfirst(strtolower($destinacao));
            
            return $cfDestinacao === $destinacaoNormalizada;
        });
        
        // Reindexar array para garantir índices numéricos
        return array_values($filtradas);
    }

    /**
     * PASSO 3: Obtém o tipo de tributação (ICMS Normal, ST, Serviço) da tabela tributacao_estadual
     * 
     * @param int $produtoId
     * @param int $clienteId
     * @return string|null
     */
    private function obterTipoTributacao($produtoId, $clienteId)
    {
        $this->db->select('te.tbe_tipo_tributacao');
        $this->db->from('produtos p1');
        $this->db->join('pessoas P', 'P.PES_ID = ' . (int)$clienteId, 'left');
        $this->db->join('enderecos E2', 'P.PES_ID = E2.PES_ID', 'left');
        $this->db->join('municipios M', 'E2.MUN_ID = M.MUN_ID', 'left');
        $this->db->join('estados E3', 'M.EST_ID = E3.EST_ID', 'left');
        $this->db->join('tributacao_estadual te', 'p1.NCM_ID = te.ncm_id AND E3.EST_UF = te.tbe_uf', 'left');
        $this->db->where('p1.PRO_ID', $produtoId);
        $this->db->where('p1.ten_id', $this->CI->session->userdata('ten_id'));
        $this->db->where('te.ten_id', $this->CI->session->userdata('ten_id'));
        $this->db->limit(1);
        
        $query = $this->db->get();
        $result = $query->row();
        
        if ($result && $result->tbe_tipo_tributacao) {
            // Normalizar para comparar com CLF_TIPO_TRIBUTACAO
            $tipo = $result->tbe_tipo_tributacao;
            if ($tipo === 'ICMS Normal') {
                return 'normal';
            } elseif ($tipo === 'ST') {
                return 'st';
            } elseif ($tipo === 'Serviço') {
                return 'st'; // Serviço também usa 'st' no banco
            }
        }
        
        return null;
    }

    /**
     * Filtra classificações por tipo de tributação
     * 
     * @param array $classificacoes
     * @param string $tipoTributacao
     * @return array
     */
    private function filtrarPorTipoTributacao($classificacoes, $tipoTributacao)
    {
        $filtradas = array_filter($classificacoes, function($cf) use ($tipoTributacao) {
            $cfTipo = strtolower($cf->tipo_icms ?? '');
            $tipoNormalizado = strtolower($tipoTributacao);
            
            return $cfTipo === $tipoNormalizado;
        });
        
        // Reindexar array para garantir índices numéricos
        return array_values($filtradas);
    }

    /**
     * PASSO 4: Obtém o TPC_ID do cliente
     * 
     * @param int $clienteId
     * @return int|null
     */
    private function obterTipoCliente($clienteId)
    {
        $this->db->select('c.TPC_ID');
        $this->db->from('pessoas P');
        $this->db->join('clientes c', 'P.PES_ID = c.PES_ID', 'left');
        $this->db->where('P.PES_ID', $clienteId);
        $this->db->limit(1);
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result && $result->TPC_ID ? (int)$result->TPC_ID : null;
    }

    /**
     * Filtra classificações por tipo de cliente
     * 
     * @param array $classificacoes
     * @param int $tipoClienteId
     * @return array
     */
    private function filtrarPorTipoCliente($classificacoes, $tipoClienteId)
    {
        $filtradas = array_filter($classificacoes, function($cf) use ($tipoClienteId) {
            return ($cf->tipo_cliente_id ?? null) == $tipoClienteId;
        });
        
        // Reindexar array para garantir índices numéricos
        return array_values($filtradas);
    }

    /**
     * PASSO 5: Obtém a natureza do contribuinte do cliente
     * 
     * @param int $clienteId
     * @return string|null
     */
    private function obterNaturezaContribuinte($clienteId)
    {
        $this->db->select('d.DOC_NATUREZA_CONTRIBUINTE');
        $this->db->from('pessoas P');
        $this->db->join('enderecos E2', 'P.PES_ID = E2.PES_ID', 'left');
        $this->db->join('documentos d', 'E2.END_ID = d.END_ID AND P.PES_ID = d.PES_ID', 'left');
        $this->db->where('P.PES_ID', $clienteId);
        $this->db->limit(1);
        
        $query = $this->db->get();
        $result = $query->row();
        
        if ($result && $result->DOC_NATUREZA_CONTRIBUINTE) {
            // Normalizar valores
            $natureza = $result->DOC_NATUREZA_CONTRIBUINTE;
            if (in_array(strtolower($natureza), ['contribuinte', 'inscrito'])) {
                return 'Contribuinte';
            } elseif (in_array(strtolower($natureza), ['não contribuinte', 'nao contribuinte', 'nao_inscrito'])) {
                return 'Não Contribuinte';
            }
            return $natureza;
        }
        
        return null;
    }

    /**
     * Filtra classificações por natureza do contribuinte
     * 
     * @param array $classificacoes
     * @param string $natureza
     * @return array
     */
    private function filtrarPorNaturezaContribuinte($classificacoes, $natureza)
    {
        $filtradas = array_filter($classificacoes, function($cf) use ($natureza) {
            $cfNatureza = $cf->natureza_contribuinte ?? '';
            
            // Normalizar para comparação
            $cfNormalizada = strtolower($cfNatureza);
            $naturezaNormalizada = strtolower($natureza);
            
            // Mapear variações
            $map = [
                'contribuinte' => ['contribuinte', 'inscrito'],
                'não contribuinte' => ['não contribuinte', 'nao contribuinte', 'nao_inscrito']
            ];
            
            foreach ($map as $key => $variacoes) {
                if (in_array($cfNormalizada, $variacoes) && in_array($naturezaNormalizada, $variacoes)) {
                    return true;
                }
            }
            
            return $cfNormalizada === $naturezaNormalizada;
        });
        
        // Reindexar array para garantir índices numéricos
        return array_values($filtradas);
    }

    /**
     * PASSO 6: Obtém o objetivo comercial do cliente
     * 
     * @param int $clienteId
     * @return string|null
     */
    private function obterObjetivoComercial($clienteId)
    {
        $this->db->select('c.CLN_OBJETIVO_COMERCIAL');
        $this->db->from('pessoas P');
        $this->db->join('clientes c', 'P.PES_ID = c.PES_ID', 'left');
        $this->db->where('P.PES_ID', $clienteId);
        $this->db->limit(1);
        
        $query = $this->db->get();
        $result = $query->row();
        
        if ($result && $result->CLN_OBJETIVO_COMERCIAL) {
            // Normalizar valores
            $objetivo = $result->CLN_OBJETIVO_COMERCIAL;
            $objetivoLower = strtolower($objetivo);
            
            if (in_array($objetivoLower, ['consumo', 'consumo'])) {
                return 'Consumo';
            } elseif (in_array($objetivoLower, ['revenda', 'revenda'])) {
                return 'Revenda';
            } elseif (in_array($objetivoLower, ['industrialização', 'industrializacao'])) {
                return 'Industrialização';
            } elseif (in_array($objetivoLower, ['orgão público', 'orgao publico'])) {
                return 'Orgão Público';
            }
            
            return $objetivo;
        }
        
        return null;
    }

    /**
     * Filtra classificações por objetivo comercial
     * 
     * @param array $classificacoes
     * @param string $objetivo
     * @return array
     */
    private function filtrarPorObjetivoComercial($classificacoes, $objetivo)
    {
        $filtradas = array_filter($classificacoes, function($cf) use ($objetivo) {
            $cfObjetivo = $cf->objetivo_comercial ?? '';
            
            // Normalizar para comparação
            $cfNormalizada = strtolower($cfObjetivo);
            $objetivoNormalizada = strtolower($objetivo);
            
            return $cfNormalizada === $objetivoNormalizada;
        });
        
        // Reindexar array para garantir índices numéricos
        return array_values($filtradas);
    }

    /**
     * Formata o resultado final
     * 
     * @param object $classificacao
     * @return array
     */
    /**
     * Formata o resultado final
     * 
     * @param object|array|null $classificacao
     * @return array|null
     */
    private function formatarResultado($classificacao)
    {
        if (!$classificacao) {
            log_message('error', 'Tentativa de formatar resultado nulo');
            return null;
        }
        
        // Converter para objeto se for array
        if (is_array($classificacao)) {
            $classificacao = (object)$classificacao;
        }
        
        return [
            'CLF_CFOP' => $classificacao->cfop ?? null,
            'CLF_CST' => $classificacao->cst ?? null,
            'CLF_CSOSN' => $classificacao->csosn ?? null,
            'CLF_CCLASSTRIB' => $classificacao->cClassTrib ?? null,
            'CLF_TIPO_TRIBUTACAO' => $classificacao->tipo_icms ?? null,
            'CLF_ID' => $classificacao->id ?? null,
            'CLF_MENSAGEM' => $classificacao->mensagem_fiscal ?? null
        ];
    }
}

