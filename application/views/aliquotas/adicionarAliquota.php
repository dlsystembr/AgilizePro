<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-percentage"></i>
                </span>
                <h5>Cadastro de Alíquota</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formAliquota" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label for="uf_origem" class="control-label">UF Origem<span class="required">*</span></label>
                        <div class="controls">
                            <select name="uf_origem" id="uf_origem" class="span12">
                                <option value="">Selecione...</option>
                                <?php foreach ($ufs as $uf => $nome) { ?>
                                    <option value="<?php echo $uf; ?>"><?php echo $uf . ' - ' . $nome; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="uf_destino" class="control-label">UF Destino<span class="required">*</span></label>
                        <div class="controls">
                            <select name="uf_destino" id="uf_destino" class="span12">
                                <option value="">Selecione...</option>
                                <option value="TODOS">Todos os Estados</option>
                                <?php foreach ($ufs as $uf => $nome) { ?>
                                    <option value="<?php echo $uf; ?>"><?php echo $uf . ' - ' . $nome; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="aliquota_origem" class="control-label">Alíquota Origem (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliquota_origem" class="span12" name="aliquota_origem" type="text" value="<?php echo set_value('aliquota_origem'); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="aliquota_destino" class="control-label">Alíquota Destino (%)<span class="required">*</span></label>
                        <div class="controls">
                            <input id="aliquota_destino" class="span12" name="aliquota_destino" type="text" value="<?php echo set_value('aliquota_destino'); ?>" />
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-success"><span class="btn btn-success"><i class="fas fa-plus"></i> Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/aliquotas" id="btnAdicionar" class="button btn btn-mini btn-warning"><span class="btn btn-warning"><i class="fas fa-arrow-left"></i> Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formAliquota').validate({
            rules: {
                uf_origem: {
                    required: true
                },
                uf_destino: {
                    required: true
                },
                aliquota_origem: {
                    required: true,
                    number: true,
                    min: 0,
                    max: 100
                },
                aliquota_destino: {
                    required: true,
                    number: true,
                    min: 0,
                    max: 100
                }
            },
            messages: {
                uf_origem: {
                    required: 'Campo obrigatório'
                },
                uf_destino: {
                    required: 'Campo obrigatório'
                },
                aliquota_origem: {
                    required: 'Campo obrigatório',
                    number: 'Digite um número válido',
                    min: 'Valor mínimo é 0',
                    max: 'Valor máximo é 100'
                },
                aliquota_destino: {
                    required: 'Campo obrigatório',
                    number: 'Digite um número válido',
                    min: 'Valor mínimo é 0',
                    max: 'Valor máximo é 100'
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