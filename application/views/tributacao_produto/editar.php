<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Tributação Produto</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container" style="margin-top:30px; max-width: 600px;">
    <h2>Editar Tributação Produto</h2>
    <form method="post">
        <div class="form-group">
            <label>Nome da Configuração</label>
            <input type="text" name="nome_configuracao" class="form-control" value="<?php echo htmlspecialchars($tributacao->nome_configuracao); ?>" required>
        </div>
        <div class="form-group">
            <label>CST IPI Saída</label>
            <input type="text" name="cst_ipi_saida" class="form-control" value="<?php echo htmlspecialchars($tributacao->cst_ipi_saida); ?>" required>
        </div>
        <div class="form-group">
            <label>Alíquota IPI Saída (%)</label>
            <input type="number" step="0.01" name="aliq_ipi_saida" class="form-control" value="<?php echo htmlspecialchars($tributacao->aliq_ipi_saida); ?>" required>
        </div>
        <div class="form-group">
            <label>CST PIS Saída</label>
            <input type="text" name="cst_pis_saida" class="form-control" value="<?php echo htmlspecialchars($tributacao->cst_pis_saida); ?>" required>
        </div>
        <div class="form-group">
            <label>Alíquota PIS Saída (%)</label>
            <input type="number" step="0.01" name="aliq_pis_saida" class="form-control" value="<?php echo htmlspecialchars($tributacao->aliq_pis_saida); ?>" required>
        </div>
        <div class="form-group">
            <label>CST COFINS Saída</label>
            <input type="text" name="cst_cofins_saida" class="form-control" value="<?php echo htmlspecialchars($tributacao->cst_cofins_saida); ?>" required>
        </div>
        <div class="form-group">
            <label>Alíquota COFINS Saída (%)</label>
            <input type="number" step="0.01" name="aliq_cofins_saida" class="form-control" value="<?php echo htmlspecialchars($tributacao->aliq_cofins_saida); ?>" required>
        </div>
        <div class="form-group">
            <label>Regime Fiscal Tributário</label>
            <select name="regime_fiscal_tributario" class="form-control" required>
                <option value="ICMS Normal (Tributado)" <?php if($tributacao->regime_fiscal_tributario == 'ICMS Normal (Tributado)') echo 'selected'; ?>>ICMS Normal (Tributado)</option>
                <option value="Substituição Tributária" <?php if($tributacao->regime_fiscal_tributario == 'Substituição Tributária') echo 'selected'; ?>>Substituição Tributária</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?php echo base_url('tributacaoproduto'); ?>" class="btn btn-default">Voltar</a>
    </form>
</div>
</body>
</html> 