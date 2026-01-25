<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>
    .form-section {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .form-section-header {
        background: linear-gradient(to right, #f8f9fa, #e9ecef);
        border-bottom: 2px solid #dee2e6;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: #495057;
        border-radius: 8px 8px 0 0;
    }

    .form-section-header i {
        font-size: 20px;
        color: #6c757d;
    }

    .form-section-content {
        padding: 25px 20px;
    }

    .control-group {
        margin-bottom: 20px;
    }

    .control-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }

    .required {
        color: #dc3545;
        margin-left: 3px;
    }

    input[type="text"],
    input[type="date"],
    select,
    textarea {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 8px 12px;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    input[type="text"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
        border-color: #80bdff;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .ui-autocomplete {
        max-height: 250px;
        overflow-y: auto;
        overflow-x: hidden;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    #cliente_selecionado {
        background: #e7f3ff;
        border-left: 4px solid #007bff;
        padding: 12px;
        border-radius: 4px;
        margin-top: 10px;
    }

    .help-block {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 5px;
    }

    .date-input-wrapper {
        position: relative;
    }

    .date-input-wrapper input[type="date"] {
        width: 100%;
    }

    /* Estilos para tabela de itens */
    #tabela-itens {
        margin-top: 15px;
    }

    #tabela-itens th {
        background-color: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
    }

    #tabela-itens td {
        font-size: 12px;
        vertical-align: middle;
    }

    .remover-item {
        padding: 4px 8px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-contract"></i>
                </span>
                <h5>Adicionar Contrato</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formContrato" method="post" enctype="multipart/form-data" class="form-horizontal">
                <div class="widget-content nopadding tab-content">

                    <!-- Seção Dados do Contrato -->
                    <div class="form-section" style="margin-top: 20px;">
                        <div class="form-section-header">
                            <i class="fas fa-file-contract"></i>
                            <span>Informações do Contrato</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Cliente -->
                            <div class="control-group">
                                <label for="cliente_busca" class="control-label">Cliente<span class="required">*</span></label>
                                <div class="controls">
                                    <input type="text" id="cliente_busca" placeholder="Digite o nome, razão social ou CPF/CNPJ do cliente..." class="span6" autocomplete="off" />
                                    <input type="hidden" id="PES_ID" name="PES_ID" value="<?php echo set_value('PES_ID'); ?>" />
                                    <div id="cliente_selecionado" style="display: none;">
                                        <strong><i class="fas fa-user"></i> Cliente Selecionado:</strong> <span id="cliente_nome"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Número do Contrato -->
                            <div class="control-group">
                                <label for="CTR_NUMERO" class="control-label">Número do Contrato<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="CTR_NUMERO" type="text" name="CTR_NUMERO" value="<?php echo set_value('CTR_NUMERO'); ?>" class="span6" placeholder="Ex: 152662" />
                                </div>
                            </div>

                            <!-- Datas -->
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="CTR_DATA_INICIO" class="control-label">Data de Início<span class="required">*</span></label>
                                        <div class="controls">
                                            <div class="date-input-wrapper">
                                                <input id="CTR_DATA_INICIO" type="text" name="CTR_DATA_INICIO" value="<?php echo set_value('CTR_DATA_INICIO'); ?>" class="datepicker" placeholder="dd/mm/aaaa" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="CTR_DATA_FIM" class="control-label">Data de Fim</label>
                                        <div class="controls">
                                            <div class="date-input-wrapper">
                                                <input id="CTR_DATA_FIM" type="text" name="CTR_DATA_FIM" value="<?php echo set_value('CTR_DATA_FIM'); ?>" class="datepicker" placeholder="dd/mm/aaaa" />
                                            </div>
                                            <span class="help-block">Deixe em branco se o contrato não tiver data de término</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de Assinante -->
                            <div class="control-group">
                                <label for="CTR_TIPO_ASSINANTE" class="control-label">Tipo de Assinante<span class="required">*</span></label>
                                <div class="controls">
                                    <select id="CTR_TIPO_ASSINANTE" name="CTR_TIPO_ASSINANTE" class="span6">
                                        <option value="">Selecione o tipo de assinante...</option>
                                        <?php foreach ($tiposAssinante as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= set_value('CTR_TIPO_ASSINANTE') == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Anexo -->
                            <div class="control-group">
                                <label for="CTR_ANEXO" class="control-label">Anexo do Contrato</label>
                                <div class="controls">
                                    <input id="CTR_ANEXO" type="file" name="CTR_ANEXO" accept=".pdf,.jpg,.jpeg,.png" />
                                    <span class="help-block"><i class="fas fa-info-circle"></i> Formatos aceitos: PDF, JPG, PNG (máx. 5MB)</span>
                                </div>
                            </div>

                            <!-- Observação -->
                            <div class="control-group">
                                <label for="CTR_OBSERVACAO" class="control-label">Observações</label>
                                <div class="controls">
                                    <textarea id="CTR_OBSERVACAO" name="CTR_OBSERVACAO" rows="4" class="span6" placeholder="Digite observações adicionais sobre o contrato..."><?php echo set_value('CTR_OBSERVACAO'); ?></textarea>
                                </div>
                            </div>

                            <!-- Situação -->
                            <div class="control-group">
                                <label for="CTR_SITUACAO" class="control-label">Situação do Contrato</label>
                                <div class="controls">
                                    <select id="CTR_SITUACAO" name="CTR_SITUACAO" class="span3">
                                        <option value="1" <?= set_value('CTR_SITUACAO', '1') == '1' ? 'selected' : '' ?>>Ativo</option>
                                        <option value="0" <?= set_value('CTR_SITUACAO') == '0' ? 'selected' : '' ?>>Inativo</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Itens do Contrato -->
                    <div class="form-section" style="margin-top: 20px;">
                        <div class="form-section-header">
                            <i class="fas fa-list"></i>
                            <span>Itens do Contrato (Serviços)</span>
                        </div>
                        <div class="form-section-content">
                            <div class="control-group">
                                <label class="control-label">Adicionar Serviço</label>
                                <div class="controls">
                                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 10px; align-items: end;">
                                        <div>
                                            <label style="font-size: 12px; margin-bottom: 5px;">Serviço:</label>
                                            <input type="hidden" id="item_servico_id">
                                            <input type="text" id="item_servico_nome" class="span12" placeholder="Digite o nome do serviço..." autocomplete="off">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; margin-bottom: 5px;">Preço:</label>
                                            <input type="text" id="item_servico_preco" class="span12 money" placeholder="0,00">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; margin-bottom: 5px;">Quantidade:</label>
                                            <input type="text" id="item_servico_quantidade" class="span12" placeholder="1,0000" value="1,0000">
                                        </div>
                                        <div>
                                            <label style="font-size: 12px; margin-bottom: 5px;">Observação:</label>
                                            <input type="text" id="item_servico_observacao" class="span12" placeholder="Opcional">
                                        </div>
                                        <div>
                                            <button type="button" id="btnAdicionarItem" class="btn btn-success" style="margin-bottom: 0;">
                                                <i class="fas fa-plus"></i> Adicionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabela de Itens -->
                            <div class="control-group">
                                <div id="itens-container" style="margin-top: 20px;">
                                    <table class="table table-bordered" id="tabela-itens" style="display: none;">
                                        <thead>
                                            <tr>
                                                <th>Serviço</th>
                                                <th style="width: 120px;">Preço</th>
                                                <th style="width: 120px;">Quantidade</th>
                                                <th style="width: 200px;">Observação</th>
                                                <th style="width: 80px;">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itens-tbody">
                                        </tbody>
                                    </table>
                                    <div id="itens-vazio" style="text-align: center; padding: 20px; color: #999;">
                                        <i class="fas fa-info-circle"></i> Nenhum serviço adicionado ainda
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center; gap: 10px;">
                                <button type="submit" class="button btn btn-mini btn-success" style="min-width: 140px">
                                    <span class="button__icon"><i class='bx bx-save'></i></span>
                                    <span class="button__text2">Salvar Contrato</span>
                                </button>
                                <a href="<?php echo base_url() ?>index.php/contratos" class="button btn btn-mini btn-warning" style="min-width: 100px">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span>
                                    <span class="button__text2">Cancelar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Configurar datepicker para campos de data
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior',
            changeMonth: true,
            changeYear: true,
            yearRange: "-100:+10"
        });

        // Autocomplete para busca de cliente
        $("#cliente_busca").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?= base_url() ?>index.php/contratos/buscarCliente",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#PES_ID").val(ui.item.id);
                $("#cliente_nome").text(ui.item.label);
                $("#cliente_selecionado").slideDown();
                return false;
            }
        });

        // ===== GERENCIAMENTO DE ITENS DO CONTRATO =====
        var itensContrato = [];
        var itemIndex = 0;

        // Função para formatar moeda
        function formatMoney(value) {
            if (!value) return '0,00';
            var num = parseFloat(String(value).replace(',', '.'));
            if (isNaN(num)) return '0,00';
            return num.toFixed(2).replace('.', ',');
        }

        // Função para parsear número
        function parseNumber(value) {
            if (!value) return 0;
            return parseFloat(String(value).replace(',', '.')) || 0;
        }

        // Autocomplete para serviços
        $("#item_servico_nome").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: "<?= base_url() ?>index.php/contratos/autoCompleteServico",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            minLength: 2,
            select: function(event, ui) {
                $("#item_servico_id").val(ui.item.id);
                $("#item_servico_nome").val(ui.item.label);
                $("#item_servico_preco").val(formatMoney(ui.item.preco || 0));
                $("#item_servico_quantidade").focus();
                return false;
            }
        });

        // Máscara de moeda
        $(".money").on('blur', function() {
            var value = parseNumber($(this).val());
            $(this).val(formatMoney(value));
        });

        // Adicionar item
        $("#btnAdicionarItem").click(function() {
            var servicoId = $("#item_servico_id").val();
            var servicoNome = $("#item_servico_nome").val().trim();
            var preco = parseNumber($("#item_servico_preco").val());
            var quantidade = parseNumber($("#item_servico_quantidade").val());
            var observacao = $("#item_servico_observacao").val().trim();

            if (!servicoId || !servicoNome) {
                alert('Selecione um serviço válido.');
                return;
            }

            if (isNaN(preco) || preco <= 0) {
                alert('Informe um preço válido maior que zero.');
                return;
            }

            if (isNaN(quantidade) || quantidade <= 0) {
                alert('Informe uma quantidade válida maior que zero.');
                return;
            }

            // Adicionar ao array
            var item = {
                index: itemIndex++,
                PRO_ID: servicoId,
                servico_nome: servicoNome,
                CTI_PRECO: formatMoney(preco),
                CTI_QUANTIDADE: quantidade.toFixed(4).replace('.', ','),
                CTI_OBSERVACAO: observacao
            };
            itensContrato.push(item);

            // Adicionar à tabela
            adicionarItemTabela(item);

            // Limpar campos
            $("#item_servico_id").val('');
            $("#item_servico_nome").val('');
            $("#item_servico_preco").val('');
            $("#item_servico_quantidade").val('1,0000');
            $("#item_servico_observacao").val('');
        });

        // Função para adicionar item na tabela
        function adicionarItemTabela(item) {
            if (itensContrato.length === 1) {
                $("#tabela-itens").show();
                $("#itens-vazio").hide();
            }

            var row = '<tr data-index="' + item.index + '">' +
                '<td>' + item.servico_nome + '</td>' +
                '<td>R$ ' + item.CTI_PRECO + '</td>' +
                '<td>' + item.CTI_QUANTIDADE + '</td>' +
                '<td>' + (item.CTI_OBSERVACAO || '-') + '</td>' +
                '<td><button type="button" class="btn btn-danger btn-mini remover-item" data-index="' + item.index + '"><i class="fas fa-trash"></i></button></td>' +
                '</tr>';

            $("#itens-tbody").append(row);
        }

        // Remover item
        $(document).on('click', '.remover-item', function() {
            var index = $(this).data('index');
            itensContrato = itensContrato.filter(function(item) {
                return item.index !== index;
            });
            $('tr[data-index="' + index + '"]').remove();

            if (itensContrato.length === 0) {
                $("#tabela-itens").hide();
                $("#itens-vazio").show();
            }
        });

        // Validação do formulário
        $("#formContrato").submit(function(e) {
            var errors = [];

            if (!$("#PES_ID").val()) {
                errors.push("• Selecione um cliente");
            }

            if (!$("#CTR_NUMERO").val().trim()) {
                errors.push("• Informe o número do contrato");
            }

            if (!$("#CTR_DATA_INICIO").val().trim()) {
                errors.push("• Informe a data de início");
            }

            if (!$("#CTR_TIPO_ASSINANTE").val()) {
                errors.push("• Selecione o tipo de assinante");
            }

            if (errors.length > 0) {
                e.preventDefault();
                swal({
                    title: "Atenção!",
                    text: "Por favor, corrija os seguintes erros:\n\n" + errors.join("\n"),
                    icon: "warning",
                    button: "OK"
                });
                return false;
            }

            // Adicionar itens ao formulário antes de enviar
            itensContrato.forEach(function(item, index) {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'itens[' + index + '][PRO_ID]',
                    value: item.PRO_ID
                }).appendTo('#formContrato');
                $('<input>').attr({
                    type: 'hidden',
                    name: 'itens[' + index + '][CTI_PRECO]',
                    value: item.CTI_PRECO
                }).appendTo('#formContrato');
                $('<input>').attr({
                    type: 'hidden',
                    name: 'itens[' + index + '][CTI_QUANTIDADE]',
                    value: item.CTI_QUANTIDADE
                }).appendTo('#formContrato');
                if (item.CTI_OBSERVACAO) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'itens[' + index + '][CTI_OBSERVACAO]',
                        value: item.CTI_OBSERVACAO
                    }).appendTo('#formContrato');
                }
            });
        });
    });
</script>
