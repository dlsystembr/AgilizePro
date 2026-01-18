<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-plus"></i>
                </span>
                <h5>Cadastro de Tributação</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formTributacao" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label for="nome_configuracao" class="control-label">Nome da Configuração<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome_configuracao" type="text" name="nome_configuracao" value="<?= set_value('nome_configuracao'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="cst_ipi_saida" class="control-label">CST IPI Saída<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cst_ipi_saida" type="text" name="cst_ipi_saida" value="<?= set_value('cst_ipi_saida'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="aliq_ipi_saida" class="control-label">Alíquota IPI Saída (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliq_ipi_saida" type="text" name="aliq_ipi_saida" value="<?= set_value('aliq_ipi_saida'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="cst_pis_saida" class="control-label">CST PIS Saída<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cst_pis_saida" type="text" name="cst_pis_saida" value="<?= set_value('cst_pis_saida'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="aliq_pis_saida" class="control-label">Alíquota PIS Saída (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliq_pis_saida" type="text" name="aliq_pis_saida" value="<?= set_value('aliq_pis_saida'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="cst_cofins_saida" class="control-label">CST COFINS Saída<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cst_cofins_saida" type="text" name="cst_cofins_saida" value="<?= set_value('cst_cofins_saida'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="aliq_cofins_saida" class="control-label">Alíquota COFINS Saída (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliq_cofins_saida" type="text" name="aliq_cofins_saida" value="<?= set_value('aliq_cofins_saida'); ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="aliq_red_icms" class="control-label">Aliq. red. ICMS (%)</label>
                        <div class="controls">
                            <input id="aliq_red_icms" type="text" name="aliq_red_icms" value="<?= set_value('aliq_red_icms'); ?>" />
                        </div>
                    </div>
                    <div id="camposSubstituicao" style="display:none;">
                        <div class="control-group">
                            <label for="aliq_iva" class="control-label">Aliq. IVA (%)</label>
                            <div class="controls">
                                <input id="aliq_iva" type="text" name="aliq_iva" value="<?= set_value('aliq_iva'); ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="aliq_rd_icms_st" class="control-label">Aliq. Rd. ICMS ST (%)</label>
                            <div class="controls">
                                <input id="aliq_rd_icms_st" type="text" name="aliq_rd_icms_st" value="<?= set_value('aliq_rd_icms_st'); ?>" />
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="regime_fiscal_tributario" class="control-label">Regime Fiscal Tributário<span class="required">*</span></label>
                        <div class="controls">
                            <select name="regime_fiscal_tributario" id="regime_fiscal_tributario">
                                <option value="ICMS Normal (Tributado)">ICMS Normal (Tributado)</option>
                                <option value="Substituição Tributária">Substituição Tributária</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-success">
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/tributacaoproduto" id="" class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formTributacao').validate({
            rules: {
                nome_configuracao: {
                    required: true
                },
                cst_ipi_saida: {
                    required: true
                },
                aliq_ipi_saida: {
                    required: true,
                    number: true
                },
                cst_pis_saida: {
                    required: true
                },
                aliq_pis_saida: {
                    required: true,
                    number: true
                },
                cst_cofins_saida: {
                    required: true
                },
                aliq_cofins_saida: {
                    required: true,
                    number: true
                },
                regime_fiscal_tributario: {
                    required: true
                }
            },
            messages: {
                nome_configuracao: {
                    required: 'Campo Requerido.'
                },
                cst_ipi_saida: {
                    required: 'Campo Requerido.'
                },
                aliq_ipi_saida: {
                    required: 'Campo Requerido.',
                    number: 'Digite um número válido.'
                },
                cst_pis_saida: {
                    required: 'Campo Requerido.'
                },
                aliq_pis_saida: {
                    required: 'Campo Requerido.',
                    number: 'Digite um número válido.'
                },
                cst_cofins_saida: {
                    required: 'Campo Requerido.'
                },
                aliq_cofins_saida: {
                    required: 'Campo Requerido.',
                    number: 'Digite um número válido.'
                },
                regime_fiscal_tributario: {
                    required: 'Campo Requerido.'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        function toggleCamposSubstituicao() {
            if ($('#regime_fiscal_tributario').val() === 'Substituição Tributária') {
                $('#camposSubstituicao').show();
            } else {
                $('#camposSubstituicao').hide();
            }
        }
        $('#regime_fiscal_tributario').change(toggleCamposSubstituicao);
        toggleCamposSubstituicao();
    });
</script> 