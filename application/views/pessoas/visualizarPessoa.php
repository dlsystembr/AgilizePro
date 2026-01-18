<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<style>
    /* Seções organizadas */
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
        width: 90px;
        text-align: right;
    }

    .form-section .controls {
        margin-left: 110px;
    }

    /* Tipos de pessoa */
    #tipos-pessoa-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: flex-start;
    }

    #tipos-pessoa-container label.checkbox {
        margin-right: 0 !important;
        margin-bottom: 5px !important;
        white-space: nowrap;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .form-section-content {
            padding: 15px;
        }
    }

    /* Padronização de altura dos inputs */
    .form-section input[type="text"],
    .form-section input[type="date"],
    .form-section input[type="email"],
    .form-section input[type="number"],
    .form-section select,
    .form-section textarea {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        border: none;
        background: transparent;
        box-shadow: none;
        cursor: default;
        padding: 4px 0;
        font-weight: 500;
        color: #333;
    }

    /* Ajuste específico para o campo código */
    .form-section input#PES_CODIGO {
        width: 150px;
        max-width: 150px;
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

    /* Remover margin do Bootstrap na segunda coluna */
    .row-flex>.span5 {
        margin-left: 0 !important;
    }

    .row-flex .form-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-radius: 0;
        margin: 0;
    }

    /* Primeira seção (Dados gerais) */
    .row-flex>.span7 .form-section {
        border-right: none;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .row-flex>.span7 .form-section-header {
        border-top-left-radius: 4px;
        border-top-right-radius: 0;
    }

    /* Segunda seção (Tipos de Pessoa) */
    .row-flex>.span5 .form-section {
        border-left: 1px solid #e0e0e0;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: 0;
    }

    .row-flex>.span5 .form-section-header {
        border-top-left-radius: 0;
        border-top-right-radius: 4px;
        border-left: 1px solid #e0e0e0;
    }

    .row-flex .form-section-content {
        flex: 1;
    }

    .telefone-row,
    .email-row,
    .endereco-row,
    .documento-row,
    .vendedor-permitido-row {
        margin-bottom: 10px;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #f9f9f9;
        word-wrap: break-word;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                <h5>Visualizar Pessoa</h5>
            </div>

            <div class="widget-content nopadding tab-content form-horizontal">
                <!-- Seções lado a lado -->
                <div class="row-fluid row-flex" style="margin: 20px 0 0 0; padding: 0;">
                    <div class="span7">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-info-circle"></i>
                                <span>Dados gerais</span>
                            </div>
                            <div class="form-section-content">
                                <!-- Linha 1: Código e Situação -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span6">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Código</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->PES_CODIGO; ?>"
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Situação</label>
                                            <div class="controls">
                                                <input type="text"
                                                    value="<?php echo $result->PES_SITUACAO == '1' ? 'Ativo' : 'Inativo'; ?>"
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Linha 2: CPF/CNPJ e Nascimento/Abertura -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span7">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">CPF/CNPJ</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->PES_CPFCNPJ; ?>"
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span5">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label
                                                class="control-label"><?php echo $result->PES_FISICO_JURIDICO == 'F' ? 'Nascimento' : 'Abertura'; ?></label>
                                            <div class="controls">
                                                <input type="text"
                                                    value="<?php echo $result->PES_NASCIMENTO_ABERTURA ? date('d/m/Y', strtotime($result->PES_NASCIMENTO_ABERTURA)) : '-'; ?>"
                                                    readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Linha 3: Nome (largura total) -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span12">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Nome</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->PES_NOME; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Linha 4: Razão Social (largura total) -->
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Razão Social</label>
                                            <div class="controls">
                                                <input type="text"
                                                    value="<?php echo $result->PES_RAZAO_SOCIAL ?: '-'; ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tipos de Pessoa (Direita) -->
                    <div class="span5">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-users"></i>
                                <span>Tipos de Pessoa</span>
                            </div>
                            <div class="form-section-content">
                                <div id="tipos-pessoa-container">
                                    <?php
                                    $this->db->where('ativo', 1);
                                    $tipos_pessoa_db = $this->db->get('tipos_pessoa')->result();
                                    $tipos_vinculados_ids = array();
                                    if (isset($tipos_vinculados)) {
                                        foreach ($tipos_vinculados as $vinculo) {
                                            $tipos_vinculados_ids[] = $vinculo->TPP_ID;
                                        }
                                    }

                                    foreach ($tipos_pessoa_db as $tipo):
                                        if (in_array($tipo->id, $tipos_vinculados_ids)):
                                            ?>
                                            <span class="badge badge-info"
                                                style="margin-right: 5px; margin-bottom: 5px; padding: 5px 10px;"><?php echo $tipo->nome; ?></span>
                                        <?php
                                        endif;
                                    endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção Contatos -->
                <div class="row-fluid" style="margin-top: 20px;">
                    <div class="span6">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-phone"></i>
                                <span>Telefones</span>
                            </div>
                            <div class="form-section-content">
                                <?php if (empty($telefones))
                                    echo '<p>Nenhum telefone cadastrado.</p>'; ?>
                                <?php foreach ($telefones as $tel): ?>
                                    <div class="telefone-row">
                                        <strong><?php echo $tel->TEL_TIPO; ?>:</strong> (<?php echo $tel->TEL_DDD; ?>)
                                        <?php echo $tel->TEL_NUMERO; ?>
                                        <?php if ($tel->TEL_OBSERVACAO): ?>
                                            <div style="color: #777; font-size: 0.9em;"><?php echo $tel->TEL_OBSERVACAO; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-envelope"></i>
                                <span>Emails</span>
                            </div>
                            <div class="form-section-content">
                                <?php if (empty($emails))
                                    echo '<p>Nenhum email cadastrado.</p>'; ?>
                                <?php foreach ($emails as $email): ?>
                                    <div class="email-row">
                                        <strong><?php echo $email->EML_TIPO; ?>
                                            (<?php echo $email->EML_NOME ?: 'Geral'; ?>):</strong>
                                        <?php echo $email->EML_EMAIL; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção Endereços -->
                <div class="form-section">
                    <div class="form-section-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Endereços</span>
                    </div>
                    <div class="form-section-content">
                        <?php if (empty($enderecos))
                            echo '<p>Nenhum endereço cadastrado.</p>'; ?>
                        <?php foreach ($enderecos as $end): ?>
                            <div class="endereco-row">
                                <?php if (isset($end->END_PADRAO) && $end->END_PADRAO): ?>
                                    <span class="label label-success" style="float: right;">Padrão</span>
                                <?php endif; ?>
                                <strong><?php echo $end->END_TIPO_ENDENRECO ?: 'Geral'; ?>:</strong><br>
                                <?php echo $end->END_LOGRADOURO; ?>, <?php echo $end->END_NUMERO; ?>
                                <?php echo $end->END_COMPLEMENTO ? ' - ' . $end->END_COMPLEMENTO : ''; ?><br>
                                <?php echo $end->BAI_NOME ?: 'Bairro não inf.'; ?> -
                                <?php echo $end->MUN_NOME ?: 'Cidade não inf.'; ?>/<?php echo $end->EST_UF ?: '-'; ?> - CEP:
                                <?php echo $end->END_CEP ?: '-'; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Seção Documentos e Observação -->
                <div class="row-fluid">
                    <div class="span6">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-file-alt"></i>
                                <span>Documentos</span>
                            </div>
                            <div class="form-section-content">
                                <?php if (empty($documentos))
                                    echo '<p>Nenhum documento cadastrado.</p>'; ?>
                                <?php foreach ($documentos as $doc): ?>
                                    <div class="documento-row">
                                        <strong><?php echo $doc->DOC_TIPO_DOCUMENTO; ?>:</strong>
                                        <?php echo $doc->DOC_NUMERO; ?><br>
                                        <small>Expedidor: <?php echo $doc->DOC_ORGAO_EXPEDIDOR ?: '-'; ?> | Natureza:
                                            <?php echo $doc->DOC_NATUREZA_CONTRIBUINTE ?: '-'; ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-sticky-note"></i>
                                <span>Observação</span>
                            </div>
                            <div class="form-section-content">
                                <p><?php echo $result->PES_OBSERVACAO ? nl2br($result->PES_OBSERVACAO) : 'Nenhuma observação.'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção Cliente -->
                <?php if (isset($cliente) && $cliente): ?>
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="fas fa-user-tag"></i>
                            <span>Cliente</span>
                        </div>
                        <div class="form-section-content">
                            <div class="row-fluid">
                                <div class="span4">
                                    <p><strong>Limite de Crédito:</strong> R$
                                        <?php echo number_format($cliente->CLN_LIMITE_CREDITO, 2, ',', '.'); ?></p>
                                    <p><strong>Dias de Carência:</strong> <?php echo $cliente->CLN_DIAS_CARENCIA; ?></p>
                                    <p><strong>Situação:</strong>
                                        <?php echo $cliente->CLN_SITUACAO == 1 ? 'Ativo' : 'Inativo'; ?></p>
                                </div>
                                <div class="span4">
                                    <p><strong>Comprar a Prazo:</strong>
                                        <?php echo $cliente->CLN_COMPRAR_APRAZO ? 'Sim' : 'Não'; ?></p>
                                    <p><strong>Bloqueio Financeiro:</strong>
                                        <?php echo $cliente->CLN_BLOQUEIO_FINANCEIRO ? 'Sim' : 'Não'; ?></p>
                                    <p><strong>Emitir NFe:</strong> <?php echo $cliente->CLN_EMITIR_NFE ? 'Sim' : 'Não'; ?>
                                    </p>
                                    <p><strong>Objetivo Comercial:</strong>
                                        <?php echo $cliente->CLN_OBJETIVO_COMERCIAL ?: '-'; ?></p>
                                </div>
                                <div class="span4">
                                    <strong>Vendedores Permitidos:</strong>
                                    <?php if (empty($vendedores_permitidos))
                                        echo '<p>Nenhum vendedor permitido.</p>'; ?>
                                    <ul>
                                        <?php foreach ($vendedores_permitidos as $vp): ?>
                                            <li><?php echo $vp->VEN_NOME; ?>
                                                <?php echo $vp->CLV_PADRAO ? '<span class="label label-info">Padrão</span>' : ''; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Seção Vendedor -->
                <?php if (isset($vendedor) && $vendedor): ?>
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="fas fa-user-tie"></i>
                            <span>Vendedor</span>
                        </div>
                        <div class="form-section-content">
                            <div class="row-fluid">
                                <div class="span4">
                                    <p><strong>Comissão:</strong>
                                        <?php echo number_format($vendedor->VEN_PERCENTUAL_COMISSAO, 2, ',', '.'); ?>%</p>
                                </div>
                                <div class="span4">
                                    <p><strong>Tipo de Comissão:</strong> <?php echo $vendedor->VEN_TIPO_COMISSAO ?: '-'; ?>
                                    </p>
                                </div>
                                <div class="span4">
                                    <p><strong>Meta Mensal:</strong> R$
                                        <?php echo number_format($vendedor->VEN_META_MENSAL, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display: flex;justify-content: center; gap: 10px;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePessoa')) { ?>
                                <a href="<?php echo base_url() ?>index.php/pessoas/editar/<?php echo $result->PES_ID; ?>"
                                    class="button btn btn-mini btn-info" style="max-width: 160px">
                                    <span class="button__icon"><i class='bx bx-edit'></i></span>
                                    <span class="button__text2">Editar</span>
                                </a>
                            <?php } ?>
                            <a href="<?php echo base_url() ?>index.php/pessoas" class="button btn btn-mini btn-warning">
                                <span class="button__icon"><i class="bx bx-undo"></i></span>
                                <span class="button__text2">Voltar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>