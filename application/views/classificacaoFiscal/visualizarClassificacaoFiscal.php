<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<style>
    /* Estilos organizados para visualização de Classificação Fiscal */
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
        font-weight: bold;
    }

    .form-section .controls {
        margin-left: 140px;
    }

    /* Garantir que inputs não estourem o container */
    .form-section input[type="text"],
    .form-section input[type="date"],
    .form-section select,
    .form-section textarea {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        height: 30px;
        padding: 4px 8px;
        line-height: 20px;
        font-size: 14px;
        border: 1px solid #ddd;
        background-color: #f5f5f5;
        cursor: default;
    }

    .form-section textarea {
        height: auto;
        resize: vertical;
        min-height: 80px;
    }

    /* Flexbox para igualar alturas das seções lado a lado */
    .row-flex {
        display: flex;
        align-items: stretch;
        gap: 0;
    }

    .row-flex>[class*="span"] {
        display: flex;
        flex-direction: column;
        padding: 0 !important;
    }

    .row-flex .form-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-radius: 0;
        margin: 0;
    }

    /* Primeira seção */
    .row-flex>.span6:first-child .form-section {
        border-right: none;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .row-flex>.span6:first-child .form-section-header {
        border-top-left-radius: 4px;
        border-top-right-radius: 0;
    }

    /* Segunda seção */
    .row-flex>.span6:last-child .form-section {
        border-left: 1px solid #e0e0e0;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: 0;
    }

    .row-flex>.span6:last-child .form-section-header {
        border-top-left-radius: 0;
        border-top-right-radius: 4px;
        border-left: 1px solid #e0e0e0;
    }

    .row-flex .form-section-content {
        flex: 1;
    }

    /* Campos obrigatórios */
    .required {
        color: #b94a48;
        font-weight: bold;
        margin-left: 2px;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .form-section-content {
            padding: 15px;
        }

        .form-section .control-label {
            width: 100px;
            text-align: left;
            margin-bottom: 5px;
        }

        .form-section .controls {
            margin-left: 0;
        }

        .row-flex {
            flex-direction: column;
        }

        .row-flex>[class*="span"] {
            margin-left: 0 !important;
            margin-bottom: 20px;
        }

        .row-flex .form-section {
            border-radius: 4px !important;
            border-left: 1px solid #e0e0e0 !important;
            border-right: 1px solid #e0e0e0 !important;
        }
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-eye"></i>
                </span>
                <h5>Visualizar Classificação Fiscal</h5>
            </div>

            <div class="widget-content nopadding tab-content form-horizontal">
                <!-- Seções lado a lado -->
                <div class="row-fluid row-flex" style="margin: 20px 0 0 0; padding: 0;">
                    <!-- Dados Principais (Esquerda) -->
                    <div class="span6">
                        <div class="form-section" style="height: 100%;">
                            <div class="form-section-header">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Dados da Classificação</span>
                            </div>
                            <div class="form-section-content">
                                <div class="control-group">
                                    <label class="control-label">Operação Comercial</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->nome_operacao ?: '-' ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Tipo de Cliente</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->nome_tipo_cliente ?: 'Não informado' ?>" readonly />
                                    </div>
                                </div>

                                <?php if ($this->data['regime_tributario'] === 'Simples Nacional') { ?>
                                    <div class="control-group">
                                        <label class="control-label">CSOSN</label>
                                        <div class="controls">
                                            <input type="text" value="<?= $result->csosn ?: 'Não informado' ?>" readonly />
                                        </div>
                                    </div>
                                <?php } else { ?>
                                    <div class="control-group">
                                        <label class="control-label">CST</label>
                                        <div class="controls">
                                            <input type="text" value="<?= $result->cst ?: 'Não informado' ?>" readonly />
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="control-group">
                                    <label class="control-label">Tipo ICMS</label>
                                    <div class="controls">
                                        <input type="text" value="<?php
                                            $tipo_icms_display = $result->tipo_icms ?? 'normal';
                                            if ($tipo_icms_display == 'normal' || $tipo_icms_display == 'ICMS Normal') {
                                                echo 'ICMS Normal';
                                            } elseif ($tipo_icms_display == 'st' || $tipo_icms_display == 'Substituição Tributaria') {
                                                echo 'Substituição Tributária (ST)';
                                            } elseif ($tipo_icms_display == 'Serviço') {
                                                echo 'Serviço';
                                            } else {
                                                echo $tipo_icms_display;
                                            }
                                        ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Natureza do Contribuinte</label>
                                    <div class="controls">
                                        <input type="text" value="<?php
                                            $natureza = $result->natureza_contribuinte;
                                            if ($natureza == 'Contribuinte' || $natureza == 'inscrito') {
                                                echo 'Contribuinte';
                                            } elseif ($natureza == 'Não Contribuinte' || $natureza == 'nao_inscrito') {
                                                echo 'Não Contribuinte';
                                            } else {
                                                echo $natureza ?: 'Não informado';
                                            }
                                        ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">CFOP</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->cfop ?: 'Não informado' ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Destinação</label>
                                    <div class="controls">
                                        <input type="text" value="<?php
                                            $destinacao = $result->destinacao;
                                            if ($destinacao == 'Estadual' || $destinacao == 'estadual') {
                                                echo 'Estadual';
                                            } elseif ($destinacao == 'Interestadual' || $destinacao == 'interestadual') {
                                                echo 'Interestadual';
                                            } else {
                                                echo $destinacao ?: 'Não informado';
                                            }
                                        ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Objetivo Comercial</label>
                                    <div class="controls">
                                        <input type="text" value="<?php
                                            $objetivo = $result->objetivo_comercial;
                                            if ($objetivo == 'Consumo' || $objetivo == 'consumo') {
                                                echo 'Consumo';
                                            } elseif ($objetivo == 'Revenda' || $objetivo == 'revenda') {
                                                echo 'Revenda';
                                            } elseif ($objetivo == 'Industrialização' || $objetivo == 'industrializacao') {
                                                echo 'Industrialização';
                                            } elseif ($objetivo == 'Orgão Público' || $objetivo == 'orgao publico') {
                                                echo 'Orgão Público';
                                            } else {
                                                echo $objetivo ?: 'Não informado';
                                            }
                                        ?>" readonly />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais (Direita) -->
                    <div class="span6">
                        <div class="form-section" style="height: 100%;">
                            <div class="form-section-header">
                                <i class="fas fa-info-circle"></i>
                                <span>Informações Adicionais</span>
                            </div>
                            <div class="form-section-content">
                                <div class="control-group">
                                    <label class="control-label">Mensagem Fiscal</label>
                                    <div class="controls">
                                        <textarea readonly style="width: 100%; resize: vertical; box-sizing: border-box;"><?php echo $result->mensagem_fiscal ?: 'Não informado'; ?></textarea>
                                    </div>
                                </div>

                                <?php if (isset($result->cClassTrib) && $result->cClassTrib): ?>
                                <div class="control-group">
                                    <label class="control-label">Classe Tributária</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->cClassTrib ?>" readonly />
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($result->cst_ibs) && $result->cst_ibs): ?>
                                <div class="control-group">
                                    <label class="control-label">CST IBS</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->cst_ibs ?>" readonly />
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($result->aliq_ibs) && $result->aliq_ibs): ?>
                                <div class="control-group">
                                    <label class="control-label">Alíquota IBS (%)</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->aliq_ibs ?>%" readonly />
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($result->cst_cbs) && $result->cst_cbs): ?>
                                <div class="control-group">
                                    <label class="control-label">CST CBS</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->cst_cbs ?>" readonly />
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($result->aliq_cbs) && $result->aliq_cbs): ?>
                                <div class="control-group">
                                    <label class="control-label">Alíquota CBS (%)</label>
                                    <div class="controls">
                                        <input type="text" value="<?= $result->aliq_cbs ?>%" readonly />
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="control-group">
                                    <label class="control-label">Data de Criação</label>
                                    <div class="controls">
                                        <input type="text" value="<?php echo $result->created_at ? date('d/m/Y H:i:s', strtotime($result->created_at)) : 'Não informado'; ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Última Alteração</label>
                                    <div class="controls">
                                        <input type="text" value="<?php echo $result->updated_at ? date('d/m/Y H:i:s', strtotime($result->updated_at)) : 'Nunca alterado'; ?>" readonly />
                                    </div>
                                </div>

                                <div class="control-group">
                                    <label class="control-label">Situação</label>
                                    <div class="controls">
                                        <span class="badge badge-success">Ativa</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display: flex; justify-content: center;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eClassificacaoFiscal')) { ?>
                                <a href="<?php echo base_url() ?>index.php/classificacaofiscal/editar/<?php echo $result->id; ?>" class="button btn btn-success" style="margin-right: 10px;">
                                    <span class="button__icon"><i class="bx bx-edit-alt"></i></span>
                                    <span class="button__text2">Editar</span>
                                </a>
                            <?php } ?>
                            <a href="<?php echo base_url() ?>index.php/classificacaofiscal" class="button btn btn-warning">
                                <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                                <span class="button__text2">Voltar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>