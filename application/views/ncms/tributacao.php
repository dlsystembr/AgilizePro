<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php 
    log_message('debug', 'Objeto NCM na view: ' . json_encode($ncm));
?>

<div class="row-fluid">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0 0;padding: 10px;">
                <h5>NCM: <?php echo isset($ncm->ncm_codigo) ? $ncm->ncm_codigo : 'N/A'; ?> - <?php echo isset($ncm->ncm_descricao) ? $ncm->ncm_descricao : 'N/A'; ?></h5>
            </div>
            <div class="widget-content">
                <div class="span12" style="margin-left: 0">
                    <div class="widget-box">
                        <div class="widget-title" style="margin: -20px 0 0">
                            <span class="icon">
                                <i class="fas fa-balance-scale"></i>
                            </span>
                            <h5>Configuração de Tributação</h5>
                        </div>
                        <div class="widget-content nopadding">
                            <?php echo $custom_error; ?>
                            <form action="<?php echo base_url() ?>index.php/ncms/salvarTributacao" id="formTributacao" method="post" class="form-horizontal">
                                <input type="hidden" id="ncm_id" name="ncm_id" value="<?php echo $ncm->ncm_id; ?>" />
                                
                                <div class="widget-content" style="padding: 15px;">
                                    <!-- Tributação Federal -->
                                    <div class="span12" style="margin-left: 0">
                                        <div class="widget-box" style="margin-top: 0;">
                                            <div class="widget-title">
                                                <h5>Tributação Federal</h5>
                                            </div>
                                            <div class="widget-content" style="padding: 10px;">
                                                <div class="span6" style="margin-left: 0">
                                                    <!-- Tributação Federal - Entrada -->
                                                    <div class="control-group">
                                                        <label for="tbf_cst_ipi_entrada" class="control-label">CST IPI Entrada<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <select name="tbf_cst_ipi_entrada" id="tbf_cst_ipi_entrada" class="span12">
                                                                <option value="">Selecione...</option>
                                                                <?php foreach ($cst_ipi_entrada as $codigo => $descricao) { ?>
                                                                    <option value="<?php echo $codigo; ?>" <?php echo isset($tributacao_federal->tbf_cst_ipi_entrada) && $tributacao_federal->tbf_cst_ipi_entrada == $codigo ? 'selected' : ''; ?>>
                                                                        <?php echo $codigo . ' - ' . $descricao; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_aliquota_ipi_entrada" class="control-label">Alíquota IPI Entrada<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <input type="text" name="tbf_aliquota_ipi_entrada" id="tbf_aliquota_ipi_entrada" value="<?php echo isset($tributacao_federal->tbf_aliquota_ipi_entrada) && $tributacao_federal->tbf_aliquota_ipi_entrada != '' ? number_format($tributacao_federal->tbf_aliquota_ipi_entrada, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_cst_pis_cofins_entrada" class="control-label">CST PIS/COFINS Entrada<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <select name="tbf_cst_pis_cofins_entrada" id="tbf_cst_pis_cofins_entrada" class="span12">
                                                                <option value="">Selecione...</option>
                                                                <?php foreach ($cst_pis_cofins_entrada as $codigo => $descricao) { ?>
                                                                    <option value="<?php echo $codigo; ?>" <?php echo isset($tributacao_federal->tbf_cst_pis_cofins_entrada) && $tributacao_federal->tbf_cst_pis_cofins_entrada == $codigo ? 'selected' : ''; ?>>
                                                                        <?php echo $codigo . ' - ' . $descricao; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_aliquota_pis_entrada" class="control-label">Alíquota PIS Entrada<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <input type="text" name="tbf_aliquota_pis_entrada" id="tbf_aliquota_pis_entrada" value="<?php echo isset($tributacao_federal->tbf_aliquota_pis_entrada) ? number_format($tributacao_federal->tbf_aliquota_pis_entrada, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_aliquota_cofins_entrada" class="control-label">Alíquota COFINS Entrada<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <input type="text" name="tbf_aliquota_cofins_entrada" id="tbf_aliquota_cofins_entrada" value="<?php echo isset($tributacao_federal->tbf_aliquota_cofins_entrada) ? number_format($tributacao_federal->tbf_aliquota_cofins_entrada, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="span6" style="padding-left: 20px;">
                                                    <!-- Tributação Federal - Saída -->
                                                    <div class="control-group">
                                                        <label for="tbf_cst_ipi_saida" class="control-label">CST IPI Saída<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <select name="tbf_cst_ipi_saida" id="tbf_cst_ipi_saida" class="span12">
                                                                <option value="">Selecione...</option>
                                                                <?php foreach ($cst_ipi_saida as $codigo => $descricao) { ?>
                                                                    <option value="<?php echo $codigo; ?>" <?php echo isset($tributacao_federal) && $tributacao_federal->tbf_cst_ipi_saida == $codigo ? 'selected' : ''; ?>>
                                                                        <?php echo $codigo . ' - ' . $descricao; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_aliquota_ipi_saida" class="control-label">Alíquota IPI Saída<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <input type="text" name="tbf_aliquota_ipi_saida" id="tbf_aliquota_ipi_saida" value="<?php echo isset($tributacao_federal->tbf_aliquota_ipi_saida) ? number_format($tributacao_federal->tbf_aliquota_ipi_saida, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_cst_pis_cofins_saida" class="control-label">CST PIS/COFINS Saída<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <select name="tbf_cst_pis_cofins_saida" id="tbf_cst_pis_cofins_saida" class="span12">
                                                                <option value="">Selecione...</option>
                                                                <?php foreach ($cst_pis_cofins_saida as $codigo => $descricao) { ?>
                                                                    <option value="<?php echo $codigo; ?>" <?php echo isset($tributacao_federal) && $tributacao_federal->tbf_cst_pis_cofins_saida == $codigo ? 'selected' : ''; ?>>
                                                                        <?php echo $codigo . ' - ' . $descricao; ?>
                                                                    </option>
                                                                <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_aliquota_pis_saida" class="control-label">Alíquota PIS Saída<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <input type="text" name="tbf_aliquota_pis_saida" id="tbf_aliquota_pis_saida" value="<?php echo isset($tributacao_federal->tbf_aliquota_pis_saida) ? number_format($tributacao_federal->tbf_aliquota_pis_saida, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                        </div>
                                                    </div>

                                                    <div class="control-group">
                                                        <label for="tbf_aliquota_cofins_saida" class="control-label">Alíquota COFINS Saída<span class="required">*</span></label>
                                                        <div class="controls">
                                                            <input type="text" name="tbf_aliquota_cofins_saida" id="tbf_aliquota_cofins_saida" value="<?php echo isset($tributacao_federal->tbf_aliquota_cofins_saida) ? number_format($tributacao_federal->tbf_aliquota_cofins_saida, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tributação Estadual -->
                                    <div class="span12" style="margin-left: 0">
                                        <div class="widget-box" style="margin-top: 0;">
                                            <div class="widget-title">
                                                <h5>Tributação Estadual</h5>
                                            </div>
                                            <div class="widget-content" style="padding: 10px;">
                                                <!-- Linha de Aplicar para Todos -->
                                                <div class="span12" style="margin-left: 0; margin-bottom: 15px; padding: 10px; background-color: #f5f5f5; border-radius: 4px;">
                                                    <table class="table table-bordered" style="margin-bottom: 0;">
                                                        <thead>
                                                            <tr>
                                                                <th style="width: 14%">Tipo de Tributação</th>
                                                                <th style="width: 10%">Alíquota ICMS</th>
                                                                <th style="width: 10%">MVA</th>
                                                                <th style="width: 10%">Alíquota ICMS ST</th>
                                                                <th style="width: 10%">Alíquota FCP</th>
                                                                <th style="width: 12%">% Redução ICMS</th>
                                                                <th style="width: 12%">% Redução ST</th>
                                                                <th style="width: 12%">Ação</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <select name="tipo_tributacao_todos" id="tipo_tributacao_todos" class="span12">
                                                                        <option value="">Selecione...</option>
                                                                        <option value="ICMS Normal" <?php echo isset($tributacao_estadual[0]->tbe_tipo_tributacao) && $tributacao_estadual[0]->tbe_tipo_tributacao == 'ICMS Normal' ? 'selected' : ''; ?>>ICMS Normal</option>
                                                                        <option value="ST" <?php echo isset($tributacao_estadual[0]->tbe_tipo_tributacao) && $tributacao_estadual[0]->tbe_tipo_tributacao == 'ST' ? 'selected' : ''; ?>>ST</option>
                                                                        <option value="Serviço" <?php echo isset($tributacao_estadual[0]->tbe_tipo_tributacao) && $tributacao_estadual[0]->tbe_tipo_tributacao == 'Serviço' ? 'selected' : ''; ?>>Serviço</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="aliquota_icms_todos" id="aliquota_icms_todos" class="span12 money" value="0,00" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="mva_todos" id="mva_todos" class="span12 money" value="0,00" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="aliquota_icms_st_todos" id="aliquota_icms_st_todos" class="span12 money" value="0,00" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="aliquota_fcp_todos" id="aliquota_fcp_todos" class="span12 money" value="0,00" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="percentual_reducao_icms_todos" id="percentual_reducao_icms_todos" class="span12 money" value="0,00" />
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="percentual_reducao_st_todos" id="percentual_reducao_st_todos" class="span12 money" value="0,00" />
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-success span12" id="aplicar_todos">Aplicar para Todos</button>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>

                                                <!-- Estados -->
                                                <div class="span12" style="margin-left: 0">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>UF</th>
                                                                <th>Tipo de Tributação</th>
                                                                <th>Alíquota ICMS</th>
                                                                <th>MVA</th>
                                                                <th>Alíquota ICMS ST</th>
                                                                <th>% Redução ICMS</th>
                                                                <th>% Redução ST</th>
                                                                <th>Alíquota FCP</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($estados as $uf => $nome) { 
                                                                $tributacao = null;
                                                                if (isset($tributacao_estadual)) {
                                                                    foreach ($tributacao_estadual as $t) {
                                                                        if ($t->tbe_uf == $uf) {
                                                                            $tributacao = $t;
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                            ?>
                                                                <tr>
                                                                    <td><?php echo $uf; ?></td>
                                                                    <td>
                                                                        <select name="tributacao_estadual[<?php echo $uf; ?>][tipo_tributacao]" class="span12">
                                                                            <option value="">Selecione...</option>
                                                                            <?php foreach ($tipos_tributacao as $codigo => $descricao) { ?>
                                                                                <option value="<?php echo $codigo; ?>" <?php echo isset($tributacao) && $tributacao->tbe_tipo_tributacao == $codigo ? 'selected' : ''; ?>>
                                                                                    <?php echo $descricao; ?>
                                                                                </option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="tributacao_estadual[<?php echo $uf; ?>][aliquota_icms]" value="<?php echo isset($tributacao->tbe_aliquota_icms) ? number_format($tributacao->tbe_aliquota_icms, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="tributacao_estadual[<?php echo $uf; ?>][mva]" value="<?php echo isset($tributacao->tbe_mva) ? number_format($tributacao->tbe_mva, 3, ',', '.') : '0,000'; ?>" class="span12 decimal" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="tributacao_estadual[<?php echo $uf; ?>][aliquota_icms_st]" value="<?php echo isset($tributacao->tbe_aliquota_icms_st) ? number_format($tributacao->tbe_aliquota_icms_st, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="tributacao_estadual[<?php echo $uf; ?>][percentual_reducao_icms]" value="<?php echo isset($tributacao->tbe_percentual_reducao_icms) ? number_format($tributacao->tbe_percentual_reducao_icms, 3, ',', '.') : '0,000'; ?>" class="span12 decimal" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="tributacao_estadual[<?php echo $uf; ?>][percentual_reducao_st]" value="<?php echo isset($tributacao->tbe_percentual_reducao_st) ? number_format($tributacao->tbe_percentual_reducao_st, 3, ',', '.') : '0,000'; ?>" class="span12 decimal" />
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" name="tributacao_estadual[<?php echo $uf; ?>][aliquota_fcp]" value="<?php echo isset($tributacao->tbe_aliquota_fcp) ? number_format($tributacao->tbe_aliquota_fcp, 2, ',', '.') : '0,00'; ?>" class="span12 money" />
                                                                    </td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                                        <div class="span6 offset3" style="display:flex;justify-content: center">
                                            <button type="submit" class="button btn btn-success">
                                                <span class="button__icon"><i class="fas fa-save"></i></span>
                                                <span class="button__text2">Salvar</span>
                                            </button>
                                            <a href="<?php echo base_url() ?>index.php/ncms" class="button btn btn-mini btn-warning">
                                                <span class="button__icon"><i class="fas fa-times"></i></span>
                                                <span class="button__text2">Cancelar</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Formatação dos campos de alíquota
    function formatNumber(value) {
        if (!value) return '0,00';
        value = value.toString().replace(/[^\d,]/g, '');
        var parts = value.split(',');
        if (parts.length > 2) {
            value = parts[0] + ',' + parts.slice(1).join('');
        }
        if (parts.length > 1 && parts[1].length > 2) {
            value = parts[0] + ',' + parts[1].substring(0, 2);
        }
        if (!value || value === ',') {
            value = '0,00';
        }
        return value;
    }

    // Formata o número quando o campo recebe input
    $('input[id^="tbf_aliquota_"]').on('input', function() {
        var value = $(this).val();
        value = value.replace(/[^\d,]/g, '');
        var parts = value.split(',');
        if (parts.length > 2) {
            value = parts[0] + ',' + parts.slice(1).join('');
        }
        if (parts.length > 1 && parts[1].length > 2) {
            value = parts[0] + ',' + parts[1].substring(0, 2);
        }
        if (!value || value === ',') {
            value = '0,00';
        }
        $(this).val(value);
    });

    // Formata o número quando o campo perde o foco
    $('input[id^="tbf_aliquota_"]').on('blur', function() {
        $(this).val(formatNumber($(this).val()));
    });

    // Formatação dos campos de tributação estadual
    $('input[name^="tributacao_estadual"]').on('input', function() {
        var value = $(this).val();
        value = value.replace(/[^\d,]/g, '');
        var parts = value.split(',');
        if (parts.length > 2) {
            value = parts[0] + ',' + parts.slice(1).join('');
        }
        if (parts.length > 1 && parts[1].length > 2) {
            value = parts[0] + ',' + parts[1].substring(0, 2);
        }
        if (!value || value === ',') {
            value = '0,00';
        }
        $(this).val(value);
    });

    // Formata o número quando o campo perde o foco
    $('input[name^="tributacao_estadual"]').on('blur', function() {
        $(this).val(formatNumber($(this).val()));
    });

    // Validação do formulário
    $('#formTributacao').submit(function(e) {
        e.preventDefault();
        var isValid = true;
        var errorFields = [];
        
        // Valida campos obrigatórios da tributação federal
        $('select[id^="tbf_cst_"]').each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('error');
                var label = $(this).closest('.control-group').find('label').text().trim();
                errorFields.push(label);
            } else {
                $(this).removeClass('error');
            }
        });

        // Valida formato das alíquotas da tributação federal
        $('input[id^="tbf_aliquota_"]').each(function() {
            var value = $(this).val();
            if (!value || !/^\d+(,\d{1,2})?$/.test(value)) {
                isValid = false;
                $(this).addClass('error');
                var label = $(this).closest('.control-group').find('label').text().trim();
                errorFields.push(label);
            } else {
                $(this).removeClass('error');
            }
        });

        if (!isValid) {
            var message = 'Por favor, preencha os seguintes campos:\n\n';
            message += errorFields.join('\n');
            alert(message);
            return false;
        }

        // Se tudo estiver ok, envia o formulário
        this.submit();
    });

    // Função para formatar número como moeda
    function formatMoney(value) {
        if (!value || value === '') {
            return '0,00';
        }
        return value;
    }

    // Aplica formatação em todos os campos numéricos
    $('.money').each(function() {
        $(this).val(formatMoney($(this).val()));
    });

    // Ao perder o foco, garante que o campo tenha um valor
    $('.money').blur(function() {
        $(this).val(formatMoney($(this).val()));
    });

    // Botão "Aplicar para Todos"
    $('#aplicar_todos').click(function() {
        var tipoTributacao = $('#tipo_tributacao_todos').val();
        var aliquotaIcms = $('#aliquota_icms_todos').val();
        var mva = $('#mva_todos').val();
        var aliquotaIcmsSt = $('#aliquota_icms_st_todos').val();
        var percentualReducaoIcms = $('#percentual_reducao_icms_todos').val();
        var percentualReducaoSt = $('#percentual_reducao_st_todos').val();
        var aliquotaFcp = $('#aliquota_fcp_todos').val();

        // Aplica apenas nos estados que não têm valores definidos
        $('select[name^="tributacao_estadual"][name$="[tipo_tributacao]"]').each(function() {
            if (!$(this).val()) {
                $(this).val(tipoTributacao);
            }
        });

        $('input[name^="tributacao_estadual"][name$="[aliquota_icms]"]').each(function() {
            if (!$(this).val() || $(this).val() === '0,00') {
                $(this).val(aliquotaIcms);
            }
        });

        $('input[name^="tributacao_estadual"][name$="[mva]"]').each(function() {
            if (!$(this).val() || $(this).val() === '0,000') {
                $(this).val(mva);
            }
        });

        $('input[name^="tributacao_estadual"][name$="[aliquota_icms_st]"]').each(function() {
            if (!$(this).val() || $(this).val() === '0,00') {
                $(this).val(aliquotaIcmsSt);
            }
        });

        $('input[name^="tributacao_estadual"][name$="[percentual_reducao_icms]"]').each(function() {
            if (!$(this).val() || $(this).val() === '0,000') {
                $(this).val(percentualReducaoIcms);
            }
        });

        $('input[name^="tributacao_estadual"][name$="[percentual_reducao_st]"]').each(function() {
            if (!$(this).val() || $(this).val() === '0,000') {
                $(this).val(percentualReducaoSt);
            }
        });

        $('input[name^="tributacao_estadual"][name$="[aliquota_fcp]"]').each(function() {
            if (!$(this).val() || $(this).val() === '0,00') {
                $(this).val(aliquotaFcp);
            }
        });
    });

    // Antes de enviar o formulário, garante que campos vazios sejam 0,00
    $('#formTributacao').submit(function() {
        $('.money').each(function() {
            $(this).val(formatMoney($(this).val()));
        });

        // Garante que o tipo de tributação não seja vazio
        var tipoTributacao = $('#tipo_tributacao_todos').val();
        if (!tipoTributacao) {
            var primeiroEstado = <?php echo isset($tributacao_estadual[0]->tbe_tipo_tributacao) ? json_encode($tributacao_estadual[0]->tbe_tipo_tributacao) : 'null'; ?>;
            if (primeiroEstado) {
                $('#tipo_tributacao_todos').val(primeiroEstado);
            }
        }

        return true;
    });
});
</script>

<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script>
$(document).ready(function() {
    // Máscara para campos monetários (2 casas decimais)
    $(".money").maskMoney({
        thousands: '.',
        decimal: ',',
        allowZero: true,
        precision: 2,
        allowNegative: false,
        suffix: '',
        clearIfNotMatch: true,
        selectAllOnFocus: true,
        formatOnBlur: true,
        allowEmpty: true
    });
    
    // Máscara para campos decimais (3 casas decimais)
    $(".decimal").maskMoney({
        thousands: '.',
        decimal: ',',
        allowZero: true,
        precision: 3,
        allowNegative: false,
        suffix: '',
        clearIfNotMatch: true,
        selectAllOnFocus: true,
        formatOnBlur: true,
        allowEmpty: true
    });
});
</script> 