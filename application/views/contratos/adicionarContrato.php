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
        });
    });
</script>
