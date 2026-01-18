<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tributação Produto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="margin-top:30px;">
    <h2>Tributação Produto</h2>
    <a href="<?php echo base_url('tributacaoproduto/adicionar'); ?>" class="btn btn-success" style="margin-bottom:15px;">Nova Configuração</a>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>CST IPI</th>
                <th>ALIQ IPI</th>
                <th>CST PIS</th>
                <th>ALIQ PIS</th>
                <th>CST COFINS</th>
                <th>ALIQ COFINS</th>
                <th>Regime Fiscal</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tributacoes as $t): ?>
            <tr>
                <td><?php echo $t->id; ?></td>
                <td><?php echo htmlspecialchars($t->nome_configuracao); ?></td>
                <td><?php echo htmlspecialchars($t->cst_ipi_saida); ?></td>
                <td><?php echo htmlspecialchars($t->aliq_ipi_saida); ?></td>
                <td><?php echo htmlspecialchars($t->cst_pis_saida); ?></td>
                <td><?php echo htmlspecialchars($t->aliq_pis_saida); ?></td>
                <td><?php echo htmlspecialchars($t->cst_cofins_saida); ?></td>
                <td><?php echo htmlspecialchars($t->aliq_cofins_saida); ?></td>
                <td><?php echo htmlspecialchars($t->regime_fiscal_tributario); ?></td>
                <td>
                    <a href="<?php echo base_url('tributacaoproduto/editar/'.$t->id); ?>" class="btn btn-primary btn-xs">Editar</a>
                    <a href="<?php echo base_url('tributacaoproduto/excluir/'.$t->id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Deseja realmente excluir?');">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html> 