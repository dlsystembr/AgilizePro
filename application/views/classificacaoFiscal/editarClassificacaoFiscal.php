<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<style>
    /* Estilos organizados para Classificação Fiscal - mesmo design de pessoas */
    .form-section {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin-bottom: 20px;
        background: #fff;
    }

    .form-section-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-section-content {
        padding: 15px;
    }

    /* Alinhamento dos campos */
    .form-section .control-label {
        width: 120px;
        text-align: right;
        margin-right: 10px;
    }

    .form-section .controls {
        margin-left: 140px;
    }

    /* Garantir que inputs não estourem o container */
    .form-section input[type="text"],
    .form-section input[type="date"],
    .form-section input[type="number"],
    .form-section select,
    .form-section textarea {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        height: 30px;
        padding: 4px 8px;
        line-height: 20px;
        font-size: 14px;
    }

    .form-section textarea {
        height: auto;
        resize: vertical;
    }

    /* Estilos para validação */
    .control-group.error input,
    .control-group.error select,
    .control-group.error textarea {
        border-color: #b94a48;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    }

    .control-group.error .help-inline {
        color: #b94a48;
        display: inline-block;
        margin-left: 10px;
        position: relative;
        padding: 5px 10px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        font-size: 12px;
        line-height: 1.4;
        white-space: nowrap;
    }

    .control-group.success input,
    .control-group.success select,
    .control-group.success textarea {
        border-color: #468847;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    }

    .help-inline {
        display: none;
        color: #b94a48;
        font-size: 12px;
    }

    .control-group.error .help-inline {
        display: inline-block;
    }

    /* Campos obrigatórios */
    .required {
        color: #b94a48;
        font-weight: bold;
        margin-left: 2px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-receipt"></i>
                </span>
                <h5>Editar Classificação Fiscal</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?= site_url('classificacaofiscal/editar/' . $result->id) ?>" id="formClassificacaoFiscal"
                method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">
                    
                    <!-- Seção Principal -->
                    <div class="form-section" style="margin: 20px 0 0 0;">
                        <div class="form-section-header">
                            <i class="fas fa-edit"></i>
                            <span>Dados da Classificação Fiscal</span>
                        </div>
                        <div class="form-section-content">
                            <div class="control-group">
                                <label for="operacao_comercial_id" class="control-label">Operação Comercial<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <select name="operacao_comercial_id" id="operacao_comercial_id" class="span12">
                                        <option value="">Selecione</option>
                                        <?php foreach ($operacoes as $o) { ?>
                                            <option value="<?= $o->opc_id ?>" <?= (set_value('operacao_comercial_id', $result->operacao_comercial_id) == $o->opc_id) ? 'selected' : '' ?>>
                                                <?= $o->opc_nome ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="tipo_cliente_id" class="control-label">Tipo de Cliente</label>
                                <div class="controls">
                                    <select name="tipo_cliente_id" id="tipo_cliente_id" class="span12">
                                        <option value="">Selecione (Opcional)</option>
                                        <?php foreach ($tipos_clientes as $tc) { ?>
                                            <option value="<?= $tc->tpc_id ?>" <?= (set_value('tipo_cliente_id', $result->tipo_cliente_id) == $tc->tpc_id) ? 'selected' : '' ?>>
                                                <?= $tc->tpc_nome ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group" id="cst_group"
                                style="display: <?= $regime_tributario === 'Simples Nacional' ? 'none' : 'block' ?>;">
                                <label for="cst" class="control-label">CST<span class="required">*</span></label>
                                <div class="controls">
                                    <select name="cst" id="cst" class="span12">
                                        <option value="">Selecione</option>
                                        <option value="00" <?= $result->cst == '00' ? 'selected' : '' ?>>00 - Tributada integralmente</option>
                                        <option value="10" <?= $result->cst == '10' ? 'selected' : '' ?>>10 - Tributada e com cobrança do ICMS por substituição tributária</option>
                                        <option value="20" <?= $result->cst == '20' ? 'selected' : '' ?>>20 - Com redução de base de cálculo</option>
                                        <option value="30" <?= $result->cst == '30' ? 'selected' : '' ?>>30 - Isenta ou não tributada e com cobrança do ICMS por substituição tributária</option>
                                        <option value="40" <?= $result->cst == '40' ? 'selected' : '' ?>>40 - Isenta</option>
                                        <option value="41" <?= $result->cst == '41' ? 'selected' : '' ?>>41 - Não tributada</option>
                                        <option value="50" <?= $result->cst == '50' ? 'selected' : '' ?>>50 - Suspensão</option>
                                        <option value="51" <?= $result->cst == '51' ? 'selected' : '' ?>>51 - Diferimento</option>
                                        <option value="60" <?= $result->cst == '60' ? 'selected' : '' ?>>60 - ICMS cobrado anteriormente por substituição tributária</option>
                                        <option value="70" <?= $result->cst == '70' ? 'selected' : '' ?>>70 - Com redução de base de cálculo e cobrança do ICMS por substituição tributária</option>
                                        <option value="90" <?= $result->cst == '90' ? 'selected' : '' ?>>90 - Outras</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group" id="csosn_group"
                                style="display: <?= $regime_tributario === 'Simples Nacional' ? 'block' : 'none' ?>;">
                                <label for="csosn" class="control-label">CSOSN<span class="required">*</span></label>
                                <div class="controls">
                                    <select name="csosn" id="csosn" class="span12">
                                        <option value="">Selecione</option>
                                        <option value="101" <?= $result->csosn == '101' ? 'selected' : '' ?>>101 - Tributada pelo Simples Nacional com permissão de crédito</option>
                                        <option value="102" <?= $result->csosn == '102' ? 'selected' : '' ?>>102 - Tributada pelo Simples Nacional sem permissão de crédito</option>
                                        <option value="103" <?= $result->csosn == '103' ? 'selected' : '' ?>>103 - Isenção do ICMS no Simples Nacional para faixa de receita bruta</option>
                                        <option value="201" <?= $result->csosn == '201' ? 'selected' : '' ?>>201 - Tributada pelo Simples Nacional com permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                                        <option value="202" <?= $result->csosn == '202' ? 'selected' : '' ?>>202 - Tributada pelo Simples Nacional sem permissão de crédito e com cobrança do ICMS por substituição tributária</option>
                                        <option value="203" <?= $result->csosn == '203' ? 'selected' : '' ?>>203 - Isenção do ICMS no Simples Nacional para faixa de receita bruta e com cobrança do ICMS por substituição tributária</option>
                                        <option value="300" <?= $result->csosn == '300' ? 'selected' : '' ?>>300 - Imune</option>
                                        <option value="400" <?= $result->csosn == '400' ? 'selected' : '' ?>>400 - Não tributada pelo Simples Nacional</option>
                                        <option value="500" <?= $result->csosn == '500' ? 'selected' : '' ?>>500 - ICMS cobrado anteriormente por substituição tributária (substituído) ou por antecipação</option>
                                        <option value="900" <?= $result->csosn == '900' ? 'selected' : '' ?>>900 - Outros</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="tipo_icms" class="control-label">Tipo ICMS<span class="required">*</span></label>
                                <div class="controls">
                                    <select name="tipo_icms" id="tipo_icms" required class="span12">
                                        <option value="normal" <?= (set_value('tipo_icms', $result->tipo_icms) == 'normal') ? 'selected' : '' ?>>ICMS Normal</option>
                                        <option value="st" <?= (set_value('tipo_icms', $result->tipo_icms) == 'st') ? 'selected' : '' ?>>Substituição Tributária</option>
                                        <option value="servico" <?= (set_value('tipo_icms', $result->tipo_icms) == 'servico' || set_value('tipo_icms', $result->tipo_icms) == 'Serviço') ? 'selected' : '' ?>>Serviço</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="natureza_contribuinte" class="control-label">Natureza do Contribuinte<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <select name="natureza_contribuinte" id="natureza_contribuinte" required class="span12">
                                        <option value="">Selecione</option>
                                        <option value="Contribuinte" <?= (set_value('natureza_contribuinte', $result->natureza_contribuinte) == 'Contribuinte') ? 'selected' : '' ?>>Contribuinte</option>
                                        <option value="Não Contribuinte" <?= (set_value('natureza_contribuinte', $result->natureza_contribuinte) == 'Não Contribuinte') ? 'selected' : '' ?>>Não Contribuinte</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="cfop" class="control-label">CFOP<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="cfop" type="text" name="cfop" value="<?= set_value('cfop', $result->cfop) ?>" class="span12" />
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="destinacao" class="control-label">Destinação<span class="required">*</span></label>
                                <div class="controls">
                                    <select name="destinacao" id="destinacao" required class="span12">
                                        <option value="">Selecione</option>
                                        <option value="Estadual" <?= $result->destinacao == 'Estadual' ? 'selected' : '' ?>>Estadual</option>
                                        <option value="Interestadual" <?= $result->destinacao == 'Interestadual' ? 'selected' : '' ?>>Interestadual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="objetivo_comercial" class="control-label">Objetivo Comercial<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <select name="objetivo_comercial" id="objetivo_comercial" required class="span12">
                                        <option value="">Selecione</option>
                                        <option value="Consumo" <?= $result->objetivo_comercial == 'Consumo' ? 'selected' : '' ?>>Consumo</option>
                                        <option value="Revenda" <?= $result->objetivo_comercial == 'Revenda' ? 'selected' : '' ?>>Revenda</option>
                                        <option value="Industrialização" <?= $result->objetivo_comercial == 'Industrialização' ? 'selected' : '' ?>>Industrialização</option>
                                        <option value="Orgão Público" <?= $result->objetivo_comercial == 'Orgão Público' ? 'selected' : '' ?>>Orgão Público</option>
                                    </select>
                                </div>
                            </div>

                            <?php
                                $finalidadeSelecionada = set_value('finalidade', $result->finalidade ?? 'Comercialização');
                                if ($finalidadeSelecionada === 'COMERCIALIZACAO') {
                                    $finalidadeSelecionada = 'Comercialização';
                                }
                            ?>
                            <div class="control-group">
                                <label for="finalidade" class="control-label">Finalidade<span class="required">*</span></label>
                                <div class="controls">
                                    <select name="finalidade" id="finalidade" class="span12">
                                        <?php foreach ($finalidadesFiscal as $valor => $rotulo) { ?>
                                            <option value="<?= $valor ?>" <?= $finalidadeSelecionada === $valor ? 'selected' : '' ?>>
                                                <?= $rotulo ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" name="finalidade_hidden" id="finalidade_hidden" value="<?= $finalidadeSelecionada ?>" />
                                    <span class="help-inline">Se o Tipo ICMS for "Serviço", a finalidade será forçada para Serviço.</span>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="cClassTrib" class="control-label">Classe Tributária (cClassTrib)</label>
                                <div class="controls">
                                    <input type="text" name="cClassTrib" id="cClassTrib" class="span12"
                                        value="<?= set_value('cClassTrib', $result->cClassTrib ?? '') ?>"
                                        placeholder="Ex: 0600402" />
                                    <small style="display: block; color: #666; margin-top: 2px;">Código da classe tributária do serviço (ex: 0600402 para Serviços de Comunicação Eletrônica)</small>
                                </div>
                            </div>

                            <div class="control-group">
                                <label for="mensagem_fiscal" class="control-label">Mensagem Fiscal</label>
                                <div class="controls">
                                    <textarea name="mensagem_fiscal" id="mensagem_fiscal" rows="3" class="span12"
                                        style="width: 100%"><?= set_value('mensagem_fiscal', $result->mensagem_fiscal ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação - mesmo estilo de pessoas -->
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                                    <span class="button__icon"><i class='bx bx-save'></i></span>
                                    <span class="button__text2">Salvar</span>
                                </button>
                                <a href="<?= site_url('classificacaofiscal') ?>"
                                    class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class='bx bx-undo'></i></span>
                                    <span class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
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
                },
                finalidade: {
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
                },
                finalidade: {
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

        // Controlar a exibição/valor da finalidade quando o Tipo ICMS for serviço
        function syncFinalidadeComTipoIcms() {
            var tipo = $('#tipo_icms').val();
            var isServico = (tipo === 'servico' || tipo === 'Serviço' || tipo === 'Serviço' || tipo === 'serviço');
            if (isServico) {
                $('#finalidade option[value="Serviço"]').show();
                $('#finalidade').val('Serviço');
                $('#finalidade_hidden').val('Serviço');
                $('#finalidade').prop('disabled', true);
            } else {
                $('#finalidade').prop('disabled', false);
                $('#finalidade option[value="Serviço"]').hide();
                if ($('#finalidade').val() === 'Serviço') {
                    $('#finalidade').val('Comercialização');
                    $('#finalidade_hidden').val('Comercialização');
                }
            }
        }

        // Sincronizar campo hidden quando o select mudar
        $('#finalidade').on('change', function() {
            $('#finalidade_hidden').val($(this).val());
        });

        // Antes de enviar o formulário, garantir que o valor está no campo correto
        $('#formClassificacaoFiscal').on('submit', function() {
            if ($('#finalidade').prop('disabled')) {
                // Se estiver desabilitado, usar o valor do hidden
                $('#finalidade').prop('disabled', false);
                $('#finalidade').val($('#finalidade_hidden').val());
            }
        });

        $('#tipo_icms').on('change', syncFinalidadeComTipoIcms);
        syncFinalidadeComTipoIcms();
    });
</script>
