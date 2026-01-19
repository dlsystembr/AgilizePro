<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>
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

    .ui-autocomplete {
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-contract"></i>
                </span>
                <h5>Editar Contrato</h5>
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
                            <span>Dados do Contrato</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Cliente -->
                            <div class="control-group">
                                <label for="cliente_busca" class="control-label">Cliente<span class="required">*</span></label>
                                <div class="controls">
                                    <input type="text" id="cliente_busca" placeholder="Digite para buscar cliente..." class="span6" autocomplete="off" value="<?= $result->PES_NOME ?>" />
                                    <input type="hidden" id="PES_ID" name="PES_ID" value="<?= $result->PES_ID ?>" />
                                    <div id="cliente_selecionado" style="margin-top: 5px; padding: 5px; background: #f0f0f0; border-radius: 3px;">
                                        <strong>Cliente:</strong> <span id="cliente_nome"><?= $result->PES_NOME ?> - <?= $result->PES_CPFCNPJ ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Número do Contrato -->
                            <div class="control-group">
                                <label for="CTR_NUMERO" class="control-label">Número do Contrato<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="CTR_NUMERO" type="text" name="CTR_NUMERO" value="<?= $result->CTR_NUMERO ?>" class="span6" />
                                </div>
                            </div>

                            <!-- Datas -->
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="CTR_DATA_INICIO" class="control-label">Data de Início<span class="required">*</span></label>
                                        <div class="controls">
                                            <input id="CTR_DATA_INICIO" type="text" name="CTR_DATA_INICIO" value="<?= date('d/m/Y', strtotime($result->CTR_DATA_INICIO)) ?>" class="datepicker" placeholder="dd/mm/aaaa" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="CTR_DATA_FIM" class="control-label">Data de Fim</label>
                                        <div class="controls">
                                            <input id="CTR_DATA_FIM" type="text" name="CTR_DATA_FIM" value="<?= $result->CTR_DATA_FIM ? date('d/m/Y', strtotime($result->CTR_DATA_FIM)) : '' ?>" class="datepicker" placeholder="dd/mm/aaaa" />
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
                                        <option value="">Selecione...</option>
                                        <?php foreach ($tiposAssinante as $key => $value): ?>
                                            <option value="<?= $key ?>" <?= $result->CTR_TIPO_ASSINANTE == $key ? 'selected' : '' ?>><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Anexo Atual -->
                            <?php if ($result->CTR_ANEXO): ?>
                            <div class="control-group">
                                <label class="control-label">Anexo Atual</label>
                                <div class="controls">
                                    <a href="<?= base_url() ?>index.php/contratos/download_anexo/<?= $result->CTR_ID ?>" class="btn btn-mini btn-info" target="_blank">
                                        <i class="fas fa-download"></i> Download do Anexo
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Novo Anexo -->
                            <div class="control-group">
                                <label for="CTR_ANEXO" class="control-label">Novo Anexo (PDF/Imagem)</label>
                                <div class="controls">
                                    <input id="CTR_ANEXO" type="file" name="CTR_ANEXO" accept=".pdf,.jpg,.jpeg,.png" />
                                    <span class="help-block">Formatos aceitos: PDF, JPG, PNG (máx. 5MB). Deixe em branco para manter o anexo atual.</span>
                                </div>
                            </div>

                            <!-- Observação -->
                            <div class="control-group">
                                <label for="CTR_OBSERVACAO" class="control-label">Observação</label>
                                <div class="controls">
                                    <textarea id="CTR_OBSERVACAO" name="CTR_OBSERVACAO" rows="4" class="span6"><?= $result->CTR_OBSERVACAO ?></textarea>
                                </div>
                            </div>

                            <!-- Situação -->
                            <div class="control-group">
                                <label for="CTR_SITUACAO" class="control-label">Situação</label>
                                <div class="controls">
                                    <select id="CTR_SITUACAO" name="CTR_SITUACAO">
                                        <option value="1" <?= $result->CTR_SITUACAO == 1 ? 'selected' : '' ?>>Ativo</option>
                                        <option value="0" <?= $result->CTR_SITUACAO == 0 ? 'selected' : '' ?>>Inativo</option>
                                    </select>
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
                                <a href="<?php echo base_url() ?>index.php/contratos/visualizar/<?= $result->CTR_ID ?>" class="button btn btn-mini btn-warning">
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
                $("#cliente_selecionado").show();
                return false;
            }
        });

        // Validação do formulário
        $("#formContrato").submit(function(e) {
            var errors = [];

            if (!$("#PES_ID").val()) {
                errors.push("Selecione um cliente");
            }

            if (!$("#CTR_NUMERO").val()) {
                errors.push("Informe o número do contrato");
            }

            if (!$("#CTR_DATA_INICIO").val()) {
                errors.push("Informe a data de início");
            }

            if (!$("#CTR_TIPO_ASSINANTE").val()) {
                errors.push("Selecione o tipo de assinante");
            }

            if (errors.length > 0) {
                e.preventDefault();
                alert("Por favor, corrija os seguintes erros:\n\n" + errors.join("\n"));
                return false;
            }
        });
    });
</script>
