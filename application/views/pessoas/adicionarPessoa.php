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

    /* Garantir que inputs não estourem o container */
    .form-section input[type="text"],
    .form-section input[type="date"],
    .form-section select {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        height: 30px;
        padding: 4px 8px;
        line-height: 20px;
        font-size: 14px;
    }

    /* Ajuste específico para o campo código que tem largura fixa */
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
        flex-direction: column;
        gap: 0;
        align-items: flex-start;
    }

    #tipos-pessoa-container label.checkbox {
        display: block !important;
        margin: 0 0 12px 0 !important;
        width: 100%;
        font-size: 14px;
    }

    /* Responsivo */
    @media (max-width: 768px) {
        .form-section-content {
            padding: 15px;
        }
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
                <h5>Cadastro de Pessoa</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formPessoa" method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">

                    <!-- Seções lado a lado -->
                    <div class="row-fluid row-flex" style="margin: 20px 0 0 0; padding: 0;">
                        <!-- Dados Gerais (Esquerda) -->
                        <div class="span7">
                            <div class="form-section" style="height: 100%;">
                                <div class="form-section-header">
                                    <i class="fas fa-edit"></i>
                                    <span>Dados gerais</span>
                                </div>
                                <div class="form-section-content">
                                    <!-- Campo Tipo oculto mas mantido para JavaScript -->
                                    <input type="hidden" id="PES_FISICO_JURIDICO" name="PES_FISICO_JURIDICO"
                                        value="<?php echo set_value('PES_FISICO_JURIDICO'); ?>">

                                    <!-- Linha 1: Código e Situação -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="PES_CODIGO" class="control-label">Código</label>
                                                <div class="controls">
                                                    <input id="PES_CODIGO" type="text" name="PES_CODIGO"
                                                        value="<?php echo set_value('PES_CODIGO'); ?>"
                                                        placeholder="Em branco = gerar" readonly
                                                        style="width: 150px;" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="PES_SITUACAO" class="control-label">Situação</label>
                                                <div class="controls">
                                                    <select id="PES_SITUACAO" name="PES_SITUACAO">
                                                        <option value="1" <?php echo set_value('PES_SITUACAO', '1') == '1' ? 'selected' : ''; ?>>Ativo</option>
                                                        <option value="0" <?php echo set_value('PES_SITUACAO') == '0' ? 'selected' : ''; ?>>Inativo</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2: CPF/CNPJ e Nascimento/Abertura -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span7">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="PES_CPFCNPJ" class="control-label">CPF/CNPJ<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <div style="display: flex; gap: 5px;">
                                                        <input id="PES_CPFCNPJ" type="text" name="PES_CPFCNPJ"
                                                            value="<?php echo set_value('PES_CPFCNPJ'); ?>"
                                                            inputmode="numeric" autocomplete="off" style="flex: 1;" />
                                                        <button type="button" id="btnBuscarCNPJ" class="btn btn-info"
                                                            style="display:none;">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span5">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="PES_NASCIMENTO_ABERTURA" id="labelNascimentoAbertura"
                                                    class="control-label">Abertura</label>
                                                <div class="controls">
                                                    <input id="PES_NASCIMENTO_ABERTURA" type="date"
                                                        name="PES_NASCIMENTO_ABERTURA"
                                                        value="<?php echo set_value('PES_NASCIMENTO_ABERTURA'); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 3: Nome (largura total) -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="PES_NOME" class="control-label">Nome<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input id="PES_NOME" type="text" name="PES_NOME"
                                                        value="<?php echo set_value('PES_NOME'); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 4: Razão Social (largura total) -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="PES_RAZAO_SOCIAL" class="control-label">Razão Social</label>
                                                <div class="controls">
                                                    <input id="PES_RAZAO_SOCIAL" type="text" name="PES_RAZAO_SOCIAL"
                                                        value="<?php echo set_value('PES_RAZAO_SOCIAL'); ?>" />
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
                                                // Buscar tipos de pessoa do banco de dados
                                                $this->db->where('ativo', 1);
                                                $this->db->order_by('nome', 'ASC');
                                                $tipos_pessoa = $this->db->get('tipos_pessoa')->result();

                                                foreach ($tipos_pessoa as $tipo): ?>
                                                    <label class="checkbox"
                                                        style="display:block; margin-bottom:12px; font-size: 14px;">
                                                        <input type="checkbox"
                                                            id="<?php echo strtoupper($tipo->nome); ?>_ENABLE"
                                                            name="TIPOS_PESSOA[]" value="<?php echo $tipo->id; ?>"
                                                            style="margin-right: 8px;" />
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
                                            <textarea id="PES_OBSERVACAO" name="PES_OBSERVACAO" rows="4"
                                                style="width:100%; resize: vertical;"><?php echo set_value('PES_OBSERVACAO'); ?></textarea>
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
                                                <label for="CLN_LIMITE_CREDITO" class="control-label">Limite de
                                                    Crédito</label>
                                                <div class="controls">
                                                    <input id="CLN_LIMITE_CREDITO" type="text" name="CLN_LIMITE_CREDITO"
                                                        class="span12" value="0,00" placeholder="0,00" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="span6">
                                            <div class="control-group">
                                                <label for="CLN_DIAS_CARENCIA" class="control-label">Dias de
                                                    Carência</label>
                                                <div class="controls">
                                                    <input id="CLN_DIAS_CARENCIA" type="number" name="CLN_DIAS_CARENCIA"
                                                        class="span12" value="0" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label for="CLN_SITUACAO" class="control-label">Situação</label>
                                        <div class="controls">
                                            <select id="CLN_SITUACAO" name="CLN_SITUACAO" class="span12">
                                                <option value="1" selected>Ativo</option>
                                                <option value="0">Inativo</option>
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
                                                <input type="checkbox" id="CLN_COMPRAR_APRAZO" name="CLN_COMPRAR_APRAZO"
                                                    value="1" /> Comprar a prazo
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" id="CLN_BLOQUEIO_FINANCEIRO"
                                                    name="CLN_BLOQUEIO_FINANCEIRO" value="1" checked /> Bloqueio
                                                financeiro
                                            </label>
                                            <label class="checkbox" style="display:block; margin-bottom: 10px;">
                                                <input type="checkbox" id="CLN_EMITIR_NFE" name="CLN_EMITIR_NFE"
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

                                <!-- Nova Linha: Objetivo Comercial -->
                                <div class="span4">
                                    <div class="control-group">
                                        <label for="CLN_OBJETIVO_COMERCIAL" class="control-label">Objetivo
                                            Comercial</label>
                                        <div class="controls">
                                            <select id="CLN_OBJETIVO_COMERCIAL" name="CLN_OBJETIVO_COMERCIAL"
                                                class="span12">
                                                <option value="Consumo" selected>Consumo</option>
                                                <option value="Revenda">Revenda</option>
                                                <option value="Industrialização">Industrialização</option>
                                                <option value="Orgão Público">Orgão Público</option>
                                            </select>
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
                                        <label for="VEN_PERCENTUAL_COMISSAO" class="control-label">Percentual de
                                            Comissão (%)</label>
                                        <div class="controls">
                                            <input id="VEN_PERCENTUAL_COMISSAO" type="text"
                                                name="VEN_PERCENTUAL_COMISSAO"
                                                value="<?php echo set_value('VEN_PERCENTUAL_COMISSAO'); ?>"
                                                placeholder="0,00" />
                                        </div>
                                    </div>
                                </div>

                                <div class="span4">
                                    <div class="control-group">
                                        <label for="VEN_TIPO_COMISSAO" class="control-label">Tipo de Comissão</label>
                                        <div class="controls">
                                            <select id="VEN_TIPO_COMISSAO" name="VEN_TIPO_COMISSAO">
                                                <option value="">Selecione</option>
                                                <option value="Sobre Venda" <?php echo set_value('VEN_TIPO_COMISSAO') == 'Sobre Venda' ? 'selected' : ''; ?>>
                                                    Sobre Venda</option>
                                                <option value="Sobre Lucro" <?php echo set_value('VEN_TIPO_COMISSAO') == 'Sobre Lucro' ? 'selected' : ''; ?>>
                                                    Sobre Lucro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="span4">
                                    <div class="control-group">
                                        <label for="VEN_META_MENSAL" class="control-label">Meta Mensal (R$)</label>
                                        <div class="controls">
                                            <input id="VEN_META_MENSAL" type="text" name="VEN_META_MENSAL"
                                                value="<?php echo set_value('VEN_META_MENSAL'); ?>"
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
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                                    <span class="button__text2">Adicionar</span>
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

<!-- Modal CPF/CNPJ Duplicado -->
<div id="modalCpfCnpjDuplicado" class="modal hide fade" tabindex="-1" role="dialog"
    aria-labelledby="modalDuplicadoLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalDuplicadoLabel">
            <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i>
            CPF/CNPJ já cadastrado
        </h3>
    </div>
    <div class="modal-body">
        <div class="alert alert-warning">
            <strong>Atenção!</strong> Este <span id="tipoCpfCnpj"></span> já está cadastrado no sistema.
        </div>
        <div style="margin-top: 20px;">
            <h4>Dados do cadastro existente:</h4>
            <table class="table table-bordered">
                <tr>
                    <td><strong>Nome:</strong></td>
                    <td id="pessoaNome"></td>
                </tr>
                <tr id="rowRazaoSocial" style="display:none;">
                    <td><strong>Razão Social:</strong></td>
                    <td id="pessoaRazaoSocial"></td>
                </tr>
                <tr>
                    <td><strong><span id="tipoCpfCnpjLabel"></span>:</strong></td>
                    <td id="pessoaCpfCnpj"></td>
                </tr>
            </table>
        </div>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>O que deseja fazer?</strong><br>
            Você pode editar o cadastro existente ou apenas visualizá-lo.
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <button id="btnVisualizarPessoa" class="btn btn-info">
            <i class="fas fa-eye"></i> Visualizar
        </button>
        <button id="btnEditarPessoa" class="btn btn-primary">
            <i class="fas fa-edit"></i> Editar
        </button>
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
                    select.append('<option value="RG">RG</option>');
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

        // Máscara dinâmica para CPF/CNPJ (igual ao cadastro de clientes)
        var cpfCnpjMascara = function (val) {
            return val.replace(/\D/g, '').length > 11 ? '00.000.000/0000-00' : '000.000.000-009';
        };
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


        var cpfCnpjOptions = {
            onKeyPress: function (val, e, field, options) {
                var cleanVal = val.replace(/\D/g, '');

                // Atualizar tipo e botão automaticamente
                if (cleanVal.length <= 11) {
                    $('#PES_FISICO_JURIDICO').val('F');
                    $('#btnBuscarCNPJ').hide();
                    atualizarTiposDocumento('F');
                    atualizarLabelData('F');
                } else {
                    $('#PES_FISICO_JURIDICO').val('J');
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

        $('#PES_CPFCNPJ').mask(cpfCnpjMascara, cpfCnpjOptions);

        // Suporte para colar (paste)
        $('#PES_CPFCNPJ').on('paste', function (e) {
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
                $('#PES_FISICO_JURIDICO').val('J');
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
                $('#PES_FISICO_JURIDICO').val('F');
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

        // Foco inicial no campo CPF/CNPJ após um pequeno delay
        setTimeout(function () {
            $('#PES_CPFCNPJ').focus();
        }, 100);

        // Verificar se CPF/CNPJ já existe quando sair do campo
        $('#PES_CPFCNPJ').on('blur', function () {
            var cpfcnpj = $(this).val().replace(/\D/g, '');
            var cpfcnpjFormatado = $(this).val();

            // Só verificar se tiver 11 (CPF) ou 14 (CNPJ) dígitos
            if (cpfcnpj.length === 11 || cpfcnpj.length === 14) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/pessoas/verificarCpfCnpj',
                    method: 'POST',
                    data: { cpfcnpj: cpfcnpj },
                    dataType: 'json',
                    success: function (response) {
                        if (response.exists) {
                            var tipo = cpfcnpj.length === 11 ? 'CPF' : 'CNPJ';

                            // Preencher dados no modal
                            $('#tipoCpfCnpj').text(tipo);
                            $('#tipoCpfCnpjLabel').text(tipo);
                            $('#pessoaNome').text(response.nome);
                            $('#pessoaCpfCnpj').text(cpfcnpjFormatado);

                            // Mostrar/esconder razão social
                            if (response.razao_social && response.razao_social.trim() !== '') {
                                $('#pessoaRazaoSocial').text(response.razao_social);
                                $('#rowRazaoSocial').show();
                            } else {
                                $('#rowRazaoSocial').hide();
                            }

                            // Configurar botões
                            $('#btnEditarPessoa').off('click').on('click', function () {
                                window.location.href = '<?php echo base_url(); ?>index.php/pessoas/editar/' + response.id;
                            });

                            $('#btnVisualizarPessoa').off('click').on('click', function () {
                                window.location.href = '<?php echo base_url(); ?>index.php/pessoas/visualizar/' + response.id;
                            });

                            // Limpar o campo CPF/CNPJ
                            $('#PES_CPFCNPJ').val('');
                            $('#PES_FISICO_JURIDICO').val('');
                            $('#btnBuscarCNPJ').hide();

                            // Mostrar modal com backdrop
                            $('#modalCpfCnpjDuplicado').modal({
                                backdrop: 'static',
                                keyboard: false
                            });

                            // Quando fechar o modal, focar no campo CPF/CNPJ
                            $('#modalCpfCnpjDuplicado').on('hidden', function () {
                                $('#PES_CPFCNPJ').focus();
                            });
                        }
                    },
                    error: function () {
                        console.log('Erro ao verificar CPF/CNPJ');
                    }
                });
            }
        });

        // Copiar nome para razão social se for Pessoa Física
        $('#PES_NOME').on('blur', function () {
            var tipo = $('#PES_FISICO_JURIDICO').val();
            var nome = $(this).val();
            var razaoSocial = $('#PES_RAZAO_SOCIAL').val();

            // Se for pessoa física (F) e nome não estiver vazio e razão social estiver vazia
            if (tipo === 'F' && nome.trim() !== '' && razaoSocial.trim() === '') {
                $('#PES_RAZAO_SOCIAL').val(nome);
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
        $('#PES_FISICO_JURIDICO').on('change', function () {
            const tipo = $(this).val();
            const cnpj = $('#PES_CPFCNPJ');

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
            const cnpj = $('#PES_CPFCNPJ').val().replace(/[^\d]/g, '');
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
                            $('#PES_NOME').val(estab.nome_fantasia);
                        } else {
                            $('#PES_NOME').val(data.razao_social);
                        }
                        $('#PES_RAZAO_SOCIAL').val(data.razao_social);

                        // Preencher data de abertura
                        if (estab.data_inicio_atividade) {
                            $('#PES_NASCIMENTO_ABERTURA').val(estab.data_inicio_atividade);
                        }

                        // Adicionar telefone se disponível (ddd1 e telefone1)
                        if (estab.ddd1 && estab.telefone1) {
                            const telefoneRow = `
                            <div class="telefone-row" style="margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; display: flex; align-items: center; gap: 10px;">
                                <select name="TEL_TIPO[]" class="form-control" style="width: 120px;">
                                    <option value="Comercial" selected>Comercial</option>
                                    <option value="Residencial">Residencial</option>
                                    <option value="Celular">Celular</option>
                                    <option value="WhatsApp">WhatsApp</option>
                                </select>
                                <input type="text" name="TEL_DDD[]" maxlength="2" placeholder="DDD" class="form-control tel-ddd" style="width: 60px;" value="${estab.ddd1}" />
                                <input type="text" name="TEL_NUMERO[]" placeholder="Número" class="form-control tel-numero" style="flex: 1;" value="${estab.telefone1}" />
                                <input type="text" name="TEL_OBSERVACAO[]" placeholder="Observação (opcional)" class="form-control" style="flex: 1;" />
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
                                    <input type="text" name="END_LOGRADOURO[]" placeholder="Logradouro" class="form-control" style="flex: 3;" value="${estab.logradouro || ''}" />
                                    <input type="text" name="END_NUMERO[]" placeholder="Número" class="form-control" style="width: 80px;" value="${estab.numero || ''}" />
                                    <input type="text" name="END_COMPLEMENTO[]" placeholder="Complemento" class="form-control" style="flex: 2;" value="${estab.complemento || ''}" />
                                </div>
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <input type="text" name="END_CEP[]" placeholder="CEP" class="form-control cep-mask" style="width: 110px;" value="${estab.cep || ''}" />
                                    <input type="text" name="END_BAIRRO[]" placeholder="Bairro" class="form-control" style="flex: 1;" value="${estab.bairro || ''}" />
                                    <input type="text" name="END_CIDADE[]" placeholder="Cidade" class="form-control" style="flex: 1;" value="${estab.cidade ? estab.cidade.nome : ''}" />
                                    <input type="text" name="END_UF[]" placeholder="UF" class="form-control" style="width: 50px;" maxlength="2" value="${estab.estado ? estab.estado.sigla : ''}" />
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
                    $('#CLN_LIMITE_CREDITO').val('0,00');
                    $('#CLN_DIAS_CARENCIA').val('0');
                    $('#CLN_BLOQUEIO_FINANCEIRO').prop('checked', true);
                    $('#CLN_EMITIR_NFE').prop('checked', true);
                    $('#CLN_OBJETIVO_COMERCIAL').val('Consumo');
                } else {
                    $('#secao-financeiro').slideUp(300);
                    $('#CLN_ENABLE').val('0');
                    // Limpar campos de cliente quando desmarcar
                    $('#CLN_LIMITE_CREDITO').val('0,00');
                    $('#CLN_DIAS_CARENCIA').val('0');
                    $('#CLN_SITUACAO').val('1');
                    $('#CLN_COMPRAR_APRAZO, #CLN_BLOQUEIO_FINANCEIRO, #CLN_EMITIR_NFE').prop('checked', false);
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
                    $('#VEN_PERCENTUAL_COMISSAO').val('');
                    $('#VEN_TIPO_COMISSAO').val('');
                    $('#VEN_META_MENSAL').val('');
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
                        optionsHtml += `<option value="${v.PES_ID}">${v.PES_NOME}</option>`;
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
                                <input type="radio" name="CLV_PADRAO" value="" class="vendedor-padrao-radio" ${checkedAttr} style="margin-right: 3px;" /> 
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
                <select name="TEL_TIPO[]" class="form-control" style="width: 120px;">
                    <option value="Comercial">Comercial</option>
                    <option value="Residencial">Residencial</option>
                    <option value="Celular">Celular</option>
                    <option value="WhatsApp">WhatsApp</option>
                </select>
                <input type="text" name="TEL_DDD[]" maxlength="2" placeholder="DDD" class="form-control tel-ddd" style="width: 60px;" />
                <input type="text" name="TEL_NUMERO[]" placeholder="Número" class="form-control tel-numero" style="flex: 1;" />
                <input type="text" name="TEL_OBSERVACAO[]" placeholder="Observação (opcional)" class="form-control" style="flex: 1;" />
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
            const tipoPessoa = $('#PES_FISICO_JURIDICO').val() || 'F';

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
                <option value="RG" ${tipo === 'RG' ? 'selected' : ''}>RG</option>
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
                    <select name="DOC_TIPO_DOCUMENTO[]" class="form-control doc-tipo" style="width: 150px;">
                        ${opcoesDocumento}
                    </select>
                    <input type="text" name="DOC_NUMERO[]" value="${numero}" placeholder="Número" class="form-control" style="flex: 1;" />
                    <input type="text" name="DOC_ORGAO_EXPEDIDOR[]" value="${orgao}" placeholder="Órgão Expedidor" class="form-control" style="flex: 1;" />
                    <select name="DOC_NATUREZA_CONTRIBUINTE[]" class="form-control" style="width: 150px;">
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
                    <input type="text" name="END_LOGRADOURO[]" placeholder="Logradouro" class="form-control" style="flex: 3;" />
                    <input type="text" name="END_NUMERO[]" placeholder="Número" class="form-control" style="width: 80px;" />
                    <input type="text" name="END_COMPLEMENTO[]" placeholder="Complemento" class="form-control" style="flex: 2;" />
                </div>
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <input type="text" name="END_CEP[]" placeholder="CEP" class="form-control cep-mask" style="width: 110px;" />
                    <input type="text" name="END_BAIRRO[]" placeholder="Bairro" class="form-control" style="flex: 1;" />
                    <input type="text" name="END_CIDADE[]" placeholder="Cidade" class="form-control" style="flex: 1;" />
                    <input type="text" name="END_UF[]" placeholder="UF" class="form-control" style="width: 50px;" maxlength="2" />
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
                    const logradouro = row.find('input[name="END_LOGRADOURO[]"]').val();
                    const numero = row.find('input[name="END_NUMERO[]"]').val();

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
        $(document).on('input', 'input[name="END_CEP[]"]', function () {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep.length === 8) {
                var enderecoRow = $(this).closest('.endereco-row');
                enderecoRow.find('input[name="END_LOGRADOURO[]"]').focus();
            }
        });

        // Buscar endereço pelo CEP quando sair do campo
        $(document).on('blur', 'input[name="END_CEP[]"]', function () {
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
                            enderecoRow.find('input[name="END_LOGRADOURO[]"]').val(data.logradouro || '');
                            enderecoRow.find('input[name="END_BAIRRO[]"]').val(data.bairro || '');
                            enderecoRow.find('input[name="END_CIDADE[]"]').val(data.localidade || '');
                            enderecoRow.find('input[name="END_UF[]"]').val(data.uf || '');
                            enderecoRow.find('input[name="END_COMPLEMENTO[]"]').val(data.complemento || '');

                            // Focar no campo número
                            enderecoRow.find('input[name="END_NUMERO[]"]').focus();

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
                                cepField.attr('placeholder', 'CEP');
                            }, 2000);

                            // Focar no campo número para continuar manualmente
                            enderecoRow.find('input[name="END_NUMERO[]"]').focus();
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
            // Apenas ao submeter o formulário para salvar (não durante busca de CNPJ)
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
                PES_FISICO_JURIDICO: { required: true },
                PES_NOME: { required: true },
                PES_CPFCNPJ: {
                    required: true,
                    customCPFCNPJ: true
                }
            },
            messages: {
                PES_FISICO_JURIDICO: 'Selecione o tipo de cliente',
                PES_NOME: 'Nome é obrigatório',
                PES_CPFCNPJ: 'CPF/CNPJ é obrigatório e deve ser válido'
            },
            invalidHandler: function (event, validator) {
                $('.alert-error').show();
            }
        });

        // Método customizado de validação
        $.validator.addMethod("customCPFCNPJ", function (value, element) {
            return validarCPFCNPJ(value);
        }, "CPF/CNPJ inválido");
    });
</script>