<?php /** @var object $result */ ?>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<style>
    /* Sugestões dropdown */
    .suggest-box {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        margin-top: 2px;
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        z-index: 9999;
        max-height: 220px;
        overflow-y: auto;
        box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
    }

    .suggest-item {
        padding: 6px 10px;
        cursor: pointer;
    }

    .suggest-item:hover {
        background: #f5f5f5;
    }

    /* Estilos para validação */
    .control-group.error input,
    .control-group.error select {
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
    .control-group.success select {
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


    /* Botões modernos */
    .btn-section {
        background: #333;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 3px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        transition: background-color 0.3s;
    }

    .btn-section:hover {
        background: #555;
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
    .form-section select {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        height: 30px;
        padding: 4px 8px;
        line-height: 20px;
        font-size: 14px;
    }

    /* Ajuste específico para o campo código */
    .form-section input#pes_codigo {
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

    .row-flex>.span7 {
        flex: 7;
        width: auto !important;
        margin-left: 0 !important;
    }

    .row-flex>.span5 {
        flex: 5;
        width: auto !important;
        margin-left: 0 !important;
    }

    .row-flex .form-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-radius: 0;
        margin: 0;
        height: 100%;
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

    /* Estilo para campo obrigatório */
    .required {
        color: #b94a48;
        font-weight: bold;
    }
</style>
<style>
    /* Endereços - cartões em duas linhas */
    .enderecos-wrapper {
        max-height: 260px;
        overflow-y: auto;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .enderecos-list {
        margin: 0;
        padding: 10px;
    }

    .endereco-card {
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 12px 14px;
        margin-bottom: 12px;
        background: #fff;
    }

    .endereco-card .line {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }

    .endereco-card .line+.line {
        margin-top: 10px;
    }

    .endereco-card .line .chunk {
        white-space: normal;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 100%;
        line-height: 1.5;
    }

    .endereco-label {
        color: #777;
        font-weight: normal;
        margin-right: 2px;
    }

    /* Grid para melhor organização */
    .endereco-card .grid-1 {
        display: grid;
        grid-template-columns: 90px 70px 1.5fr 1.2fr auto;
        gap: 10px;
        align-items: center;
    }

    .endereco-card .grid-2 {
        display: grid;
        grid-template-columns: 120px 2.2fr 110px 1.6fr;
        gap: 10px;
        align-items: center;
    }

    .endereco-card .grid-3 {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 10px;
        align-items: center;
    }

    .endereco-card .actions {
        justify-self: end;
    }

    .endereco-card .line .grow {
        flex: 1;
        min-width: 120px;
    }

    .endereco-card .actions {
        display: flex;
        gap: 6px;
        margin-left: auto;
    }

    .endereco-badge {
        display: inline-block;
        padding: 2px 6px;
        font-size: 12px;
        border-radius: 10px;
        background: #f5f5f5;
        border: 1px solid #e5e5e5;
    }

    @media (max-width: 768px) {
        .endereco-card .actions {
            width: 100%;
            margin-top: 6px;
        }
    }

    .enderecos-toolbar {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 6px;
    }

    .enderecos-wrapper {
        width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                <h5>Editar Pessoa</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formPessoa" method="post" class="form-horizontal">
                <?php echo form_hidden('pes_id', $result->pes_id) ?>
                <div class="widget-content nopadding tab-content">

                    <!-- Seções lado a lado -->
                    <div class="row-fluid row-flex" style="margin: 20px 0 0 0; padding: 0;">
                        <div class="span7">
                            <div class="form-section" style="height: 100%;">
                                <div class="form-section-header">
                                    <i class="fas fa-edit"></i>
                                    <span>Dados gerais</span>
                                </div>
                                <div class="form-section-content">
                                    <!-- Campo Tipo oculto mas mantido para JavaScript -->
                                    <input type="hidden" id="pes_fisico_juridico" name="pes_fisico_juridico"
                                        value="<?php echo $result->pes_fisico_juridico; ?>">

                                    <!-- Linha 1: Código e Situação -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="pes_codigo" class="control-label">Código</label>
                                                <div class="controls">
                                                    <input id="pes_codigo" type="text" name="pes_codigo"
                                                        value="<?php echo $result->pes_codigo; ?>" readonly
                                                        style="width: 150px;" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="pes_situacao" class="control-label">Situação</label>
                                                <div class="controls">
                                                    <select id="pes_situacao" name="pes_situacao">
                                                        <option value="1" <?php echo $result->pes_situacao == '1' ? 'selected' : ''; ?>>Ativo</option>
                                                        <option value="0" <?php echo $result->pes_situacao == '0' ? 'selected' : ''; ?>>Inativo</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2: CPF/CNPJ e Nascimento/Abertura -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span7">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="pes_cpfcnpj" class="control-label">CPF/CNPJ<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input id="pes_cpfcnpj" type="text" name="pes_cpfcnpj"
                                                        value="<?php echo $result->pes_cpfcnpj; ?>" inputmode="numeric"
                                                        autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span5">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="pes_nascimento_abertura" id="labelNascimentoAbertura"
                                                    class="control-label">Abertura</label>
                                                <div class="controls">
                                                    <input id="pes_nascimento_abertura" type="date"
                                                        name="pes_nascimento_abertura"
                                                        value="<?php echo $result->pes_nascimento_abertura; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 3: Nome (largura total) -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="pes_nome" class="control-label">Nome<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input id="pes_nome" type="text" name="pes_nome"
                                                        value="<?php echo $result->pes_nome; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 4: Razão Social (largura total) -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="pes_razao_social" class="control-label">Razão Social</label>
                                                <div class="controls">
                                                    <input id="pes_razao_social" type="text" name="pes_razao_social"
                                                        value="<?php echo $result->pes_razao_social; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- Tipos de Pessoa (Direita) -->
                        <div class="span5">
                            <div class="form-section" style="height: 100%;">
                                <div class="form-section-header">
                                    <i class="fas fa-users"></i>
                                    <span>Tipos de Pessoa<span class="required">*</span></span>
                                </div>
                                <div class="form-section-content" style="padding: 0;">
                                    <div class="control-group" style="margin: 0;">
                                        <div class="controls" style="margin: 0;">
                                            <div id="tipos-pessoa-container"
                                                style="padding: 20px; background-color: #f9f9f9; min-height: 200px;">
                                                <div id="tipos-pessoa-error"
                                                    style="display: none; margin-bottom: 15px; padding: 10px; background-color: #f2dede; border: 1px solid #ebccd1; border-radius: 4px; color: #a94442;">
                                                    <i class="fas fa-exclamation-triangle"></i> Selecione pelo menos um
                                                    tipo de pessoa
                                                </div>
                                                <?php
                                                // Debug
                                                error_log('Tipos vinculados na view: ' . print_r($tipos_vinculados, true));

                                                // Buscar tipos de pessoa do banco de dados
                                                $this->db->where('ativo', 1);
                                                $this->db->order_by('nome', 'ASC');
                                                $tipos_pessoa = $this->db->get('tipos_pessoa')->result();

                                                // Criar array de tipos vinculados para facilitar verificação
                                                $tipos_vinculados_ids = array();
                                                if (isset($tipos_vinculados) && !empty($tipos_vinculados)) {
                                                    foreach ($tipos_vinculados as $vinculo) {
                                                        $tipos_vinculados_ids[] = $vinculo->TPP_ID;
                                                    }
                                                }

                                                error_log('IDs extraídos: ' . print_r($tipos_vinculados_ids, true));

                                                foreach ($tipos_pessoa as $tipo):
                                                    $checked = in_array($tipo->id, $tipos_vinculados_ids) ? 'checked' : '';

                                                    // Fallback/Verificação direta: se for Cliente ou Vendedor, verificar as tabelas específicas
                                                    if (!$checked) {
                                                        if (strtolower($tipo->nome) == 'cliente' && isset($cliente) && $cliente) {
                                                            $checked = 'checked';
                                                        } elseif (strtolower($tipo->nome) == 'vendedor' && isset($vendedor) && $vendedor) {
                                                            $checked = 'checked';
                                                        }
                                                    }
                                                    ?>
                                                    <label class="checkbox"
                                                        style="display:block; margin-bottom:8px; width: 100%;">
                                                        <input type="checkbox"
                                                            id="<?php echo strtoupper($tipo->nome); ?>_ENABLE"
                                                            name="TIPOS_PESSOA[]" value="<?php echo $tipo->id; ?>"
                                                            style="margin-right: 8px;" <?php echo $checked; ?> />
                                                        <?php echo $tipo->nome; ?>
                                                    </label>
                                                <?php endforeach; ?>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Contatos -->
                    <div class="row-fluid"
                        style="margin: 0; padding: 0; overflow: hidden; display: flex; margin-top: 30px;">
                        <div class="span6"
                            style="padding: 0; margin: 0; width: 50%; float: left; display: flex; flex-direction: column;">
                            <div class="form-section"
                                style="margin: 0; padding: 0; border-right: 1px solid #ddd; flex: 1; display: flex; flex-direction: column;">
                                <div class="form-section-header"
                                    style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <i class="fas fa-phone"></i>
                                        <span>Telefones</span>
                                    </div>
                                    <button type="button" id="btnAdicionarTelefone" class="btn btn-success btn-mini">
                                        <i class="fas fa-plus"></i> Adicionar Telefone
                                    </button>
                                </div>
                                <div class="form-section-content" style="flex: 1;">
                                    <div id="telefones-container">
                                        <!-- Telefones serão adicionados dinamicamente aqui -->
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="span6"
                            style="padding: 0; margin: 0; width: 50%; float: left; display: flex; flex-direction: column;">
                            <div class="form-section"
                                style="margin: 0; padding: 0; border-left: 1px solid #ddd; flex: 1; display: flex; flex-direction: column;">
                                <div class="form-section-header"
                                    style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <i class="fas fa-envelope"></i>
                                        <span>Emails</span>
                                    </div>
                                    <button type="button" id="btnAdicionarEmail" class="btn btn-success btn-mini">
                                        <i class="fas fa-plus"></i> Adicionar Email
                                    </button>
                                </div>
                                <div class="form-section-content" style="flex: 1;">
                                    <div id="emails-container">
                                        <!-- Emails serão adicionados dinamicamente aqui -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Endereços -->
                    <div class="form-section" style="margin-top: 30px;">
                        <div class="form-section-header"
                            style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Endereços</span>
                            </div>
                            <button type="button" id="btnAdicionarEndereco" class="btn btn-success btn-mini">
                                <i class="fas fa-plus"></i> Adicionar Endereço
                            </button>
                        </div>
                        <div class="form-section-content">
                            <div id="enderecos-container"></div>
                        </div>
                    </div>

                    <!-- Seção Documentos e Observação -->
                    <div class="row-fluid"
                        style="margin: 0; padding: 0; overflow: hidden; display: flex; margin-top: 30px;">
                        <!-- Documentos (esquerda) -->
                        <div class="span6"
                            style="padding: 0; margin: 0; width: 50%; float: left; display: flex; flex-direction: column;">
                            <div class="form-section"
                                style="margin: 0; padding: 0; border-right: 1px solid #ddd; flex: 1; display: flex; flex-direction: column;">
                                <div class="form-section-header"
                                    style="min-height: 41px; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <i class="fas fa-file-alt"></i>
                                        <span>Documentos</span>
                                    </div>
                                    <button type="button" id="btnAddDocumento" class="btn btn-success btn-mini">
                                        <i class="fas fa-plus"></i> Adicionar Documento
                                    </button>
                                </div>
                                <div class="form-section-content" style="flex: 1;">
                                    <div id="documentos-container"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Observação (direita) -->
                        <div class="span6"
                            style="padding: 0; margin: 0; width: 50%; float: left; display: flex; flex-direction: column;">
                            <div class="form-section"
                                style="margin: 0; padding: 0; border-left: 1px solid #ddd; flex: 1; display: flex; flex-direction: column;">
                                <div class="form-section-header"
                                    style="min-height: 41px; display: flex; align-items: center;">
                                    <i class="fas fa-sticky-note"></i>
                                    <span>Observação</span>
                                </div>
                                <div class="form-section-content" style="flex: 1;">
                                    <div class="control-group" style="margin: 0;">
                                        <div class="controls">
                                            <textarea id="pes_observacao" name="pes_observacao" rows="4"
                                                style="width:100%; resize: vertical;"><?php echo isset($result->pes_observacao) ? $result->pes_observacao : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Cliente -->
                    <div class="form-section" id="secao-financeiro" style="display:none; margin-top: 30px;">
                        <div class="form-section-header">
                            <i class="fas fa-user-tag"></i>
                            <span>Cliente</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Campo hidden para identificar que é cliente -->
                            <input type="hidden" id="CLN_ENABLE" name="CLN_ENABLE" value="0" />

                            <div class="row-fluid">
                                <!-- Coluna 1: Dados Financeiros -->
                                <div class="span4">
                                    <div class="row-fluid">
                                        <div class="span6">
                                            <div class="control-group">
                                                <label for="cln_limite_credito" class="control-label">Limite de
                                                    Crédito</label>
                                                <div class="controls">
                                                    <input id="cln_limite_credito" type="text" name="cln_limite_credito"
                                                        class="span12" value="0,00" placeholder="0,00" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span6">
                                            <div class="control-group">
                                                <label for="cln_dias_carencia" class="control-label">Dias de
                                                    Carência</label>
                                                <div class="controls">
                                                    <input id="cln_dias_carencia" type="number" name="cln_dias_carencia"
                                                        class="span12" value="0" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label for="cln_situacao" class="control-label">Situação</label>
                                        <div class="controls">
                                            <select id="cln_situacao" name="cln_situacao" class="span12">
                                                <option value="1" <?php echo (isset($cliente) && $cliente->cln_situacao == 1) ? 'selected' : ''; ?>>Ativo</option>
                                                <option value="0" <?php echo (isset($cliente) && $cliente->cln_situacao == 0) ? 'selected' : ''; ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label for="cln_objetivo_comercial" class="control-label">Objetivo Comercial</label>
                                        <div class="controls">
                                            <select id="cln_objetivo_comercial" name="cln_objetivo_comercial" class="span12">
                                                <option value="Consumo" <?php echo (isset($cliente) && $cliente->cln_objetivo_comercial == 'Consumo') ? 'selected' : ''; ?>>Consumo</option>
                                                <option value="Revenda" <?php echo (isset($cliente) && $cliente->cln_objetivo_comercial == 'Revenda') ? 'selected' : ''; ?>>Revenda</option>
                                                <option value="Industrialização" <?php echo (isset($cliente) && $cliente->cln_objetivo_comercial == 'Industrialização') ? 'selected' : ''; ?>>Industrialização</option>
                                                <option value="Orgão Público" <?php echo (isset($cliente) && $cliente->cln_objetivo_comercial == 'Orgão Público') ? 'selected' : ''; ?>>Orgão Público</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label for="tpc_id" class="control-label">Tipo de Cliente</label>
                                        <div class="controls">
                                            <select id="tpc_id" name="tpc_id" class="span12">
                                                <option value="">Selecione um tipo</option>
                                                <?php if (!empty($tipos_clientes)): ?>
                                                    <?php foreach ($tipos_clientes as $tc): ?>
                                                        <option value="<?php echo $tc->tpc_id; ?>" <?php echo (isset($cliente) && $cliente->tpc_id == $tc->tpc_id) ? 'selected' : set_select('tpc_id', $tc->tpc_id); ?>>
                                                            <?php echo htmlspecialchars($tc->tpc_nome, ENT_QUOTES, 'UTF-8'); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <option value="" disabled>Nenhum tipo cadastrado</option>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coluna 2: Opções -->
                                <div class="span4">
                                    <div class="control-group">
                                        <label class="control-label">Opções</label>
                                        <div class="controls">
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" id="cln_comprar_aprazo" name="cln_comprar_aprazo"
                                                    value="1" /> Comprar a prazo
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" id="cln_bloqueio_financeiro"
                                                    name="cln_bloqueio_financeiro" value="1" checked /> Bloqueio
                                                financeiro
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" id="cln_emitir_nfe" name="cln_emitir_nfe"
                                                    value="1" checked /> Emitir NFe
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Coluna 3: Vendedores Permitidos -->
                                <div class="span4">
                                    <div class="control-group">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <label class="control-label" style="margin: 0;">
                                                <i class="fas fa-users"></i> Vendedores Permitidos
                                            </label>
                                            <button type="button" id="btnAdicionarVendedorPermitido"
                                                class="btn btn-success btn-mini">
                                                <i class="fas fa-plus"></i> Adicionar Vendedor
                                            </button>
                                        </div>
                                        <div class="controls" style="margin-left: 0;">
                                            <div id="vendedores-permitidos-container">
                                                <!-- Vendedores serão adicionados aqui -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Seção Vendedor -->
                    <div class="form-section" id="secao-vendedor" style="display:none; margin-top: 30px;">
                        <div class="form-section-header">
                            <i class="fas fa-user-tie"></i>
                            <span>Vendedor</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Campo hidden para identificar que é vendedor -->
                            <input type="hidden" id="VEN_ENABLE" name="VEN_ENABLE" value="0" />

                            <div class="row-fluid">
                                <div class="span4">
                                    <div class="control-group">
                                        <label for="ven_percentual_comissao" class="control-label">Percentual de
                                            Comissão (%)</label>
                                        <div class="controls">
                                            <input id="ven_percentual_comissao" type="text"
                                                name="ven_percentual_comissao"
                                                value="<?php echo set_value('ven_percentual_comissao'); ?>"
                                                placeholder="0,00" />
                                        </div>
                                    </div>
                                </div>

                                <div class="span4">
                                    <div class="control-group">
                                        <label for="ven_tipo_comissao" class="control-label">Tipo de Comissão</label>
                                        <div class="controls">
                                            <select id="ven_tipo_comissao" name="ven_tipo_comissao">
                                                <option value="">Selecione</option>
                                                <option value="Sobre Venda" <?php echo set_value('ven_tipo_comissao') == 'Sobre Venda' ? 'selected' : ''; ?>>
                                                    Sobre Venda</option>
                                                <option value="Sobre Lucro" <?php echo set_value('ven_tipo_comissao') == 'Sobre Lucro' ? 'selected' : ''; ?>>
                                                    Sobre Lucro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="span4">
                                    <div class="control-group">
                                        <label for="ven_meta_mensal" class="control-label">Meta Mensal (R$)</label>
                                        <div class="controls">
                                            <input id="ven_meta_mensal" type="text" name="ven_meta_mensal"
                                                value="<?php echo set_value('ven_meta_mensal'); ?>"
                                                placeholder="0,00" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                                    <span class="button__icon"><i class='bx bx-save'></i></span>
                                    <span class="button__text2">Salvar</span>
                                </button>
                                <a href="<?php echo base_url() ?>index.php/pessoas" id=""
                                    class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span>
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

<script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>

<script>
    $(document).ready(function () {
        let telefoneIndex = 0;
        let emailIndex = 0;
        let documentoIndex = 0;
        let enderecoIndex = 0;

        // Função para atualizar tipos de documento baseado em PF/PJ
        function atualizarTiposDocumento(tipo) {
            $('.doc-tipo').each(function () {
                const select = $(this);
                const currentVal = select.val();
                select.empty();

                if (tipo === 'J') {
                    // CNPJ: apenas IE e IM
                    select.append('<option value="">Tipo</option>');
                    select.append('<option value="Inscrição Estadual">Inscrição Estadual</option>');
                    select.append('<option value="Inscrição Municipal">Inscrição Municipal</option>');
                } else {
                    // CPF: todos os tipos
                    select.append('<option value="">Tipo</option>');
                    select.append('<option value="rg">RG</option>');
                    select.append('<option value="Inscrição Estadual">Inscrição Estadual</option>');
                    select.append('<option value="Inscrição Municipal">Inscrição Municipal</option>');
                    select.append('<option value="CNH">CNH</option>');
                    select.append('<option value="Passaporte">Passaporte</option>');
                    select.append('<option value="Outros">Outros</option>');
                }

                // Restaurar valor se ainda válido
                if (currentVal && select.find(`option[value="${currentVal}"]`).length > 0) {
                    select.val(currentVal);
                }
            });
        }

        // Função para atualizar o label da data baseado no tipo de pessoa
        function atualizarLabelData(tipo) {
            var label = $('#labelNascimentoAbertura');
            if (tipo === 'F') {
                label.text('Nascimento');
            } else if (tipo === 'J') {
                label.text('Abertura');
            } else {
                label.text('Abertura');
            }
        }


        // Máscara dinâmica para CPF/CNPJ (igual ao cadastro de clientes)
        var cpfCnpjMascara = function (val) {
            return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009';
        };

        var cpfCnpjOptions = {
            onKeyPress: function (val, e, field, options) {
                var cleanVal = val.replace(/\D/g, '');

                // Atualizar tipo e botão automaticamente
                if (cleanVal.length <= 11) {
                    $('#pes_fisico_juridico').val('F');
                    $('#btnBuscarCNPJ').hide();
                    atualizarTiposDocumento('F');
                    atualizarLabelData('F');
                } else {
                    $('#pes_fisico_juridico').val('J');
                    $('#btnBuscarCNPJ').show();
                    atualizarTiposDocumento('J');
                    atualizarLabelData('J');

                    // Focar no botão de buscar quando CNPJ completo
                    if (cleanVal.length === 14) {
                        setTimeout(function () {
                            $('#btnBuscarCNPJ').focus();
                        }, 100);
                    }
                }

                field.mask(cpfCnpjMascara.apply({}, arguments), options);
            }
        };

        $('#pes_cpfcnpj').mask(cpfCnpjMascara, cpfCnpjOptions);

        // Suporte para colar (paste)
        $('#pes_cpfcnpj').on('paste', function (e) {
            e.preventDefault();
            var campo = $(this);
            var clipboardData = (e.originalEvent || e).clipboardData.getData('text/plain');
            var val = clipboardData.replace(/\D/g, '');

            // Aplicar máscara correta imediatamente
            campo.unmask();

            if (val.length > 11) {
                // CNPJ
                campo.mask('00.000.000/0000-00');
                campo.val(val);
                $('#pes_fisico_juridico').val('J');
                $('#btnBuscarCNPJ').show();
                atualizarTiposDocumento('J');
                atualizarLabelData('J');

                if (val.length === 14) {
                    setTimeout(function () {
                        $('#btnBuscarCNPJ').focus();
                    }, 100);
                }
            } else {
                // CPF
                campo.mask('000.000.000-009');
                campo.val(val);
                $('#pes_fisico_juridico').val('F');
                $('#btnBuscarCNPJ').hide();
                atualizarTiposDocumento('F');
                atualizarLabelData('F');
            }

            // Reaplicar máscara dinâmica após um delay
            setTimeout(function () {
                campo.unmask();
                campo.mask(cpfCnpjMascara, cpfCnpjOptions);
            }, 100);
        });

        // Na edição, não focar automaticamente em nenhum campo
        // Na edição, não verificar CPF/CNPJ duplicado (é o próprio registro)

        // Copiar nome para razão social se for Pessoa Física
        $('#pes_nome').on('blur', function () {
            var tipo = $('#pes_fisico_juridico').val();
            var nome = $(this).val();
            var razaoSocial = $('#pes_razao_social').val();

            // Se for pessoa física (F) e nome não estiver vazio e razão social estiver vazia
            if (tipo === 'F' && nome.trim() !== '' && razaoSocial.trim() === '') {
                $('#pes_razao_social').val(nome);
            }
        });

        // Validação CPF/CNPJ
        function validarCPFCNPJ(valor) {
            valor = valor.replace(/[^\d]/g, '');

            if (valor.length === 11) {
                // Validar CPF
                if (valor === "00000000000" || valor === "11111111111" || valor === "22222222222" ||
                    valor === "33333333333" || valor === "44444444444" || valor === "55555555555" ||
                    valor === "66666666666" || valor === "77777777777" || valor === "88888888888" ||
                    valor === "99999999999") {
                    return false;
                }

                let soma = 0;
                for (let i = 0; i < 9; i++) {
                    soma += parseInt(valor.charAt(i)) * (10 - i);
                }
                let resto = 11 - (soma % 11);
                if (resto === 10 || resto === 11) resto = 0;
                if (resto !== parseInt(valor.charAt(9))) return false;

                soma = 0;
                for (let i = 0; i < 10; i++) {
                    soma += parseInt(valor.charAt(i)) * (11 - i);
                }
                resto = 11 - (soma % 11);
                if (resto === 10 || resto === 11) resto = 0;
                return resto === parseInt(valor.charAt(10));
            } else if (valor.length === 14) {
                // Validar CNPJ
                if (valor === "00000000000000" || valor === "11111111111111" || valor === "22222222222222" ||
                    valor === "33333333333333" || valor === "44444444444444" || valor === "55555555555555" ||
                    valor === "66666666666666" || valor === "77777777777777" || valor === "88888888888888" ||
                    valor === "99999999999999") {
                    return false;
                }

                let tamanho = valor.length - 2;
                let numeros = valor.substring(0, tamanho);
                let digitos = valor.substring(tamanho);
                let soma = 0;
                let pos = tamanho - 7;

                for (let i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) pos = 9;
                }

                let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                if (resultado !== parseInt(digitos.charAt(0))) return false;

                tamanho = tamanho + 1;
                numeros = valor.substring(0, tamanho);
                soma = 0;
                pos = tamanho - 7;

                for (let i = tamanho; i >= 1; i--) {
                    soma += numeros.charAt(tamanho - i) * pos--;
                    if (pos < 2) pos = 9;
                }

                resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
                return resultado === parseInt(digitos.charAt(1));
            }
            return false;
        }

        // Controle do tipo F/J (quando mudado manualmente)
        $('#pes_fisico_juridico').on('change', function () {
            const tipo = $(this).val();
            const cnpj = $('#pes_cpfcnpj');

            if (tipo === 'F') {
                $('#btnBuscarCNPJ').hide();
                // Atualizar tipos de documento para Pessoa Física
                atualizarTiposDocumento('F');
            } else if (tipo === 'J') {
                $('#btnBuscarCNPJ').show();
                // Atualizar tipos de documento para Pessoa Jurídica
                atualizarTiposDocumento('J');
            } else {
                $('#btnBuscarCNPJ').hide();
                atualizarTiposDocumento('F');
            }

            // Retornar foco ao campo CPF/CNPJ
            cnpj.focus();
        });

        // Busca CNPJ
        $('#btnBuscarCNPJ').on('click', function () {
            const cnpj = $('#pes_cpfcnpj').val().replace(/[^\d]/g, '');
            const btn = $(this);

            if (cnpj.length !== 14) {
                alert('CNPJ deve ter 14 dígitos');
                return;
            }

            if (!validarCPFCNPJ(cnpj)) {
                alert('CNPJ inválido');
                return;
            }

            // Feedback visual
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: `https://publica.cnpj.ws/cnpj/${cnpj}`,
                method: 'GET',
                dataType: 'json',
                timeout: 10000,
                success: function (data) {
                    if (data.razao_social) {
                        // Os dados estão em data.estabelecimento
                        const estab = data.estabelecimento;

                        // Preencher nome fantasia
                        if (estab.nome_fantasia) {
                            $('#pes_nome').val(estab.nome_fantasia);
                        } else {
                            $('#pes_nome').val(data.razao_social);
                        }
                        $('#pes_razao_social').val(data.razao_social);

                        // Preencher data de abertura
                        if (estab.data_inicio_atividade) {
                            $('#pes_nascimento_abertura').val(estab.data_inicio_atividade);
                        }

                        // Adicionar telefone se disponível (ddd1 e telefone1)
                        if (estab.ddd1 && estab.telefone1) {
                            const telefoneRow = `
                            <div class="telefone-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                                <select name="tel_tipo[]" class="form-control" style="width: 120px;">
                                    <option value="Comercial" selected>Comercial</option>
                                    <option value="Residencial">Residencial</option>
                                    <option value="Celular">Celular</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                </select>
                                <input type="text" name="tel_ddd[]" maxlength="2" placeholder="DDD" class="form-control tel-ddd" style="width: 60px;" value="${estab.ddd1}" />
                                <input type="text" name="tel_numero[]" placeholder="Número" class="form-control tel-numero" style="flex: 1;" value="${estab.telefone1}" />
                                <input type="text" name="tel_observacao[]" placeholder="Observação (opcional)" class="form-control" style="flex: 1;" />
                                <button type="button" class="btn btn-mini btn-danger remove-telefone" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                            $('#telefones-container').append(telefoneRow);
                        }

                        // Adicionar email se disponível
                        if (estab.email) {
                            const emailRow = `
                            <div class="email-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                                <select name="EMAIL_TIPO[]" class="form-control" style="width: 120px;">
                                    <option value="Comercial" selected>Comercial</option>
                                    <option value="Pessoal">Pessoal</option>
                                    <option value="Outros">Outros</option>
                                </select>
                                <input type="text" name="EMAIL_NOME[]" placeholder="Nome" class="form-control" style="flex: 1;" value="Principal" />
                                <input type="email" name="EMAIL_ENDERECO[]" placeholder="Email" class="form-control" style="flex: 1.5;" value="${estab.email.toLowerCase()}" />
                                <button type="button" class="btn btn-mini btn-danger remove-email" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                            $('#emails-container').append(emailRow);
                            emailIndex++;
                        }

                        // Adicionar endereço principal inline
                        if (estab.logradouro) {
                            const isPrimeiro = $('#enderecos-container .endereco-row').length === 0;
                            const checkedAttr = isPrimeiro ? 'checked' : '';

                            const enderecoRow = `
                            <div class="endereco-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <input type="text" name="end_logradouro[]" placeholder="Logradouro" class="form-control" style="flex: 3;" value="${estab.logradouro || ''}" />
                                    <input type="text" name="end_numero[]" placeholder="Número" class="form-control" style="width: 80px;" value="${estab.numero || ''}" />
                                    <input type="text" name="end_complemento[]" placeholder="Complemento" class="form-control" style="flex: 2;" value="${estab.complemento || ''}" />
                                </div>
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <input type="text" name="end_cep[]" placeholder="cep" class="form-control cep-mask" style="width: 110px;" value="${estab.cep || ''}" />
                                    <input type="text" name="END_BAIRRO[]" placeholder="Bairro" class="form-control" style="flex: 1;" value="${estab.bairro || ''}" />
                                    <input type="text" name="END_CIDADE[]" placeholder="Cidade" class="form-control" style="flex: 1;" value="${estab.cidade ? estab.cidade.nome : ''}" />
                                    <input type="text" name="END_UF[]" placeholder="uf" class="form-control" style="width: 50px;" maxlength="2" value="${estab.estado ? estab.estado.sigla : ''}" />
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <select name="END_TIPO[]" class="form-control" style="width: 150px;">
                                        <option value="Comercial" selected>Comercial</option>
                                        <option value="Residencial">Residencial</option>
                                        <option value="Cobrança">Cobrança</option>
                                        <option value="Entrega">Entrega</option>
                                        <option value="Outros">Outros</option>
                                    </select>
                                    <label class="checkbox" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                                        <input type="radio" name="endereco_padrao" value="novo_${enderecoIndex}" ${checkedAttr} />
                                        <strong>Endereço Padrão</strong>
                                    </label>
                                    <div style="flex: 1;"></div>
                                    <button type="button" class="btn btn-mini btn-danger remove-endereco" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                            $('#enderecos-container').append(enderecoRow);
                            enderecoIndex++;
                            atualizarOpcoesEndereco();

                            // Aplicar máscara de CEP
                            $('.cep-mask').mask('00000-000');
                        }

                        // Mostrar indicador de carregamento nos documentos
                        const loadingDoc = `
                        <div id="loading-documentos" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f0f8ff; text-align: center;">
                            <i class="fas fa-spinner fa-spin"></i> Buscando Inscrições Estaduais...
                        </div>
                    `;
                        $('#documentos-container').append(loadingDoc);

                        // Buscar Inscrição Estadual da API CNPJ.WS (tem dados completos)
                        // Aguardar um pouco para garantir que o endereço foi adicionado
                        setTimeout(function () {
                            $.ajax({
                                url: `https://publica.cnpj.ws/cnpj/${cnpj}`,
                                method: 'GET',
                                dataType: 'json',
                                success: function (dataCNPJWS) {
                                    // Remover indicador de carregamento
                                    $('#loading-documentos').remove();
                                    console.log('Dados CNPJ.WS:', dataCNPJWS);

                                    // Verificar se tem inscrições estaduais no estabelecimento
                                    if (dataCNPJWS.estabelecimento &&
                                        dataCNPJWS.estabelecimento.inscricoes_estaduais &&
                                        dataCNPJWS.estabelecimento.inscricoes_estaduais.length > 0) {

                                        // Adicionar cada IE encontrada
                                        dataCNPJWS.estabelecimento.inscricoes_estaduais.forEach(function (ie) {
                                            if (ie.inscricao_estadual && ie.inscricao_estadual.trim() !== '') {
                                                var estadoUF = ie.estado ? ie.estado.sigla || ie.estado : (estab.estado ? estab.estado.sigla : '');
                                                adicionarDocumento({
                                                    tipo: 'Inscrição Estadual',
                                                    numero: ie.inscricao_estadual,
                                                    orgao: `SEFAZ ${estadoUF}`,
                                                    natureza: ie.ativo ? 'Contribuinte' : 'Não Contribuinte'
                                                });
                                                console.log('IE adicionada:', ie.inscricao_estadual, 'Estado:', estadoUF);

                                                // Vincular ao primeiro endereço automaticamente
                                                setTimeout(function () {
                                                    var primeiroEndereco = $('#enderecos-container .endereco-row').first();
                                                    if (primeiroEndereco.length > 0) {
                                                        var ultimoDocumento = $('#documentos-container .documento-row').last();
                                                        var selectEndereco = ultimoDocumento.find('select[name="DOC_ENDE_IDX[]"]');
                                                        if (selectEndereco.length > 0) {
                                                            selectEndereco.val('0'); // Primeiro endereço tem índice 0
                                                            console.log('IE vinculada ao primeiro endereço');
                                                        }
                                                    }
                                                }, 50);
                                            }
                                        });
                                    } else {
                                        console.log('Nenhuma Inscrição Estadual encontrada para este CNPJ');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    // Remover indicador de carregamento
                                    $('#loading-documentos').remove();
                                    console.log('Não foi possível buscar IE da CNPJ.WS:', error);
                                    console.log('Resposta:', xhr.responseText);
                                }
                            });
                        }, 100);
                    } else {
                        alert('CNPJ não encontrado na base de dados');
                    }
                },
                error: function (xhr, status, error) {
                    // Log detalhado para debug
                    console.error('Erro na busca de CNPJ:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('XHR Status:', xhr.status);
                    console.error('Response:', xhr.responseText || 'Sem resposta');

                    // Mensagens de erro mais específicas
                    if (xhr.status === 0) {
                        alert('Erro de conexão. Verifique sua internet e tente novamente.');
                    } else if (xhr.status === 404) {
                        alert('CNPJ não encontrado na base de dados.');
                    } else if (xhr.status === 429) {
                        alert('Limite de requisições excedido. Aguarde alguns segundos e tente novamente.');
                    } else if (xhr.status === 500) {
                        alert('Erro no servidor da API. Tente novamente em alguns instantes.');
                    } else if (status === 'timeout') {
                        alert('Tempo de espera esgotado. Verifique sua conexão e tente novamente.');
                    } else if (status === 'parsererror') {
                        alert('Erro ao processar resposta da API. Tente novamente.');
                    } else {
                        alert('Erro ao buscar dados do CNPJ. Tente novamente em alguns instantes.');
                    }
                },
                complete: function () {
                    // Restaurar botão
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i>');
                }
            });
        });

        // Controle das seções de tipos de pessoa
        $(document).on('change', 'input[name="TIPOS_PESSOA[]"]', function () {
            var checkboxId = $(this).attr('id');

            // Verificar se é o checkbox de Cliente
            if (checkboxId === 'CLIENTE_ENABLE') {
                if ($(this).is(':checked')) {
                    $('#secao-financeiro').slideDown(300);
                    $('#CLN_ENABLE').val('1');
                    // Definir valores padrão
                    $('#cln_limite_credito').val('0,00');
                    $('#cln_dias_carencia').val('0');
                    $('#cln_bloqueio_financeiro').prop('checked', true);
                    $('#cln_emitir_nfe').prop('checked', true);
                    $('#cln_objetivo_comercial').val('Consumo');
                } else {
                    $('#secao-financeiro').slideUp(300);
                    $('#CLN_ENABLE').val('0');
                    // Limpar campos de cliente quando desmarcar
                    $('#cln_limite_credito').val('0,00');
                    $('#cln_dias_carencia').val('0');
                    $('#cln_situacao').val('1');
                    $('#cln_comprar_aprazo, #cln_bloqueio_financeiro, #cln_emitir_nfe').prop('checked', false);
                    $('#cln_objetivo_comercial').val('');
                    $('#tpc_id').val('');
                    // Limpar vendedores permitidos
                    $('#vendedores-permitidos-container').empty();
                }
            }

            // Verificar se é o checkbox de Vendedor
            if (checkboxId === 'VENDEDOR_ENABLE') {
                if ($(this).is(':checked')) {
                    $('#secao-vendedor').slideDown(300);
                    $('#VEN_ENABLE').val('1');
                } else {
                    $('#secao-vendedor').slideUp(300);
                    $('#VEN_ENABLE').val('0');
                    // Limpar campos de vendedor quando desmarcar
                    $('#ven_percentual_comissao').val('');
                    $('#ven_tipo_comissao').val('');
                    $('#ven_meta_mensal').val('');
                }
            }
        });

        // Adicionar vendedor permitido
        $('#btnAdicionarVendedorPermitido').on('click', function () {
            // Buscar lista de vendedores
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/pessoas/listarVendedores',
                method: 'GET',
                dataType: 'json',
                success: function (vendedores) {
                    if (vendedores.length === 0) {
                        alert('Nenhum vendedor cadastrado no sistema.');
                        return;
                    }

                    // Criar select com vendedores
                    var optionsHtml = '<option value="">Selecione um vendedor</option>';
                    vendedores.forEach(function (v) {
                        optionsHtml += `<option value="${v.pes_id}">${v.pes_nome}</option>`;
                    });

                    // Verificar se é o primeiro vendedor
                    var isPrimeiro = $('#vendedores-permitidos-container .vendedor-permitido-row').length === 0;
                    var checkedAttr = isPrimeiro ? 'checked' : '';

                    const vendedorRow = `
                    <div class="vendedor-permitido-row" style="margin-bottom: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <select name="CLV_VEN_PES_ID[]" class="form-control vendedor-select" style="flex: 1; font-size: 12px;">
                                ${optionsHtml}
                            </select>
                            <label class="checkbox" style="margin: 0; white-space: nowrap;">
                                <input type="radio" name="clv_padrao" value="" class="vendedor-padrao-radio" ${checkedAttr} style="margin-right: 3px;" /> 
                                <small>Padrão</small>
                            </label>
                            <button type="button" class="btn btn-mini btn-danger remove-vendedor-permitido" style="width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                <i class="fas fa-trash" style="font-size: 10px;"></i>
                            </button>
                        </div>
                    </div>
                `;
                    $('#vendedores-permitidos-container').append(vendedorRow);

                    // Se for o primeiro, marcar como padrão após um delay
                    if (isPrimeiro) {
                        setTimeout(function () {
                            var primeiroRadio = $('#vendedores-permitidos-container .vendedor-padrao-radio').first();
                            var primeiroSelect = $('#vendedores-permitidos-container .vendedor-select').first();
                            var primeiroValor = primeiroSelect.val();
                            if (primeiroValor) {
                                primeiroRadio.val(primeiroValor);
                            }
                        }, 100);
                    }
                },
                error: function () {
                    alert('Erro ao buscar vendedores.');
                }
            });
        });

        // Remover vendedor permitido
        $(document).on('click', '.remove-vendedor-permitido', function () {
            var row = $(this).closest('.vendedor-permitido-row');
            var radioPadrao = row.find('.vendedor-padrao-radio');
            var eraPadrao = radioPadrao.is(':checked');

            row.remove();

            // Se removeu o padrão e ainda tem vendedores, marcar o primeiro como padrão
            if (eraPadrao) {
                var primeiroVendedor = $('#vendedores-permitidos-container .vendedor-permitido-row').first();
                if (primeiroVendedor.length > 0) {
                    var primeiroRadio = primeiroVendedor.find('.vendedor-padrao-radio');
                    var primeiroSelect = primeiroVendedor.find('.vendedor-select');
                    var vendedorId = primeiroSelect.val();

                    if (vendedorId) {
                        primeiroRadio.prop('checked', true);
                        primeiroRadio.val(vendedorId);
                    }
                }
            }
        });

        // Controlar radio de vendedor padrão (só um marcado)
        $(document).on('change', '.vendedor-padrao-radio', function () {
            if ($(this).is(':checked')) {
                var vendedorRow = $(this).closest('.vendedor-permitido-row');
                var vendedorId = vendedorRow.find('.vendedor-select').val();

                if (!vendedorId) {
                    alert('Selecione um vendedor antes de marcá-lo como padrão.');
                    $(this).prop('checked', false);
                    return;
                }

                // Desmarcar outros radios
                $('.vendedor-padrao-radio').not(this).prop('checked', false);

                // Atualizar valores dos radios
                $('.vendedor-padrao-radio').each(function () {
                    var row = $(this).closest('.vendedor-permitido-row');
                    var vid = row.find('.vendedor-select').val();
                    $(this).val(vid);
                });
            }
        });

        // Adicionar telefone
        $('#btnAdicionarTelefone').on('click', function () {
            const telefoneRow = `
            <div class="telefone-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                <select name="tel_tipo[]" class="form-control" style="width: 120px;">
                    <option value="Comercial">Comercial</option>
                    <option value="Residencial">Residencial</option>
                    <option value="Celular">Celular</option>
                    <option value="WhatsApp">WhatsApp</option>
                </select>
                <input type="text" name="tel_ddd[]" maxlength="2" placeholder="DDD" class="form-control tel-ddd" style="width: 60px;" />
                <input type="text" name="tel_numero[]" placeholder="Número" class="form-control tel-numero" style="flex: 1;" />
                <input type="text" name="tel_observacao[]" placeholder="Observação (opcional)" class="form-control" style="flex: 1;" />
                <button type="button" class="btn btn-mini btn-danger remove-telefone" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
            $('#telefones-container').append(telefoneRow);
        });

        // Adicionar email
        $('#btnAdicionarEmail').on('click', function () {
            const emailRow = `
            <div class="email-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                <select name="EMAIL_TIPO[]" class="form-control" style="width: 120px;">
                    <option value="Comercial">Comercial</option>
                    <option value="Pessoal">Pessoal</option>
                    <option value="Outros">Outros</option>
                </select>
                <input type="text" name="EMAIL_NOME[]" placeholder="Nome" class="form-control" style="flex: 1;" />
                <input type="email" name="EMAIL_ENDERECO[]" placeholder="Email" class="form-control" style="flex: 1.5;" />
                <button type="button" class="btn btn-mini btn-danger remove-email" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
            $('#emails-container').append(emailRow);
        });

        // Remover telefone
        $(document).on('click', '.remove-telefone', function () {
            $(this).closest('.telefone-row').remove();
        });

        // Auto-focus no campo número quando DDD estiver completo
        $(document).on('input', '.tel-ddd', function () {
            if ($(this).val().length === 2) {
                $(this).closest('.telefone-row').find('.tel-numero').focus();
            }
        });

        // Remover email
        $(document).on('click', '.remove-email', function () {
            $(this).closest('.email-row').remove();
        });

        // Adicionar documento
        $('#btnAddDocumento').on('click', function () {
            adicionarDocumento();
        });

        function adicionarDocumento(dados = {}) {
            const tipo = dados.tipo || '';
            const numero = dados.numero || '';
            const orgao = dados.orgao || '';
            const natureza = dados.natureza || '';

            const showEndereco = tipo === 'Inscrição Estadual' || tipo === 'Inscrição Municipal';
            const tipoPessoa = $('#pes_fisico_juridico').val() || 'F';

            let opcoesDocumento = '';
            if (tipoPessoa === 'J') {
                opcoesDocumento = `
                <option value="">Tipo</option>
                <option value="Inscrição Estadual" ${tipo === 'Inscrição Estadual' ? 'selected' : ''}>Inscrição Estadual</option>
                <option value="Inscrição Municipal" ${tipo === 'Inscrição Municipal' ? 'selected' : ''}>Inscrição Municipal</option>
            `;
            } else {
                opcoesDocumento = `
                <option value="">Tipo</option>
                <option value="rg" ${tipo === 'rg' ? 'selected' : ''}>RG</option>
                <option value="Inscrição Estadual" ${tipo === 'Inscrição Estadual' ? 'selected' : ''}>Inscrição Estadual</option>
                <option value="Inscrição Municipal" ${tipo === 'Inscrição Municipal' ? 'selected' : ''}>Inscrição Municipal</option>
                <option value="CNH" ${tipo === 'CNH' ? 'selected' : ''}>CNH</option>
                <option value="Passaporte" ${tipo === 'Passaporte' ? 'selected' : ''}>Passaporte</option>
                <option value="Outros" ${tipo === 'Outros' ? 'selected' : ''}>Outros</option>
            `;
            }

            const documentoRow = `
            <div class="documento-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                <div style="display: flex; gap: 10px; margin-bottom: ${showEndereco ? '10px' : '0'}; align-items: center;">
                    <select name="doc_tipo_documento[]" class="form-control doc-tipo" style="width: 150px;">
                        ${opcoesDocumento}
                    </select>
                    <input type="text" name="doc_numero[]" value="${numero}" placeholder="Número" class="form-control" style="flex: 1;" />
                    <input type="text" name="doc_orgao_expedidor[]" value="${orgao}" placeholder="Órgão Expedidor" class="form-control" style="flex: 1;" />
                    <select name="doc_natureza_contribuinte[]" class="form-control" style="width: 150px;">
                        <option value="">Natureza</option>
                        <option value="Contribuinte" ${natureza === 'Contribuinte' ? 'selected' : ''}>Contribuinte</option>
                        <option value="Não Contribuinte">Não Contribuinte</option>
                    </select>
                    <button type="button" class="btn btn-mini btn-danger remove-documento" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="endereco-vinculo" style="display: ${showEndereco ? 'flex' : 'none'}; gap: 10px; align-items: center;">
                    <label style="margin: 0; white-space: nowrap;">Vincular ao endereço:</label>
                    <select name="DOC_ENDE_IDX[]" class="form-control" style="flex: 1;">
                        <option value="">Selecione um endereço</option>
                    </select>
                </div>
            </div>
        `;
            $('#documentos-container').append(documentoRow);
            documentoIndex++;

            // Atualizar opções de endereço
            atualizarOpcoesEndereco();
        }

        // Remover documento
        $(document).on('click', '.remove-documento', function () {
            $(this).closest('.documento-row').remove();
        });

        // Controle do campo endereço nos documentos
        $(document).on('change', '.doc-tipo', function () {
            const row = $(this).closest('.documento-row');
            const tipo = $(this).val();
            const enderecoVinculo = row.find('.endereco-vinculo');

            if (tipo === 'Inscrição Estadual' || tipo === 'Inscrição Municipal') {
                enderecoVinculo.show();
            } else {
                enderecoVinculo.hide();
            }
        });

        // Adicionar endereço inline
        $('#btnAdicionarEndereco').on('click', function () {
            const isPrimeiro = $('#enderecos-container .endereco-row').length === 0;
            const checkedAttr = isPrimeiro ? 'checked' : '';

            const enderecoRow = `
            <div class="endereco-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="end_logradouro[]" placeholder="Logradouro" class="form-control" style="flex: 3;" />
                    <input type="text" name="end_numero[]" placeholder="Número" class="form-control" style="width: 80px;" />
                    <input type="text" name="end_complemento[]" placeholder="Complemento" class="form-control" style="flex: 2;" />
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="end_cep[]" placeholder="cep" class="form-control cep-mask" style="width: 110px;" />
                    <input type="text" name="END_BAIRRO[]" placeholder="Bairro" class="form-control" style="flex: 1;" />
                    <input type="text" name="END_CIDADE[]" placeholder="Cidade *" class="form-control" style="flex: 1;" title="Campo obrigatório - necessário para determinar o município" />
                    <input type="text" name="END_UF[]" placeholder="UF *" class="form-control" style="width: 50px;" maxlength="2" title="Campo obrigatório - necessário para determinar o estado" />
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <select name="END_TIPO[]" class="form-control" style="width: 150px;">
                        <option value="Comercial">Comercial</option>
                        <option value="Residencial">Residencial</option>
                        <option value="Cobrança">Cobrança</option>
                        <option value="Entrega">Entrega</option>
                        <option value="Outros">Outros</option>
                    </select>
                    <label class="checkbox" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                        <input type="radio" name="endereco_padrao" value="novo_${enderecoIndex}" ${checkedAttr} style="margin-right: 5px;" />
                        <strong>Endereço Padrão</strong>
                    </label>
                    <div style="flex: 1;"></div>
                    <button type="button" class="btn btn-mini btn-danger remove-endereco" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
            $('#enderecos-container').append(enderecoRow);
            enderecoIndex++;
            atualizarOpcoesEndereco();

            // Aplicar máscara de CEP no novo campo
            $('.cep-mask').mask('00000-000');
        });

        function atualizarOpcoesEndereco() {
            $('select[name="DOC_ENDE_IDX[]"]').each(function () {
                const select = $(this);
                const currentVal = select.val();
                select.empty().append('<option value="">Selecione</option>');

                $('#enderecos-container .endereco-row').each(function (index) {
                    const row = $(this);
                    const tipo = row.find('select[name="END_TIPO[]"]').val();
                    const logradouro = row.find('input[name="end_logradouro[]"]').val();
                    const numero = row.find('input[name="end_numero[]"]').val();

                    select.append(`<option value="${index}">${tipo} - ${logradouro}, ${numero}</option>`);
                });

                if (currentVal) {
                    select.val(currentVal);
                }
            });
        }

        // Controlar endereço padrão (apenas um pode ser marcado)
        $(document).on('change', 'input[name="endereco_padrao"]', function () {
            if ($(this).is(':checked')) {
                // Desmarcar todos os outros
                $('input[name="endereco_padrao"]').not(this).prop('checked', false);
            }
        });

        // Remover endereço
        $(document).on('click', '.remove-endereco', function () {
            var eraPadrao = $(this).closest('.endereco-row').find('input[name="endereco_padrao"]').is(':checked');
            $(this).closest('.endereco-row').remove();

            // Se removeu o padrão, marcar o primeiro disponível
            if (eraPadrao) {
                $('input[name="endereco_padrao"]').first().prop('checked', true);
            }

            atualizarOpcoesEndereco();
        });

        // Auto-focus no logradouro quando CEP completo
        $(document).on('input', 'input[name="end_cep[]"]', function () {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                var enderecoRow = $(this).closest('.endereco-row');
                enderecoRow.find('input[name="end_logradouro[]"]').focus();
            }
        });

        // Buscar endereço pelo CEP quando sair do campo
        $(document).on('blur', 'input[name="end_cep[]"]', function () {
            var cepField = $(this);
            var cep = cepField.val().replace(/\D/g, '');

            // Verificar se CEP tem 8 dígitos
            if (cep.length === 8) {
                var enderecoRow = cepField.closest('.endereco-row');

                // Mostrar loading no campo
                cepField.css('background-color', '#f0f8ff');

                $.ajax({
                    url: `https://viacep.com.br/ws/${cep}/json/`,
                    method: 'GET',
                    dataType: 'json',
                    timeout: 5000,
                    success: function (data) {
                        if (!data.erro) {
                            // Preencher campos do endereço
                            enderecoRow.find('input[name="end_logradouro[]"]').val(data.logradouro || '');
                            enderecoRow.find('input[name="END_BAIRRO[]"]').val(data.bairro || '');
                            enderecoRow.find('input[name="END_CIDADE[]"]').val(data.localidade || '');
                            enderecoRow.find('input[name="END_UF[]"]').val(data.uf || '');
                            enderecoRow.find('input[name="end_complemento[]"]').val(data.complemento || '');

                            // Focar no campo número
                            enderecoRow.find('input[name="end_numero[]"]').focus();

                            // Feedback visual de sucesso
                            cepField.css('background-color', '#d4edda');
                            setTimeout(function () {
                                cepField.css('background-color', '');
                            }, 1000);
                        } else {
                            // CEP não encontrado - feedback visual
                            cepField.css('background-color', '#f8d7da');
                            cepField.attr('placeholder', 'CEP não encontrado');
                            setTimeout(function () {
                                cepField.css('background-color', '');
                                cepField.attr('placeholder', 'cep');
                            }, 2000);

                            // Focar no campo número para continuar manualmente
                            enderecoRow.find('input[name="end_numero[]"]').focus();
                        }
                    },
                    error: function () {
                        console.log('Erro ao buscar CEP');
                        cepField.css('background-color', '');
                    }
                });
            }
        });

        // Validação do formulário
        $('#formPessoa').on('submit', function (e) {
            // Validar se pelo menos um tipo de pessoa foi selecionado
            var tiposSelecionados = $('input[name="TIPOS_PESSOA[]"]:checked').length;

            if (tiposSelecionados === 0) {
                e.preventDefault();

                // Mostrar mensagem de erro
                var errorDiv = document.getElementById('tipos-pessoa-error');
                if (errorDiv) {
                    errorDiv.style.display = 'block';
                }
                $('#tipos-pessoa-error').css('display', 'block');

                // Marcar borda em vermelho
                $('#tipos-pessoa-container').css('border', '2px solid #a94442');

                // Scroll até a seção de tipos de pessoa
                $('html, body').animate({
                    scrollTop: $('#tipos-pessoa-container').offset().top - 100
                }, 500);

                return false;
            } else {
                // Remover mensagem de erro se houver tipos selecionados
                $('#tipos-pessoa-error').css('display', 'none');
                $('#tipos-pessoa-container').css('border', '1px solid #ddd');
            }
        });

        // Remover erro quando usuário marcar um checkbox
        $('input[name="TIPOS_PESSOA[]"]').on('change', function () {
            if ($('input[name="TIPOS_PESSOA[]"]:checked').length > 0) {
                $('#tipos-pessoa-error').css('display', 'none');
                $('#tipos-pessoa-container').css('border', '1px solid #ddd');
            }
        });

        $('#formPessoa').validate({
            errorElement: 'span',
            errorClass: 'help-inline',
            focusInvalid: true,
            highlight: function (element) {
                $(element).closest('.control-group').addClass('error');
            },
            unhighlight: function (element) {
                $(element).closest('.control-group').removeClass('error');
                $(element).closest('.control-group').addClass('success');
            },
            errorPlacement: function (error, element) {
                error.addClass('help-inline');
                element.closest('.controls').append(error);
            },
            rules: {
                pes_fisico_juridico: { required: true },
                pes_nome: { required: true },
                pes_cpfcnpj: {
                    required: true,
                    customCPFCNPJ: true
                },
                'END_CIDADE[]': { required: true },
                'END_UF[]': { required: true }
            },
            messages: {
                pes_fisico_juridico: 'Selecione o tipo de cliente',
                pes_nome: 'Nome é obrigatório',
                pes_cpfcnpj: 'CPF/CNPJ é obrigatório e deve ser válido',
                'END_CIDADE[]': 'Cidade é obrigatória para determinar o município',
                'END_UF[]': 'UF é obrigatória para determinar o estado'
            },
            invalidHandler: function (event, validator) {
                $('.alert-error').show();
            }
        });

        // Método customizado de validação
        $.validator.addMethod("customCPFCNPJ", function (value, element) {
            return validarCPFCNPJ(value);
        }, "CPF/CNPJ inválido");

        // AJAX Submission logic
        $('#formPessoa').submit(function (e) {
            e.preventDefault();

            if (!$(this).valid()) return;

            var form = $(this);
            var btn = form.find('button[type=submit]');
            var originalBtnText = btn.html();

            // Disable button and show spinner
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Salvando...');

            // Remove existing alerts
            $('.alert').remove();
            $('.form_error').remove();

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.result) {
                        // Success
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false,
                            willClose: () => {
                                window.location.href = response.redirect;
                            }
                        });
                    } else {
                        // Validation Error
                        Swal.fire({
                            icon: 'error',
                            title: 'Atenção',
                            html: response.message
                        });
                    }
                    // Reset button
                    btn.prop('disabled', false).html(originalBtnText);
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro Interno',
                        text: 'Ocorreu um erro ao processar a requisição.'
                    });
                    console.error(error);
                    btn.prop('disabled', false).html(originalBtnText);
                }
            });
        });

        // ==========================================
        // CARREGAR DADOS EXISTENTES NA EDIÇÃO
        // ==========================================

        // Carregar telefones
        <?php if (isset($telefones) && !empty($telefones)): ?>
            <?php foreach ($telefones as $tel): ?>
                const telefoneExistente<?php echo $tel->tel_id; ?> = `
                <div class="telefone-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                    <input type="hidden" name="tel_id[]" value="<?php echo $tel->tel_id; ?>" />
                    <select name="tel_tipo[]" class="form-control" style="width: 120px;">
                        <option value="Comercial" <?php echo $tel->tel_tipo == 'Comercial' ? 'selected' : ''; ?>>Comercial</option>
                        <option value="Residencial" <?php echo $tel->tel_tipo == 'Residencial' ? 'selected' : ''; ?>>Residencial</option>
                        <option value="Celular" <?php echo $tel->tel_tipo == 'Celular' ? 'selected' : ''; ?>>Celular</option>
                        <option value="WhatsApp" <?php echo $tel->tel_tipo == 'WhatsApp' ? 'selected' : ''; ?>>WhatsApp</option>
                    </select>
                    <input type="text" name="tel_ddd[]" maxlength="2" placeholder="DDD" class="form-control tel-ddd" style="width: 60px;" value="<?php echo $tel->tel_ddd; ?>" />
                    <input type="text" name="tel_numero[]" placeholder="Número" class="form-control tel-numero" style="flex: 1;" value="<?php echo $tel->tel_numero; ?>" />
                    <input type="text" name="tel_observacao[]" placeholder="Observação (opcional)" class="form-control" style="flex: 1;" value="<?php echo $tel->tel_observacao; ?>" />
                    <button type="button" class="btn btn-mini btn-danger remove-telefone" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
                $('#telefones-container').append(telefoneExistente<?php echo $tel->tel_id; ?>);
            <?php endforeach; ?>
        <?php endif; ?>

        // Carregar emails
        <?php if (isset($emails) && !empty($emails)): ?>
            <?php foreach ($emails as $email): ?>
                const emailExistente<?php echo $email->eml_id; ?> = `
                <div class="email-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                    <input type="hidden" name="EMAIL_ID[]" value="<?php echo $email->eml_id; ?>" />
                    <select name="EMAIL_TIPO[]" class="form-control" style="width: 120px;">
                        <option value="Comercial" <?php echo $email->eml_tipo == 'Comercial' ? 'selected' : ''; ?>>Comercial</option>
                        <option value="Pessoal" <?php echo $email->eml_tipo == 'Pessoal' ? 'selected' : ''; ?>>Pessoal</option>
                        <option value="Outros" <?php echo $email->eml_tipo == 'Outros' ? 'selected' : ''; ?>>Outros</option>
                    </select>
                    <input type="text" name="EMAIL_NOME[]" placeholder="Nome" class="form-control" style="flex: 1;" value="<?php echo $email->eml_nome; ?>" />
                    <input type="email" name="EMAIL_ENDERECO[]" placeholder="Email" class="form-control" style="flex: 1.5;" value="<?php echo $email->eml_email; ?>" />
                    <button type="button" class="btn btn-mini btn-danger remove-email" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
                $('#emails-container').append(emailExistente<?php echo $email->eml_id; ?>);
                emailIndex++;
            <?php endforeach; ?>
        <?php endif; ?>

        // Carregar endereços
        console.log('Endereços recebidos:', <?php echo json_encode(isset($enderecos) ? count($enderecos) : 0); ?>);
        <?php if (isset($enderecos) && !empty($enderecos)): ?>
            console.log('Carregando endereços...');
            <?php foreach ($enderecos as $end): ?>
                console.log('Endereço ID:', <?php echo $end->end_id; ?>);
                const enderecoExistente<?php echo $end->end_id; ?> = `
                <div class="endereco-row" style="margin-bottom: 15px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                    <input type="hidden" name="end_id[]" value="<?php echo $end->end_id; ?>" />
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="end_logradouro[]" placeholder="Logradouro" class="form-control" style="flex: 3;" value="<?php echo isset($end->end_logradouro) ? $end->end_logradouro : ''; ?>" />
                        <input type="text" name="end_numero[]" placeholder="Número" class="form-control" style="width: 80px;" value="<?php echo isset($end->end_numero) ? $end->end_numero : ''; ?>" />
                        <input type="text" name="end_complemento[]" placeholder="Complemento" class="form-control" style="flex: 2;" value="<?php echo isset($end->end_complemento) ? $end->end_complemento : ''; ?>" />
                    </div>
                    <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                        <input type="text" name="end_cep[]" placeholder="cep" class="form-control cep-mask" style="width: 110px;" value="<?php echo isset($end->end_cep) ? $end->end_cep : ''; ?>" />
                        <input type="text" name="END_BAIRRO[]" placeholder="Bairro" class="form-control" style="flex: 1;" value="<?php echo isset($end->bai_nome) ? $end->bai_nome : ''; ?>" />
                        <input type="text" name="END_CIDADE[]" placeholder="Cidade *" class="form-control" style="flex: 1;" value="<?php echo isset($end->mun_nome) ? $end->mun_nome : ''; ?>" title="Campo obrigatório - necessário para determinar o município" />
                        <input type="text" name="END_UF[]" placeholder="UF *" class="form-control" style="width: 50px;" maxlength="2" value="<?php echo isset($end->est_uf) ? $end->est_uf : ''; ?>" title="Campo obrigatório - necessário para determinar o estado" />
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <select name="END_TIPO[]" class="form-control" style="width: 150px;">
                            <option value="Comercial" <?php echo $end->end_tipo_endenreco == 'Geral' || $end->end_tipo_endenreco == 'Comercial' ? 'selected' : ''; ?>>Comercial</option>
                            <option value="Residencial" <?php echo $end->end_tipo_endenreco == 'Residencial' ? 'selected' : ''; ?>>Residencial</option>
                            <option value="Cobrança" <?php echo $end->end_tipo_endenreco == 'Cobranca' ? 'selected' : ''; ?>>Cobrança</option>
                            <option value="Entrega" <?php echo $end->end_tipo_endenreco == 'Entrega' ? 'selected' : ''; ?>>Entrega</option>
                            <option value="Outros" <?php echo $end->end_tipo_endenreco == 'Outros' ? 'selected' : ''; ?>>Outros</option>
                        </select>
                        <label class="checkbox" style="margin: 0; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="endereco_padrao" value="<?php echo $end->end_id; ?>" <?php echo ($end->end_padrao == 1) ? 'checked' : ''; ?> />
                            <strong>Endereço Padrão</strong>
                        </label>
                        <div style="flex: 1;"></div>
                        <button type="button" class="btn btn-mini btn-danger remove-endereco" style="width: 30px; height: 30px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
                $('#enderecos-container').append(enderecoExistente<?php echo $end->end_id; ?>);
                enderecoIndex++;

                // Aplicar máscara de CEP
                $('.cep-mask').mask('00000-000');
            <?php endforeach; ?>

            // Atualizar opções de endereço após carregar todos
            atualizarOpcoesEndereco();
        <?php endif; ?>

        // Carregar documentos
        <?php if (isset($documentos) && !empty($documentos)): ?>
            <?php foreach ($documentos as $doc): ?>
                adicionarDocumento({
                    tipo: '<?php echo $doc->doc_tipo_documento; ?>',
                    numero: '<?php echo $doc->doc_numero; ?>',
                    orgao: '<?php echo $doc->doc_orgao_expedidor; ?>',
                    natureza: '<?php echo $doc->doc_natureza_contribuinte; ?>'
                });
            <?php endforeach; ?>
        <?php endif; ?>

        // Marcar tipos de pessoa vinculados
        <?php if (isset($tipos_vinculados) && !empty($tipos_vinculados)): ?>
            <?php foreach ($tipos_vinculados as $tipo): ?>
                $('input[name="TIPOS_PESSOA[]"][value="<?php echo $tipo->TPP_ID; ?>"]').prop('checked', true).trigger('change');
            <?php endforeach; ?>
        <?php endif; ?>

        // Carregar dados de cliente
        <?php if (isset($cliente) && $cliente): ?>
            $('#CLN_ENABLE').val('1');
            $('#cln_limite_credito').val('<?php echo number_format($cliente->cln_limite_credito, 2, ',', '.'); ?>');
            $('#cln_dias_carencia').val('<?php echo $cliente->cln_dias_carencia; ?>');
            $('#cln_situacao').val('<?php echo $cliente->cln_situacao; ?>');
            $('#cln_comprar_aprazo').prop('checked', <?php echo $cliente->cln_comprar_aprazo ? 'true' : 'false'; ?>);
            $('#cln_bloqueio_financeiro').prop('checked', <?php echo $cliente->cln_bloqueio_financeiro ? 'true' : 'false'; ?>);
            $('#cln_emitir_nfe').prop('checked', <?php echo $cliente->cln_emitir_nfe ? 'true' : 'false'; ?>);
            $('#cln_objetivo_comercial').val('<?php echo isset($cliente->cln_objetivo_comercial) ? $cliente->cln_objetivo_comercial : ''; ?>');
            $('#secao-financeiro').show();

            // Carregar vendedores permitidos
            <?php if (isset($vendedores_permitidos) && !empty($vendedores_permitidos)): ?>
                <?php foreach ($vendedores_permitidos as $vp): ?>
                    // Buscar lista de vendedores e adicionar
                    $.ajax({
                        url: '<?php echo base_url(); ?>index.php/pessoas/listarVendedores',
                        method: 'GET',
                        dataType: 'json',
                        async: false,
                        success: function (vendedores) {
                            var optionsHtml = '<option value="">Selecione um vendedor</option>';
                            vendedores.forEach(function (v) {
                                var selected = v.pes_id == <?php echo $vp->VEN_PES_ID; ?> ? 'selected' : '';
                                optionsHtml += `<option value="${v.pes_id}" ${selected}>${v.pes_nome}</option>`;
                            });

                            var checkedAttr = <?php echo $vp->clv_padrao ? 'true' : 'false'; ?> ? 'checked' : '';

                            const vendedorRow = `
                            <div class="vendedor-permitido-row" style="margin-bottom: 8px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;">
                                <input type="hidden" name="clv_id[]" value="<?php echo $vp->clv_id; ?>" />
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <select name="CLV_VEN_PES_ID[]" class="form-control vendedor-select" style="flex: 1; font-size: 12px;">
                                        ${optionsHtml}
                                    </select>
                                    <label class="checkbox" style="margin: 0; white-space: nowrap;">
                                        <input type="radio" name="clv_padrao" value="<?php echo $vp->VEN_PES_ID; ?>" class="vendedor-padrao-radio" ${checkedAttr} style="margin-right: 3px;" /> 
                                        <small>Padrão</small>
                                    </label>
                                    <button type="button" class="btn btn-mini btn-danger remove-vendedor-permitido" style="width: 25px; height: 25px; padding: 0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                        <i class="fas fa-trash" style="font-size: 10px;"></i>
                                    </button>
                                </div>
                            </div>
                        `;
                            $('#vendedores-permitidos-container').append(vendedorRow);
                        }
                    });
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>

        // Carregar dados de vendedor
        <?php if (isset($vendedor) && $vendedor): ?>
            $('#VEN_ENABLE').val('1');
            $('#ven_percentual_comissao').val('<?php echo number_format($vendedor->ven_percentual_comissao, 2, ',', '.'); ?>');
            $('#ven_tipo_comissao').val('<?php echo $vendedor->ven_tipo_comissao; ?>');
            $('#ven_meta_mensal').val('<?php echo number_format($vendedor->ven_meta_mensal, 2, ',', '.'); ?>');
            $('#secao-vendedor').show();
        <?php endif; ?>

        // Mostrar botão buscar CNPJ se for pessoa jurídica
        <?php if (isset($result) && $result->pes_fisico_juridico == 'J'): ?>
            $('#btnBuscarCNPJ').show();
        <?php endif; ?>
    });
</script>