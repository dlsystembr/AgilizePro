<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<style>
    /* Estilos globais para simular a tela de edição */
    .form-section {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 20px;
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .form-section-header {
        background: #f8f9fa;
        border-bottom: 1px solid #ddd;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-section-content {
        padding: 15px;
    }

    /* Alinhamento dos campos exatamente como na edição */
    .form-section .control-label {
        width: 90px !important;
        text-align: right;
        font-weight: bold;
    }

    .form-section .controls {
        margin-left: 110px !important;
    }

    .row-flex {
        display: flex;
        align-items: stretch;
        gap: 0;
    }

    .row-flex>[class*="span"] {
        display: flex;
        flex-direction: column;
        padding: 0 !important;
        margin: 0 !important;
        width: auto !important; /* Forçar preenchimento flexível */
    }

    .row-flex>.span7 {
        flex: 7;
        width: auto !important;
    }

    .row-flex>.span5 {
        flex: 5;
        width: auto !important;
    }

    .row-flex>.span6 {
        flex: 1;
        width: auto !important;
    }

    /* Regras para unir seções lado a lado */
    .row-flex .form-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-radius: 0;
        margin: 0;
    }

    /* Primeira seção da dupla (Esquerda) */
    .row-flex>[class*="span"]:first-child .form-section {
        border-right: none;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }

    /* Segunda seção da dupla (Direita) */
    .row-flex>[class*="span"]:last-child .form-section {
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
    }

    /* Ajuste para o conteúdo do cabeçalho da seção unida */
    .row-flex>[class*="span"]:first-child .form-section-header {
        border-top-right-radius: 0;
    }

    .row-flex>[class*="span"]:last-child .form-section-header {
        border-top-left-radius: 0;
    }

    /* Ajustes para inputs no estilo do formulário */
    .form-section input:not([type="checkbox"]):not([type="radio"]),
    .form-section select,
    .form-section textarea {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        height: 30px !important; /* Altura padrão da edição */
        padding: 4px 8px !important;
        line-height: 20px !important;
        font-size: 14px !important;
        border: 1px solid #ccc !important;
        background-color: #fff !important;
        cursor: default !important;
        box-shadow: none !important;
        margin-bottom: 0 !important;
    }

    .form-section textarea {
        height: auto !important;
    }

    .form-section .control-group {
        margin-bottom: 10px !important;
    }

    .required {
        color: #b94a48;
    }

    /* Garantir que as seções tenham margens consistentes com a edição */
    .row-fluid.row-flex {
        margin: 0 !important;
        width: 100% !important;
    }

    .widget-content.nopadding {
        padding: 0 !important;
    }

    .widget-content.form-horizontal {
        padding: 0 !important;
    }

    /* Forçar labels e controles para não terem espaços extras */
    .form-horizontal .control-label {
        padding-top: 5px !important;
        width: 90px !important;
    }

    .form-horizontal .controls {
        margin-left: 110px !important;
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
                <!-- Seções lado a lado: Dados Gerais e Tipos de Pessoa -->
                <div class="row-fluid row-flex" style="margin: 20px 0 0 0; padding: 0;">
                    <div class="span7">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-edit"></i>
                                <span>Dados gerais</span>
                            </div>
                            <div class="form-section-content">
                                <!-- Linha 1: Código e Situação -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span6">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Código</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->PES_CODIGO; ?>" readonly
                                                    style="width: 150px;" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Situação</label>
                                            <div class="controls">
                                                <select disabled class="span12">
                                                    <option selected>
                                                        <?php echo $result->PES_SITUACAO == '1' ? 'Ativo' : 'Inativo'; ?>
                                                    </option>
                                                </select>
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
                                                <input type="text" value="<?php echo $result->PES_CPFCNPJ; ?>" readonly
                                                    class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span5">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label
                                                class="control-label"><?php echo $result->PES_FISICO_JURIDICO == 'F' ? 'Nascimento' : 'Abertura'; ?></label>
                                            <div class="controls">
                                                <input type="date"
                                                    value="<?php echo $result->PES_NASCIMENTO_ABERTURA; ?>" readonly
                                                    class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Linha 3: Nome -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span12">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Nome</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->PES_NOME; ?>" readonly
                                                    class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Linha 4: Razão Social -->
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Razão Social</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->PES_RAZAO_SOCIAL; ?>"
                                                    readonly class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="span5">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-users"></i>
                                <span>Tipos de Pessoa</span>
                            </div>
                            <div class="form-section-content" style="background-color: #f9f9f9; flex: 1;">
                                <?php
                                $this->db->where('ativo', 1);
                                $this->db->order_by('nome', 'ASC');
                                $tipos_pessoa_db = $this->db->get('tipos_pessoa')->result();

                                $tipos_vinculados_ids = array();
                                if (isset($tipos_vinculados)) {
                                    foreach ($tipos_vinculados as $vinculo) {
                                        $tipos_vinculados_ids[] = $vinculo->TPP_ID;
                                    }
                                }

                                foreach ($tipos_pessoa_db as $tipo):
                                    $checked = in_array($tipo->id, $tipos_vinculados_ids) ? 'checked' : '';
                                    if (!$checked) {
                                        if (strtolower($tipo->nome) == 'cliente' && isset($cliente) && $cliente)
                                            $checked = 'checked';
                                        elseif (strtolower($tipo->nome) == 'vendedor' && isset($vendedor) && $vendedor)
                                            $checked = 'checked';
                                    }
                                    ?>
                                    <label class="checkbox" style="display:block; margin-bottom:8px; width: 100%;">
                                        <input type="checkbox" <?php echo $checked; ?> disabled
                                            style="margin-right: 8px;" />
                                        <?php echo $tipo->nome; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contatos -->
                <div class="row-fluid row-flex">
                    <div class="span6">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-phone"></i>
                                <span>Telefones</span>
                            </div>
                            <div class="form-section-content">
                                <?php if (empty($telefones)): ?>
                                    <p class="text-muted">Nenhum telefone cadastrado.</p>
                                <?php else: ?>
                                    <?php foreach ($telefones as $tel): ?>
                                        <div class="row-fluid" style="margin-bottom: 10px; display: flex; gap: 5px;">
                                            <select disabled style="width: 120px; flex-shrink: 0;">
                                                <option selected><?php echo $tel->TEL_TIPO; ?></option>
                                            </select>
                                            <input type="text" readonly style="width: 50px; flex-shrink: 0;" value="<?php echo $tel->TEL_DDD; ?>" placeholder="DDD">
                                            <input type="text" readonly style="flex: 1;" value="<?php echo $tel->TEL_NUMERO; ?>" placeholder="Número">
                                            <input type="text" readonly style="flex: 1;" value="<?php echo $tel->TEL_OBSERVACAO; ?>" placeholder="Observação">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                                <?php if (empty($emails)): ?>
                                    <p class="text-muted">Nenhum email cadastrado.</p>
                                <?php else: ?>
                                    <?php foreach ($emails as $email): ?>
                                        <div class="row-fluid" style="margin-bottom: 10px; display: flex; gap: 5px;">
                                            <select disabled style="width: 120px; flex-shrink: 0;">
                                                <option selected><?php echo $email->EML_TIPO; ?></option>
                                            </select>
                                            <input type="text" readonly style="flex: 1;" value="<?php echo $email->EML_NOME ?: 'Geral'; ?>" placeholder="Nome">
                                            <input type="text" readonly style="flex: 1.5;" value="<?php echo $email->EML_EMAIL; ?>" placeholder="Email">
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Endereços -->
                <div class="form-section">
                    <div class="form-section-header">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Endereços</span>
                    </div>
                    <div class="form-section-content">
                        <?php if (empty($enderecos)): ?>
                            <p class="text-muted" style="padding: 10px;">Nenhum endereço cadastrado.</p>
                        <?php else: ?>
                            <?php foreach ($enderecos as $end): ?>
                                <div class="endereco-entry" style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px dashed #eee;">
                                    <div class="row-fluid" style="margin-bottom: 5px; display: flex; gap: 5px;">
                                        <input type="text" readonly style="flex: 3;" value="<?php echo $end->END_LOGRADOURO; ?>" placeholder="Logradouro">
                                        <input type="text" readonly style="width: 80px; flex-shrink: 0;" value="<?php echo $end->END_NUMERO; ?>" placeholder="Número">
                                        <input type="text" readonly style="flex: 2;" value="<?php echo $end->END_COMPLEMENTO; ?>" placeholder="Complemento">
                                    </div>
                                    <div class="row-fluid" style="margin-bottom: 5px; display: flex; gap: 5px;">
                                        <input type="text" readonly style="width: 100px; flex-shrink: 0;" value="<?php echo $end->END_CEP; ?>" placeholder="CEP">
                                        <input type="text" readonly style="flex: 1;" value="<?php echo $end->BAI_NOME; ?>" placeholder="Bairro">
                                        <input type="text" readonly style="flex: 1;" value="<?php echo $end->MUN_NOME; ?>" placeholder="Cidade">
                                        <input type="text" readonly style="width: 40px; flex-shrink: 0;" value="<?php echo $end->EST_UF; ?>" placeholder="UF">
                                    </div>
                                    <div class="row-fluid" style="display: flex; gap: 10px; align-items: center;">
                                        <select disabled style="width: 150px; flex-shrink: 0;">
                                            <option selected><?php echo $end->END_TIPO_ENDENRECO ?: 'Geral'; ?></option>
                                        </select>
                                        <label class="checkbox" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                                            <input type="checkbox" disabled <?php echo (isset($end->END_PADRAO) && $end->END_PADRAO) ? 'checked' : ''; ?> />
                                            <strong>Endereço Padrão</strong>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Documentos e Observação -->
                <div class="row-fluid row-flex">
                    <div class="span6">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-file-alt"></i>
                                <span>Documentos</span>
                            </div>
                            <div class="form-section-content">
                                <?php if (empty($documentos)): ?>
                                    <p class="text-muted" style="padding: 10px;">Nenhum documento cadastrado.</p>
                                <?php else: ?>
                                    <?php foreach ($documentos as $doc): ?>
                                        <div class="documento-entry" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #eee;">
                                            <div class="row-fluid" style="display: flex; gap: 5px; margin-bottom: <?php echo $doc->DOC_ENDE_IDX !== null ? '5px' : '0'; ?>;">
                                                <select disabled style="width: 150px; flex-shrink: 0;">
                                                    <option selected><?php echo $doc->DOC_TIPO_DOCUMENTO; ?></option>
                                                </select>
                                                <input type="text" readonly style="flex: 1;" value="<?php echo $doc->DOC_NUMERO; ?>" placeholder="Número">
                                                <input type="text" readonly style="flex: 1;" value="<?php echo $doc->DOC_ORGAO_EXPEDIDOR; ?>" placeholder="Órgão Expedidor">
                                                <select disabled style="width: 120px; flex-shrink: 0;">
                                                    <option selected><?php echo $doc->DOC_NATUREZA_CONTRIBUINTE ?: 'Natureza'; ?></option>
                                                </select>
                                            </div>
                                            <?php if ($doc->DOC_ENDE_IDX !== null): ?>
                                            <div class="row-fluid" style="display: flex; gap: 10px; align-items: center;">
                                                <label style="margin: 0; white-space: nowrap; font-size: 12px; color: #666;">Vincular ao endereço:</label>
                                                <select disabled style="flex: 1;">
                                                    <?php
                                                    if (isset($enderecos[$doc->DOC_ENDE_IDX])) {
                                                        $e = $enderecos[$doc->DOC_ENDE_IDX];
                                                        echo "<option selected>{$e->END_TIPO_ENDENRECO} - {$e->END_LOGRADOURO}, {$e->END_NUMERO}</option>";
                                                    } else {
                                                        echo "<option selected>Endereço vinculado</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
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
                                <textarea readonly rows="6"
                                    style="width:100%; resize: vertical; box-sizing: border-box;"><?php echo $result->PES_OBSERVACAO; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cliente -->
                <?php if (isset($cliente) && $cliente): ?>
                    <div class="form-section" style="margin-top: 30px;">
                        <div class="form-section-header">
                            <i class="fas fa-user-tag"></i>
                            <span>Dados de Cliente</span>
                        </div>
                        <div class="form-section-content">
                            <div class="row-fluid">
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Limite de Crédito</label>
                                        <div class="controls">
                                            <input type="text" readonly
                                                value="<?php echo number_format($cliente->CLN_LIMITE_CREDITO, 2, ',', '.'); ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Dias de Carência</label>
                                        <div class="controls">
                                            <input type="text" readonly value="<?php echo $cliente->CLN_DIAS_CARENCIA; ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Situação</label>
                                        <div class="controls">
                                            <select disabled class="span12">
                                                <option selected>
                                                    <?php echo $cliente->CLN_SITUACAO == 1 ? 'Ativo' : 'Inativo'; ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Opções</label>
                                        <div class="controls">
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo $cliente->CLN_COMPRAR_APRAZO ? 'checked' : ''; ?>> Comprar a prazo
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo $cliente->CLN_BLOQUEIO_FINANCEIRO ? 'checked' : ''; ?>> Bloqueio financeiro
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo $cliente->CLN_EMITIR_NFE ? 'checked' : ''; ?>> Emitir NFe
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Vendedores Permitidos</label>
                                        <div class="controls" style="margin-left: 0;">
                                            <?php if (empty($vendedores_permitidos)): ?>
                                                <p class="text-muted">Nenhum vendedor vinculado.</p>
                                            <?php else: ?>
                                                <?php foreach ($vendedores_permitidos as $vp): ?>
                                                    <div
                                                        style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                                                        <select disabled class="span12" style="font-size: 12px;">
                                                            <option selected><?php echo $vp->VEN_NOME; ?></option>
                                                        </select>
                                                        <label class="checkbox" style="margin: 0; white-space: nowrap;">
                                                            <input type="radio" disabled <?php echo $vp->CLV_PADRAO ? 'checked' : ''; ?>>
                                                            <small>Padrão</small>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row-fluid" style="margin-top: 20px;">
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Objetivo Comercial</label>
                                        <div class="controls">
                                            <select disabled class="span12">
                                                <option selected>
                                                    <?php echo $cliente->CLN_OBJETIVO_COMERCIAL ?: 'Consumo'; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Vendedor -->
                <?php if (isset($vendedor) && $vendedor): ?>
                    <div class="form-section" style="margin-top: 30px;">
                        <div class="form-section-header">
                            <i class="fas fa-user-tie"></i>
                            <span>Dados de Vendedor</span>
                        </div>
                        <div class="form-section-content">
                            <div class="row-fluid">
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Percentual de Comissão (%)</label>
                                        <div class="controls">
                                            <input type="text" readonly
                                                value="<?php echo number_format($vendedor->VEN_PERCENTUAL_COMISSAO, 2, ',', '.'); ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Tipo de Comissão</label>
                                        <div class="controls">
                                            <select disabled class="span12">
                                                <option selected><?php echo $vendedor->VEN_TIPO_COMISSAO ?: 'Selecione'; ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Meta Mensal (R$)</label>
                                        <div class="controls">
                                            <input type="text" readonly
                                                value="<?php echo number_format($vendedor->VEN_META_MENSAL, 2, ',', '.'); ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Ações -->
                <div class="form-actions" style="margin: 0; padding: 20px; text-align: center;">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePessoa')): ?>
                        <a href="<?php echo base_url('index.php/pessoas/editar/' . $result->PES_ID); ?>" class="btn btn-info" style="margin: 0 5px;">
                            <i class='bx bx-edit'></i> Editar
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo base_url('index.php/pessoas'); ?>" class="btn btn-warning" style="margin: 0 5px;">
                        <i class="bx bx-undo"></i> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>