<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovante de Cancelamento - NFe <?php echo $nfe->numero_nfe; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h2 {
            font-size: 18px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #666;
        }
        .info-value {
            flex: 1;
        }
        .justificativa {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .justificativa h3 {
            margin-top: 0;
            color: #333;
        }
        .justificativa p {
            margin: 10px 0;
            line-height: 1.5;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: none;
                box-shadow: none;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>COMPROVANTE DE CANCELAMENTO DE NOTA FISCAL ELETRÔNICA</h1>
            <p>Documento Auxiliar da Nota Fiscal Eletrônica</p>
        </div>

        <div class="info-section">
            <h2>Dados da NFe</h2>
            <div class="info-row">
                <div class="info-label">Número:</div>
                <div class="info-value"><?php echo $nfe->numero_nfe; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Chave de Acesso:</div>
                <div class="info-value"><?php echo $nfe->chave_nfe; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Data de Emissão:</div>
                <div class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($nfe->created_at)); ?></div>
            </div>
        </div>

        <div class="info-section">
            <h2>Dados do Cancelamento</h2>
            <div class="info-row">
                <div class="info-label">Protocolo:</div>
                <div class="info-value"><?php echo $documento->protocolo; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Data do Cancelamento:</div>
                <div class="info-value"><?php echo date('d/m/Y H:i:s', strtotime($documento->data_evento)); ?></div>
            </div>
        </div>

        <div class="justificativa">
            <h3>Justificativa do Cancelamento</h3>
            <p><?php echo nl2br($documento->justificativa); ?></p>
        </div>

        <div class="footer">
            <p>Este documento é um comprovante do cancelamento da Nota Fiscal Eletrônica.</p>
            <p>Data de impressão: <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Imprimir Comprovante</button>
    </div>
</body>
</html> 