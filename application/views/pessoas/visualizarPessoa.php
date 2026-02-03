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
        min-width: 115px !important;
        width: auto !important;
        text-align: right;
        white-space: nowrap;
        font-weight: bold;
    }

    .form-section .controls {
        margin-left: 125px !important;
    }

    /* Campo Código – espaço para exibir */
    .form-section-content .linha-1-dados .span4:first-child .controls input {
        min-width: 90px;
        max-width: 180px;
        width: 100%;
    }

    /* Reduzir espaço entre Abertura e Situação na 1ª linha */
    .linha-1-dados .span4:nth-child(2) { padding-right: 4px; }
    .linha-1-dados .span4:nth-child(3) { padding-left: 4px; }

    .row-flex {
        display: flex;
        align-items: stretch;
        gap: 0;
    }

    /* Interruptor Ativo/Inativo – compacto, sutil, texto centralizado (somente leitura) */
    .switch-interruptor {
        position: relative;
        display: inline-block;
        width: 68px;
        height: 24px;
        flex-shrink: 0;
    }
    .switch-interruptor input[type="checkbox"] {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .switch-interruptor .slider {
        position: absolute;
        cursor: default;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #c45c5c;
        border-radius: 24px;
        transition: .22s ease;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.08);
    }
    .switch-interruptor .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: #fff;
        border-radius: 50%;
        transition: .22s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,.18);
    }
    .switch-interruptor .slider .switch-label {
        position: absolute;
        top: 0;
        bottom: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        color: #fff;
        text-shadow: 0 1px 1px rgba(0,0,0,.12);
        pointer-events: none;
        letter-spacing: .02em;
    }
    .switch-interruptor .slider .switch-label { left: 24px; right: 3px; }
    .switch-interruptor input:checked + .slider .switch-label { left: 3px; right: 24px; }
    .switch-interruptor input:checked + .slider {
        background-color: #5a9f5a;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.08);
    }
    .switch-interruptor input:checked + .slider:before {
        transform: translateX(44px);
    }
    .switch-interruptor-disabled .slider {
        cursor: default;
        opacity: .95;
    }

    .row-flex>[class*="span"] {
        display: flex;
        flex-direction: column;
        padding: 0 !important;
        margin: 0 !important;
        width: auto !important;
    }

    /* Proporções iguais ao editar/adicionar: Dados gerais (8) | Tipos de Pessoa (4) */
    .row-flex>.span8 {
        flex: 8;
        width: auto !important;
        margin-left: 0 !important;
    }

    .row-flex>.span4 {
        flex: 4;
        width: auto !important;
        margin-left: 0 !important;
    }

    .row-flex>.span6 {
        flex: 1;
        width: auto !important;
    }

    .row-flex .form-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-radius: 0;
        margin: 0;
        height: 100%;
    }

    /* Primeira seção (Dados gerais) – igual ao editar */
    .row-flex>.span8 .form-section {
        border-right: none;
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }

    .row-flex>.span8 .form-section-header {
        border-top-left-radius: 4px;
        border-top-right-radius: 0;
    }

    /* Segunda seção (Tipos de Pessoa) – igual ao editar */
    .row-flex>.span4 .form-section {
        border-left: 1px solid #e0e0e0;
        border-top-right-radius: 4px;
        border-bottom-right-radius: 4px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        margin-left: 0;
    }

    .row-flex>.span4 .form-section-header {
        border-top-right-radius: 4px;
        border-top-left-radius: 0;
        border-left: 1px solid #e0e0e0;
    }

    /* Tipos de Pessoa: evitar corte dos labels (ex.: Funcionário) */
    .row-flex>.span4 .form-section-content {
        overflow: visible;
        padding: 15px 20px;
    }

    .row-flex>.span4 .form-section-content label.checkbox {
        overflow: visible;
        white-space: nowrap;
        flex: 0 0 auto;
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        width: auto;
        min-width: 0;
    }

    .row-flex>.span4 .form-section-content label.checkbox input {
        flex-shrink: 0;
        margin-right: 8px;
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

    /* Labels na seção Dados gerais (igual ao editar): não sobrescrever por form-horizontal */
    .form-section .control-label {
        padding-top: 5px !important;
        min-width: 115px !important;
        width: auto !important;
    }

    .form-section .controls {
        margin-left: 125px !important;
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
                    <div class="span8">
                        <div class="form-section">
                            <div class="form-section-header">
                                <i class="fas fa-edit"></i>
                                <span>Dados gerais</span>
                            </div>
                            <div class="form-section-content">
                                <!-- 1ª Linha: Código (mais espaço) | Abertura (menos) | Situação (menos espaço entre) -->
                                <div class="row-fluid linha-1-dados" style="margin-bottom: 15px;">
                                    <div class="span4">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Código</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->pes_codigo; ?>" readonly
                                                    class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label"><?php echo $result->pes_fisico_juridico == 'F' ? 'Nascimento' : 'Abertura'; ?></label>
                                            <div class="controls">
                                                <input type="date"
                                                    value="<?php echo $result->pes_nascimento_abertura; ?>" readonly
                                                    class="span12" style="min-width: 120px;" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Situação</label>
                                            <div class="controls">
                                                <label class="switch-interruptor switch-interruptor-disabled">
                                                    <input type="checkbox" disabled <?php echo $result->pes_situacao == '1' ? 'checked' : ''; ?> />
                                                    <span class="slider"><span class="switch-label"><?php echo $result->pes_situacao == '1' ? 'Ativo' : 'Inativo'; ?></span></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2ª Linha: CPF/CNPJ | Regime Tributário (50/50) -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span6">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">CPF/CNPJ</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->pes_cpfcnpj; ?>" readonly
                                                    class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Regime Tributário</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo isset($result->pes_regime_tributario) && $result->pes_regime_tributario !== '' && $result->pes_regime_tributario !== null ? htmlspecialchars($result->pes_regime_tributario) : '-'; ?>" readonly class="span12" style="width: 100%;" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3ª Linha: Nome -->
                                <div class="row-fluid" style="margin-bottom: 15px;">
                                    <div class="span12">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Nome</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->pes_nome; ?>" readonly
                                                    class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4ª Linha: Razão Social -->
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="control-group" style="margin-bottom: 0;">
                                            <label class="control-label">Razão Social</label>
                                            <div class="controls">
                                                <input type="text" value="<?php echo $result->pes_razao_social; ?>"
                                                    readonly class="span12" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="span4">
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
                                                <option selected><?php echo $tel->tel_tipo; ?></option>
                                            </select>
                                            <input type="text" readonly style="width: 50px; flex-shrink: 0;" value="<?php echo $tel->tel_ddd; ?>" placeholder="DDD">
                                            <input type="text" readonly style="flex: 1;" value="<?php echo $tel->tel_numero; ?>" placeholder="Número">
                                            <input type="text" readonly style="flex: 1;" value="<?php echo $tel->tel_observacao; ?>" placeholder="Observação">
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
                                                <option selected><?php echo $email->eml_tipo; ?></option>
                                            </select>
                                            <input type="text" readonly style="flex: 1;" value="<?php echo $email->eml_nome ?: 'Geral'; ?>" placeholder="Nome">
                                            <input type="text" readonly style="flex: 1.5;" value="<?php echo $email->eml_email; ?>" placeholder="Email">
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
                                        <input type="text" readonly style="flex: 3;" value="<?php echo $end->end_logradouro; ?>" placeholder="Logradouro">
                                        <input type="text" readonly style="width: 80px; flex-shrink: 0;" value="<?php echo $end->end_numero; ?>" placeholder="Número">
                                        <input type="text" readonly style="flex: 2;" value="<?php echo $end->end_complemento; ?>" placeholder="Complemento">
                                    </div>
                                    <div class="row-fluid" style="margin-bottom: 5px; display: flex; gap: 5px;">
                                        <input type="text" readonly style="width: 100px; flex-shrink: 0;" value="<?php echo $end->end_cep; ?>" placeholder="cep">
                                        <input type="text" readonly style="flex: 1;" value="<?php echo $end->bai_nome; ?>" placeholder="Bairro">
                                        <input type="text" readonly style="flex: 1;" value="<?php echo $end->mun_nome; ?>" placeholder="Cidade">
                                        <input type="text" readonly style="width: 40px; flex-shrink: 0;" value="<?php echo $end->est_uf; ?>" placeholder="uf">
                                    </div>
                                    <div class="row-fluid" style="display: flex; gap: 10px; align-items: center;">
                                        <select disabled style="width: 150px; flex-shrink: 0;">
                                            <option selected><?php echo $end->end_tipo_endenreco ?: 'Geral'; ?></option>
                                        </select>
                                        <span style="white-space: nowrap; margin-right: 6px;">End. padrão</span>
                                        <label class="switch-interruptor switch-interruptor-disabled" style="margin: 0;">
                                            <input type="checkbox" disabled <?php echo (isset($end->end_padrao) && $end->end_padrao) ? 'checked' : ''; ?> />
                                            <span class="slider"><span class="switch-label"><?php echo (isset($end->end_padrao) && $end->end_padrao) ? 'Sim' : 'Não'; ?></span></span>
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
                                <?php
                                $doc_tipo_ie = 'Inscrição Estadual';
                                $mostrar_ie = (isset($result->pes_regime_tributario) && $result->pes_regime_tributario === 'Regime Normal');
                                $documentos_visiveis = array_filter($documentos, function ($doc) use ($doc_tipo_ie, $mostrar_ie) {
                                    $e_ie = (isset($doc->doc_tipo_documento) && trim($doc->doc_tipo_documento) === $doc_tipo_ie);
                                    if ($e_ie) {
                                        return $mostrar_ie;
                                    }
                                    return true;
                                });
                                ?>
                                <?php if (empty($documentos_visiveis)): ?>
                                    <p class="text-muted" style="padding: 10px;">Nenhum documento cadastrado.</p>
                                <?php else: ?>
                                    <?php foreach ($documentos_visiveis as $doc): ?>
                                        <div class="documento-entry" style="margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px dashed #eee;">
                                            <div class="row-fluid" style="display: flex; gap: 5px; margin-bottom: <?php echo $doc->DOC_ENDE_IDX !== null ? '5px' : '0'; ?>;">
                                                <select disabled style="width: 150px; flex-shrink: 0;">
                                                    <option selected><?php echo $doc->doc_tipo_documento; ?></option>
                                                </select>
                                                <input type="text" readonly style="flex: 1;" value="<?php echo $doc->doc_numero; ?>" placeholder="Número">
                                                <input type="text" readonly style="flex: 1;" value="<?php echo $doc->doc_orgao_expedidor; ?>" placeholder="Órgão Expedidor">
                                                <select disabled style="width: 120px; flex-shrink: 0;">
                                                    <option selected><?php echo $doc->doc_natureza_contribuinte ?: 'Natureza'; ?></option>
                                                </select>
                                            </div>
                                            <?php if ($doc->DOC_ENDE_IDX !== null): ?>
                                            <div class="row-fluid" style="display: flex; gap: 10px; align-items: center;">
                                                <label style="margin: 0; white-space: nowrap; font-size: 12px; color: #666;">Vincular ao endereço:</label>
                                                <select disabled style="flex: 1;">
                                                    <?php
                                                    if (isset($enderecos[$doc->DOC_ENDE_IDX])) {
                                                        $e = $enderecos[$doc->DOC_ENDE_IDX];
                                                        echo "<option selected>{$e->end_tipo_endenreco} - {$e->end_logradouro}, {$e->end_numero}</option>";
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
                                    style="width:100%; resize: vertical; box-sizing: border-box;"><?php echo $result->pes_observacao; ?></textarea>
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
                                                value="<?php echo number_format($cliente->cln_limite_credito, 2, ',', '.'); ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Dias de Carência</label>
                                        <div class="controls">
                                            <input type="text" readonly value="<?php echo $cliente->cln_dias_carencia; ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                    <div class="control-group">
                                        <label class="control-label">Situação</label>
                                        <div class="controls">
                                            <select disabled class="span12">
                                                <option selected>
                                                    <?php echo $cliente->cln_situacao == 1 ? 'Ativo' : 'Inativo'; ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Objetivo Comercial</label>
                                        <div class="controls">
                                            <select disabled class="span12">
                                                <option selected>
                                                    <?php echo $cliente->cln_objetivo_comercial ?: 'Consumo'; ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label">Tipo de Cliente</label>
                                        <div class="controls">
                                            <input type="text" readonly value="<?php echo isset($cliente->tpc_nome) ? $cliente->tpc_nome : 'Não definido'; ?>" class="span12">
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Opções</label>
                                        <div class="controls">
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo $cliente->cln_comprar_aprazo ? 'checked' : ''; ?>> Comprar a prazo
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo $cliente->cln_bloqueio_financeiro ? 'checked' : ''; ?>> Bloqueio financeiro
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo $cliente->cln_emitir_nfe ? 'checked' : ''; ?>> Emitir NFe
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" disabled <?php echo !empty($cliente->cln_cobrar_irrf) ? 'checked' : ''; ?>> Cobrar IRRF na NFCom
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
                                                            <input type="radio" disabled <?php echo $vp->clv_padrao ? 'checked' : ''; ?>>
                                                            <small>Padrão</small>
                                                        </label>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
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
                                                value="<?php echo number_format($vendedor->ven_percentual_comissao, 2, ',', '.'); ?>"
                                                class="span12">
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Tipo de Comissão</label>
                                        <div class="controls">
                                            <select disabled class="span12">
                                                <option selected><?php echo $vendedor->ven_tipo_comissao ?: 'Selecione'; ?>
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
                                                value="<?php echo number_format($vendedor->ven_meta_mensal, 2, ',', '.'); ?>"
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
                        <a href="<?php echo base_url('index.php/pessoas/editar/' . $result->pes_id); ?>" class="btn btn-info" style="margin: 0 5px;">
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