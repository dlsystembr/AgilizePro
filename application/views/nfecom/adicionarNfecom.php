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
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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

    .servico-row .grid-layout > div {
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

    #total-servicos, #valor-liquido {
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
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formNfecom" method="post" class="form-horizontal">
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
                                    <!-- Linha 1: Cliente -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="cliente" class="control-label">Cliente<span class="required">*</span></label>
                                                <small style="display: block; color: #666; margin-top: 2px;">
                                                    <?php echo count($clientes_iniciais); ?> clientes disponíveis. Ordem alfabética.
                                                </small>
                        <div class="controls">
                                                    <select name="clientes_id" id="cliente" required style="width: 100%;">
                                                        <option value="">Selecione um cliente ou digite para buscar...</option>
                                                        <?php foreach ($clientes_iniciais as $cliente): ?>
                                                            <option value="<?php echo $cliente->id; ?>" <?php echo (isset($_POST['clientes_id']) && $_POST['clientes_id'] == $cliente->id) ? 'selected' : ''; ?>>
                                                                <?php echo $cliente->text; ?><?php echo !empty($cliente->cpf_cnpj) ? ' (' . $cliente->cpf_cnpj . ')' : ''; ?>
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
                                                <label for="enderecoClienteSelect" class="control-label">Endereço<span class="required">*</span></label>
                                                <small style="display: block; color: #666; margin-top: 2px;">Endereço padrão do cliente será selecionado automaticamente.</small>
                        <div class="controls">
                                                    <select name="enderecoClienteSelect" id="enderecoClienteSelect" disabled required>
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
                            <input type="text" name="contatoCliente" id="contatoCliente" readonly>
                        </div>
                    </div>
                                        </div>
                                    </div>


                                    <!-- Linha 3: Número do Contrato -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="numeroContrato" class="control-label">Contrato<span class="required">*</span></label>
                        <div class="controls">
                            <input type="text" name="numeroContrato" id="numeroContrato" value="<?php echo set_value('numeroContrato'); ?>" required>
                        </div>
                    </div>
                        </div>
                    </div>

                                    <!-- Linha 4: Data Emissão e Data Contrato -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataEmissao" class="control-label">Emissão<span class="required">*</span></label>
                        <div class="controls">
                            <input type="date" name="dataEmissao" id="dataEmissao" value="<?php echo set_value('dataEmissao', date('Y-m-d')); ?>" required>
                        </div>
                    </div>
                                        </div>
                                        <div class="span6">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataContratoIni" class="control-label">Contrato<span class="required">*</span></label>
                        <div class="controls">
                                                    <input type="date" name="dataContratoIni" id="dataContratoIni" value="<?php echo set_value('dataContratoIni'); ?>" required>
                                                </div>
                                            </div>
                        </div>
                    </div>

                                    <!-- Linha 5: Observações -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="observacoes" class="control-label">Observações<span class="required">*</span></label>
                        <div class="controls">
                                                    <textarea name="observacoes" id="observacoes" rows="3" required><?php echo set_value('observacoes'); ?></textarea>
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
                                    <i class="fas fa-calculator"></i>
                                    <span>Valores e Períodos</span>
                    </div>
                                <div class="form-section-content">

                                    <!-- Linha 2: Data Vencimento -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataVencimento" class="control-label">Vencimento<span class="required">*</span></label>
                        <div class="controls">
                            <input type="date" name="dataVencimento" id="dataVencimento" value="<?php echo set_value('dataVencimento'); ?>" required>
                                                </div>
                                            </div>
                        </div>
                    </div>

                                    <!-- Linha 3: Período Início -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataPeriodoIni" class="control-label">Período Início<span class="required">*</span></label>
                        <div class="controls">
                            <input type="date" name="dataPeriodoIni" id="dataPeriodoIni" value="<?php echo set_value('dataPeriodoIni'); ?>" required>
                                                </div>
                                            </div>
                        </div>
                    </div>

                                    <!-- Linha 4: Período Fim -->
                                    <div class="row-fluid" style="margin-bottom: 15px;">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                                                <label for="dataPeriodoFim" class="control-label">Período Fim<span class="required">*</span></label>
                        <div class="controls">
                            <input type="date" name="dataPeriodoFim" id="dataPeriodoFim" value="<?php echo set_value('dataPeriodoFim'); ?>" required>
                                                </div>
                                            </div>
                        </div>
                    </div>

                                    <!-- Linha 5: Dados Bancários -->
                                    <div class="row-fluid">
                                        <div class="span12">
                                            <div class="control-group" style="margin-bottom: 0;">
                        <label for="dadosBancarios" class="control-label">Dados Bancários</label>
                        <div class="controls">
                                                    <textarea name="dadosBancarios" id="dadosBancarios" rows="2"><?php echo set_value('dadosBancarios'); ?></textarea>
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
                                <div class="row-fluid">
                                    <div class="span6">
                                        <input type="hidden" id="idServicoNfecom" />
                                        <label for="">Serviço *</label>
                                        <input type="text" class="span12" id="servicoNfecom"
                                            placeholder="Digite o nome do serviço" />
                                    </div>
                                    <div class="span2">
                                        <label for="">Preço *</label>
                                        <input type="text" placeholder="Preço" id="precoServicoNfecom"
                                            class="span12 money" data-affixes-stay="true" data-thousands=""
                                            data-decimal="." />
                                    </div>
                                    <div class="span2">
                                        <label for="">Quantidade *</label>
                                        <input type="text" placeholder="Quantidade" id="quantidadeServicoNfecom"
                                            class="span12" />
                                    </div>
                                    <div class="span2">
                                        <label for="">&nbsp;</label>
                                        <button type="button" id="btnAdicionarServicoNfecom"
                                            class="button btn btn-success">
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
                                                <th>Serviço</th>
                                                <th width="8%">Quantidade</th>
                                                <th width="10%">Preço</th>
                                                <th width="6%">Ações</th>
                                                <th width="10%">Sub-totals</th>
                                            </tr>
                                        </thead>
                                        <tbody id="servicos-list-body"></tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" style="text-align: right"><strong>Total:</strong>
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
                            <div id="servicos-resumo" style="margin-top: 15px; padding: 15px; background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; display: none;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong>Total dos Serviços:</strong> R$ <span id="total-servicos">0,00</span>
                                    </div>
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <label for="comissaoAgencia" style="margin: 0;">Comissão:</label>
                                        <input type="number" name="comissaoAgencia" id="comissaoAgencia" step="0.01" value="0" placeholder="0,00" style="width: 80px; text-align: right;">
                                    </div>
                                </div>
                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #dee2e6;">
                                    <strong>Valor Líquido:</strong> R$ <span id="valor-liquido">0,00</span>
                        </div>
                    </div>

                            <?php if (empty($servicos)): ?>
                            <div id="servicos-aviso" style="margin-top: 15px; padding: 15px; background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; color: #856404;">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Atenção:</strong> Nenhum serviço encontrado na base de dados.
                                <br><small>Para adicionar serviços, vá em <strong>Produtos → Adicionar</strong> e defina o tipo como "Serviço" (pro_tipo = 2).</small>
                            </div>
                            <?php endif; ?>

                            <!-- Campo oculto para valor bruto (calculado automaticamente) -->
                            <input type="hidden" name="valorBruto" id="valorBruto" value="0">
                            <div id="servicos-error" style="display: none; margin-top: 10px; padding: 10px; background-color: #f2dede; border: 1px solid #ebccd1; border-radius: 4px; color: #a94442;">
                                <i class="fas fa-exclamation-triangle"></i> Adicione pelo menos um serviço
                            </div>
                        </div>
                    </div>

                    <!-- Campos ocultos necessários para processamento -->
                    <input type="hidden" name="enderecoClienteId" id="enderecoClienteId" value="<?php echo set_value('enderecoClienteId'); ?>">
                    <input type="hidden" name="logradouroCliente" id="logradouroCliente" value="<?php echo set_value('logradouroCliente'); ?>">
                    <input type="hidden" name="numeroCliente" id="numeroCliente" value="<?php echo set_value('numeroCliente'); ?>">
                    <input type="hidden" name="bairroCliente" id="bairroCliente" value="<?php echo set_value('bairroCliente'); ?>">
                    <input type="hidden" name="municipioCliente" id="municipioCliente" value="<?php echo set_value('municipioCliente'); ?>">
                    <input type="hidden" name="codMunCliente" id="codMunCliente" value="<?php echo set_value('codMunCliente'); ?>">
                    <input type="hidden" name="cepCliente" id="cepCliente" value="<?php echo set_value('cepCliente'); ?>">
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

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.min.css">
<script src="<?php echo base_url(); ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
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
    }).on('select2:open', function () {
        // Garantir que as opções iniciais estejam sempre disponíveis
        console.log('📋 Select2 aberto - opções iniciais disponíveis');
    });

    // Função para buscar endereços do cliente
    $('#cliente').change(function(){
        var clienteId = $(this).val();
        if(clienteId) {
            // Buscar telefones do cliente
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/nfecom/getTelefonesCliente/' + clienteId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        $('#contatoCliente').val('');
                        return;
                    }
                    const telefone = data.telefone || '';
                    const celular = data.celular || '';
                    const contato = telefone && celular ? `${telefone} / ${celular}` : (telefone || celular);
                    $('#contatoCliente').val(contato);
                },
                error: function() {
                    $('#contatoCliente').val('');
                }
            });

            // Buscar endereços do cliente
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/nfecom/getEnderecosCliente/' + clienteId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
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

                    $.each(data, function(index, endereco) {
                        console.log('🏠 Endereço processado:', endereco.id, endereco.enderecoCompleto, 'Padrão:', endereco.enderecoPadrao);
                        options += '<option value="' + endereco.id + '" data-endereco="' + JSON.stringify(endereco).replace(/"/g, '&quot;') + '">' + endereco.enderecoCompleto + '</option>';

                        // Identificar endereço padrão (END_PADRAO = 1)
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
                        setTimeout(function() {
                            $('#enderecoClienteSelect').val(primeiroEndereco.id).trigger('change');
                            console.log('🏠 Primeiro endereço selecionado automaticamente:', primeiroEndereco.enderecoCompleto);
                        }, 100);
                    } else if (enderecoPadrao) {
                        console.log('✅ Selecionando endereço padrão automaticamente...');
                        setTimeout(function() {
                            $('#enderecoClienteSelect').val(enderecoPadrao.id).trigger('change');
                        }, 100);
                    } else {
                        console.log('⚠️  Nenhum endereço encontrado para este cliente');
                        $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
                    }
                },
                error: function() {
                    alert('Erro ao buscar endereços do cliente');
                    $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Erro ao carregar endereços</option>');
                }
            });
        } else {
            // Limpar campos quando nenhum cliente selecionado
            $('#enderecoClienteId, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
            $('#enderecoClienteSelect').prop('disabled', true).html('<option value="">Selecione um cliente primeiro</option>');
            $('#contatoCliente').val('');
        }
    });

    // Preencher datas com a data atual (exceto período fim)
    const hoje = new Date().toISOString().split('T')[0];
    if (!$('#dataEmissao').val()) {
        $('#dataEmissao').val(hoje);
    }
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
    $('#enderecoClienteSelect').change(function(){
        console.log('🏠 Evento change do endereço disparado');
        var enderecoId = $(this).val();
        console.log('📍 ID do endereço selecionado:', enderecoId);

        if(enderecoId) {
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
        const normalized = String(value || '').replace(',', '.');
        return parseFloat(normalized) || 0;
    }

    function formatMoney(value) {
        return value.toFixed(2).replace('.', ',');
    }

    $("#quantidadeServicoNfecom").keyup(function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    $("#servicoNfecom").autocomplete({
        source: "<?php echo base_url(); ?>index.php/nfecom/autoCompleteServico",
        minLength: 2,
        select: function (event, ui) {
            $("#idServicoNfecom").val(ui.item.id);
            $("#precoServicoNfecom").val(ui.item.preco);
            $("#quantidadeServicoNfecom").focus();
        }
    });

    $("#servicoNfecom").on('input', function () {
        if (!$(this).val()) {
            $("#idServicoNfecom").val('');
        }
    });

    // Evitar submit com Enter e navegar entre campos do item
    $(document).on('keydown', '#servicoNfecom, #precoServicoNfecom, #quantidadeServicoNfecom, #btnAdicionarServicoNfecom', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();

            const fields = ['#servicoNfecom', '#precoServicoNfecom', '#quantidadeServicoNfecom', '#btnAdicionarServicoNfecom'];
            const currentIndex = fields.indexOf('#' + e.target.id);
            const nextIndex = Math.min(currentIndex + 1, fields.length - 1);

            if (nextIndex === fields.length - 1) {
                $('#btnAdicionarServicoNfecom').focus();
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
        $("#servicoNfecom").val('').focus();
        $("#precoServicoNfecom").val('');
        $("#quantidadeServicoNfecom").val('');
    }

    function adicionarServicoNfecom() {
        const servicoId = $("#idServicoNfecom").val();
        const servicoNome = $("#servicoNfecom").val().trim();
        const preco = parseNumber($("#precoServicoNfecom").val());
        const quantidade = parseNumber($("#quantidadeServicoNfecom").val());

        if (!servicoId || !servicoNome || preco <= 0 || quantidade <= 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    type: "error",
                    title: "Atenção",
                    text: "Informe um serviço válido, preço e quantidade."
                });
            } else {
                alert('Informe um serviço válido, preço e quantidade.');
            }
            return;
        }

        const valorItem = quantidade * preco;
        const valorProduto = valorItem;
        const cfop = '5307';
        const unidade = '4';

        const row = `
            <tr data-index="${servicoIndex}" data-valor-produto="${valorProduto}">
                <td>${servicoNome}</td>
                <td><div align="center">${quantidade}</div></td>
                <td><div align="center">R$ ${formatMoney(preco)}</div></td>
                <td>
                    <div align="center">
                        <span class="btn-nwe4 servico-remove" title="Excluir Serviço"><i class="bx bx-trash-alt"></i></span>
                    </div>
                    <input type="hidden" name="servicos[${servicoIndex}][id]" value="${servicoId}">
                    <input type="hidden" name="servicos[${servicoIndex}][quantidade]" value="${quantidade}">
                    <input type="hidden" name="servicos[${servicoIndex}][valorUnitario]" value="${preco}">
                    <input type="hidden" name="servicos[${servicoIndex}][valorDesconto]" value="0">
                    <input type="hidden" name="servicos[${servicoIndex}][valorOutros]" value="0">
                    <input type="hidden" name="servicos[${servicoIndex}][cfop]" value="${cfop}">
                    <input type="hidden" name="servicos[${servicoIndex}][unidade]" value="${unidade}">
                </td>
                <td><div align="center">R$: ${formatMoney(valorProduto)}</div></td>
            </tr>
        `;

        $('#servicos-list-body').append(row);
        servicoIndex++;
        limparServicoFormulario();
        atualizarTotais();
        atualizarValidacaoServicos();
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
    $('#btnAdicionarServicoNfecom').on('click', function() {
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
    $(document).on('click', '.servico-remove', function() {
        $(this).closest('tr').remove();
        atualizarTotais();
        atualizarValidacaoServicos();
    });

    // Função para calcular e atualizar totais
    function atualizarTotais() {
        let totalServicos = 0;
        const comissao = parseFloat($('#comissaoAgencia').val()) || 0;

        // Calcular total de todos os serviços adicionados
        $('#servicos-list-body tr').each(function() {
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
    $('#comissaoAgencia').on('input', function() {
        atualizarTotais();
    });

    // Função para atualizar validação de serviços
    function atualizarValidacaoServicos() {
        const numServicos = $('#servicos-list-body tr').length;
        if (numServicos === 0) {
            $('#servicos-error').show();
        } else {
            $('#servicos-error').hide();
        }
    }

    // Cálculo automático é feito pela função atualizarTotais()

    // Inicializar totais e validação ao carregar a página
    atualizarTotais();
    atualizarValidacaoServicos();

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
            dataEmissao: { required: true },
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
            dataEmissao: 'Data de emissão é obrigatória',
            comissaoAgencia: 'Comissão deve ser um valor numérico',
            dataVencimento: 'Data de vencimento é obrigatória',
            dataPeriodoIni: 'Data de início do período é obrigatória',
            dataPeriodoFim: 'Data de fim do período é obrigatória'
        },
        invalidHandler: function (event, validator) {
            $('.alert-error').show();
        },
        submitHandler: function(form) {
            // Validar se há pelo menos um serviço válido
            const servicosValidos = $('#servicos-list-body tr').length;

            if (servicosValidos === 0) {
                $('#servicos-error').show();
                $('html, body').animate({
                    scrollTop: $('#servicos-container').offset().top - 100
                }, 500);
                return false;
            }

            // Atualizar o valor bruto com o total calculado antes de enviar
            atualizarTotais();

            $('#servicos-error').hide();
            form.submit();
        }
    });
});
</script>