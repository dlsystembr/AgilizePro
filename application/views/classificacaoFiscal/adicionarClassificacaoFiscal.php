<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-receipt"></i>
                </span>
                <h5>Cadastro de Classificação Fiscal</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?= site_url('classificacaofiscal/adicionar') ?>" id="formClassificacaoFiscal"
                    method="post" class="form-horizontal">
                    <div class="control-group">
                        <label for="operacao_comercial_id" class="control-label">Operação Comercial<span
                                class="required">*</span></label>
                        <div class="controls">
                            <select name="operacao_comercial_id" id="operacao_comercial_id">
                                <option value="">Selecione</option>
                                <?php foreach ($operacoes as $o) { ?>
                                    <option value="<?= $o->OPC_ID ?>" <?= set_value('operacao_comercial_id', $prefill['operacao_comercial_id'] ?? '') == $o->OPC_ID ? 'selected' : '' ?>>
                                        <?= $o->OPC_NOME ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group" id="cst_group"
                        style="display: <?= $regime_tributario === 'Simples Nacional' ? 'none' : 'block' ?>;">
                        <label for="cst" class="control-label">CST<span class="required">*</span></label>
                        <div class="controls">
                            <select name="cst" id="cst">
                                <option value="">Selecione</option>
                                <option value="00" <?= set_value('cst', $prefill['cst'] ?? '') == '00' ? 'selected' : '' ?>>00 - Tributada integralmente</option>
                                <option value="10" <?= set_value('cst', $prefill['cst'] ?? '') == '10' ? 'selected' : '' ?>>10 - Tributada e com cobrança do ICMS por substituição tributária</option>
                                <option value="20" <?= set_value('cst', $prefill['cst'] ?? '') == '20' ? 'selected' : '' ?>>20 - Com redução de base de cálculo</option>
                                <option value="30" <?= set_value('cst', $prefill['cst'] ?? '') == '30' ? 'selected' : '' ?>>30 - Isenta ou não tributada e com cobrança do ICMS por substituição tributária
                                </option>
                                <option value="40" <?= set_value('cst', $prefill['cst'] ?? '') == '40' ? 'selected' : '' ?>>40 - Isenta</option>
                                <option value="41" <?= set_value('cst', $prefill['cst'] ?? '') == '41' ? 'selected' : '' ?>>41 - Não tributada</option>
                                <option value="50" <?= set_value('cst', $prefill['cst'] ?? '') == '50' ? 'selected' : '' ?>>50 - Suspensão</option>
                                <option value="51" <?= set_value('cst', $prefill['cst'] ?? '') == '51' ? 'selected' : '' ?>>51 - Diferimento</option>
                                <option value="60" <?= set_value('cst', $prefill['cst'] ?? '') == '60' ? 'selected' : '' ?>>60 - ICMS cobrado anteriormente por substituição tributária</option>
                                <option value="70" <?= set_value('cst', $prefill['cst'] ?? '') == '70' ? 'selected' : '' ?>>70 - Com redução de base de cálculo e cobrança do ICMS por substituição
                                    tributária</option>
                                <option value="90" <?= set_value('cst', $prefill['cst'] ?? '') == '90' ? 'selected' : '' ?>>90 - Outras</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group" id="csosn_group"
                        style="display: <?= $regime_tributario === 'Simples Nacional' ? 'block' : 'none' ?>;">
                        <label for="csosn" class="control-label">CSOSN<span class="required">*</span></label>
                        <div class="controls">
                            <select name="csosn" id="csosn">
                                <option value="">Selecione</option>
                                <option value="101" <?= set_value('csosn', $prefill['csosn'] ?? '') == '101' ? 'selected' : '' ?>>101 - Tributada pelo Simples Nacional com permissão de crédito</option>
                                <option value="102" <?= set_value('csosn', $prefill['csosn'] ?? '') == '102' ? 'selected' : '' ?>>102 - Tributada pelo Simples Nacional sem permissão de crédito</option>
                                <option value="103" <?= set_value('csosn', $prefill['csosn'] ?? '') == '103' ? 'selected' : '' ?>>103 - Isenção do ICMS no Simples Nacional para faixa de receita bruta</option>
                                <option value="201" <?= set_value('csosn', $prefill['csosn'] ?? '') == '201' ? 'selected' : '' ?>>201 - Tributada pelo Simples Nacional com permissão de crédito e com cobrança
                                    do ICMS por substituição tributária</option>
                                <option value="202" <?= set_value('csosn', $prefill['csosn'] ?? '') == '202' ? 'selected' : '' ?>>202 - Tributada pelo Simples Nacional sem permissão de crédito e com cobrança
                                    do ICMS por substituição tributária</option>
                                <option value="203" <?= set_value('csosn', $prefill['csosn'] ?? '') == '203' ? 'selected' : '' ?>>203 - Isenção do ICMS no Simples Nacional para faixa de receita bruta e com
                                    cobrança do ICMS por substituição tributária</option>
                                <option value="300" <?= set_value('csosn', $prefill['csosn'] ?? '') == '300' ? 'selected' : '' ?>>300 - Imune</option>
                                <option value="400" <?= set_value('csosn', $prefill['csosn'] ?? '') == '400' ? 'selected' : '' ?>>400 - Não tributada pelo Simples Nacional</option>
                                <option value="500" <?= set_value('csosn', $prefill['csosn'] ?? '') == '500' ? 'selected' : '' ?>>500 - ICMS cobrado anteriormente por substituição tributária (substituído) ou
                                    por antecipação</option>
                                <option value="900" <?= set_value('csosn', $prefill['csosn'] ?? '') == '900' ? 'selected' : '' ?>>900 - Outros</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="tipo_icms" class="control-label">Tipo ICMS<span class="required">*</span></label>
                        <div class="controls">
                            <select name="tipo_icms" id="tipo_icms" required>
                                <option value="normal" <?= set_value('tipo_icms', $prefill['tipo_icms'] ?? 'normal') == 'normal' ? 'selected' : '' ?>>Normal</option>
                                <option value="st" <?= set_value('tipo_icms', $prefill['tipo_icms'] ?? '') == 'st' ? 'selected' : '' ?>>Substituição Tributária (ST)</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="natureza_contribuinte" class="control-label">Natureza do Contribuinte<span
                                class="required">*</span></label>
                        <div class="controls">
                            <select name="natureza_contribuinte" id="natureza_contribuinte">
                                <option value="inscrito" <?= set_value('natureza_contribuinte', $prefill['natureza_contribuinte'] ?? '') == 'inscrito' ? 'selected' : '' ?>>Inscrito
                                </option>
                                <option value="nao_inscrito" <?= set_value('natureza_contribuinte', $prefill['natureza_contribuinte'] ?? '') == 'nao_inscrito' ? 'selected' : '' ?>>Não
                                    Inscrito</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="cfop" class="control-label">CFOP<span class="required">*</span></label>
                        <div class="controls">
                            <input id="cfop" type="text" name="cfop"
                                value="<?= set_value('cfop', $prefill['cfop'] ?? '') ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="destinacao" class="control-label">Destinação<span class="required">*</span></label>
                        <div class="controls">
                            <select name="destinacao" id="destinacao" required>
                                <option value="">Selecione</option>
                                <option value="estadual" <?= set_value('destinacao', $prefill['destinacao'] ?? '') == 'estadual' ? 'selected' : '' ?>>Estadual</option>
                                <option value="interestadual" <?= set_value('destinacao', $prefill['destinacao'] ?? '') == 'interestadual' ? 'selected' : '' ?>>Interestadual</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="objetivo_comercial" class="control-label">Objetivo Comercial<span
                                class="required">*</span></label>
                        <div class="controls">
                            <select name="objetivo_comercial" id="objetivo_comercial" required>
                                <option value="">Selecione</option>
                                <option value="consumo" <?= set_value('objetivo_comercial', $prefill['objetivo_comercial'] ?? '') == 'consumo' ? 'selected' : '' ?>>Consumo
                                </option>
                                <option value="revenda" <?= set_value('objetivo_comercial', $prefill['objetivo_comercial'] ?? '') == 'revenda' ? 'selected' : '' ?>>Revenda
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-success">
                                    <span class="button__icon"><i class='bx bx-save'></i></span> <span
                                        class="button__text2">Salvar</span>
                                </button>
                                <a href="<?= site_url('classificacaofiscal') ?>"
                                    class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class='bx bx-undo'></i></span> <span
                                        class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#formClassificacaoFiscal').validate({
            rules: {
                operacao_comercial_id: {
                    required: true
                },
                natureza_contribuinte: {
                    required: true
                },
                cfop: {
                    required: true
                },
                destinacao: {
                    required: true
                },
                objetivo_comercial: {
                    required: true
                }
            },
            messages: {
                operacao_comercial_id: {
                    required: 'Campo obrigatório'
                },
                natureza_contribuinte: {
                    required: 'Campo obrigatório'
                },
                cfop: {
                    required: 'Campo obrigatório'
                },
                destinacao: {
                    required: 'Campo obrigatório'
                },
                objetivo_comercial: {
                    required: 'Campo obrigatório'
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });

        // Adiciona regra de validação dinâmica baseada no regime tributário
        if ('<?= $regime_tributario ?>' === 'Simples Nacional') {
            $('#csosn').rules('add', {
                required: true,
                messages: {
                    required: 'Campo obrigatório'
                }
            });
        } else {
            $('#cst').rules('add', {
                required: true,
                messages: {
                    required: 'Campo obrigatório'
                }
            });
        }
    });
</script>