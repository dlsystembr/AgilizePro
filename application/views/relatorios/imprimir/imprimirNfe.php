<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .header p {
            margin: 5px 0;
        }
        .summary {
            margin-bottom: 20px;
        }
        .summary-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            width: 48%;
            float: left;
            margin-right: 2%;
        }
        .summary-box h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
        }
        .summary-box p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .clear {
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <?php echo $topo; ?>
        <h2><?php echo $title; ?></h2>
        <p>Período: <?php echo date('d/m/Y', strtotime($dataInicial)) . ' até ' . date('d/m/Y', strtotime($dataFinal)); ?></p>
    </div>

    <div class="summary">
        <div class="summary-box">
            <h3>Total NFe</h3>
            <p>R$ <?php echo number_format($total, 2, ',', '.'); ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Devolução</h3>
            <p>R$ <?php echo number_format($total_devolucao, 2, ',', '.'); ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Cancelamento</h3>
            <p>R$ <?php echo number_format($total_cancelamento, 2, ',', '.'); ?></p>
        </div>
        <div class="summary-box">
            <h3>Total Líquido</h3>
            <p>R$ <?php echo number_format($total_liquido, 2, ',', '.'); ?></p>
        </div>
        <div class="clear"></div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Data Emissão</th>
                <th>Número NFe</th>
                <th>Chave NFe</th>
                <th>Cliente</th>
                <th>Valor Total</th>
                <th>Modelo</th>
                <th>Status</th>
                <th>Protocolo</th>
                <th>Retorno</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($nfe as $n): ?>
            <tr>
                <td><?php echo $n->id; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($n->created_at)); ?></td>
                <td><?php echo $n->numero_nfe; ?></td>
                <td><?php echo $n->chave_nfe; ?></td>
                <td><?php echo $n->nomeCliente; ?></td>
                <td>R$ <?php echo number_format($n->valor_total, 2, ',', '.'); ?></td>
                <td><?php echo $n->modelo == 65 ? 'NFC-e' : 'NFe'; ?></td>
                <td><?php echo $n->status == 1 ? 'Autorizada' : ($n->status == 2 ? 'Cancelada' : 'Rejeitada'); ?></td>
                <td><?php echo $n->protocolo; ?></td>
                <td><?php echo $n->chave_retorno_evento; ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html> 