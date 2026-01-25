<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DANFE NFCom - <?php echo $nfecom->nfc_nnf; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 10px;
        }
        .danfe-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #000;
        }
        .header {
            border-bottom: 1px solid #000;
            padding: 10px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
        }
        .info-section {
            display: table;
            width: 100%;
            border-bottom: 1px solid #000;
        }
        .info-left, .info-right {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .info-right {
            border-left: 1px solid #000;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .items-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .totals-section {
            border-top: 1px solid #000;
            padding: 10px;
            margin-top: 10px;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        .totals-label {
            font-weight: bold;
            text-align: right;
        }
        .totals-value {
            text-align: right;
        }
        .footer {
            border-top: 1px solid #000;
            padding: 10px;
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
        .complementary-info {
            border-top: 1px solid #000;
            padding: 10px;
            margin-top: 10px;
            font-size: 10px;
        }
        .qr-code {
            text-align: center;
            margin: 10px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
        }
        .status-authorized {
            background-color: #4CAF50;
        }
        .status-pending {
            background-color: #FF9800;
        }
        .status-rejected {
            background-color: #F44336;
        }
    </style>
</head>
<body>
    <div class="danfe-container">
        <div class="header">
            <h1>DANFE NFCom</h1>
            <h2>Documento Auxiliar da Nota Fiscal de Comunicação</h2>
            <div class="status-badge <?php
                echo match($nfecom->nfc_status) {
                    3 => 'status-authorized',
                    2 => 'status-pending',
                    4 => 'status-rejected',
                    default => 'status-pending'
                };
            ?>">
                <?php
                echo match($nfecom->nfc_status) {
                    0 => 'RASCUNHO',
                    1 => 'SALVO',
                    2 => 'ENVIADO',
                    3 => 'AUTORIZADO',
                    4 => 'REJEITADO',
                    default => 'DESCONHECIDO'
                };
                ?>
            </div>
        </div>

        <div class="info-section">
            <div class="info-left">
                <div class="info-item">
                    <span class="info-label">Número NF:</span> <?php echo $nfecom->nfc_nnf; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Série:</span> <?php echo $nfecom->nfc_serie; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Chave de Acesso:</span>
                </div>
                <div class="info-item" style="word-break: break-all;">
                    <?php echo $nfecom->nfc_ch_nfcom; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Data Emissão:</span> <?php echo date('d/m/Y H:i', strtotime($nfecom->nfc_dhemi)); ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Número Contrato:</span> <?php echo $nfecom->nfc_n_contrato; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Data Início Contrato:</span> <?php echo date('d/m/Y', strtotime($nfecom->nfc_d_contrato_ini)); ?>
                </div>
            </div>
            <div class="info-right">
                <div class="info-item">
                    <span class="info-label">Emitente:</span>
                </div>
                <div class="info-item"><?php echo $nfecom->nfc_x_nome_emit; ?></div>
                <div class="info-item">
                    <span class="info-label">CNPJ:</span> <?php echo $nfecom->nfc_cnpj_emit; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">IE:</span> <?php echo $nfecom->nfc_ie_emit; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Endereço:</span>
                </div>
                <div class="info-item">
                    <?php echo $nfecom->nfc_x_lgr_emit . ', ' . $nfecom->nfc_nro_emit; ?>
                </div>
                <div class="info-item">
                    <?php echo $nfecom->nfc_x_bairro_emit . ' - ' . $nfecom->nfc_x_mun_emit . '/' . $nfecom->nfc_uf_emit; ?>
                </div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-left">
                <div class="info-item">
                    <span class="info-label">Destinatário:</span>
                </div>
                <div class="info-item"><?php echo $nfecom->nfc_x_nome_dest; ?></div>
                <div class="info-item">
                    <span class="info-label">CNPJ:</span> <?php echo $nfecom->nfc_cnpj_dest; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Endereço:</span>
                </div>
                <div class="info-item">
                    <?php echo $nfecom->nfc_x_lgr_dest . ', ' . $nfecom->nfc_nro_dest; ?>
                </div>
                <div class="info-item">
                    <?php echo $nfecom->nfc_x_bairro_dest . ' - ' . $nfecom->nfc_x_mun_dest . '/' . $nfecom->nfc_uf_dest; ?>
                </div>
            </div>
            <div class="info-right">
                <div class="info-item">
                    <span class="info-label">Competência:</span> <?php echo $nfecom->nfc_compet_fat; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Data Vencimento:</span> <?php echo date('d/m/Y', strtotime($nfecom->nfc_d_venc_fat)); ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Período de Uso:</span>
                </div>
                <div class="info-item">
                    <?php echo date('d/m/Y', strtotime($nfecom->nfc_d_per_uso_ini)) . ' à ' . date('d/m/Y', strtotime($nfecom->nfc_d_per_uso_fim)); ?>
                </div>
                <?php if ($nfecom->nfc_n_prot): ?>
                <div class="info-item">
                    <span class="info-label">Protocolo:</span> <?php echo $nfecom->nfc_n_prot; ?>
                </div>
                <div class="info-item">
                    <span class="info-label">Data Autorização:</span> <?php echo date('d/m/Y H:i', strtotime($nfecom->nfc_dh_recbto)); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <h3 style="text-align: center; margin: 20px 0 10px 0;">Itens da NFCom</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">Item</th>
                    <th style="width: 10%;">Código</th>
                    <th style="width: 40%;">Descrição</th>
                    <th style="width: 8%;">CFOP</th>
                    <th style="width: 10%;">Qtde</th>
                    <th style="width: 12%;">Vlr Unit.</th>
                    <th style="width: 15%;">Vlr Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($itens as $item): ?>
                <tr>
                    <td style="text-align: center;"><?php echo $item->nfi_n_item; ?></td>
                    <td><?php echo $item->nfi_c_prod; ?></td>
                    <td><?php echo $item->nfi_x_prod; ?></td>
                    <td style="text-align: center;"><?php echo $item->nfi_cfop; ?></td>
                    <td style="text-align: right;"><?php echo number_format($item->nfi_q_faturada, 4, ',', '.'); ?></td>
                    <td style="text-align: right;">R$ <?php echo number_format($item->nfi_v_item, 2, ',', '.'); ?></td>
                    <td style="text-align: right;">R$ <?php echo number_format($item->nfi_v_prod, 2, ',', '.'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td class="totals-label" style="width: 70%;"><strong>Valor dos Produtos:</strong></td>
                    <td class="totals-value">R$ <?php echo number_format($nfecom->nfc_v_prod, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td class="totals-label"><strong>PIS:</strong></td>
                    <td class="totals-value">R$ <?php echo number_format($nfecom->nfc_v_pis, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td class="totals-label"><strong>COFINS:</strong></td>
                    <td class="totals-value">R$ <?php echo number_format($nfecom->nfc_v_cofins, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td class="totals-label"><strong>IRRF:</strong></td>
                    <td class="totals-value">R$ <?php echo number_format($nfecom->nfc_v_irrf, 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td class="totals-label"><strong>Valor Líquido da NF:</strong></td>
                    <td class="totals-value" style="font-size: 14px; font-weight: bold;">R$ <?php echo number_format($nfecom->nfc_v_nf, 2, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <?php if ($nfecom->nfc_inf_cpl): ?>
        <div class="complementary-info">
            <strong>Informações Complementares:</strong><br>
            <?php echo nl2br($nfecom->nfc_inf_cpl); ?>
        </div>
        <?php endif; ?>

        <div class="qr-code">
            <strong>QR Code NFCom</strong><br>
            <!-- Aqui seria gerado o QR Code real -->
            <div style="border: 1px solid #000; width: 100px; height: 100px; margin: 10px auto; display: flex; align-items: center; justify-content: center;">
                QR CODE
            </div>
        </div>

        <div class="footer">
            <p><strong>NFCom - Nota Fiscal de Comunicação</strong></p>
            <p>Emitida em <?php echo date('d/m/Y \à\s H:i', strtotime($nfecom->nfc_dhemi)); ?></p>
            <p>Este documento é uma representação simplificada da DANFE NFCom</p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px;">Imprimir DANFE</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; margin-left: 10px;">Fechar</button>
    </div>
</body>
</html>