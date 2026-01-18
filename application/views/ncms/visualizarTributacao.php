<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="span12" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-balance-scale"></i>
            </span>
            <h5>NCM: <?php echo $ncm->NCM_CODIGO; ?> - <?php echo $ncm->NCM_DESCRICAO; ?></h5>
        </div>
        <div class="widget-content nopadding">
            <?php if ($this->session->flashdata('success')) { ?>
                <div class="alert alert-success">
                    <button type="button" class="close" data-dismiss="alert">
                        <i class="fas fa-times"></i>
                    </button>
                    <i class="fas fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php } ?>

            <div class="widget-content" style="padding: 15px;">
                <div class="span12" style="margin-left: 0">
                    <div class="span6" style="margin-left: 0">
                        <!-- Tributação Federal - Entrada -->
                        <div class="widget-box" style="margin-top: 0;">
                            <div class="widget-title">
                                <h5>Tributação Federal - Entrada</h5>
                            </div>
                            <div class="widget-content" style="padding: 10px;">
                                <div class="control-group">
                                    <label class="control-label">CST IPI</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo $tributacao->tbf_cst_ipi_entrada; ?></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Alíquota IPI</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo number_format($tributacao->tbf_aliquota_ipi_entrada, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">CST PIS/COFINS</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo $tributacao->tbf_cst_pis_cofins_entrada; ?></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Alíquota PIS</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo number_format($tributacao->tbf_aliquota_pis_entrada, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Alíquota COFINS</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo number_format($tributacao->tbf_aliquota_cofins_entrada, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="span6" style="padding-left: 20px;">
                        <!-- Tributação Federal - Saída -->
                        <div class="widget-box" style="margin-top: 0;">
                            <div class="widget-title">
                                <h5>Tributação Federal - Saída</h5>
                            </div>
                            <div class="widget-content" style="padding: 10px;">
                                <div class="control-group">
                                    <label class="control-label">CST IPI</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo $tributacao->tbf_cst_ipi_saida; ?></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Alíquota IPI</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo number_format($tributacao->tbf_aliquota_ipi_saida, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">CST PIS/COFINS</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo $tributacao->tbf_cst_pis_cofins_saida; ?></span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Alíquota PIS</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo number_format($tributacao->tbf_aliquota_pis_saida, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Alíquota COFINS</label>
                                    <div class="controls">
                                        <span class="span12"><?php echo number_format($tributacao->tbf_aliquota_cofins_saida, 2, ',', '.'); ?>%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tributação Estadual -->
                <div class="span12" style="margin-left: 0; margin-top: 20px;">
                    <div class="widget-box">
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
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNcm')) { ?>
                            <a href="<?php echo base_url() ?>index.php/ncms/tributacao/<?php echo $ncm->NCM_ID; ?>" class="button btn btn-warning">
                                <span class="button__icon"><i class="fas fa-edit"></i></span>
                                <span class="button__text2">Editar</span>
                            </a>
                        <?php } ?>
                        <a href="<?php echo base_url() ?>index.php/ncms" class="button btn btn-mini btn-inverse">
                            <span class="button__icon"><i class="fas fa-arrow-left"></i></span>
                            <span class="button__text2">Voltar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 