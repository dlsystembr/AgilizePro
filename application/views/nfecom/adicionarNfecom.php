<style>
    /* Estilos organizados para NFE COM */
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
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Nova NFECom</h5>
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
            <form action="<?php echo current_url(); ?>" id="formNfecom" method="post" class="form-horizontal">
                <!-- Campo hidden para data de emissão (gerada automaticamente) -->
                <input type="hidden" name="dataEmissao" id="dataEmissao" value="<?php echo date('d/m/Y'); ?>">
                <div class="widget-content nopadding tab-content">

                    <!-- Seções lado a lado -->
                    <div class="row-fluid row-flex" style="margin: 20px 0 0 0; padding: 0;">
                        <!-- Dados Principais (Esquerda) -->
                        <div class="span6">
                            <div class="form-section" style="height: 100%;">
                                <div class="form-section-header">
                                    <i class="fas fa-edit"></i>
                                    <span>Dados Principais</span>
                                </div>
                                <div class="form-section-content">
                                    <!-- Linha 0: Operação Comercial -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="opc_id" class="control-label">Operação Comercial<span
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
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="cliente" class="control-label">Cliente<span
                                                        class="required">*</span></label>
                                                <small style="display: block; color: #666; margin-top: 2px;">
                                                    <?php echo count($clientes_iniciais); ?> clientes disponíveis. Ordem
                                                    alfabética.
                                                </small>
                                                <div class="controls">
                                                    <select name="clientes_id" id="cliente" required
                                                        style="width: 100%;">
                                                        <option value="">Selecione um cliente ou digite para buscar...
                                                        </option>
                                                        <?php foreach ($clientes_iniciais as $cliente): ?>
                                                            <option value="<?php echo $cliente->id; ?>" <?php echo (isset($_POST['clientes_id']) && $_POST['clientes_id'] == $cliente->id) ? 'selected' : ''; ?>>
                                                                <?php echo $cliente->text; ?>
                                                                <?php echo !empty($cliente->cpf_cnpj) ? ' (' . $cliente->cpf_cnpj . ')' : ''; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2: Endereço do Cliente -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="enderecoClienteSelect" class="control-label">Endereço<span
                                                        class="required">*</span></label>
                                                <small style="display: block; color: #666; margin-top: 2px;">Endereço
                                                    padrão do cliente será selecionado automaticamente.</small>
                                                <div class="controls">
                                                    <select name="enderecoClienteSelect" id="enderecoClienteSelect"
                                                        disabled required>
                                                        <option value="">Selecione um cliente primeiro</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 2.1: Contato do Cliente -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="contatoCliente" class="control-label">Contato</label>
                                                <div class="controls">
                                                    <input type="text" name="contatoCliente" id="contatoCliente"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 3: Código do Contrato -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="codigoContrato" class="control-label">Código do Contrato</label>
                                                <small style="display: block; color: #666; margin-top: 2px;">
                                                    Digite o código do contrato para preencher automaticamente cliente e serviços.
                                                </small>
                                                <div class="controls">
                                                    <input type="hidden" id="contratoId" name="contratoId" value="">
                                                    <input type="text" id="codigoContrato" name="codigoContrato" 
                                                        class="span12" placeholder="Digite o código do contrato..." 
                                                        autocomplete="off" style="width: 100%;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Linha 3.1: Número do Contrato -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="numeroContrato" class="control-label">Número do Contrato<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="text" name="numeroContrato" id="numeroContrato"
                                                        value="<?php echo set_value('numeroContrato'); ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                                    <!-- Linha 5: Observações -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="observacoes" class="control-label">Observações<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <textarea name="observacoes" id="observacoes" rows="5"
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

                                    <!-- Linha 1: Código Assinante -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
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
                                    <div class="row-fluid" style="margin-bottom: 15px;">
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
                                    <div class="row-fluid" style="margin-bottom: 15px;">
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
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataPeriodoIni" class="control-label">Período Início<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="date" name="dataPeriodoIni" id="dataPeriodoIni"
                                                        value="<?php echo set_value('dataPeriodoIni'); ?>" required
                                                        class="span12">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataPeriodoFim" class="control-label">Período Fim<span
                                                        class="required">*</span></label>
                                                <div class="controls">
                                                    <input type="date" name="dataPeriodoFim" id="dataPeriodoFim"
                                                        value="<?php echo set_value('dataPeriodoFim'); ?>" required
                                                        class="span12">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-section-header"
                                        style="margin-top: 15px; border-top: 1px solid #eee; padding-top: 10px; margin-left: -15px; margin-right: -15px; padding-left: 15px;">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Forma de Pagamento</span>
                                    </div>

                                    <!-- Linha 6: Vencimento -->
                                    <div class="row-fluid" style="margin-bottom: 15px; margin-top: 15px;">
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
                                    <div class="row-fluid" style="margin-bottom: 10px; margin-top: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="nfc_linha_digitavel" class="control-label">Boleto (Linha
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
                                    <div class="row-fluid" style="margin-bottom: 10px;">
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
                                                <label for="dadosBancarios" class="control-label">Dados
                                                    Bancários</label>
                                                <div class="controls">
                                                    <textarea name="dadosBancarios" id="dadosBancarios"
                                                        rows="2"><?php echo set_value('dadosBancarios'); ?></textarea>
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
                                    <div class="span4">
                                        <label for="">Serviço/Produto:<span class="required">*</span></label>
                                        <input type="hidden" id="idServicoNfecom">
                                        <input type="hidden" id="cClassServicoNfecom">
                                        <input type="hidden" id="uMedServicoNfecom">
                                        <input type="text" class="span12" id="servicoNfecom" data-pro-id=""
                                            placeholder="Selecione um cliente primeiro" disabled>
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço:<span class="required">*</span></label>
                                        <input type="text" placeholder="Preço" id="precoServicoNfecom"
                                            class="span12 money">
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade:<span class="required">*</span></label>
                                        <input type="text" placeholder="Quantidade" id="quantidadeServicoNfecom"
                                            class="span12">
                                    </div>
                                    <div class="span2">
                                        <label for="">Desconto:</label>
                                        <input type="text" placeholder="0,00" id="descontoServicoNfecom"
                                            class="span12 money">
                                    </div>
                                    <div class="span2">
                                        <label for="">Outros:</label>
                                        <input type="text" placeholder="0,00" id="outrosServicoNfecom"
                                            class="span12 money">
                                    </div>
                                    <div class="span12"
                                        style="margin-left: 0; margin-top: 10px; display: flex; justify-content: flex-end;">
                                        <button type="button" class="btn btn-success" id="btnAdicionarServicoNfecom">
                                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span
                                                class="button__text2">Adicionar</span></button>
                                    </div>
                                </div>
                            </div>

                            <div class="widget-box" id="servicos-container">
                                <div class="widget_content nopadding">
                                    <table width="100%" class="table table-bordered" id="tblServicosNfecom">
                                        <thead>
                                            <tr>
                                                <th>Produto/Serviço</th>
                                                <th width="8%">cClass</th>
                                                <th width="6%">Unid</th>
                                                <th width="8%">Quantidade</th>
                                                <th width="10%">Preço</th>
                                                <th width="8%">Desconto</th>
                                                <th width="8%">Outros</th>
                                                <th width="8%">CST ICMS</th>
                                                <th width="8%">CFOP</th>
                                                <th width="6%">Ações</th>
                                                <th width="12%">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody id="servicos-list-body"></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="10" style="text-align: right"><strong>Total:</strong>
                                                </td>
                                                <td>
                                                    <div align="center"><strong>R$
                                                            <span id="total-servicos-table">0,00</span></strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>

                            <!-- Resumo dos valores calculados -->
                            <div id="servicos-resumo"
                                style="margin-top: 15px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; display: none;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong>Total dos Serviços:</strong> R$ <span id="total-servicos">0,00</span>
                                    </div>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <label for="comissaoAgencia" style="margin: 0;">Comissão:</label>
                                        <input type="number" name="comissaoAgencia" id="comissaoAgencia" step="0.01"
                                            value="0" placeholder="0,00" style="width: 80px; text-align: right;">
                                    </div>
                                </div>
                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #dee2e6;">
                                    <strong>Valor Líquido:</strong> R$ <span id="valor-liquido">0,00</span>
                                </div>
                            </div>

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
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                                    <span class="button__text2">Salvar</span>
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
        setTimeout(function() {
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
                }, function() {
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
            minimumInputLength: 2,
            allowClear: true,
            ajax: {
                url: '<?php echo base_url(); ?>index.php/nfecom/buscarClientes',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        q: params.term,
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
                return cliente.text + (cliente.cpf_cnpj ? ' (' + cliente.cpf_cnpj + ')' : '');
            },
            templateSelection: function (cliente) {
                return cliente.text || cliente.text;
            },
            language: {
                inputTooShort: function () {
                    return 'Digite pelo menos 2 caracteres';
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
            $('#servicoNfecom').prop('disabled', true).attr('placeholder', 'Selecione um cliente primeiro');
            // Limpar código do contrato
            $('#codigoContrato, #contratoId').val('');
        }).on('select2:open', function () {
            // Garantir que as opções iniciais estejam sempre disponíveis
            console.log('📋 Select2 aberto - opções iniciais disponíveis');
        });

        // Habilitar campo de serviço se já houver cliente selecionado ao carregar a página
        if ($('#cliente').val()) {
            $('#servicoNfecom').prop('disabled', false).attr('placeholder', 'Digite o nome do serviço');
        }

        // Função para buscar endereços do cliente
        $('#cliente').change(function () {
            var clienteId = $(this).val();
            if (clienteId) {
                // Habilitar campo de serviço quando cliente for selecionado
                $('#servicoNfecom').prop('disabled', false).attr('placeholder', 'Digite o nome do serviço');
                // Limpar campos de contrato
                $('#codigoContrato, #contratoId').val('');
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
                            $('#codigoContrato').val(contrato.ctr_numero);
                            $('#contratoId').val(contrato.ctr_id);
                            buscarServicosContrato(contrato.ctr_id);
                        }
                    },
                    error: function(xhr, status, error) {
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
                            $('#contatoCliente').val('');
                            return;
                        }
                        const telefone = data.telefone || '';
                        const celular = data.celular || '';
                        const contato = telefone && celular ? `${telefone} / ${celular}` : (telefone || celular);
                        $('#contatoCliente').val(contato);
                    },
                    error: function () {
                        $('#contatoCliente').val('');
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
                        }
                    },
                    error: function () {
                        alert('Erro ao buscar endereços do cliente');
                        $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Erro ao carregar endereços</option>');
                    }
                });
            } else {
                // Desabilitar campo de serviço quando nenhum cliente selecionado
                $('#servicoNfecom').prop('disabled', true).attr('placeholder', 'Selecione um cliente primeiro');
                // Limpar campos quando nenhum cliente selecionado
                $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
                $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Selecione um cliente primeiro</option>');
                $('#contatoCliente').val('');
                $('#codigoContrato, #contratoId').val('');
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

        // Autocomplete para código do contrato
        $('#codigoContrato').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/nfecom/buscarContratoPorCodigo',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    },
                    error: function() {
                        response([]);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                event.preventDefault();
                
                // Preencher dados do contrato
                $('#codigoContrato').val(ui.item.ctr_numero);
                $('#contratoId').val(ui.item.ctr_id);
                $('#numeroContrato').val(ui.item.ctr_numero);
                
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
            focus: function(event, ui) {
                event.preventDefault();
                return false;
            }
        }).autocomplete("instance")._renderItem = function(ul, item) {
            var label = item.ctr_numero;
            if (item.ctr_data_inicio) {
                label += ' - ' + new Date(item.ctr_data_inicio).toLocaleDateString('pt-BR');
            }
            if (item.pes_nome) {
                label += ' (' + item.pes_nome + ')';
            }
            return $("<li>")
                .append("<div>" + label + "</div>")
                .appendTo(ul);
        };

        // Função para buscar serviços do contrato e preencher automaticamente
        function buscarServicosContrato(contratoId) {
            if (!contratoId) {
                return;
            }

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/nfecom/getServicosContrato/' + contratoId,
                type: 'GET',
                dataType: 'json',
                success: function(servicosContrato) {
                    if (servicosContrato && servicosContrato.length > 0) {
                        // Limpar serviços existentes primeiro
                        servicos = [];
                        $('#servicos-container').empty();
                        
                        // Adicionar cada serviço do contrato usando a função existente
                        servicosContrato.forEach(function(servico) {
                            // Preencher campos do formulário
                            var servicoId = servico.pro_id || servico.idServicos;
                            var servicoNome = servico.nome || servico.pro_descricao || '';
                            var servicoPreco = parseFloat(servico.cti_preco || servico.preco || 0);
                            var servicoQuantidade = parseFloat(servico.cti_quantidade || servico.quantidade || 1);
                            
                            // Preencher campos do formulário
                            $("#idServicoNfecom").val(servicoId);
                            $("#servicoNfecom").val(servicoNome);
                            $("#precoServicoNfecom").val(formatMoney(servicoPreco));
                            $("#quantidadeServicoNfecom").val(formatarQuantidade(servicoQuantidade));
                            $("#descontoServicoNfecom").val('0,00');
                            $("#outrosServicoNfecom").val('0,00');
                            
                            // Buscar dados completos do serviço via autocomplete para obter cClass e uMed
                            $.ajax({
                                url: "<?php echo base_url(); ?>index.php/nfecom/autoCompleteServico",
                                dataType: "json",
                                data: { term: servicoNome },
                                success: function(data) {
                                    if (data && data.length > 0) {
                                        var servicoCompleto = data.find(function(s) {
                                            return s.id == servicoId || s.label == servicoNome;
                                        }) || data[0];
                                        
                                        if (servicoCompleto) {
                                            $("#cClassServicoNfecom").val(servicoCompleto.cClass || '');
                                            $("#uMedServicoNfecom").val(servicoCompleto.uMed || 'UN');
                                        }
                                    }
                                    
                                    // Adicionar serviço usando a função existente
                                    adicionarServicoNfecom();
                                },
                                error: function() {
                                    // Se não encontrar, adicionar mesmo assim com valores padrão
                                    $("#cClassServicoNfecom").val('');
                                    $("#uMedServicoNfecom").val('UN');
                                    adicionarServicoNfecom();
                                }
                            });
                        });
                        
                        console.log('✅ ' + servicosContrato.length + ' serviço(s) do contrato adicionado(s) automaticamente');
                    } else {
                        console.log('ℹ️ Nenhum serviço encontrado para este contrato');
                    }
                },
                error: function(xhr, status, error) {
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
            } else {
                console.log('🧹 Limpando campos - nenhum endereço selecionado');
                // Limpar campos quando nenhum endereço selecionado
                $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
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

        $("#quantidadeServicoNfecom").keyup(function () {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });

        $("#servicoNfecom").autocomplete({
            source: "<?php echo base_url(); ?>index.php/nfecom/autoCompleteServico",
            minLength: 2,
            select: function (event, ui) {
                $("#idServicoNfecom").val(ui.item.id);
                // Formatar preço com vírgula quando vier do autocomplete
                const precoFormatado = formatMoney(ui.item.preco);
                $("#precoServicoNfecom").val(precoFormatado);
                $("#cClassServicoNfecom").val(ui.item.cClass);
                $("#uMedServicoNfecom").val(ui.item.uMed);
                $("#precoServicoNfecom").focus();
            }
        });

        $("#servicoNfecom").on('input', function () {
            // Se alterar o nome, limpamos o ID para garantir que seja tratado como texto livre ou novo
            // Mas mantemos os outros dados preenchidos pelo autocomplete caso seja apenas uma correção de nome
            // Se o usuário apagar tudo, aí limpamos tudo
            if (!$(this).val()) {
                $("#idServicoNfecom").val('');
                limparServicoFormulario();
            }
        });

        // Evitar submit com Enter e navegar entre campos do item
        $(document).on('keydown', '#servicoNfecom, #precoServicoNfecom, #quantidadeServicoNfecom, #descontoServicoNfecom, #outrosServicoNfecom, #btnAdicionarServicoNfecom', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();

                const fields = ['#servicoNfecom', '#precoServicoNfecom', '#quantidadeServicoNfecom', '#descontoServicoNfecom', '#outrosServicoNfecom', '#btnAdicionarServicoNfecom'];
                const currentIndex = fields.indexOf('#' + e.target.id);
                const nextIndex = Math.min(currentIndex + 1, fields.length - 1);

                if (nextIndex === fields.length - 1) {
                    $('#btnAdicionarServicoNfecom').click();
                } else {
                    $(fields[nextIndex]).focus();
                }
            }
        });

        // Impedir submit do formulário principal via Enter no bloco de serviços
        $('#formNfecom').on('keydown', function (e) {
            if (e.key === 'Enter' && $(e.target).closest('.form-section').length) {
                e.preventDefault();
            }
        });

        function limparServicoFormulario() {
            $("#idServicoNfecom").val('');
            $("#servicoNfecom").val('');
            $("#precoServicoNfecom").val('');
            $("#quantidadeServicoNfecom").val('');
            $("#descontoServicoNfecom").val('');
            $("#outrosServicoNfecom").val('');
            $("#cClassServicoNfecom").val('');
            $("#uMedServicoNfecom").val('');
        }

        function adicionarServicoNfecom() {
            const servicoId = $("#idServicoNfecom").val();
            const servicoNome = $("#servicoNfecom").val().trim();
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
                    success: function(response) {
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
                    error: function(xhr, status, error) {
                        console.log('❌ Erro ao buscar classificação fiscal:', error);
                    }
                });
            } else {
                console.log('⚠️  Operação comercial ou cliente não selecionado. Usando valores padrão.');
            }

            const row = `
            <tr data-index="${servicoIndex}" data-valor-produto="${valorProduto}">
                <td>
                    <input type="text" name="servicos[${servicoIndex}][nome]" value="${servicoNome}" class="span12">
                    <input type="hidden" name="servicos[${servicoIndex}][id]" value="${servicoId}">
                </td>
                <td>
                    ${cClass}
                    <input type="hidden" name="servicos[${servicoIndex}][c_class]" value="${cClass}">
                </td>
                <td>
                    ${unidade}
                    <input type="hidden" name="servicos[${servicoIndex}][u_med]" value="${unidade}">
                </td>
                <td><div align="center">${formatarQuantidade(quantidade)}</div>
                    <input type="hidden" name="servicos[${servicoIndex}][quantidade]" value="${quantidade}">
                </td>
                <td><div align="center">R$ ${formatMoney(preco)}</div>
                    <input type="hidden" name="servicos[${servicoIndex}][valorUnitario]" value="${preco}">
                </td>
                <td>
                    R$ ${formatMoney(vDesc)}
                    <input type="hidden" name="servicos[${servicoIndex}][v_desc]" value="${vDesc}">
                </td>
                <td>
                    R$ ${formatMoney(vOutros)}
                    <input type="hidden" name="servicos[${servicoIndex}][v_outro]" value="${vOutros}">
                </td>
                <td>
                    <select name="servicos[${servicoIndex}][cst_icms]" class="span12" style="margin:0; width: 100%;">
                        <option value="00" ${String(defaultCst) === '00' ? 'selected' : ''}>00 - Tribut. Integral</option>
                        <option value="20" ${String(defaultCst) === '20' ? 'selected' : ''}>20 - Red. Base Calc.</option>
                        <option value="30" ${String(defaultCst) === '30' ? 'selected' : ''}>30 - Isenta/Não Trib. c/ Cobrança</option>
                        <option value="40" ${String(defaultCst) === '40' ? 'selected' : ''}>40 - Isenta</option>
                        <option value="41" ${String(defaultCst) === '41' ? 'selected' : ''}>41 - Não Tributada</option>
                        <option value="50" ${String(defaultCst) === '50' ? 'selected' : ''}>50 - Suspensão</option>
                        <option value="51" ${String(defaultCst) === '51' ? 'selected' : ''}>51 - Diferimento</option>
                        <option value="60" ${String(defaultCst) === '60' ? 'selected' : ''}>60 - ICMS cobrado anteriormente por substituição tributária</option>
                        <option value="70" ${String(defaultCst) === '70' ? 'selected' : ''}>70 - Com redução de base de cálculo e cobrança do ICMS por substituição tributária</option>
                        <option value="90" ${String(defaultCst) === '90' ? 'selected' : ''}>90 - Outras</option>
                    </select>
                    ${clfId ? '<input type="hidden" name="servicos[' + servicoIndex + '][clf_id]" value="' + clfId + '">' : ''}
                </td>
                <td>
                    <select name="servicos[${servicoIndex}][cfop]" class="span12" style="margin:0; width: 100%;" id="cfop_${servicoIndex}">
                        <option value="5301" ${String(defaultCfop) === '5301' ? 'selected' : ''}>5301 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituto</option>
                        <option value="5302" ${String(defaultCfop) === '5302' ? 'selected' : ''}>5302 - Venda de produção do estabelecimento de produto sujeito ao regime de substituição tributária, em operação entre contribuintes substitutos do mesmo produto</option>
                        <option value="5303" ${String(defaultCfop) === '5303' ? 'selected' : ''}>5303 - Venda de produção do estabelecimento que não deva por ele entrar na apuração</option>
                        <option value="5304" ${String(defaultCfop) === '5304' ? 'selected' : ''}>5304 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5305" ${String(defaultCfop) === '5305' ? 'selected' : ''}>5305 - Venda de produção do estabelecimento o que não deva por ele entrar na apuração, em operação com produto sujeito ao regime de substituição tributária na condição de contribuinte substituído</option>
                        <option value="5306" ${String(defaultCfop) === '5306' ? 'selected' : ''}>5306 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5307" ${String(defaultCfop) === '5307' ? 'selected' : ''}>5307 - Venda de produção do estabelecimento, efetuada fora do estabelecimento</option>
                        <option value="5308" ${String(defaultCfop) === '5308' ? 'selected' : ''}>5308 - Venda de produção do estabelecimento, efetuada fora do estabelecimento, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5309" ${String(defaultCfop) === '5309' ? 'selected' : ''}>5309 - Venda de produção do estabelecimento a outro estabelecimento da mesma empresa</option>
                        <option value="5310" ${String(defaultCfop) === '5310' ? 'selected' : ''}>5310 - Venda de produção do estabelecimento a empresa industrial em operação com produto sujeito ao regime de substituição tributária</option>
                        <option value="5311" ${String(defaultCfop) === '5311' ? 'selected' : ''}>5311 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5312" ${String(defaultCfop) === '5312' ? 'selected' : ''}>5312 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5313" ${String(defaultCfop) === '5313' ? 'selected' : ''}>5313 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5314" ${String(defaultCfop) === '5314' ? 'selected' : ''}>5314 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5315" ${String(defaultCfop) === '5315' ? 'selected' : ''}>5315 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5321" ${String(defaultCfop) === '5321' ? 'selected' : ''}>5321 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5322" ${String(defaultCfop) === '5322' ? 'selected' : ''}>5322 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5323" ${String(defaultCfop) === '5323' ? 'selected' : ''}>5323 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5324" ${String(defaultCfop) === '5324' ? 'selected' : ''}>5324 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5325" ${String(defaultCfop) === '5325' ? 'selected' : ''}>5325 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5351" ${String(defaultCfop) === '5351' ? 'selected' : ''}>5351 - Remessa de produção do estabelecimento para armazenamento</option>
                        <option value="5352" ${String(defaultCfop) === '5352' ? 'selected' : ''}>5352 - Remessa de produção do estabelecimento com destino a outro estabelecimento da mesma empresa</option>
                        <option value="5353" ${String(defaultCfop) === '5353' ? 'selected' : ''}>5353 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="5354" ${String(defaultCfop) === '5354' ? 'selected' : ''}>5354 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="5355" ${String(defaultCfop) === '5355' ? 'selected' : ''}>5355 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="5356" ${String(defaultCfop) === '5356' ? 'selected' : ''}>5356 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="5357" ${String(defaultCfop) === '5357' ? 'selected' : ''}>5357 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="5358" ${String(defaultCfop) === '5358' ? 'selected' : ''}>5358 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="5359" ${String(defaultCfop) === '5359' ? 'selected' : ''}>5359 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="5360" ${String(defaultCfop) === '5360' ? 'selected' : ''}>5360 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="5401" ${String(defaultCfop) === '5401' ? 'selected' : ''}>5401 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituto</option>
                        <option value="5402" ${String(defaultCfop) === '5402' ? 'selected' : ''}>5402 - Venda de produção do estabelecimento de produto sujeito ao regime de substituição tributária, em operação entre contribuintes substitutos do mesmo produto</option>
                        <option value="5403" ${String(defaultCfop) === '5403' ? 'selected' : ''}>5403 - Venda de produção do estabelecimento que não deva por ele entrar na apuração</option>
                        <option value="5405" ${String(defaultCfop) === '5405' ? 'selected' : ''}>5405 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5408" ${String(defaultCfop) === '5408' ? 'selected' : ''}>5408 - Venda de produção do estabelecimento, efetuada fora do estabelecimento</option>
                        <option value="5409" ${String(defaultCfop) === '5409' ? 'selected' : ''}>5409 - Venda de produção do estabelecimento a outro estabelecimento da mesma empresa</option>
                        <option value="5410" ${String(defaultCfop) === '5410' ? 'selected' : ''}>5410 - Venda de produção do estabelecimento a empresa industrial em operação com produto sujeito ao regime de substituição tributária</option>
                        <option value="5411" ${String(defaultCfop) === '5411' ? 'selected' : ''}>5411 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5412" ${String(defaultCfop) === '5412' ? 'selected' : ''}>5412 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5413" ${String(defaultCfop) === '5413' ? 'selected' : ''}>5413 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5414" ${String(defaultCfop) === '5414' ? 'selected' : ''}>5414 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="5415" ${String(defaultCfop) === '5415' ? 'selected' : ''}>5415 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6301" ${String(defaultCfop) === '6301' ? 'selected' : ''}>6301 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituto</option>
                        <option value="6302" ${String(defaultCfop) === '6302' ? 'selected' : ''}>6302 - Venda de produção do estabelecimento de produto sujeito ao regime de substituição tributária, em operação entre contribuintes substitutos do mesmo produto</option>
                        <option value="6303" ${String(defaultCfop) === '6303' ? 'selected' : ''}>6303 - Venda de produção do estabelecimento que não deva por ele entrar na apuração</option>
                        <option value="6304" ${String(defaultCfop) === '6304' ? 'selected' : ''}>6304 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6305" ${String(defaultCfop) === '6305' ? 'selected' : ''}>6305 - Venda de produção do estabelecimento o que não deva por ele entrar na apuração, em operação com produto sujeito ao regime de substituição tributária na condição de contribuinte substituído</option>
                        <option value="6306" ${String(defaultCfop) === '6306' ? 'selected' : ''}>6306 - Venda de produção do estabelecimento em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6307" ${String(defaultCfop) === '6307' ? 'selected' : ''}>6307 - Venda de produção do estabelecimento, efetuada fora do estabelecimento</option>
                        <option value="6308" ${String(defaultCfop) === '6308' ? 'selected' : ''}>6308 - Venda de produção do estabelecimento, efetuada fora do estabelecimento, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6309" ${String(defaultCfop) === '6309' ? 'selected' : ''}>6309 - Venda de produção do estabelecimento a outro estabelecimento da mesma empresa</option>
                        <option value="6310" ${String(defaultCfop) === '6310' ? 'selected' : ''}>6310 - Venda de produção do estabelecimento a empresa industrial em operação com produto sujeito ao regime de substituição tributária</option>
                        <option value="6311" ${String(defaultCfop) === '6311' ? 'selected' : ''}>6311 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6312" ${String(defaultCfop) === '6312' ? 'selected' : ''}>6312 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6313" ${String(defaultCfop) === '6313' ? 'selected' : ''}>6313 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6314" ${String(defaultCfop) === '6314' ? 'selected' : ''}>6314 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6315" ${String(defaultCfop) === '6315' ? 'selected' : ''}>6315 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6321" ${String(defaultCfop) === '6321' ? 'selected' : ''}>6321 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6322" ${String(defaultCfop) === '6322' ? 'selected' : ''}>6322 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6323" ${String(defaultCfop) === '6323' ? 'selected' : ''}>6323 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6324" ${String(defaultCfop) === '6324' ? 'selected' : ''}>6324 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6325" ${String(defaultCfop) === '6325' ? 'selected' : ''}>6325 - Venda de produção do estabelecimento, de produto já adquirido ou recebido de terceiros, em operação com produto sujeito ao regime de substituição tributária, na condição de contribuinte substituído</option>
                        <option value="6351" ${String(defaultCfop) === '6351' ? 'selected' : ''}>6351 - Remessa de produção do estabelecimento para armazenamento</option>
                        <option value="6352" ${String(defaultCfop) === '6352' ? 'selected' : ''}>6352 - Remessa de produção do estabelecimento com destino a outro estabelecimento da mesma empresa</option>
                        <option value="6353" ${String(defaultCfop) === '6353' ? 'selected' : ''}>6353 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="6354" ${String(defaultCfop) === '6354' ? 'selected' : ''}>6354 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="6355" ${String(defaultCfop) === '6355' ? 'selected' : ''}>6355 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="6356" ${String(defaultCfop) === '6356' ? 'selected' : ''}>6356 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="6357" ${String(defaultCfop) === '6357' ? 'selected' : ''}>6357 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="6358" ${String(defaultCfop) === '6358' ? 'selected' : ''}>6358 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                        <option value="6359" ${String(defaultCfop) === '6359' ? 'selected' : ''}>6359 - Remessa de produção do estabelecimento para industrialização por encomenda</option>
                        <option value="6360" ${String(defaultCfop) === '6360' ? 'selected' : ''}>6360 - Remessa de produção do estabelecimento para industrialização sob o regime de drawback</option>
                    </select>
                </td>
                <td>
                    <div align="center">
                        <span class="btn-nwe4 servico-remove" title="Excluir Serviço"><i class="bx bx-trash-alt"></i></span>
                    </div>
                </td>
                <td><div align="center">R$: ${formatMoney(valorProduto)}</div></td>
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
            
            servicoIndex++;
            limparServicoFormulario();
            atualizarTotais();
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

        // Enter no botão adiciona o item sem submit
        $('#btnAdicionarServicoNfecom').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                adicionarServicoNfecom();
            }
        });

        // Remover serviço
        $(document).on('click', '.servico-remove', function () {
            $(this).closest('tr').remove();
            atualizarTotais();
        });

        // Função para calcular e atualizar totais
        function atualizarTotais() {
            let totalServicos = 0;
            const comissao = parseFloat($('#comissaoAgencia').val()) || 0;

            // Calcular total de todos os serviços adicionados
            $('#servicos-list-body tr').each(function () {
                const valorProduto = parseFloat($(this).data('valor-produto')) || 0;
                totalServicos += valorProduto;
            });

            const valorLiquido = totalServicos - comissao;

            // Atualizar interface
            $('#total-servicos').text(formatMoney(totalServicos));
            $('#total-servicos-table').text(formatMoney(totalServicos));
            $('#valor-liquido').text(formatMoney(valorLiquido));
            $('#valorBruto').val(totalServicos.toFixed(2));

            // Mostrar/esconder resumo
            if (totalServicos > 0) {
                $('#servicos-resumo').show();
            } else {
                $('#servicos-resumo').hide();
            }

            console.log('💰 Totais atualizados:', { totalServicos, comissao, valorLiquido });
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