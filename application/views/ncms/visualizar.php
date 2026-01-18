<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0 0;padding: 10px;">
                <h5>NCM: <?php echo $ncm->NCM_CODIGO; ?> - <?php echo $ncm->NCM_DESCRICAO; ?></h5>
            </div>
            <div class="widget-content">
                <div class="widget-content" style="padding: 15px;">
                    <div class="span12" style="margin-left: 0">
                        <!-- Tributação Federal -->
                        <div class="widget-box" style="margin-top: 0;">
                            <div class="widget-title">
                                <h5>Tributação Federal</h5>
                            </div>
                            <div class="widget-content" style="padding: 10px;">
                                <?php if ($tributacao_federal) { ?>
                                <div class="row-fluid">
                                    <div class="span6">
                                        <strong>CST IPI Entrada:</strong> <?php echo $tributacao_federal->tbf_cst_ipi_entrada; ?><br>
                                        <strong>Alíquota IPI Entrada:</strong> <?php echo number_format($tributacao_federal->tbf_aliquota_ipi_entrada, 2, ',', '.'); ?>%<br>
                                        <strong>CST PIS/COFINS Entrada:</strong> <?php echo $tributacao_federal->tbf_cst_pis_cofins_entrada; ?><br>
                                        <strong>Alíquota PIS Entrada:</strong> <?php echo number_format($tributacao_federal->tbf_aliquota_pis_entrada, 2, ',', '.'); ?>%<br>
                                        <strong>Alíquota COFINS Entrada:</strong> <?php echo number_format($tributacao_federal->tbf_aliquota_cofins_entrada, 2, ',', '.'); ?>%<br>
                        </div>
                        <div class="span6">
                                        <strong>CST IPI Saída:</strong> <?php echo $tributacao_federal->tbf_cst_ipi_saida; ?><br>
                                        <strong>Alíquota IPI Saída:</strong> <?php echo number_format($tributacao_federal->tbf_aliquota_ipi_saida, 2, ',', '.'); ?>%<br>
                                        <strong>CST PIS/COFINS Saída:</strong> <?php echo $tributacao_federal->tbf_cst_pis_cofins_saida; ?><br>
                                        <strong>Alíquota PIS Saída:</strong> <?php echo number_format($tributacao_federal->tbf_aliquota_pis_saida, 2, ',', '.'); ?>%<br>
                                        <strong>Alíquota COFINS Saída:</strong> <?php echo number_format($tributacao_federal->tbf_aliquota_cofins_saida, 2, ',', '.'); ?>%<br>
                    </div>
                                </div>
                                <?php } else { ?>
                                <div class="alert alert-info">
                                    Nenhuma tributação federal cadastrada para este NCM.
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Tributação Estadual -->
                        <div class="widget-box" style="margin-top: 20px;">
                            <div class="widget-title">
                                <h5>Tributação Estadual</h5>
                            </div>
                            <div class="widget-content" style="padding: 10px;">
                                <table class="table table-bordered table-condensed">
                                    <thead>
                                        <tr>
                                            <th>UF</th>
                                            <th>Tipo</th>
                                            <th>Alíquota ICMS</th>
                                            <th>MVA</th>
                                            <th>Alíquota ICMS ST</th>
                                            <th>Redução ICMS</th>
                                            <th>Redução ST</th>
                                            <th>Alíquota FCP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($tributacao_estadual)) {
                                            foreach ($tributacao_estadual as $t) { ?>
                                                <tr>
                                                    <td><?php echo $t->tbe_uf; ?></td>
                                                    <td><?php echo $t->tbe_tipo_tributacao; ?></td>
                                                    <td><?php echo number_format($t->tbe_aliquota_icms, 2, ',', '.'); ?>%</td>
                                                    <td><?php echo number_format($t->tbe_mva, 3, ',', '.'); ?>%</td>
                                                    <td><?php echo number_format($t->tbe_aliquota_icms_st, 2, ',', '.'); ?>%</td>
                                                    <td><?php echo number_format($t->tbe_percentual_reducao_icms, 3, ',', '.'); ?>%</td>
                                                    <td><?php echo number_format($t->tbe_percentual_reducao_st, 3, ',', '.'); ?>%</td>
                                                    <td><?php echo number_format($t->tbe_aliquota_fcp, 2, ',', '.'); ?>%</td>
                                                </tr>
                                        <?php } 
                                        } else { ?>
                                            <tr><td colspan="8">Nenhuma tributação estadual cadastrada para este NCM.</td></tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                            <a href="<?php echo base_url() ?>index.php/ncms/tributacao/<?php echo $ncm->NCM_ID; ?>" class="button btn btn-success">
                                <span class="button__icon"><i class="fas fa-edit"></i></span>
                                <span class="button__text2">Editar Configurações</span>
                            </a>
                            <a href="<?php echo base_url() ?>index.php/ncms" class="button btn btn-warning">
                                <span class="button__icon"><i class="fas fa-times"></i></span>
                                <span class="button__text2">Cancelar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 