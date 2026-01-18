<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>NFC-e</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @page {
            size: 58mm auto;
            margin: 0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 10px;
            background: #fff;
        }
        .text-center, .center {
            text-align: center;
        }
        .header {
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        .empresa {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .endereco, .documento, .info {
            margin-bottom: 2px;
        }
        .bold {
            font-weight: bold;
        }
        .section {
            margin: 2px 0;
        }
        .linha {
            border-top: 1px dashed #000;
            margin: 4px 0;
        }
        .produtos, .pagamentos, .totais {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .produtos th, .pagamentos th, .totais th {
            font-size: 10px;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding: 1px 0;
        }
        .produtos td, .pagamentos td, .totais td {
            font-size: 10px;
            padding: 1px 0;
            border: none;
        }
        .totais td {
            font-weight: bold;
        }
        .qrcode {
            text-align: center;
            margin: 8px 0 2px 0;
        }
        .qrcode svg, .qrcode img {
            width: 110px !important;
            height: 110px !important;
        }
        .footer {
            font-size: 9px;
            text-align: center;
            margin-top: 4px;
        }
        .linha-bloco {
            border-top: 1px solid #000;
            margin: 2px 0;
        }
        @media print {
            html, body {
                width: 210mm !important;
                min-width: 210mm !important;
                max-width: 210mm !important;
                height: 297mm !important;
                min-height: 297mm !important;
                max-height: 297mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
            }
            .container {
                width: 58mm !important;
                min-width: 58mm !important;
                max-width: 58mm !important;
                margin-left: auto !important;
                margin-right: auto !important;
                margin-top: 0 !important;
                margin-bottom: 0 !important;
                background: #fff !important;
            }
            .no-print {
                display: none !important;
            }
            @page {
                size: 58mm auto;
                margin: 0;
            }
            .qrcode svg, .qrcode img {
                width: 120px !important;
                height: 120px !important;
            }
        }
        .container {
            width: 58mm;
            min-width: 58mm;
            max-width: 58mm;
            margin-left: auto;
            margin-right: auto;
            margin-top: 0;
            margin-bottom: 0;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Cabeçalho -->
        <div class="header center">
            <?php if (isset($emitente->url_logo)): ?>
                <img src="<?php echo base_url($emitente->url_logo); ?>" alt="Logo" style="max-width: 60px; display:block; margin:0 auto 2px auto;">
            <?php endif; ?>
            <div class="empresa"><?php echo $emitente->nome; ?></div>
            <div>CNPJ: <?php echo $emitente->cnpj; ?></div>
            <div><?php echo $emitente->rua . ', ' . $emitente->numero . ' - ' . $emitente->bairro; ?></div>
            <div><?php echo $emitente->cidade . ' - ' . $emitente->uf; ?> CEP: <?php echo $emitente->cep; ?></div>
            <div>IE: <?php echo $emitente->ie; ?> Tel: <?php echo $emitente->telefone; ?></div>
        </div>

        <div class="linha"></div>

        <!-- Informações da NFC-e -->
        <div class="center bold" style="font-size:11px;">
            Documento Auxiliar da Nota Fiscal de Consumidor Eletrônica
        </div>
        <div class="center" style="margin:2px 0 2px 0;">
            NFC-e nº <span class="bold"><?php echo $nfe->numero_nfe; ?></span> Série 001
            <br>
            <?php echo date('d/m/Y'); ?> &nbsp; <?php echo date('H:i:s'); ?>
        </div>
        <div class="center" style="font-size:10px; font-weight:bold; margin-bottom:2px;">
            <?php echo isset($nfe->ambiente) ? ($nfe->ambiente == 1 ? 'Via consumidor' : 'AMBIENTE DE HOMOLOGAÇÃO - SEM VALOR FISCAL') : 'AMBIENTE NÃO DEFINIDO'; ?>
        </div>

        <div class="linha"></div>

        <!-- Produtos -->
        <div class="section">
            <table class="produtos">
                <thead>
                    <tr>
                        <th>Qtd</th>
                        <th>Descrição</th>
                        <th>Vl Unit</th>
                        <th>Vl Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?php echo $produto['quantidade']; ?></td>
                        <td><?php echo $produto['descricao']; ?></td>
                        <td>R$ <?php echo number_format($produto['valorUnitario'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($produto['valorTotal'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Totais -->
        <div class="section">
            <table class="totais">
                <tr>
                    <td>Valor Total:</td>
                    <td>R$ <?php echo number_format($total['total'], 2, ',', '.'); ?></td>
                </tr>
                <tr>
                    <td>Desconto:</td>
                    <td>R$ <?php echo number_format($total['desconto'], 2, ',', '.'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Formas de Pagamento -->
        <div class="section">
            <table class="pagamentos">
                <thead>
                    <tr>
                        <th>Forma</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pagamentos as $pagamento): ?>
                    <tr>
                        <td><?php echo $pagamento['tipo']; ?></td>
                        <td>R$ <?php echo number_format($pagamento['valor'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="linha"></div>

        <!-- Consulta e Chave de Acesso -->
        <div class="center" style="font-size:10px;">
            Consulte pela Chave de Acesso em<br>
            <span style="font-size:9px;word-break:break-all;">[URL da Sefaz]</span><br>
            <?php echo wordwrap($nfe->chave_nfe, 4, ' ', true); ?>
        </div>
        <div class="center bold" style="margin:2px 0 2px 0;">
            Protocolo de autorização: <?php echo $nfe->protocolo; ?><br>
            Data de autorização: <?php echo date('d/m/Y H:i:s'); ?>
        </div>

        <!-- QR Code -->
        <div class="qrcode">
            <?php echo $qrcode_url; ?>
        </div>

        <div class="linha"></div>

        <!-- Informações Adicionais -->
        <div class="footer">
            Tributos Totais Incidentes (Lei Federal 12.741/2012) - Total R$0,00 10%Federal 40%Estadual 30%Municipal 30%<br>
            <span style="font-size:9px;">Chave de Acesso: <?php echo $nfe->chave_nfe; ?></span>
        </div>
        <button class="no-print" onclick="window.print()" style="width: 100%; margin-top: 10px; padding: 6px;">Imprimir NFCe</button>
    </div>

    <script>
        window.onload = function() {
            // Ajusta o tamanho da fonte para caber na página
            var container = document.querySelector('.container');
            var fontSize = 12;
            while (container.offsetHeight > window.innerHeight && fontSize > 8) {
                fontSize--;
                container.style.fontSize = fontSize + 'px';
            }
        }
    </script>
</body>
</html> 