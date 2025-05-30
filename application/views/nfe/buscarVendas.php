<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <h5>Vendas para Emissão de NF-e</h5>
            </div>
            <div class="widget-content nopadding">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Operação Comercial</th>
                            <th>Valor Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($vendas) { ?>
                            <?php foreach ($vendas as $v) { ?>
                                <tr>
                                    <td><?php echo $v->idVendas ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($v->dataVenda)) ?></td>
                                    <td><?php echo $v->nomeCliente ?></td>
                                    <td><?php echo $v->nome_operacao ?></td>
                                    <td>R$ <?php echo number_format($v->valor_total ?: 0, 2, ',', '.') ?></td>
                                    <td>
                                        <a href="<?php echo base_url() ?>index.php/nfe/emitir/<?php echo $v->idVendas ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-file-invoice"></i> Emitir NFe
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6">Nenhuma venda encontrada</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div> 