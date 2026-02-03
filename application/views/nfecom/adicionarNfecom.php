<style>
    /* Estilos organizados para NFE COM */
    .form-section {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin-bottom: 6px;
        background: #fff;
    }

    .form-section-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 6px 10px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
        color: #333;
        font-size: 13px;
    }

    .form-section-content {
        padding: 8px 10px;
    }

    /* Alinhamento dos campos */
    .form-section .control-label {
        width: 120px;
        text-align: right;
        margin-right: 10px;
        font-size: 13px;
        line-height: 1.3;
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
        height: 28px;
        padding: 3px 6px;
        line-height: 18px;
        font-size: 13px;
    }

    .form-section textarea {
        height: auto;
        resize: vertical;
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
    .row-flex>.span6 {
        margin-left: 0 !important;
    }

    .row-flex .form-section {
        flex: 1;
        display: flex;
        flex-direction: column;
        border-radius: 0;
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

    /* Select2 - clientes */
    .cliente-select2 {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.2;
    }

    .cliente-select2-title {
        font-weight: 600;
        color: #222;
    }

    .cliente-select2-meta {
        font-size: 11px;
        color: #666;
        white-space: normal;
        word-break: break-word;
    }

    .servico-select2 {
        display: flex;
        flex-direction: column;
        gap: 2px;
        line-height: 1.2;
    }

    .servico-select2-title {
        font-weight: 600;
        color: #222;
    }

    .servico-select2-meta {
        font-size: 11px;
        color: #666;
        white-space: normal;
        word-break: break-word;
    }

    .servico-row-tax {
        background: #fafafa;
    }

    .servico-row-tax td {
        width: 100%;
    }

    .servico-tax-grid {
        display: flex;
        flex-wrap: nowrap;
        gap: 4px 8px;
        font-size: 10px;
        color: #555;
        align-items: center;
        white-space: nowrap;
        overflow-x: hidden;
        scrollbar-width: thin;
        width: 100%;
        justify-content: space-between;
    }

    .servico-tax-grid strong {
        color: #333;
        font-weight: 600;
    }

    .servico-tax-grid .tax-item {
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        min-width: 0;
        flex: 0 1 auto;
    }

    .servico-tax-grid .tax-item.tight {
        gap: 4px;
    }

    .servico-tax-grid .tax-item .tax-inline {
        display: inline-block;
    }

    .servico-row-main td,
    .servico-row-main input[type="text"] {
        font-size: 14px;
    }

    #tblServicosNfecom {
        table-layout: fixed;
    }

    #tblServicosNfecom th,
    #tblServicosNfecom td {
        overflow: hidden;
    }

    .servico-row-tax td {
        overflow: visible;
    }

    .servico-row-main td:nth-child(2) input {
        width: 100%;
        box-sizing: border-box;
        min-width: 0;
    }

    .servico-row-main td.col-quantidade,
    .servico-row-main td.col-preco-unit,
    .servico-row-main td.col-desc,
    .servico-row-main td.col-outros,
    .servico-row-main td.col-total,
    .servico-row-main th.col-quantidade,
    .servico-row-main th.col-preco-unit,
    .servico-row-main th.col-desc,
    .servico-row-main th.col-outros,
    .servico-row-main th.col-total {
        text-align: right;
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

    /* Seção de Serviços */
    .servico-row {
        transition: all 0.3s ease;
    }

    .servico-row:hover {
        border-color: #007cba;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .servico-row .form-control {
        height: 30px;
        padding: 4px 8px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .servico-row input[readonly] {
        background-color: #f5f5f5;
        border-color: #ddd;
    }

    /* Grid layout para campos de serviço */
    .servico-row .grid-layout {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr 1fr 0.5fr;
        gap: 8px;
        align-items: end;
        margin-bottom: 10px;
    }

    .servico-row .grid-layout>div {
        display: flex;
        flex-direction: column;
    }

    .servico-row .grid-layout label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        font-size: 12px;
    }

    .servico-row .grid-layout input,
    .servico-row .grid-layout select {
        width: 100%;
        height: 30px;
        padding: 4px 8px;
        border: 1px solid #ccc;
        border-radius: 3px;
        font-size: 12px;
    }

    /* Valores calculados */
    .servico-row .valores-calculados {
        display: flex;
        gap: 15px;
        padding-top: 8px;
        border-top: 1px solid #dee2e6;
        font-size: 13px;
    }

    .servico-row .valores-calculados strong {
        color: #333;
    }

    .servico-row .valores-calculados .valor-display {
        color: #28a745;
        font-weight: bold;
    }

    /* Resumo dos valores */
    #servicos-resumo {
        font-size: 14px;
    }

    #servicos-resumo strong {
        color: #333;
    }

    #total-servicos,
    #valor-liquido {
        font-weight: bold;
        color: #28a745;
        font-size: 16px;
    }

    #comissaoAgencia {
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 2px 4px;
        font-size: 12px;
    }

    /* Melhorar aparência do Select2 */
    .select2-container--default .select2-selection--single {
        height: 30px;
        border: 1px solid #ccc;
        border-radius: 3px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        padding-left: 8px;
        color: #555;
    }

    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #999;
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
            <div class="widget-title" style="margin: -20px 0 0; padding: 8px 15px;">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5 style="margin: 0; line-height: 1.2;">Nova NFECom</h5>
            </div>
            <?php
            // Só exibir erro se realmente houver um erro (não apenas no carregamento inicial)
            $error_message = '';
            $success_message = '';

            if ($this->session->flashdata('error')) {
                $error_message = $this->session->flashdata('error');
            } elseif ($custom_error !== '' && $custom_error !== false && $custom_error !== true) {
                // Se custom_error for true, significa que houve erro mas sem mensagem específica
                // Só exibir se houver mensagem de validação ou se foi um POST
                if ($_SERVER['REQUEST_METHOD'] === 'POST' || !is_bool($custom_error)) {
                    $error_message = is_bool($custom_error) ? 'Ocorreu um erro ao processar o formulário. Verifique os campos obrigatórios.' : $custom_error;
                }
            }

            // Verificar mensagem de sucesso
            if ($this->session->flashdata('success')) {
                $success_message = $this->session->flashdata('success');
            }

            if ($error_message) {
                echo '<div class="alert alert-danger" style="margin-bottom: 20px;">';
                echo '<i class="fas fa-exclamation-triangle"></i> ';
                echo $error_message;
                echo '</div>';
            }
            ?>
            <form action="<?php echo site_url('nfecom/adicionar'); ?>" id="formNfecom" method="post"
                class="form-horizontal">
                <!-- Campo hidden para data de emissão (gerada automaticamente) -->
                <input type="hidden" name="dataEmissao" id="dataEmissao" value="<?php echo date('d/m/Y'); ?>">
                <div class="widget-content nopadding tab-content">

                    <!-- Seções lado a lado -->
                    <div class="row-fluid row-flex" style="margin: 8px 0 0 0; padding: 0;">
                        <!-- Dados Principais (Esquerda) -->
                        <div class="span6">
                            <div class="form-section" style="height: 100%;">
                                <div class="form-section-header">
                                    <i class="fas fa-edit"></i>
                                    <span>Dados Principais</span>
                                </div>
                                <div class="form-section-content">
                                    <!-- Linha 0: Operação Comercial -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="opc_id" class="control-label">Op. Comercial<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <select name="opc_id" id="opc_id" required style="width: 100%;">
                                                        <option value="">Selecione uma operação...</option>
                                                        <?php foreach ($operacoes as $index => $op): ?>
                                                            <option value="<?php echo $op->opc_id; ?>" <?php echo ((isset($_POST['opc_id']) && $_POST['opc_id'] == $op->opc_id) || (!isset($_POST['opc_id']) && $index === 0)) ? 'selected' : ''; ?>>
                                                                <?php echo $op->opc_nome; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Linha 1: Cliente -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="cliente" class="control-label">Cliente<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <select name="clientes_id" id="cliente" required
                                                        style="width: 100%;">
                                                        <option value="">Selecione um cliente ou digite para buscar...
                                                        </option>
                                                        <?php foreach ($clientes_iniciais as $cliente): ?>
                                                            <option value="<?php echo $cliente->id; ?>"
                                                                data-nome-fantasia="<?php echo htmlspecialchars($cliente->nome_fantasia ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-razao-social="<?php echo htmlspecialchars($cliente->razao_social ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-cpf-cnpj="<?php echo htmlspecialchars($cliente->cpf_cnpj ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                data-codigo="<?php echo htmlspecialchars($cliente->codigo ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                                                <?php echo (isset($_POST['clientes_id']) && $_POST['clientes_id'] == $cliente->id) ? 'selected' : ''; ?>>
                                                                <?php echo $cliente->text; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2: Endereço do Cliente -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="enderecoClienteSelect" class="control-label">Endereço<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <select name="enderecoClienteSelect" id="enderecoClienteSelect"
                                                        disabled required>
                                                        <option value="">Selecione um cliente primeiro</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2.1: Contatos do Cliente -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="contatoTelefone" class="control-label">Telefone</label>
                                                <div class="controls">
                                                    <input type="text" name="contatoTelefone" id="contatoTelefone"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="contatoEmail" class="control-label">Email</label>
                                                <div class="controls">
                                                    <input type="text" name="contatoEmail" id="contatoEmail" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="contratoId" name="contratoId" value="">
                                    <!-- Linha 5: Observações -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="observacoes" class="control-label">Observações<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <textarea name="observacoes" id="observacoes" rows="8"
                                                        required><?php echo set_value('observacoes'); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Valores e Datas (Direita) -->
                        <div class="span6">
                            <div class="form-section" style="height: 100%;">
                                <div class="form-section-header">
                                    <i class="fas fa-user-tag"></i>
                                    <span>Dados do Assinante e Pagamento</span>
                                </div>
                                <div class="form-section-content">

                                    <!-- Linha 1: Número do Contrato + Código Assinante -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="numeroContrato" class="control-label">Nº do
                                                    Contrato<span class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="text" name="numeroContrato" id="numeroContrato"
                                                        value="<?php echo set_value('numeroContrato'); ?>"
                                                        placeholder="Digite o número do contrato..." required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="iCodAssinante" class="control-label">Cód. Assinante</label>
                                                <div class="controls">
                                                    <input type="text" name="iCodAssinante" id="iCodAssinante"
                                                        value="<?php echo set_value('iCodAssinante'); ?>"
                                                        placeholder="Vazio = CPF/CNPJ">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2: Tipo Assinante e Tipo Serviço -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="tpAssinante" class="control-label">Tipo Assinante<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <select name="tpAssinante" id="tpAssinante" required class="span12">
                                                        <option value="1" <?php echo set_select('tpAssinante', '1'); ?>>
                                                            1 - Comercial</option>
                                                        <option value="2" <?php echo set_select('tpAssinante', '2'); ?>>
                                                            2 - Industrial</option>
                                                        <option value="3" <?php echo set_select('tpAssinante', '3', true); ?>>3 - Residencial/PF</option>
                                                        <option value="4" <?php echo set_select('tpAssinante', '4'); ?>>
                                                            4 - Produtor Rural</option>
                                                        <option value="5" <?php echo set_select('tpAssinante', '5'); ?>>
                                                            5 - Órgão Público Estadual</option>
                                                        <option value="6" <?php echo set_select('tpAssinante', '6'); ?>>
                                                            6 - Prestador de Telecom</option>
                                                        <option value="7" <?php echo set_select('tpAssinante', '7'); ?>>
                                                            7 - Missões Diplomáticas</option>
                                                        <option value="8" <?php echo set_select('tpAssinante', '8'); ?>>
                                                            8 - Igrejas e Templos</option>
                                                        <option value="99" <?php echo set_select('tpAssinante', '99'); ?>>99 - Outros</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="tpServUtil" class="control-label">Tipo Serviço<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <select name="tpServUtil" id="tpServUtil" required class="span12">
                                                        <option value="1" <?php echo set_select('tpServUtil', '1'); ?>>1
                                                            - Telefonia</option>
                                                        <option value="2" <?php echo set_select('tpServUtil', '2'); ?>>2
                                                            - Com. de Dados</option>
                                                        <option value="3" <?php echo set_select('tpServUtil', '3'); ?>>3
                                                            - TV por Assinatura</option>
                                                        <option value="4" <?php echo set_select('tpServUtil', '4'); ?>>4
                                                            - Internet</option>
                                                        <option value="5" <?php echo set_select('tpServUtil', '5'); ?>>5
                                                            - Multimídia</option>
                                                        <option value="6" <?php echo set_select('tpServUtil', '6', true); ?>>6 - Outros</option>
                                                        <option value="7" <?php echo set_select('tpServUtil', '7'); ?>>7
                                                            - Vários (Combo)</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 3: Data s Contrato -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataContratoIni" class="control-label">Início Contrato<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="date" name="dataContratoIni" id="dataContratoIni"
                                                        value="<?php echo set_value('dataContratoIni'); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataContratoFim" class="control-label">Fim Contrato</label>
                                                <div class="controls">
                                                    <input type="date" name="dataContratoFim" id="dataContratoFim"
                                                        value="<?php echo set_value('dataContratoFim'); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 5: Período Uso -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataPeriodoIni" class="control-label">Período Início<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="date" name="dataPeriodoIni" id="dataPeriodoIni"
                                                        value="<?php echo set_value('dataPeriodoIni') ?: date('Y-m-d'); ?>"
                                                        required class="span12">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataPeriodoFim" class="control-label">Período Fim<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="date" name="dataPeriodoFim" id="dataPeriodoFim"
                                                        value="<?php echo set_value('dataPeriodoFim') ?: date('Y-m-d', strtotime('+30 days')); ?>"
                                                        required class="span12">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-section-header"
                                        style="margin-top: 6px; border-top: 1px solid #eee; padding-top: 4px; margin-left: -10px; margin-right: -10px; padding-left: 10px;">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Forma de Pagamento</span>
                                    </div>

                                    <!-- Linha 6: Vencimento -->
                                    <div class="row-fluid" style="margin-bottom: 4px; margin-top: 6px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataVencimento" class="control-label">Vencimento<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="date" name="dataVencimento" id="dataVencimento"
                                                        value="<?php echo set_value('dataVencimento'); ?>" required
                                                        class="span12">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 6: Boleto -->
                                    <div class="row-fluid" style="margin-bottom: 4px; margin-top: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="nfc_linha_digitavel" class="control-label">Bol. (Linha
                                                    Digitável)</label>
                                                <div class="controls">
                                                    <input type="text" name="nfc_linha_digitavel"
                                                        id="nfc_linha_digitavel"
                                                        value="<?php echo set_value('nfc_linha_digitavel'); ?>"
                                                        placeholder="Linha digitável para pagamento">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 7: Pix -->
                                    <div class="row-fluid" style="margin-bottom: 4px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="nfc_chave_pix" class="control-label">Pix (Chave Pix)</label>
                                                <div class="controls">
                                                    <input type="text" name="nfc_chave_pix" id="nfc_chave_pix"
                                                        value="<?php echo set_value('nfc_chave_pix'); ?>"
                                                        placeholder="Chave Pix para o QR Code">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 5: Dados Bancários -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dados_bancarios" class="control-label">Dados
                                                    Bancários</label>
                                                <div class="controls">
                                                    <textarea name="dados_bancarios" id="dados_bancarios" rows="2"
                                                        placeholder="Ex: Banco 001, Agência 1234, C/C 56789-0"><?php echo set_value('dados_bancarios'); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Serviços -->
                    <div class="form-section" style="margin-top: 30px;">
                        <div class="form-section-header">
                            <div>
                                <i class="fas fa-tools"></i>
                                <span>Serviços<span class="required">*</span></span>
                            </div>
                        </div>
                        <div class="form-section-content">
                            <div class="span12 well" style="padding: 1%; margin-left: 0">
                                <div class="span12" style="margin-left: 0">
                                    <div class="span3">
                                        <label for="">Serviço/Produto:<span class="required">*</span></label>
                                        <input type="hidden" id="idServicoNfecom">
                                        <input type="hidden" id="cClassServicoNfecom">
                                        <input type="hidden" id="uMedServicoNfecom">
                                        <select class="span12" id="servicoNfecom" disabled>
                                            <option value="">Selecione um serviço/produto...</option>
                                        </select>
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço:<span class="required">*</span></label>
                                        <input type="text" placeholder="Preço" id="precoServicoNfecom"
                                            class="span12 money">
                                    </div>
                                    <div class="span2">
                                        <label for="">Qtd:<span class="required">*</span></label>
                                        <input type="text" placeholder="Qtd" id="quantidadeServicoNfecom"
                                            class="span12">
                                    </div>
                                    <div class="span2">
                                        <label for="">Desc:</label>
                                        <input type="text" placeholder="0,00" id="descontoServicoNfecom"
                                            class="span12 money">
                                    </div>
                                    <div class="span2">
                                        <label for="">Outros:</label>
                                        <input type="text" placeholder="0,00" id="outrosServicoNfecom"
                                            class="span12 money">
                                    </div>
                                    <div class="span1">
                                        <label for="">&nbsp;</label>
                                        <div style="display: flex; gap: 4px;">
                                            <button type="button" class="btn btn-success"
                                                style="width: 50%; padding: 4px 0;" id="btnAdicionarServicoNfecom"
                                                title="Adicionar Item">
                                                <i class='bx bx-plus-circle'></i>
                                            </button>
                                            <button type="button" class="btn btn-warning"
                                                style="width: 50%; padding: 4px 0;" id="btnLimparServicoNfecom"
                                                title="Limpar Campos">
                                                <i class='bx bx-brush'></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-box" id="servicos-container">
                                <div class="widget_content nopadding">
                                    <table width="100%" class="table table-bordered" id="tblServicosNfecom">
                                        <thead>
                                            <tr class="servico-row-main">
                                                <th width="4%">Código</th>
                                                <th>Descrição</th>
                                                <th width="3%">Unid</th>
                                                <th width="5%" class="col-quantidade">Quantidade</th>
                                                <th width="9%" class="col-preco-unit">Preço Unit.</th>
                                                <th width="6%" class="col-desc">Desc.</th>
                                                <th width="6%" class="col-outros">Outros</th>
                                                <th width="10%" class="col-total">Total</th>
                                                <th width="5%">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="servicos-list-body"></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="7" style="text-align: right"><strong>Total:</strong></td>
                                                <td>
                                                    <div align="center"><strong>R$
                                                            <span id="total-servicos-table">0,00</span></strong>
                                                    </div>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Resumo dos valores calculados - Minimalista MapOS -->
                            <div id="servicos-resumo" class="resumo-minimal" style="display: none;">
                                <div class="row-fluid">
                                    <div class="span4">
                                        <div class="resumo-block">
                                            <span class="resumo-title">SUBTOTAL</span>
                                            <div class="resumo-line">
                                                <span>Faturamento Bruto:</span>
                                                <strong class="color-bruto">R$ <span
                                                        id="total-servicos">0,00</span></strong>
                                            </div>
                                            <div class="resumo-line">
                                                <span>(-) Comissão Agência:</span>
                                                <div class="input-mini-wrapper">
                                                    R$ <input type="number" name="comissaoAgencia" id="comissaoAgencia"
                                                        step="0.01" value="0">
                                                </div>
                                            </div>
                                            <div class="resumo-line highlight-total">
                                                <span>Valor Líquido:</span>
                                                <strong>R$ <span id="valor-liquido">0,00</span></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="span4">
                                        <div class="resumo-block">
                                            <span class="resumo-title">IMPOSTOS E RETENÇÕES</span>
                                            <div class="resumo-line">
                                                <span>PIS:</span>
                                                <span>R$ <span id="total-pis">0,00</span></span>
                                            </div>
                                            <div class="resumo-line">
                                                <span>COFINS:</span>
                                                <span>R$ <span id="total-cofins">0,00</span></span>
                                            </div>
                                            <div class="resumo-line tax-irrf">
                                                <span>IRRF:</span>
                                                <strong>R$ <span id="total-irrf">0,00</span></strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="span4">
                                        <div class="resumo-block total-final">
                                            <span class="resumo-title">TOTAL DA NOTA FISCAL</span>
                                            <div class="total-big">
                                                <small>R$</small> <span id="valor-nf">0,00</span>
                                            </div>
                                            <div class="total-note">
                                                Fórmula: Valor Líquido - IRRF
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <style>
                                .resumo-minimal {
                                    margin-top: 25px;
                                    padding: 20px;
                                    background: #fff;
                                    border: 1px solid #ddd;
                                    border-radius: 4px;
                                    box-sizing: border-box;
                                }

                                .resumo-minimal *,
                                .resumo-minimal *:before,
                                .resumo-minimal *:after {
                                    box-sizing: border-box;
                                }

                                .resumo-block {
                                    padding: 0 15px;
                                    border-right: 1px solid #eee;
                                    min-height: 100px;
                                }

                                .span4:last-child .resumo-block {
                                    border-right: none;
                                    padding-right: 0;
                                }

                                .resumo-title {
                                    display: block;
                                    font-size: 11px;
                                    font-weight: bold;
                                    color: #999;
                                    margin-bottom: 12px;
                                    letter-spacing: 1px;
                                    text-transform: uppercase;
                                }

                                .resumo-line {
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    font-size: 13px;
                                    margin-bottom: 8px;
                                    color: #555;
                                }

                                .color-bruto {
                                    color: #468847;
                                }

                                /* Verde AgilizePro */

                                .highlight-total {
                                    color: #2b6893;
                                    border-top: 1px solid #f5f5f5;
                                    padding-top: 10px;
                                    margin-top: 10px;
                                }

                                .tax-irrf {
                                    color: #b94a48;
                                    border-top: 1px solid #f5f5f5;
                                    padding-top: 10px;
                                    margin-top: 10px;
                                }

                                .total-final {
                                    text-align: right;
                                    padding-left: 15px;
                                }

                                .total-big {
                                    font-size: 38px;
                                    font-weight: bold;
                                    color: #333;
                                    line-height: 1.2;
                                    margin: 5px 0;
                                }

                                .total-big small {
                                    font-size: 18px;
                                    font-weight: normal;
                                    color: #999;
                                }

                                .total-note {
                                    font-size: 11px;
                                    color: #bbb;
                                    font-style: italic;
                                }

                                .input-mini-wrapper input {
                                    margin: 0 !important;
                                    font-size: 12px !important;
                                    font-weight: bold !important;
                                    text-align: right !important;
                                    font-weight: 800;
                                    letter-spacing: -1px;
                                }

                                .resumo-info {
                                    padding: 10px 18px;
                                    background: #fffbeb;
                                    border-top: 1px solid #fef3c7;
                                    color: #92400e;
                                    font-size: 11px;
                                    display: flex;
                                    align-items: center;
                                    gap: 8px;
                                    font-style: italic;
                                }
                            </style>

                            <?php if (empty($servicos)): ?>
                                <div id="servicos-aviso"
                                    style="margin-top: 15px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Atenção:</strong> Nenhum serviço encontrado na base de dados.
                                    <br><small>Para adicionar serviços, vá em <strong>Produtos → Adicionar</strong> e defina
                                        o tipo como "Serviço" (pro_tipo = 2).</small>
                                </div>
                            <?php endif; ?>

                            <!-- Campo oculto para valor bruto (calculado automaticamente) -->
                            <input type="hidden" name="valorBruto" id="valorBruto" value="0">
                        </div>
                    </div>

                    <!-- Campos ocultos necessários para processamento -->
                    <input type="hidden" name="enderecoClienteId" id="enderecoClienteId"
                        value="<?php echo set_value('enderecoClienteId'); ?>">
                    <input type="hidden" name="logradouroCliente" id="logradouroCliente"
                        value="<?php echo set_value('logradouroCliente'); ?>">
                    <input type="hidden" name="numeroCliente" id="numeroCliente"
                        value="<?php echo set_value('numeroCliente'); ?>">
                    <input type="hidden" name="bairroCliente" id="bairroCliente"
                        value="<?php echo set_value('bairroCliente'); ?>">
                    <input type="hidden" name="municipioCliente" id="municipioCliente"
                        value="<?php echo set_value('municipioCliente'); ?>">
                    <input type="hidden" name="codMunCliente" id="codMunCliente"
                        value="<?php echo set_value('codMunCliente'); ?>">
                    <input type="hidden" name="cepCliente" id="cepCliente"
                        value="<?php echo set_value('cepCliente'); ?>">
                    <input type="hidden" name="ufCliente" id="ufCliente" value="<?php echo set_value('ufCliente'); ?>">

                    <!-- Botões de ação -->
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 8px;">
                                <button type="submit" name="acao" value="salvar" class="button btn btn-mini btn-success" style="max-width: 160px">
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                                    <span class="button__text2">Salvar</span>
                                </button>
                                <button type="submit" name="acao" value="salvar_e_emitir" class="button btn btn-mini btn-primary" style="max-width: 180px" title="Salvar a NFCom e enviar para autorização na SEFAZ">
                                    <span class="button__icon"><i class='bx bx-send'></i></span>
                                    <span class="button__text2">Salvar e Emitir</span>
                                </button>
                                <a href="<?php echo base_url() ?>index.php/nfecom" id=""
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

<link rel="stylesheet"
    href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.min.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // Exibir mensagem de sucesso com SweetAlert
        <?php if (!empty($success_message)): ?>
            // Aguardar um pouco para garantir que o DOM está pronto e evitar conflito com o template
            setTimeout(function () {
                // Prevenir que o template geral exiba o SweetAlert
                if (typeof window.__swalPrevented === 'undefined') {
                    window.__swalPrevented = true;
                }

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: '<?php echo addslashes($success_message); ?>',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showCloseButton: false,
                        buttonsStyling: true,
                        customClass: {
                            confirmButton: 'btn btn-success'
                        }
                    }).then((result) => {
                        // Redirecionar para a listagem após clicar em OK
                        if (result.isConfirmed || result.isDismissed) {
                            window.location.href = '<?php echo base_url(); ?>index.php/nfecom';
                        }
                    });
                } else if (typeof swal !== 'undefined') {
                    // Fallback para versão antiga do SweetAlert
                    swal({
                        title: "Sucesso!",
                        text: "<?php echo addslashes($success_message); ?>",
                        type: "success",
                        confirmButtonText: "OK",
                        closeOnConfirm: true
                    }, function () {
                        window.location.href = '<?php echo base_url(); ?>index.php/nfecom';
                    });
                } else {
                    alert('<?php echo addslashes($success_message); ?>');
                    window.location.href = '<?php echo base_url(); ?>index.php/nfecom';
                }
            }, 200);
        <?php endif; ?>

        // Configurar Select2 para busca de clientes (opções iniciais + busca AJAX)
        $('#cliente').select2({
            placeholder: 'Selecione um cliente ou digite para buscar...',
            minimumInputLength: 0,
            allowClear: true,
            ajax: {
                url: '<?php echo base_url(); ?>index.php/nfecom/buscarClientes',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (cliente) {
                if (cliente.loading) return cliente.text;

                var elData = cliente.element ? $(cliente.element).data() : {};
                var nomeFantasia = cliente.nome_fantasia || elData.nomeFantasia || '';
                var razaoSocial = cliente.razao_social || elData.razaoSocial || '';
                var cpfCnpj = cliente.cpf_cnpj || elData.cpfCnpj || '';
                var codigo = cliente.codigo || elData.codigo || '';
                var tituloBase = cliente.text || nomeFantasia || razaoSocial || '';
                var titulo = codigo ? (codigo + ' - ' + tituloBase) : tituloBase;

                var metaParts = [];
                if (razaoSocial && razaoSocial !== titulo) {
                    metaParts.push('Razão: ' + razaoSocial);
                }
                if (cpfCnpj) {
                    metaParts.push('Doc: ' + cpfCnpj);
                }

                var meta = metaParts.length ? '<div class="cliente-select2-meta">' + metaParts.join(' • ') + '</div>' : '';

                return '<div class="cliente-select2">' +
                    '<div class="cliente-select2-title">' + titulo + '</div>' +
                    meta +
                    '</div>';
            },
            templateSelection: function (cliente) {
                return cliente.text || cliente.text;
            },
            language: {
                inputTooShort: function () {
                    return 'Digite para buscar';
                },
                noResults: function () {
                    return 'Nenhum cliente encontrado';
                },
                searching: function () {
                    return 'Buscando...';
                },
                loadingMore: function () {
                    return 'Carregando mais resultados...';
                }
            }
        }).on('select2:clear', function () {
            // Limpar dados do cliente quando o campo for limpo
            $('#nomeCliente, #cnpjCliente').val('');
            $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Selecione um cliente primeiro</option>');
            $('#dadosClienteSection').slideUp(300);
            // Desabilitar campo de serviço quando cliente for removido
            $('#servicoNfecom').prop('disabled', true).val(null).trigger('change');
            // Limpar número/identificador do contrato
            $('#numeroContrato, #contratoId').val('');
        }).on('select2:open', function () {
            // Garantir que as opções iniciais estejam sempre disponíveis
            console.log('📋 Select2 aberto - opções iniciais disponíveis');
        });

        var unidadesServicoMap = {};
        var unidadesProdutoMap = {};
        function carregarUnidadesServicoMap() {
            $.getJSON('<?php echo base_url(); ?>assets/json/unidades_servico.json', function (data) {
                if (data && data.unidades_servico) {
                    data.unidades_servico.forEach(function (unidade) {
                        if (unidade.valor) {
                            unidadesServicoMap[unidade.valor] = unidade.descricao;
                        }
                        if (unidade.codigo) {
                            unidadesServicoMap[unidade.codigo] = unidade.descricao;
                        }
                    });
                }
            });
        }

        function carregarUnidadesProdutoMap() {
            $.getJSON('<?php echo base_url(); ?>assets/json/tabela_medidas.json', function (data) {
                if (data && data.medidas) {
                    data.medidas.forEach(function (medida) {
                        if (medida.sigla) {
                            unidadesProdutoMap[medida.sigla] = medida.descricao;
                        }
                    });
                }
            });
        }

        function getUnidadeDescricao(valor) {
            if (!valor) {
                return '';
            }
            return unidadesServicoMap[valor] || unidadesProdutoMap[valor] || valor;
        }

        carregarUnidadesServicoMap();
        carregarUnidadesProdutoMap();

        // Configurar Select2 para busca de serviços/produtos
        $('#servicoNfecom').select2({
            placeholder: 'Selecione um serviço/produto...',
            minimumInputLength: 0,
            allowClear: true,
            ajax: {
                url: '<?php echo base_url(); ?>index.php/nfecom/autoCompleteServico',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        term: params.term || ''
                    };
                },
                processResults: function (data) {
                    var results = $.map(data || [], function (item) {
                        return {
                            id: item.id,
                            text: item.label,
                            preco: item.preco,
                            cClass: item.cClass,
                            uMed: item.uMed
                        };
                    });
                    return { results: results };
                },
                cache: true
            },
            escapeMarkup: function (markup) {
                return markup;
            },
            templateResult: function (servico) {
                if (servico.loading) return servico.text;

                var elData = servico.element ? $(servico.element).data() : {};
                var preco = servico.preco !== undefined ? servico.preco : elData.preco;
                var cClass = servico.cClass || elData.cClass || '';
                var uMed = servico.uMed || elData.uMed || '';
                var uMedDescricao = getUnidadeDescricao(uMed);

                var metaParts = [];
                if (preco !== undefined && preco !== null && preco !== '') {
                    metaParts.push('Preço: ' + formatMoney(preco));
                }
                if (cClass) {
                    metaParts.push('cClass: ' + cClass);
                }
                if (uMedDescricao) {
                    metaParts.push('Unid: ' + uMedDescricao);
                }

                var meta = metaParts.length ? '<div class="servico-select2-meta">' + metaParts.join(' • ') + '</div>' : '';

                return '<div class="servico-select2">' +
                    '<div class="servico-select2-title">' + (servico.text || '') + '</div>' +
                    meta +
                    '</div>';
            },
            templateSelection: function (servico) {
                return servico.text || servico.text;
            },
            language: {
                inputTooShort: function () {
                    return 'Digite para buscar';
                },
                noResults: function () {
                    return 'Nenhum serviço encontrado';
                },
                searching: function () {
                    return 'Buscando...';
                },
                loadingMore: function () {
                    return 'Carregando mais resultados...';
                }
            }
        }).on('select2:select', function (e) {
            var servico = e.params.data || {};
            $('#idServicoNfecom').val(servico.id || '');
            if (servico.preco !== undefined) {
                $('#precoServicoNfecom').val(formatMoney(servico.preco));
            }
            if (!$('#quantidadeServicoNfecom').val()) {
                $('#quantidadeServicoNfecom').val('1');
            }
            $('#cClassServicoNfecom').val(servico.cClass || '');
            $('#uMedServicoNfecom').val(servico.uMed || 'UN');
            $('#precoServicoNfecom').focus().select();
        }).on('select2:clear', function () {
            limparServicoFormulario();
        });

        function formatarPrecoInput(selector) {
            const valor = $(selector).val();
            if (!valor) {
                return;
            }
            $(selector).val(formatMoney(valor));
        }

        $('#precoServicoNfecom')
            .on('focus', function () {
                $(this).select();
            })
            .on('blur', function () {
                formatarPrecoInput('#precoServicoNfecom');
            })
            .on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formatarPrecoInput('#precoServicoNfecom');
                    if (!$('#quantidadeServicoNfecom').val()) {
                        $('#quantidadeServicoNfecom').val('1');
                    }
                    $('#quantidadeServicoNfecom').focus().select();
                }
            });

        $('#descontoServicoNfecom, #outrosServicoNfecom')
            .on('focus', function () {
                $(this).select();
            })
            .on('blur', function () {
                formatarPrecoInput('#' + this.id);
            })
            .on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    formatarPrecoInput('#' + this.id);
                    $('#btnAdicionarServicoNfecom').focus();
                }
            });

        $('#quantidadeServicoNfecom')
            .on('focus', function () {
                if (!$(this).val()) {
                    $(this).val('1');
                }
                $(this).select();
            });

        var clienteSelecionadoAnterior = $('#cliente').val() || '';
        var ignorarMudancaCliente = false;
        var recalcularTributosPendentes = false;

        // Habilitar campo de serviço se já houver cliente selecionado ao carregar a página
        if ($('#cliente').val()) {
            $('#servicoNfecom').prop('disabled', false);
        }

        // Função para buscar endereços do cliente
        $('#cliente').change(function () {
            if (ignorarMudancaCliente) {
                ignorarMudancaCliente = false;
                return;
            }

            var clienteId = $(this).val();

            if (clienteId !== clienteSelecionadoAnterior) {
                var temItens = $('#servicos-list-body .servico-row-main').length > 0;
                if (temItens) {
                    var confirmar = confirm('Cliente alterado. Recalcular os dados tributários dos itens?');
                    if (!confirmar) {
                        ignorarMudancaCliente = true;
                        $(this).val(clienteSelecionadoAnterior).trigger('change');
                        return;
                    }
                    recalcularTributosPendentes = true;
                }
                clienteSelecionadoAnterior = clienteId;
            }

            if (clienteId) {
                // Habilitar campo de serviço quando cliente for selecionado
                $('#servicoNfecom').prop('disabled', false);
                // Limpar campos de contrato
                $('#numeroContrato, #contratoId').val('');
                $('#numeroContrato, #dataContratoIni, #dataContratoFim, #observacoes').val('');
                $('#tpAssinante').val('3'); // Resetar para padrão

                // Buscar contratos do cliente (opcional - manter para compatibilidade)
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/getContratosCliente/' + clienteId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (contratos) {
                        console.log('📋 Contratos recebidos:', contratos);

                        if (contratos.error) {
                            console.log('⚠️ Erro ao buscar contratos:', contratos.error);
                            return;
                        }

                        // Se houver apenas 1 contrato, preencher automaticamente
                        if (contratos.length === 1) {
                            console.log('✅ Apenas 1 contrato encontrado, preenchendo automaticamente...');
                            var contrato = contratos[0];
                            preencherDadosContrato(contrato);
                            $('#numeroContrato').val(contrato.ctr_numero);
                            $('#contratoId').val(contrato.ctr_id);
                            buscarServicosContrato(contrato.ctr_id);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('❌ Erro ao buscar contratos:', error);
                    }
                });

                // Buscar telefones do cliente
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/getTelefonesCliente/' + clienteId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        if (data.error) {
                            $('#contatoTelefone, #contatoEmail').val('');
                            return;
                        }
                        const telefone = data.telefone || '';
                        const celular = data.celular || '';
                        const email = data.email || '';
                        const contatoTelefone = telefone && celular ? `${telefone} / ${celular}` : (telefone || celular);
                        $('#contatoTelefone').val(contatoTelefone);
                        $('#contatoEmail').val(email);
                    },
                    error: function () {
                        $('#contatoTelefone, #contatoEmail').val('');
                    }
                });

                // Buscar endereços do cliente
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/getEnderecosCliente/' + clienteId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (data) {
                        console.log('📡 Dados recebidos da API de endereços:', data);

                        if (data.error) {
                            alert(data.error);
                            $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Nenhum endereço encontrado</option>');
                            return;
                        }

                        // Habilitar e popular o select de endereços
                        $('#enderecoClienteSelect').prop('disabled', false);
                        var options = '<option value="">Selecione um endereço</option>';
                        var enderecoPadrao = null;

                        $.each(data, function (index, endereco) {
                            console.log('🏠 Endereço processado:', endereco.id, endereco.enderecoCompleto, 'Padrão:', endereco.enderecoPadrao);
                            options += '<option value="' + endereco.id + '" data-endereco="' + JSON.stringify(endereco).replace(/"/g, '&quot;') + '">' + endereco.enderecoCompleto + '</option>';

                            // Identificar endereço padrão (end_padrao = 1)
                            if (endereco.enderecoPadrao == 1 && !enderecoPadrao) {
                                enderecoPadrao = endereco;
                                console.log('🎯 Endereço padrão encontrado:', enderecoPadrao);
                            }
                        });
                        $('#enderecoClienteSelect').html(options);

                        // Selecionar automaticamente o primeiro endereço
                        if (data.length > 0) {
                            var primeiroEndereco = data[0];
                            console.log('✅ Selecionando primeiro endereço automaticamente...');
                            setTimeout(function () {
                                $('#enderecoClienteSelect').val(primeiroEndereco.id).trigger('change');
                                console.log('🏠 Primeiro endereço selecionado automaticamente:', primeiroEndereco.enderecoCompleto);
                            }, 100);
                        } else if (enderecoPadrao) {
                            console.log('✅ Selecionando endereço padrão automaticamente...');
                            setTimeout(function () {
                                $('#enderecoClienteSelect').val(enderecoPadrao.id).trigger('change');
                            }, 100);
                        } else {
                            console.log('⚠️  Nenhum endereço encontrado para este cliente');
                            $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
                            if (recalcularTributosPendentes) {
                                recalcularTributosItens();
                                recalcularTributosPendentes = false;
                            }
                        }
                    },
                    error: function () {
                        alert('Erro ao buscar endereços do cliente');
                        $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Erro ao carregar endereços</option>');
                        if (recalcularTributosPendentes) {
                            recalcularTributosItens();
                            recalcularTributosPendentes = false;
                        }
                    }
                });
            } else {
                // Desabilitar campo de serviço quando nenhum cliente selecionado
                $('#servicoNfecom').prop('disabled', true).val(null).trigger('change');
                // Limpar campos quando nenhum cliente selecionado
                $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
                $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Selecione um cliente primeiro</option>');
                $('#contatoTelefone, #contatoEmail').val('');
                $('#numeroContrato, #contratoId').val('');
                $('#numeroContrato, #dataContratoIni, #dataContratoFim, #observacoes').val('');
                $('#tpAssinante').val('3');
            }
        });

        // Função para preencher dados do contrato
        function preencherDadosContrato(contrato) {
            console.log('📝 Preenchendo dados do contrato:', contrato);

            if (contrato.ctr_numero) {
                $('#numeroContrato').val(contrato.ctr_numero);
            }

            if (contrato.ctr_data_inicio) {
                $('#dataContratoIni').val(contrato.ctr_data_inicio);
            }

            if (contrato.ctr_data_fim) {
                $('#dataContratoFim').val(contrato.ctr_data_fim);
            }

            if (contrato.ctr_observacao) {
                $('#observacoes').val(contrato.ctr_observacao);
            }

            if (contrato.ctr_tipo_assinante) {
                $('#tpAssinante').val(contrato.ctr_tipo_assinante);
            }

            console.log('✅ Dados do contrato preenchidos com sucesso');
        }

        // Autocomplete para número do contrato
        // Compatível com jQuery UI 1.9.2 (sem usar .instance)
        var autocompleteContrato = $('#numeroContrato').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/buscarContratoPorCodigo',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        // Formatar os dados para exibição no autocomplete
                        var formattedData = $.map(data, function (item) {
                            var label = item.ctr_numero || item.label || item.value;
                            if (item.ctr_data_inicio) {
                                label += ' - ' + new Date(item.ctr_data_inicio).toLocaleDateString('pt-BR');
                            }
                            if (item.pes_nome) {
                                label += ' (' + item.pes_nome + ')';
                            }
                            return {
                                label: label,
                                value: item.ctr_numero || item.value,
                                // Manter todos os dados originais
                                ctr_id: item.ctr_id,
                                ctr_numero: item.ctr_numero,
                                ctr_data_inicio: item.ctr_data_inicio,
                                ctr_data_fim: item.ctr_data_fim,
                                ctr_observacao: item.ctr_observacao,
                                ctr_tipo_assinante: item.ctr_tipo_assinante,
                                cln_id: item.cln_id,
                                pes_nome: item.pes_nome,
                                pes_razao_social: item.pes_razao_social,
                                pes_cpfcnpj: item.pes_cpfcnpj
                            };
                        });
                        response(formattedData);
                    },
                    error: function () {
                        response([]);
                    }
                });
            },
            minLength: 2,
            select: function (event, ui) {
                event.preventDefault();

                // Preencher dados do contrato
                $('#numeroContrato').val(ui.item.ctr_numero);
                $('#contratoId').val(ui.item.ctr_id);

                if (ui.item.ctr_data_inicio) {
                    $('#dataContratoIni').val(ui.item.ctr_data_inicio);
                }
                if (ui.item.ctr_data_fim) {
                    $('#dataContratoFim').val(ui.item.ctr_data_fim);
                }
                if (ui.item.ctr_observacao) {
                    $('#observacoes').val(ui.item.ctr_observacao);
                }
                if (ui.item.ctr_tipo_assinante) {
                    $('#tpAssinante').val(ui.item.ctr_tipo_assinante);
                }

                // Preencher cliente se não estiver preenchido
                if (ui.item.cln_id) {
                    var clienteAtual = $('#cliente').val();
                    if (!clienteAtual || clienteAtual != ui.item.cln_id) {
                        // Buscar o cliente no select2 ou criar nova opção
                        var clienteExiste = $('#cliente option[value="' + ui.item.cln_id + '"]').length > 0;

                        if (!clienteExiste) {
                            // Criar nova opção
                            var labelCliente = ui.item.pes_nome;
                            if (ui.item.pes_razao_social) {
                                labelCliente = ui.item.pes_razao_social + ' (' + ui.item.pes_nome + ')';
                            }
                            if (ui.item.pes_cpfcnpj) {
                                labelCliente += ' (' + ui.item.pes_cpfcnpj + ')';
                            }
                            var newOption = new Option(labelCliente, ui.item.cln_id, true, true);
                            $('#cliente').append(newOption);
                        }

                        // Selecionar o cliente
                        $('#cliente').val(ui.item.cln_id).trigger('change');
                    }
                }

                // Buscar e preencher serviços do contrato
                buscarServicosContrato(ui.item.ctr_id);

                return false;
            },
            focus: function (event, ui) {
                event.preventDefault();
                return false;
            }
        });

        function setServicoSelecionado(servico) {
            if (!servico || !servico.id) {
                return;
            }

            $('#idServicoNfecom').val(servico.id);

            var optionExistente = $('#servicoNfecom option[value="' + servico.id + '"]');
            if (optionExistente.length === 0) {
                var newOption = new Option(servico.text || '', servico.id, true, true);
                $(newOption).data('preco', servico.preco || '');
                $(newOption).data('cClass', servico.cClass || '');
                $(newOption).data('uMed', servico.uMed || '');
                $('#servicoNfecom').append(newOption);
            }

            $('#servicoNfecom').val(servico.id).trigger('change');

            if (servico.preco !== undefined && servico.preco !== null && servico.preco !== '') {
                $('#precoServicoNfecom').val(formatMoney(servico.preco));
            }
            if (servico.cClass !== undefined) {
                $('#cClassServicoNfecom').val(servico.cClass || '');
            }
            if (servico.uMed !== undefined) {
                $('#uMedServicoNfecom').val(servico.uMed || 'UN');
            }
        }

        function atualizarLinhaTributos($taxRow, dados) {
            if (!$taxRow || $taxRow.length === 0) {
                return;
            }
            $taxRow.find('.tax-base-icms').text(formatMoney(dados.baseIcms || 0));
            $taxRow.find('.tax-aliq-icms').text(formatarPercentual(dados.aliqIcms || 0));
            $taxRow.find('.tax-valor-icms').text(formatMoney(dados.valorIcms || 0));
            $taxRow.find('.tax-base-icmsst').text(formatMoney(dados.baseIcmsSt || 0));
            $taxRow.find('.tax-aliq-icmsst').text(formatarPercentual(dados.aliqIcmsSt || 0));
            $taxRow.find('.tax-valor-icmsst').text(formatMoney(dados.valorIcmsSt || 0));
            $taxRow.find('.tax-base-irrf').text(formatMoney(dados.baseIrrf || 0));
            $taxRow.find('.tax-valor-irrf').text(formatMoney(dados.valorIrrf || 0));
            $taxRow.find('.tax-valor-pis').text(formatMoney(dados.valorPis || 0));
            $taxRow.find('.tax-valor-cofins').text(formatMoney(dados.valorCofins || 0));

            if (dados.cst !== undefined) {
                $taxRow.find('.tax-cst-value').text(dados.cst);
                $taxRow.find('input[name$="[cst_icms]"]').val(dados.cst);
            }
            if (dados.cfop !== undefined) {
                $taxRow.find('.tax-cfop-value').text(dados.cfop);
                $taxRow.find('input[name$="[cfop]"]').val(dados.cfop);
            }
        }

        function editarServicoLinha($row) {
            const index = $row.data('index');
            const $taxRow = $row.next('.servico-row-tax[data-index="' + index + '"]');

            const servicoId = $row.find('input[name="servicos[' + index + '][id]"]').val();
            const servicoNome = $row.find('input[name="servicos[' + index + '][nome]"]').val();
            const unidade = $row.find('input[name="servicos[' + index + '][u_med]"]').val();
            const preco = $row.find('input[name="servicos[' + index + '][valorUnitario]"]').val();
            const quantidade = $row.find('input[name="servicos[' + index + '][quantidade]"]').val();
            const vDesc = $taxRow.find('input[name="servicos[' + index + '][v_desc]"]').val() || '0';
            const vOutros = $taxRow.find('input[name="servicos[' + index + '][v_outro]"]').val() || '0';
            const cClass = $taxRow.find('input[name="servicos[' + index + '][c_class]"]').val() || '';

            setServicoSelecionado({
                id: servicoId,
                text: servicoNome
            });

            $('#cClassServicoNfecom').val(cClass);
            $('#uMedServicoNfecom').val(unidade);
            $('#precoServicoNfecom').val(formatMoney(preco));
            $('#quantidadeServicoNfecom').val(formatarQuantidade(quantidade || 1));
            $('#descontoServicoNfecom').val(formatMoney(vDesc));
            $('#outrosServicoNfecom').val(formatMoney(vOutros));

            $taxRow.remove();
            $row.remove();
            atualizarTotais();

            $('#precoServicoNfecom').focus().select();
        }

        function recalcularTributosItens() {
            const operacaoComercialId = $("#opc_id").val();
            const clienteId = $("#cliente").val();
            if (!operacaoComercialId || !clienteId) {
                return;
            }

            $('#servicos-list-body .servico-row-main').each(function () {
                const $row = $(this);
                const index = $row.data('index');
                const $taxRow = $row.next('.servico-row-tax[data-index="' + index + '"]');

                const servicoId = $row.find('input[name="servicos[' + index + '][id]"]').val();
                const valorUnitario = $row.find('input[name="servicos[' + index + '][valorUnitario]"]').val();
                const quantidade = $row.find('input[name="servicos[' + index + '][quantidade]"]').val();
                const vDesc = $taxRow.find('input[name="servicos[' + index + '][v_desc]"]').val() || 0;
                const vOutros = $taxRow.find('input[name="servicos[' + index + '][v_outro]"]').val() || 0;

                let defaultCfop = $taxRow.find('input[name="servicos[' + index + '][cfop]"]').val() || '5303';
                let defaultCst = $taxRow.find('input[name="servicos[' + index + '][cst_icms]"]').val() || '00';

                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/getClassificacaoFiscal',
                    type: 'POST',
                    async: false,
                    data: {
                        operacao_comercial_id: operacaoComercialId,
                        cliente_id: clienteId,
                        produto_id: servicoId || null
                    },
                    success: function (response) {
                        if (response.success && response.data) {
                            defaultCfop = response.data.cfop || defaultCfop;
                            defaultCst = response.data.cst || defaultCst;
                        }
                    }
                });

                let dadosTrib = {
                    baseIcms: 0,
                    aliqIcms: 0,
                    valorIcms: 0,
                    baseIcmsSt: 0,
                    aliqIcmsSt: 0,
                    valorIcmsSt: 0,
                    valorPis: 0,
                    valorCofins: 0,
                    baseIrrf: 0,
                    valorIrrf: 0,
                    cst: defaultCst,
                    cfop: defaultCfop
                };

                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/previewTributacaoItem',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data: {
                        produto_id: servicoId,
                        cliente_id: clienteId,
                        operacao_id: operacaoComercialId,
                        valor_unitario: valorUnitario,
                        quantidade: quantidade,
                        v_desc: vDesc,
                        v_outro: vOutros,
                        endereco_id: $('#enderecoClienteId').val() || ''
                    },
                    success: function (response) {
                        if (response.success && response.data) {
                            const dados = response.data;
                            dadosTrib.baseIcms = parseFloat((dados.icms && dados.icms.base) || 0);
                            dadosTrib.aliqIcms = parseFloat((dados.icms && dados.icms.aliquota) || 0);
                            dadosTrib.valorIcms = parseFloat((dados.icms && dados.icms.valor) || 0);
                            dadosTrib.baseIcmsSt = parseFloat((dados.icms_st && dados.icms_st.base) || 0);
                            dadosTrib.aliqIcmsSt = parseFloat((dados.icms_st && dados.icms_st.aliquota) || 0);
                            dadosTrib.valorIcmsSt = parseFloat((dados.icms_st && dados.icms_st.valor) || 0);
                            dadosTrib.valorPis = parseFloat((dados.pis && dados.pis.valor) || 0);
                            dadosTrib.valorCofins = parseFloat((dados.cofins && dados.cofins.valor) || 0);
                            dadosTrib.baseIrrf = parseFloat((dados.irrf && dados.irrf.base) || 0);
                            dadosTrib.valorIrrf = parseFloat((dados.irrf && dados.irrf.valor) || 0);
                        }
                    }
                });

                atualizarLinhaTributos($taxRow, dadosTrib);
            });
        }

        // Função para buscar serviços do contrato e preencher automaticamente
        function buscarServicosContrato(contratoId) {
            if (!contratoId) {
                return;
            }

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/nfecom/getServicosContrato/' + contratoId,
                type: 'GET',
                dataType: 'json',
                success: function (servicosContrato) {
                    if (servicosContrato && servicosContrato.length > 0) {
                        // Limpar serviços existentes primeiro
                        $('#servicos-list-body').empty();
                        servicoIndex = 0; // Resetar o índice

                        // Função recursiva para adicionar serviços sequencialmente
                        function adicionarServicoSequencial(index) {
                            if (index >= servicosContrato.length) {
                                console.log('✅ ' + servicosContrato.length + ' serviço(s) do contrato adicionado(s) automaticamente');
                                atualizarTotais();
                                return;
                            }

                            var servico = servicosContrato[index];
                            var servicoId = servico.pro_id || servico.idServicos;
                            var servicoNome = servico.nome || servico.pro_descricao || '';
                            var servicoPreco = parseFloat(servico.cti_preco || servico.preco || 0);
                            var servicoQuantidade = parseFloat(servico.cti_quantidade || servico.quantidade || 1);

                            // Preencher campos do formulário
                            $("#idServicoNfecom").val(servicoId);
                            setServicoSelecionado({
                                id: servicoId,
                                text: servicoNome
                            });
                            $("#precoServicoNfecom").val(formatMoney(servicoPreco));
                            $("#quantidadeServicoNfecom").val(formatarQuantidade(servicoQuantidade));
                            $("#descontoServicoNfecom").val('0,00');
                            $("#outrosServicoNfecom").val('0,00');

                            // Buscar dados completos do serviço via autocomplete para obter cClass e uMed
                            $.ajax({
                                url: "<?php echo base_url(); ?>index.php/nfecom/autoCompleteServico",
                                dataType: "json",
                                data: { term: servicoNome },
                                success: function (data) {
                                    if (data && data.length > 0) {
                                        var servicoCompleto = data.find(function (s) {
                                            return s.id == servicoId || s.label == servicoNome;
                                        }) || data[0];

                                        if (servicoCompleto) {
                                            $("#cClassServicoNfecom").val(servicoCompleto.cClass || '');
                                            $("#uMedServicoNfecom").val(servicoCompleto.uMed || 'UN');
                                        }
                                    } else {
                                        // Valores padrão se não encontrar
                                        $("#cClassServicoNfecom").val('');
                                        $("#uMedServicoNfecom").val('UN');
                                    }

                                    // Adicionar serviço usando a função existente
                                    adicionarServicoNfecom();

                                    // Aguardar um pouco antes de adicionar o próximo para garantir que o DOM foi atualizado
                                    setTimeout(function () {
                                        adicionarServicoSequencial(index + 1);
                                    }, 100);
                                },
                                error: function () {
                                    // Se não encontrar, adicionar mesmo assim com valores padrão
                                    $("#cClassServicoNfecom").val('');
                                    $("#uMedServicoNfecom").val('UN');
                                    adicionarServicoNfecom();

                                    // Aguardar um pouco antes de adicionar o próximo
                                    setTimeout(function () {
                                        adicionarServicoSequencial(index + 1);
                                    }, 100);
                                }
                            });
                        }

                        // Iniciar adição sequencial
                        adicionarServicoSequencial(0);
                    } else {
                        console.log('ℹ️ Nenhum serviço encontrado para este contrato');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Erro ao buscar serviços do contrato:', error);
                }
            });
        }

        // Preencher datas com a data atual (exceto período fim)
        const hoje = new Date().toISOString().split('T')[0];
        if (!$('#dataContratoIni').val()) {
            $('#dataContratoIni').val(hoje);
        }
        if (!$('#dataVencimento').val()) {
            $('#dataVencimento').val(hoje);
        }
        if (!$('#dataPeriodoIni').val()) {
            $('#dataPeriodoIni').val(hoje);
        }

        // Função para processar seleção de endereço
        $('#enderecoClienteSelect').change(function () {
            console.log('🏠 Evento change do endereço disparado');
            var enderecoId = $(this).val();
            console.log('📍 ID do endereço selecionado:', enderecoId);

            if (enderecoId) {
                // Obter dados do endereço da opção selecionada
                // jQuery .data() já converte automaticamente para objeto
                var enderecoData = $(this).find('option:selected').data('endereco');
                console.log('📋 Dados do endereço:', enderecoData);

                // Preencher campos ocultos necessários para processamento
                $('#enderecoClienteId').val(enderecoData.id);
                $('#logradouroCliente').val(enderecoData.logradouro || '');
                $('#numeroCliente').val(enderecoData.numero || '');
                $('#bairroCliente').val(''); // Bairro não disponível na estrutura atual
                $('#municipioCliente').val(enderecoData.municipio || '');
                $('#codMunCliente').val(enderecoData.codMun || '');
                $('#cepCliente').val(enderecoData.cep || '');
                $('#ufCliente').val(enderecoData.uf || '');

                console.log('✅ Campos ocultos preenchidos para endereço ID:', enderecoData.id);
                if (recalcularTributosPendentes) {
                    recalcularTributosItens();
                    recalcularTributosPendentes = false;
                }
            } else {
                console.log('🧹 Limpando campos - nenhum endereço selecionado');
                // Limpar campos quando nenhum endereço selecionado
                $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
                if (recalcularTributosPendentes) {
                    recalcularTributosItens();
                    recalcularTributosPendentes = false;
                }
            }
        });

        // Sistema de Serviços - padrão OS
        let servicoIndex = 0;

        function parseNumber(value) {
            if (!value || value === '') return 0;

            const strValue = String(value).trim();

            // Se tem vírgula, é formato brasileiro (1.234,56 ou 1234,56)
            if (strValue.indexOf(',') > -1) {
                // Remover pontos (separadores de milhar) e trocar vírgula por ponto
                const normalized = strValue.replace(/\./g, '').replace(',', '.');
                const parsed = parseFloat(normalized);
                return isNaN(parsed) ? 0 : parsed;
            }

            // Se tem ponto, verificar se é separador decimal ou de milhar
            if (strValue.indexOf('.') > -1) {
                const parts = strValue.split('.');
                // Se tem mais de 2 partes, o último ponto é separador de milhar
                // Ex: "1.234.567" -> parts = ["1", "234", "567"]
                // Se tem 2 partes e a última tem 2 dígitos, é decimal
                // Ex: "1234.56" -> parts = ["1234", "56"]
                if (parts.length === 2 && parts[1].length <= 2) {
                    // É formato decimal (1234.56)
                    return parseFloat(strValue);
                } else {
                    // É formato com separadores de milhar (1.234.567)
                    // Remover todos os pontos
                    const normalized = strValue.replace(/\./g, '');
                    const parsed = parseFloat(normalized);
                    return isNaN(parsed) ? 0 : parsed;
                }
            }

            // Apenas números sem separadores
            const parsed = parseFloat(strValue);
            return isNaN(parsed) ? 0 : parsed;
        }

        function formatMoney(value) {
            // Converter para número
            let numValue;

            if (typeof value === 'string') {
                // Detectar formato: se tem vírgula, é formato BR (1.234,56)
                // Se tem ponto após 2 dígitos do final, é formato US (1234.56)
                const hasComma = value.indexOf(',') > -1;
                const hasDot = value.indexOf('.') > -1;

                if (hasComma && !hasDot) {
                    // Formato BR sem ponto: "1234,56" -> 1234.56
                    numValue = parseFloat(value.replace(',', '.'));
                } else if (hasComma && hasDot) {
                    // Formato BR completo: "1.234,56" -> remover pontos, trocar vírgula por ponto
                    numValue = parseFloat(value.replace(/\./g, '').replace(',', '.'));
                } else if (hasDot) {
                    // Formato US: "1234.56" -> 1234.56 (já está correto)
                    numValue = parseFloat(value);
                } else {
                    // Apenas números: "1234" -> 1234
                    numValue = parseFloat(value);
                }
            } else {
                numValue = parseFloat(value);
            }

            if (isNaN(numValue)) return '0,00';

            // Formatar com 2 casas decimais usando vírgula (formato brasileiro)
            return numValue.toFixed(2).replace('.', ',');
        }

        function formatarQuantidade(value) {
            // Converter para número
            let numValue = parseFloat(value);

            if (isNaN(numValue)) return '0';

            // Se for número inteiro, retornar sem decimais
            if (numValue % 1 === 0) {
                return numValue.toString();
            }

            // Se tiver decimais, formatar com vírgula (máximo 4 casas decimais)
            return numValue.toFixed(4).replace(/\.?0+$/, '').replace('.', ',');
        }

        function formatarPercentual(value) {
            const numValue = parseFloat(value) || 0;
            return numValue.toFixed(2).replace('.', ',') + '%';
        }

        $("#quantidadeServicoNfecom").keyup(function () {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });

        // Evitar submit com Enter e navegar entre campos do item
        $(document).on('keydown', '#servicoNfecom, #precoServicoNfecom, #quantidadeServicoNfecom, #descontoServicoNfecom, #outrosServicoNfecom, #btnAdicionarServicoNfecom', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                // Se estiver no Select2 e ele estiver aberto, deixar o Select2 tratar o Enter
                if (e.target.id === 'servicoNfecom' && $('#servicoNfecom').data('select2') && $('#servicoNfecom').data('select2').isOpen()) {
                    return;
                }

                const servicoVal = $('#servicoNfecom').val();

                // Se o serviço não estiver selecionado e tentar dar Enter, não avançar
                if (!servicoVal && e.target.id === 'servicoNfecom') {
                    console.log('⚠️ Selecione um serviço antes de avançar');
                    return;
                }

                const fields = ['#servicoNfecom', '#precoServicoNfecom', '#quantidadeServicoNfecom', '#descontoServicoNfecom', '#outrosServicoNfecom', '#btnAdicionarServicoNfecom'];
                const currentIndex = fields.indexOf('#' + e.target.id);
                const nextIndex = Math.min(currentIndex + 1, fields.length - 1);

                if (nextIndex === fields.length - 1 && e.target.id !== 'btnAdicionarServicoNfecom') {
                    // Se o próximo campo for o botão e tivermos serviço, vamos para o botão
                    if (servicoVal) {
                        $(fields[nextIndex]).focus();
                    } else {
                        // Se não tem serviço, volta para o início ou abre o select
                        $('#servicoNfecom').select2('open');
                    }
                } else if (e.target.id === 'btnAdicionarServicoNfecom') {
                    // Se já estivermos no botão e der Enter, só adiciona se tiver serviço
                    if (servicoVal) {
                        adicionarServicoNfecom();
                    } else {
                        $('#servicoNfecom').select2('open');
                    }
                } else {
                    $(fields[nextIndex]).focus();
                }
            }
        });

        // Impedir submit do formulário principal via Enter no bloco de serviços
        $('#formNfecom').on('keydown', function (e) {
            if (e.key === 'Enter' && $(e.target).closest('.form-section').length) {
                if (!$(e.target).is('textarea')) {
                    e.preventDefault();
                }
            }
        });

        function limparServicoFormulario() {
            $("#idServicoNfecom").val('');
            $("#servicoNfecom").val(null).trigger('change');
            $("#precoServicoNfecom").val('');
            $("#quantidadeServicoNfecom").val('');
            $("#descontoServicoNfecom").val('');
            $("#outrosServicoNfecom").val('');
            $("#cClassServicoNfecom").val('');
            $("#uMedServicoNfecom").val('');
        }

        function adicionarServicoNfecom() {
            const servicoData = $('#servicoNfecom').select2('data')[0] || {};
            const servicoId = servicoData.id || $("#idServicoNfecom").val();
            let servicoNome = (servicoData.text || '').trim();
            if (!servicoNome) {
                servicoNome = ($('#servicoNfecom option:selected').text() || '').trim();
            }
            let cClass = $("#cClassServicoNfecom").val();
            const unidade = $("#uMedServicoNfecom").val();
            const precoRaw = $("#precoServicoNfecom").val();
            const quantidadeRaw = $("#quantidadeServicoNfecom").val();
            const preco = parseNumber(precoRaw);
            const quantidade = parseNumber(quantidadeRaw);

            console.log('🔍 Valores parseados:', {
                precoRaw: precoRaw,
                preco: preco,
                quantidadeRaw: quantidadeRaw,
                quantidade: quantidade
            });
            const vDesc = parseNumber($("#descontoServicoNfecom").val() || '0');
            const vOutros = parseNumber($("#outrosServicoNfecom").val() || '0');

            console.log('🔍 Validação de serviço:', {
                servicoId: servicoId,
                servicoNome: servicoNome,
                precoRaw: precoRaw,
                preco: preco,
                quantidadeRaw: quantidadeRaw,
                quantidade: quantidade
            });

            if (!servicoId || !servicoNome) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Selecione um serviço válido."
                    });
                } else {
                    alert('Selecione um serviço válido.');
                }
                return;
            }

            if (isNaN(preco) || preco <= 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Informe um preço válido maior que zero."
                    });
                } else {
                    alert('Informe um preço válido maior que zero.');
                }
                return;
            }

            if (isNaN(quantidade) || quantidade <= 0) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        type: "error",
                        title: "Atenção",
                        text: "Informe uma quantidade válida maior que zero."
                    });
                } else {
                    alert('Informe uma quantidade válida maior que zero.');
                }
                return;
            }

            const valorItem = quantidade * preco;
            const valorProduto = valorItem - vDesc + vOutros;

            // Valores padrão temporários
            let defaultCfop = '5303';
            let defaultCst = '00';
            let clfId = null; // ID da classificação fiscal
            let baseIcms = 0;
            let aliqIcms = 0;
            let valorIcms = 0;
            let baseIcmsSt = 0;
            let aliqIcmsSt = 0;
            let valorIcmsSt = 0;
            let valorPis = 0;
            let valorCofins = 0;
            let baseIrrf = 0;
            let valorIrrf = 0;

            // Buscar classificação fiscal se tiver operação comercial e cliente
            const operacaoComercialId = $("#opc_id").val(); // Campo correto é opc_id
            const clienteId = $("#cliente").val();

            if (operacaoComercialId && clienteId) {
                console.log('🔍 Buscando classificação fiscal...');
                console.log('   opc_id:', operacaoComercialId);
                console.log('   Cliente ID:', clienteId);
                console.log('   Produto ID:', servicoId);

                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/getClassificacaoFiscal',
                    type: 'POST',
                    data: {
                        operacao_comercial_id: operacaoComercialId,
                        cliente_id: clienteId,
                        produto_id: servicoId || null
                    },
                    async: false, // Síncrono para aguardar o resultado
                    success: function (response) {
                        console.log('📋 Resposta da classificação fiscal:', response);
                        if (response.success && response.data) {
                            clfId = response.data.id;
                            defaultCfop = response.data.cfop || defaultCfop;
                            defaultCst = response.data.cst || defaultCst;

                            // cClass vem do produto, não da classificação fiscal
                            // cClassTrib é apenas informativo da classificação fiscal

                            console.log('✅ Classificação Fiscal encontrada:');
                            console.log('   clf_id:', clfId);
                            console.log('   CFOP:', defaultCfop);
                            console.log('   CST:', defaultCst);
                            console.log('   CSOSN:', response.data.csosn);
                            console.log('   cClassTrib (informativo):', response.data.cClassTrib);
                            console.log('   cClass (do produto):', cClass);
                            console.log('   Mensagem Fiscal:', response.data.mensagem_fiscal);
                        } else {
                            console.log('⚠️  Classificação fiscal não encontrada:', response.error || 'Erro desconhecido');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('❌ Erro ao buscar classificação fiscal:', error);
                    }
                });

                // Pré-visualizar tributação (mesma base do backend)
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/previewTributacaoItem',
                    type: 'POST',
                    dataType: 'json',
                    async: false,
                    data: {
                        produto_id: servicoId,
                        cliente_id: clienteId,
                        operacao_id: operacaoComercialId,
                        valor_unitario: preco,
                        quantidade: quantidade,
                        v_desc: vDesc,
                        v_outro: vOutros,
                        endereco_id: $('#enderecoClienteId').val() || ''
                    },
                    success: function (response) {
                        if (response.success && response.data) {
                            const dados = response.data;
                            baseIcms = parseFloat((dados.icms && dados.icms.base) || 0);
                            aliqIcms = parseFloat((dados.icms && dados.icms.aliquota) || 0);
                            valorIcms = parseFloat((dados.icms && dados.icms.valor) || 0);
                            baseIcmsSt = parseFloat((dados.icms_st && dados.icms_st.base) || 0);
                            aliqIcmsSt = parseFloat((dados.icms_st && dados.icms_st.aliquota) || 0);
                            valorIcmsSt = parseFloat((dados.icms_st && dados.icms_st.valor) || 0);
                            valorPis = parseFloat((dados.pis && dados.pis.valor) || 0);
                            valorCofins = parseFloat((dados.cofins && dados.cofins.valor) || 0);
                            baseIrrf = parseFloat((dados.irrf && dados.irrf.base) || 0);
                            valorIrrf = parseFloat((dados.irrf && dados.irrf.valor) || 0);
                        } else {
                            console.log('⚠️  Pré-visualização tributária não retornou dados:', response.error || 'Erro desconhecido');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('❌ Erro ao pré-visualizar tributação:', error);
                    }
                });
            } else {
                console.log('⚠️  Operação comercial ou cliente não selecionado. Usando valores padrão.');
            }

            const unidadeDescricao = getUnidadeDescricao(unidade);
            const row = `
            <tr class="servico-row-main" data-index="${servicoIndex}" data-valor-produto="${valorProduto}">
                <td>
                    <div align="center">${servicoId || ''}</div>
                    <input type="hidden" name="servicos[${servicoIndex}][id]" value="${servicoId}">
                </td>
                <td>
                    <input type="text" name="servicos[${servicoIndex}][nome]" value="${servicoNome}" class="span12">
                </td>
                <td>
                    ${unidadeDescricao || '-'}
                    <input type="hidden" name="servicos[${servicoIndex}][u_med]" value="${unidade}">
                </td>
                <td class="col-quantidade"><div>${formatarQuantidade(quantidade)}</div>
                    <input type="hidden" name="servicos[${servicoIndex}][quantidade]" value="${quantidade}">
                </td>
                <td class="col-preco-unit"><div>R$ ${formatMoney(preco)}</div>
                    <input type="hidden" name="servicos[${servicoIndex}][valorUnitario]" value="${preco}">
                </td>
                <td class="col-desc"><div>R$ ${formatMoney(vDesc)}</div></td>
                <td class="col-outros"><div>R$ ${formatMoney(vOutros)}</div></td>
                <td class="col-total"><div>R$ ${formatMoney(valorProduto)}</div></td>
                <td>
                    <div align="center" style="display:flex; gap:8px; justify-content:center;">
                        <span class="btn-nwe4 servico-edit" title="Editar Serviço"><i class="bx bx-edit-alt"></i></span>
                        <span class="btn-nwe4 servico-remove" title="Excluir Serviço"><i class="bx bx-trash-alt"></i></span>
                    </div>
                </td>
            </tr>
            <tr class="servico-row-tax" data-index="${servicoIndex}">
                <td colspan="9">
                    <div class="servico-tax-grid">
                        <span class="tax-item"><strong>Classe:</strong> ${cClass || '-'}</span>
                        <input type="hidden" name="servicos[${servicoIndex}][v_desc]" value="${vDesc}">
                        <input type="hidden" name="servicos[${servicoIndex}][v_outro]" value="${vOutros}">
                        <span class="tax-item"><strong>Base ICMS:</strong> <span class="tax-base-icms">${formatMoney(baseIcms)}</span></span>
                        <span class="tax-item"><strong>Alíq. ICMS:</strong> <span class="tax-aliq-icms">${formatarPercentual(aliqIcms)}</span></span>
                        <span class="tax-item"><strong>Valor ICMS:</strong> <span class="tax-valor-icms">${formatMoney(valorIcms)}</span></span>
                        <span class="tax-item"><strong>ICMS ST:</strong> <span class="tax-valor-icmsst">${formatMoney(valorIcmsSt)}</span> (<span class="tax-base-icmsst">${formatMoney(baseIcmsSt)}</span>/<span class="tax-aliq-icmsst">${formatarPercentual(aliqIcmsSt)}</span>)</span>
                        <span class="tax-item"><strong>IRRF:</strong> <span class="tax-valor-irrf">${formatMoney(valorIrrf)}</span> (<span class="tax-base-irrf">${formatMoney(baseIrrf)}</span>)</span>
                        <span class="tax-item"><strong>PIS:</strong> <span class="tax-valor-pis">${formatMoney(valorPis)}</span></span>
                        <span class="tax-item"><strong>COFINS:</strong> <span class="tax-valor-cofins">${formatMoney(valorCofins)}</span></span>
                        <span class="tax-item"><strong>CST:</strong> <span class="tax-cst-value">${defaultCst}</span>
                            <input type="hidden" name="servicos[${servicoIndex}][cst_icms]" value="${defaultCst}">
                            ${clfId ? '<input type="hidden" name="servicos[' + servicoIndex + '][clf_id]" value="' + clfId + '">' : ''}
                        </span>
                        <span class="tax-item tight"><strong>CFOP:</strong><span class="tax-cfop-value tax-inline">${defaultCfop}</span>
                            <input type="hidden" name="servicos[${servicoIndex}][cfop]" value="${defaultCfop}">
                        </span>
                        <input type="hidden" name="servicos[${servicoIndex}][c_class]" value="${cClass}">
                    </div>
                </td>
            </tr>
        `;

            $('#servicos-list-body').append(row);

            console.log('✅ Serviço adicionado com sucesso:', {
                index: servicoIndex,
                id: servicoId,
                nome: servicoNome,
                quantidade: quantidade,
                preco: preco,
                valorUnitario: preco,
                valorProduto: valorProduto,
                cClass: cClass,
                clfId: clfId
            });

            // Verificar se a linha foi realmente adicionada ao DOM
            const linhasAposAdicao = $('#servicos-list-body tr').length;
            console.log('📊 Total de linhas na tabela após adicionar:', linhasAposAdicao);

            servicoIndex++;
            limparServicoFormulario();
            atualizarTotais();

            // Log adicional para debug
            console.log('🔍 Verificação final - Campos hidden na tabela:', {
                totalLinhas: $('#servicos-list-body tr').length,
                ultimaLinha: $('#servicos-list-body tr:last').find('input[type="hidden"]').length,
                servicosInputs: $('input[name^="servicos["]').length
            });
        }

        // Debug: Mostrar informações sobre serviços e clientes
        console.log('🔍 Debug NFECOM:');
        console.log('   📋 Serviços - Total carregados:', <?php echo count($servicos); ?>);
        <?php if (empty($servicos)): ?>
            console.log('   ⚠️  Nenhum serviço encontrado! Verifique se há produtos com pro_tipo = 2');
        <?php endif; ?>
        console.log('   👥 Clientes - Carregados:', <?php echo count($clientes_iniciais); ?>, 'iniciais + busca AJAX');
        console.log('   📍 Endereços - Seleção automática do endereço padrão ativada');
        console.log('   💰 Valores - Cálculo automático com CFOP, unidade, desconto e outros');
        console.log('   🔢 Série - Valor padrão "1" (não controlado na tela)');

        // Botão para adicionar serviço
        $('#btnAdicionarServicoNfecom').on('click', function () {
            adicionarServicoNfecom();
        });

        // Botão para limpar serviço
        $('#btnLimparServicoNfecom').on('click', function () {
            limparServicoFormulario();
            $('#servicoNfecom').select2('open');
        });


        // Remover serviço
        $(document).on('click', '.servico-remove', function () {
            const $mainRow = $(this).closest('tr');
            const index = $mainRow.data('index');
            $mainRow.next('.servico-row-tax[data-index="' + index + '"]').remove();
            $mainRow.remove();
            atualizarTotais();
        });

        // Editar serviço
        $(document).on('click', '.servico-edit', function () {
            const $mainRow = $(this).closest('tr');
            editarServicoLinha($mainRow);
        });

        // Função para calcular e atualizar totais
        function atualizarTotais() {
            let totalServicos = 0;
            let totalPis = 0;
            let totalCofins = 0;
            let totalIrrf = 0;
            const comissao = parseFloat($('#comissaoAgencia').val()) || 0;

            // Calcular total de todos os serviços adicionados E somar os impostos dos itens
            $('#servicos-list-body .servico-row-main').each(function () {
                const valorProduto = parseFloat($(this).data('valor-produto')) || 0;
                totalServicos += valorProduto;

                // Buscar a linha de impostos correspondente (próxima linha com classe servico-row-tax)
                const taxRow = $(this).next('.servico-row-tax');
                if (taxRow.length > 0) {
                    // Extrair valores dos impostos exibidos na linha de tributação
                    const pisTxt = taxRow.find('.tax-valor-pis').text().replace(/[^\d,.-]/g, '').replace(',', '.');
                    const cofinsTxt = taxRow.find('.tax-valor-cofins').text().replace(/[^\d,.-]/g, '').replace(',', '.');
                    const irrfTxt = taxRow.find('.tax-valor-irrf').text().replace(/[^\d,.-]/g, '').replace(',', '.');

                    totalPis += parseFloat(pisTxt) || 0;
                    totalCofins += parseFloat(cofinsTxt) || 0;
                    totalIrrf += parseFloat(irrfTxt) || 0;
                }
            });

            // Valor Líquido = Total - Comissão
            const valorLiquido = totalServicos - comissao;

            // Valor da NF = Valor Líquido - IRRF (somado dos itens)
            // Conforme regra G137: vNF = vProd + vOutro - vDesc - vRetPIS - vRetCofins - vRetCSLL - vIRRF
            const valorNF = valorLiquido - totalIrrf;

            // Atualizar interface
            $('#total-servicos').text(formatMoney(totalServicos));
            $('#total-servicos-table').text(formatMoney(totalServicos));
            $('#valor-liquido').text(formatMoney(valorLiquido));
            $('#total-pis').text(formatMoney(totalPis));
            $('#total-cofins').text(formatMoney(totalCofins));
            $('#total-irrf').text(formatMoney(totalIrrf));
            $('#valor-nf').text(formatMoney(valorNF));
            $('#valorBruto').val(totalServicos.toFixed(2));

            // Mostrar/esconder resumo
            if (totalServicos > 0) {
                $('#servicos-resumo').show();
            } else {
                $('#servicos-resumo').hide();
            }

            console.log('💰 Totais atualizados (somados dos itens):', {
                totalServicos,
                comissao,
                valorLiquido,
                pis: totalPis,
                cofins: totalCofins,
                irrf: totalIrrf,
                valorNF
            });
        }

        // Atualizar totais quando comissão muda
        $('#comissaoAgencia').on('input', function () {
            atualizarTotais();
        });

        // Função para atualizar validação de serviços (removida mensagem de erro)
        function atualizarValidacaoServicos() {
            // Validação removida - não exibe mais mensagem de erro
        }

        // Cálculo automático é feito pela função atualizarTotais()

        // Inicializar totais ao carregar a página
        atualizarTotais();

        // Validação do formulário
        $('#formNfecom').validate({
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
                clientes_id: { required: true },
                enderecoClienteSelect: { required: true },
                observacoes: { required: true },
                numeroContrato: { required: true },
                dataContratoIni: { required: true },
                comissaoAgencia: { number: true },
                dataVencimento: { required: true },
                dataPeriodoIni: { required: true },
                dataPeriodoFim: { required: true }
            },
            messages: {
                clientes_id: 'Selecione um cliente',
                enderecoClienteSelect: 'Selecione um endereço',
                observacoes: 'Observações são obrigatórias',
                numeroContrato: 'Número do contrato é obrigatório',
                dataContratoIni: 'Data de início do contrato é obrigatória',
                comissaoAgencia: 'Comissão deve ser um valor numérico',
                dataVencimento: 'Data de vencimento é obrigatória',
                dataPeriodoIni: 'Data de início do período é obrigatória',
                dataPeriodoFim: 'Data de fim do período é obrigatória'
            },
            invalidHandler: function (event, validator) {
                $('.alert-error').show();
            },
            submitHandler: function (form) {
                // Validar se há pelo menos um serviço válido
                const servicosValidos = $('#servicos-list-body tr').length;

                if (servicosValidos === 0) {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            type: "error",
                            title: "Atenção",
                            text: "É necessário adicionar pelo menos um serviço à NFCom."
                        });
                    } else {
                        alert('É necessário adicionar pelo menos um serviço à NFCom.');
                    }
                    $('html, body').animate({
                        scrollTop: $('#servicos-container').offset().top - 100
                    }, 500);
                    return false;
                }

                // Atualizar o valor bruto com o total calculado antes de enviar
                atualizarTotais();

                form.submit();
            }
        });
    });
</script>