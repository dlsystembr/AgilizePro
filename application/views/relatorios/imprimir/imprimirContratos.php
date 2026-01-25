<!DOCTYPE html>
<html>
<head>
    <title>MAPOS - Relatório de Contratos</title>
    <meta charset="UTF-8" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 10px;
            padding: 5mm;
        }
        
        .header-section {
            border-bottom: 2px solid #2D3E50;
            padding-bottom: 10px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        table th, 
        table td {
            border: 1px solid #ddd;
            padding: 5px 8px;
            font-size: 9px;
            white-space: nowrap;
            text-align: left;
        }

        table thead th {
            background-color: #2D3E50;
            color: #fff;
            text-transform: uppercase;
            font-weight: bold;
            text-align: center;
            font-size: 10px;
            padding: 8px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge-active { 
            background-color: #5bb75b; 
            color: #fff; 
            padding: 3px 8px; 
            border-radius: 3px; 
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }
        
        .badge-inactive {
            background-color: #999;
            color: #fff;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8px;
            display: inline-block;
        }

        .footer-info {
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #ddd;
            text-align: right;
            font-size: 8px;
            color: #666;
        }
        
        .item-row td {
            white-space: normal;
            padding: 5px;
        }
        
        .item-table {
            width: 100%;
            font-size: 8px;
            margin-top: 5px;
        }
        
        .item-table th,
        .item-table td {
            padding: 3px 5px;
            font-size: 8px;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho padronizado -->
    <?php 
    // Sempre exibir o topo, mesmo que vazio
    if (isset($topo)) {
        echo $topo;
    } else {
        // Fallback se o topo não foi gerado
        echo '<div style="border-bottom: 2px solid #2D3E50; padding-bottom: 10px; margin-bottom: 10px;">';
        echo '<h3 style="text-align: center; margin: 0; font-size: 16px; color: #2D3E50;">';
        echo isset($title) ? $title : 'Relatório de Contratos';
        echo '</h3>';
        echo '<p style="text-align: right; margin: 5px 0 0 0; font-size: 9px; color: #666;">';
        echo 'Página {PAGENO} de {nbpg} | Gerado em ' . date('d/m/Y H:i:s');
        echo '</p>';
        echo '</div>';
    }
    ?>
    
    <!-- Tabela de dados -->
    <table>
        <thead>
            <tr>
                <th style="width: 40px;">Nº</th>
                <th style="width: 180px;">CLIENTE</th>
                <th style="width: 120px;">CPF/CNPJ</th>
                <th style="width: 80px;">DATA INÍCIO</th>
                <th style="width: 80px;">DATA FIM</th>
                <th style="width: 130px;">TIPO ASSINANTE</th>
                <th style="width: 60px;">SITUAÇÃO</th>
                <th style="width: 110px;">DATA CADASTRO</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $tiposAssinante = [
                '1' => 'Comercial',
                '2' => 'Industrial',
                '3' => 'Residencial/PF',
                '4' => 'Produtor Rural',
                '5' => 'Orgão Público Estadual',
                '6' => 'Prestador de Telecom',
                '7' => 'Missões Diplomáticas',
                '8' => 'Igrejas e Templos',
                '99' => 'Outros'
            ];

            foreach ($contratos as $c) : 
                $isArray = is_array($c);
                $ctr_id = $isArray ? $c['ctr_id'] : $c->ctr_id;
                $ctr_numero = $isArray ? $c['ctr_numero'] : $c->ctr_numero;
                $pes_nome = $isArray ? ($c['pes_nome'] ?: $c['pes_razao_social']) : ($c->pes_nome ?: $c->pes_razao_social);
                $pes_cpfcnpj = $isArray ? $c['pes_cpfcnpj'] : $c->pes_cpfcnpj;
                $ctr_data_inicio = $isArray ? $c['ctr_data_inicio'] : $c->ctr_data_inicio;
                $ctr_data_fim = $isArray ? $c['ctr_data_fim'] : $c->ctr_data_fim;
                $ctr_tipo_assinante = $isArray ? $c['ctr_tipo_assinante'] : $c->ctr_tipo_assinante;
                $ctr_situacao = $isArray ? $c['ctr_situacao'] : $c->ctr_situacao;
                $ctr_data_cadastro = $isArray ? $c['ctr_data_cadastro'] : $c->ctr_data_cadastro;
            ?>
                <tr>
                    <td style="text-align: center;"><strong><?= $ctr_numero ?></strong></td>
                    <td><?= htmlspecialchars($pes_nome ?: '-') ?></td>
                    <td style="text-align: center;"><?= $pes_cpfcnpj ?: '-' ?></td>
                    <td style="text-align: center;"><?= $ctr_data_inicio ? date('d/m/Y', strtotime($ctr_data_inicio)) : '-' ?></td>
                    <td style="text-align: center;"><?= $ctr_data_fim ? date('d/m/Y', strtotime($ctr_data_fim)) : '-' ?></td>
                    <td style="text-align: center;"><?= $tiposAssinante[$ctr_tipo_assinante] ?? '-' ?></td>
                    <td style="text-align: center;">
                        <span class="<?= (int)$ctr_situacao === 1 ? 'badge-active' : 'badge-inactive' ?>">
                            <?= (int)$ctr_situacao === 1 ? 'ATIVO' : 'INATIVO' ?>
                        </span>
                    </td>
                    <td style="text-align: center;"><?= $ctr_data_cadastro ? date('d/m/Y H:i', strtotime($ctr_data_cadastro)) : '-' ?></td>
                </tr>
                
                <?php if (isset($incluirItens) && $incluirItens && isset($itensContratos[$ctr_id]) && !empty($itensContratos[$ctr_id])) : ?>
                    <tr class="item-row">
                        <td colspan="8" style="padding-left: 20px;">
                            <strong>Itens do Contrato:</strong>
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th style="text-align: left;">Serviço</th>
                                        <th style="text-align: center;">Quantidade</th>
                                        <th style="text-align: right;">Preço Unit.</th>
                                        <th style="text-align: right;">Total</th>
                                        <th style="text-align: center;">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalContrato = 0;
                                    foreach ($itensContratos[$ctr_id] as $item) : 
                                        $itemTotal = floatval($item->cti_preco) * floatval($item->cti_quantidade);
                                        $totalContrato += $itemTotal;
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item->pro_descricao ?: '-') ?></td>
                                            <td style="text-align: center;"><?= number_format($item->cti_quantidade, 2, ',', '.') ?> <?= $item->pro_unid_medida ?: '' ?></td>
                                            <td style="text-align: right;">R$ <?= number_format($item->cti_preco, 2, ',', '.') ?></td>
                                            <td style="text-align: right;">R$ <?= number_format($itemTotal, 2, ',', '.') ?></td>
                                            <td style="text-align: center;">
                                                <span class="<?= (int)$item->cti_ativo === 1 ? 'badge-active' : 'badge-inactive' ?>">
                                                    <?= (int)$item->cti_ativo === 1 ? 'ATIVO' : 'INATIVO' ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <tr style="background-color: #e0e0e0; font-weight: bold;">
                                        <td colspan="3" style="text-align: right;">Total do Contrato:</td>
                                        <td style="text-align: right;">R$ <?= number_format($totalContrato, 2, ',', '.') ?></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
            
            <?php if (empty($contratos)) : ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Nenhum contrato encontrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer-info">
        Relatório gerado em <?= date('d/m/Y H:i:s') ?> | Total de registros: <?= count($contratos) ?>
    </div>
</body>
</html>
