<?php

defined('BASEPATH') or exit('No direct script access allowed');

class NFComMake
{
    private $dom;
    private $nfcom;
    private $infNFCom;

    public function __construct()
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->dom->formatOutput = false;
        $this->dom->preserveWhiteSpace = false;
    }

    public function build($dados)
    {
        $this->dom = new DOMDocument('1.0', 'UTF-8');
        $this->nfcom = $this->dom->createElement('NFCom');
        $this->nfcom->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfcom');
        $this->dom->appendChild($this->nfcom);

        // Garantir que a chave tenha exatamente 44 dígitos numéricos
        $chave = preg_replace('/\D/', '', $dados['chave'] ?? '');
        if (strlen($chave) > 44) {
            // Se tiver mais de 44 dígitos, pegar apenas os últimos 44
            $chave = substr($chave, -44);
        } elseif (strlen($chave) < 44) {
            // Se tiver menos, completar com zeros à esquerda
            $chave = str_pad($chave, 44, '0', STR_PAD_LEFT);
        }
        // O Id deve ser exatamente "NFCom" + 44 dígitos
        $id = 'NFCom' . $chave;
        $this->infNFCom = $this->dom->createElement('infNFCom');
        $this->infNFCom->setAttribute('Id', $id);
        $this->infNFCom->setAttribute('versao', '1.00');
        $this->nfcom->appendChild($this->infNFCom);

        // ide
        $this->addIde($dados['ide']);
        // emit
        $this->addEmit($dados['emitente']);
        // dest
        $this->addDest($dados['destinatario']);
        // assinante
        $this->addAssinante($dados['assinante']);
        // Verificar se há IRRF no total para garantir que seja incluído nos itens
        $temIrrfNoTotal = false;
        $totalIrrf = 0;
        if (isset($dados['totais']['retTribTot']['vIRRF'])) {
            $totalIrrf = floatval($dados['totais']['retTribTot']['vIRRF']);
            $temIrrfNoTotal = ($totalIrrf > 0);
        }
        
        // det
        foreach ($dados['itens'] as $nItem => $item) {
            // Se houver IRRF no total mas não no item, garantir que seja incluído
            if ($temIrrfNoTotal && empty($item['imposto']['irrf'])) {
                // Calcular IRRF proporcional do item (4,8% sobre o valor do produto)
                $valorProduto = floatval($item['valor_total'] ?? 0);
                $irrfItem = round(($valorProduto * 4.8) / 100, 2);
                
                if ($irrfItem > 0) {
                    $item['imposto']['irrf'] = [
                        'vRetPIS' => 0.00,
                        'vRetCofins' => 0.00,
                        'vRetCSLL' => 0.00,
                        'vBCIRRF' => $valorProduto,
                        'vIRRF' => $irrfItem
                    ];
                }
            }
            $this->addDet($nItem + 1, $item);
        }
        // total
        $this->addTotal($dados['totais']);
        // gFat
        $this->addGFat($dados['faturamento']);
        // gSub
        if (!empty($dados['substituicao'])) {
            $this->addGSub($dados['substituicao']);
        }
        // gAjuste
        if (!empty($dados['ajuste'])) {
            $this->addGAjuste($dados['ajuste']);
        }
        // infAdic
        $this->addInfAdic($dados['informacoes_adicionais']);
        // infNFComSupl
        $this->addInfNFComSupl($dados['suplementar']);

        return $this->dom->saveXML();
    }

    private function addIde($ide)
    {
        $node = $this->dom->createElement('ide');
        $this->append($node, 'cUF', $ide['cUF']);
        $this->append($node, 'tpAmb', $ide['tpAmb']);
        $this->append($node, 'mod', '62');
        $this->append($node, 'serie', $ide['serie']);
        $this->append($node, 'nNF', $ide['nNF']);
        $this->append($node, 'cNF', $ide['cNF']);
        $this->append($node, 'cDV', $ide['cDV']);
        $this->append($node, 'dhEmi', $ide['dhEmi']);
        $this->append($node, 'tpEmis', $ide['tpEmis']);
        $this->append($node, 'nSiteAutoriz', $ide['nSiteAutoriz']);
        $this->append($node, 'cMunFG', $ide['cMunFG']);
        $this->append($node, 'finNFCom', $ide['finNFCom']);
        $this->append($node, 'tpFat', $ide['tpFat']);
        $this->append($node, 'verProc', $ide['verProc'] ?? '1.0.0');
        $this->infNFCom->appendChild($node);
    }

    private function addEmit($emit)
    {
        $node = $this->dom->createElement('emit');
        $this->append($node, 'CNPJ', preg_replace('/\D/', '', $emit['cnpj']));
        $this->append($node, 'IE', preg_replace('/\D/', '', $emit['ie']));
        $this->append($node, 'CRT', $emit['crt'] ?? '3');
        $this->append($node, 'xNome', $this->clean($emit['razao_social']));

        if (!empty($emit['nome_fantasia'])) {
            $this->append($node, 'xFant', $this->clean($emit['nome_fantasia']));
        }

        $ender = $this->dom->createElement('enderEmit');
        $this->append($ender, 'xLgr', $this->clean($emit['endereco']['logradouro']));
        $this->append($ender, 'nro', $emit['endereco']['numero']);
        if (!empty($emit['endereco']['complemento'])) {
            $this->append($ender, 'xCpl', $this->clean($emit['endereco']['complemento']));
        }
        $this->append($ender, 'xBairro', $this->clean($emit['endereco']['bairro']));
        $this->append($ender, 'cMun', $emit['endereco']['codigo_municipio']);
        $this->append($ender, 'xMun', $this->clean($emit['endereco']['municipio']));
        $this->append($ender, 'CEP', preg_replace('/\D/', '', $emit['endereco']['cep']));
        $this->append($ender, 'UF', $emit['endereco']['uf']);
        if (!empty($emit['telefone'])) {
            $this->append($ender, 'fone', preg_replace('/\D/', '', $emit['telefone']));
        }
        $node->appendChild($ender);
        $this->infNFCom->appendChild($node);
    }

    private function addDest($dest)
    {
        $node = $this->dom->createElement('dest');
        $this->append($node, 'xNome', $this->clean($dest['nome']));

        if (!empty($dest['cnpj'])) {
            $this->append($node, 'CNPJ', preg_replace('/\D/', '', $dest['cnpj']));
        } elseif (!empty($dest['cpf'])) {
            $this->append($node, 'CPF', preg_replace('/\D/', '', $dest['cpf']));
        }

        if (!empty($dest['id_estrangeiro'])) {
            $this->append($node, 'idEstrangeiro', $this->clean($dest['id_estrangeiro']));
        }

        $this->append($node, 'indIEDest', $dest['indicador_ie'] ?? '9');
        if (!empty($dest['ie'])) {
            $this->append($node, 'IE', preg_replace('/\D/', '', $dest['ie']));
        }

        if (!empty($dest['endereco'])) {
            $ender = $this->dom->createElement('enderDest');
            $this->append($ender, 'xLgr', $this->clean($dest['endereco']['logradouro']));
            $this->append($ender, 'nro', $dest['endereco']['numero']);
            if (!empty($dest['endereco']['complemento'])) {
                $this->append($ender, 'xCpl', $this->clean($dest['endereco']['complemento']));
            }
            $this->append($ender, 'xBairro', $this->clean($dest['endereco']['bairro']));
            $this->append($ender, 'cMun', $dest['endereco']['codigo_municipio']);
            $this->append($ender, 'xMun', $this->clean($dest['endereco']['municipio']));
            $this->append($ender, 'CEP', preg_replace('/\D/', '', $dest['endereco']['cep']));
            $this->append($ender, 'UF', $dest['endereco']['uf']);
            if (!empty($dest['endereco']['fone'])) {
                $this->append($ender, 'fone', preg_replace('/\D/', '', $dest['endereco']['fone']));
            }
            $node->appendChild($ender);
        }

        $this->infNFCom->appendChild($node);
    }

    private function addAssinante($ass)
    {
        $node = $this->dom->createElement('assinante');
        $this->append($node, 'iCodAssinante', $ass['codigo']);
        $this->append($node, 'tpAssinante', $ass['tipo']);
        $this->append($node, 'tpServUtil', $ass['tipo_servico']);
        if (!empty($ass['numero_contrato'])) {
            $this->append($node, 'nContrato', $ass['numero_contrato']);
        }
        if (!empty($ass['data_inicio_contrato'])) {
            $this->append($node, 'dContratoIni', $ass['data_inicio_contrato']);
        }
        if (!empty($ass['data_fim_contrato'])) {
            $this->append($node, 'dContratoFim', $ass['data_fim_contrato']);
        }
        if (!empty($ass['nro_terminal'])) {
            $this->append($node, 'nTerm', $ass['nro_terminal']);
        }
        if (!empty($ass['uf_principal'])) {
            $this->append($node, 'UFPrincipal', $ass['uf_principal']);
        }
        $this->infNFCom->appendChild($node);
    }

    private function addDet($nItem, $item)
    {
        $node = $this->dom->createElement('det');
        $node->setAttribute('nItem', $nItem);

        $prod = $this->dom->createElement('prod');
        $this->append($prod, 'cProd', $item['codigo']);
        $this->append($prod, 'xProd', $this->clean($item['descricao']));
        $this->append($prod, 'cClass', $item['classificacao_item']);
        if (!empty($item['cod_servico'])) {
            $this->append($prod, 'cServ', $item['cod_servico']);
        }
        $this->append($prod, 'CFOP', $item['cfop']);
        $this->append($prod, 'uMed', $item['unidade']);
        $this->append($prod, 'qFaturada', number_format($item['quantidade'], 4, '.', ''));
        $this->append($prod, 'vItem', number_format($item['valor_unitario'], 2, '.', ''));
        $this->append($prod, 'vDesc', number_format($item['desconto'] ?? 0, 2, '.', ''));
        $this->append($prod, 'vOutro', number_format($item['outros'] ?? 0, 2, '.', ''));
        $this->append($prod, 'vProd', number_format($item['valor_total'], 2, '.', ''));
        $node->appendChild($prod);

        $imposto = $this->dom->createElement('imposto');
        // ICMS (simplified for now, usually needs more cases)
        if (!empty($item['imposto']['icms'])) {
            $icms = $item['imposto']['icms'];
            $cst = $icms['cst'];

            // ICMS40 é usado para isenção/não tributado (CST 40, 41, 50, etc.)
            // Para ICMS40, apenas CST é obrigatório, e opcionalmente vICMSDeson
            // NÃO deve incluir vBC, pICMS ou vICMS
            $icmsNode = $this->dom->createElement('ICMS40');
            $this->append($icmsNode, 'CST', $cst);
            
            // Apenas vICMSDeson é permitido no ICMS40 (se houver valor desonerado)
            if (isset($icms['vICMSDeson']) && floatval($icms['vICMSDeson']) > 0) {
                $this->append($icmsNode, 'vICMSDeson', number_format($icms['vICMSDeson'], 2, '.', ''));
            }
            
            $imposto->appendChild($icmsNode);
        }

        // PIS
        if (!empty($item['imposto']['pis'])) {
            $pis = $item['imposto']['pis'];
            $pisNode = $this->dom->createElement('PIS');
            $this->append($pisNode, 'CST', $pis['cst']);
            if (isset($pis['vBC']))
                $this->append($pisNode, 'vBC', number_format($pis['vBC'], 2, '.', ''));
            if (isset($pis['pPIS']))
                $this->append($pisNode, 'pPIS', number_format($pis['pPIS'], 2, '.', ''));
            if (isset($pis['vPIS']))
                $this->append($pisNode, 'vPIS', number_format($pis['vPIS'], 2, '.', ''));
            $imposto->appendChild($pisNode);
        }

        // COFINS
        if (!empty($item['imposto']['cofins'])) {
            $cofins = $item['imposto']['cofins'];
            $cofNode = $this->dom->createElement('COFINS');
            $this->append($cofNode, 'CST', $cofins['cst']);
            if (isset($cofins['vBC']))
                $this->append($cofNode, 'vBC', number_format($cofins['vBC'], 2, '.', ''));
            if (isset($cofins['pCOFINS']))
                $this->append($cofNode, 'pCOFINS', number_format($cofins['pCOFINS'], 2, '.', ''));
            if (isset($cofins['vCOFINS']))
                $this->append($cofNode, 'vCOFINS', number_format($cofins['vCOFINS'], 2, '.', ''));
            $imposto->appendChild($cofNode);
        }

        // retTrib - Retenções tributárias (IRRF, PIS retido, COFINS retido, CSLL retido)
        // OBRIGATÓRIO quando há IRRF no total (conforme rejeição 685)
        // SEMPRE incluir o grupo retTrib se houver dados de IRRF no item
        $temIrrfNoItem = false;
        $irrfData = null;
        
        if (!empty($item['imposto']['irrf'])) {
            $irrfData = $item['imposto']['irrf'];
            $vBCIRRF = isset($irrfData['vBCIRRF']) ? floatval($irrfData['vBCIRRF']) : 0;
            $vIRRF = isset($irrfData['vIRRF']) ? floatval($irrfData['vIRRF']) : 0;
            $temIrrfNoItem = ($vBCIRRF > 0 || $vIRRF > 0);
        }
        
        // Se houver IRRF no item, SEMPRE incluir o grupo retTrib
        if ($temIrrfNoItem && $irrfData) {
            $retTribNode = $this->dom->createElement('retTrib');
            $this->append($retTribNode, 'vRetPIS', number_format($irrfData['vRetPIS'] ?? 0, 2, '.', ''));
            $this->append($retTribNode, 'vRetCofins', number_format($irrfData['vRetCofins'] ?? 0, 2, '.', ''));
            $this->append($retTribNode, 'vRetCSLL', number_format($irrfData['vRetCSLL'] ?? 0, 2, '.', ''));
            
            // vBCIRRF e vIRRF são obrigatórios quando há IRRF
            $vBCIRRF = isset($irrfData['vBCIRRF']) ? floatval($irrfData['vBCIRRF']) : 0;
            $vIRRF = isset($irrfData['vIRRF']) ? floatval($irrfData['vIRRF']) : 0;
            
            if ($vBCIRRF > 0) {
                $this->append($retTribNode, 'vBCIRRF', number_format($vBCIRRF, 2, '.', ''));
            }
            if ($vIRRF > 0) {
                $this->append($retTribNode, 'vIRRF', number_format($vIRRF, 2, '.', ''));
            }
            
            $imposto->appendChild($retTribNode);
        }

        $node->appendChild($imposto);
        $this->infNFCom->appendChild($node);
    }

    private function addTotal($totais)
    {
        $node = $this->dom->createElement('total');
        $this->append($node, 'vProd', number_format($totais['vProd'], 2, '.', ''));

        $icmsNode = $this->dom->createElement('ICMSTot');
        $this->append($icmsNode, 'vBC', number_format($totais['icms']['vBC'], 2, '.', ''));
        $this->append($icmsNode, 'vICMS', number_format($totais['icms']['vICMS'], 2, '.', ''));
        $this->append($icmsNode, 'vICMSDeson', number_format($totais['icms']['vICMSDeson'] ?? 0, 2, '.', ''));
        $this->append($icmsNode, 'vFCP', number_format($totais['icms']['vFCP'] ?? 0, 2, '.', ''));
        $node->appendChild($icmsNode);

        $this->append($node, 'vCOFINS', number_format($totais['vCOFINS'] ?? 0, 2, '.', ''));
        $this->append($node, 'vPIS', number_format($totais['vPIS'] ?? 0, 2, '.', ''));
        $this->append($node, 'vFUNTTEL', number_format($totais['vFUNTTEL'] ?? 0, 2, '.', ''));
        $this->append($node, 'vFUST', number_format($totais['vFUST'] ?? 0, 2, '.', ''));

        // vRetTribTot - totais de retenção tributária
        if (isset($totais['retTribTot'])) {
            $retTribNode = $this->dom->createElement('vRetTribTot');
            $this->append($retTribNode, 'vRetPIS', number_format($totais['retTribTot']['vRetPIS'] ?? 0, 2, '.', ''));
            $this->append($retTribNode, 'vRetCofins', number_format($totais['retTribTot']['vRetCofins'] ?? 0, 2, '.', ''));
            $this->append($retTribNode, 'vRetCSLL', number_format($totais['retTribTot']['vRetCSLL'] ?? 0, 2, '.', ''));
            $this->append($retTribNode, 'vIRRF', number_format($totais['retTribTot']['vIRRF'] ?? 0, 2, '.', ''));
            $node->appendChild($retTribNode);
        }

        $this->append($node, 'vDesc', number_format($totais['vDesc'] ?? 0, 2, '.', ''));
        $this->append($node, 'vOutro', number_format($totais['vOutro'] ?? 0, 2, '.', ''));
        $this->append($node, 'vNF', number_format($totais['vNF'], 2, '.', ''));

        $this->infNFCom->appendChild($node);
    }

    private function addGFat($fat)
    {
        $node = $this->dom->createElement('gFat');
        
        // Competência no formato YYYYMM (sem hífen)
        $competencia = $fat['competencia'] ?? date('Ym');
        // Remover hífen se existir (ex: 2025-12 -> 202512)
        $competencia = str_replace('-', '', $competencia);
        // Se ainda tiver formato YYYY-MM, remover o hífen
        if (strlen($competencia) == 7 && strpos($competencia, '-') !== false) {
            $competencia = str_replace('-', '', $competencia);
        }
        // Garantir que tenha 6 dígitos (YYYYMM)
        if (strlen($competencia) != 6) {
            // Tentar converter de outros formatos
            if (preg_match('/^(\d{4})-(\d{2})$/', $competencia, $matches)) {
                $competencia = $matches[1] . $matches[2];
            } elseif (preg_match('/^(\d{4})(\d{2})$/', $competencia)) {
                // Já está no formato correto
            } else {
                $competencia = date('Ym');
            }
        }
        
        $this->append($node, 'CompetFat', $competencia);
        $this->append($node, 'dVencFat', $fat['vencimento']);
        // PERÍODO DE USO: Usar exatamente o valor informado (respeitar formato do banco)
        // O período deve estar no formato YYYY-MM-DD (ISO) conforme salvo no banco
        $periodoInicio = $fat['periodo_inicio'];
        $periodoFim = $fat['periodo_fim'];
        
        // Se estiver em formato brasileiro (dd/mm/yyyy), converter para ISO (yyyy-mm-dd)
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $periodoInicio)) {
            $parts = explode('/', $periodoInicio);
            $periodoInicio = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $periodoFim)) {
            $parts = explode('/', $periodoFim);
            $periodoFim = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        
        $this->append($node, 'dPerUsoIni', $periodoInicio);
        $this->append($node, 'dPerUsoFim', $periodoFim);
        if (!empty($fat['cod_barras'])) {
            $this->append($node, 'codBarras', $fat['cod_barras']);
        }
        $this->infNFCom->appendChild($node);
    }

    private function addInfAdic($inf)
    {
        if (empty($inf['complementar']))
            return;

        $node = $this->dom->createElement('infAdic');
        $this->append($node, 'infCpl', $this->clean($inf['complementar']));
        $this->infNFCom->appendChild($node);
    }

    private function addInfNFComSupl($supl)
    {
        $node = $this->dom->createElement('infNFComSupl');
        $qrNode = $this->dom->createElement('qrCodNFCom');
        $qrNode->appendChild($this->dom->createCDATASection($supl['qrCode']));
        $node->appendChild($qrNode);
        $this->nfcom->appendChild($node);
    }

    private function addGSub($sub)
    {
        $node = $this->dom->createElement('gSub');
        $this->append($node, 'chOriginal', $sub['chave_original']);
        $this->infNFCom->appendChild($node);
    }

    private function addGAjuste($ajuste)
    {
        $node = $this->dom->createElement('gAjuste');
        $this->append($node, 'chReferenciada', $ajuste['chave_referenciada']);
        $this->infNFCom->appendChild($node);
    }

    private function append($parent, $name, $value)
    {
        $el = $this->dom->createElement($name, $value);
        $parent->appendChild($el);
        return $el;
    }

    private function clean($string)
    {
        if (empty($string))
            return '';

        // Replace line breaks with semicolon + space
        $string = str_replace(["\r\n", "\r", "\n"], '; ', $string);

        // Ensure UTF-8
        if (!mb_check_encoding($string, 'UTF-8')) {
            $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
        }

        $string = str_replace(['&', '<', '>', '"', "'"], ['&amp;', '&lt;', '&gt;', '&quot;', '&apos;'], $string);
        // Remove non-printable characters but keep accents
        $string = preg_replace('/[\x00-\x1F\x7F]/', '', $string);
        return trim($string);
    }
}
