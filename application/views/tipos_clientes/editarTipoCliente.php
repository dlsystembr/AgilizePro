<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-users"></i>
                </span>
                <h5>Editar Tipo de Cliente</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formTipoCliente" method="post" class="form-horizontal">
                    <?php echo form_hidden('id', $result->TPC_ID) ?>
                    <div class="control-group">
                        <label for="codigo" class="control-label">Código</label>
                        <div class="controls">
                            <input id="codigo" type="text" name="codigo" value="<?php echo $result->TPC_ID; ?>" readonly />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo $result->TPC_NOME; ?>" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="codigoCliente" class="control-label">Código Cliente</label>
                        <div class="controls">
                            <input id="codigoCliente" type="text" name="codigoCliente" value="<?php echo $result->TPC_CODIGO_CLIENTE; ?>" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary">
                                    <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span>
                                </button>
                                <a title="Voltar" class="button btn btn-mini btn-warning" href="<?php echo base_url() ?>index.php/tipos_clientes">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formTipoCliente').validate({
            rules: {
                nome: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: 'Campo Requerido.'
                }
            },

            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>

