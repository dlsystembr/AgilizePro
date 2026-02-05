<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-users-cog"></i></span>
        <h5>Cadastro de Grupo de Usuário</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <?php if (!empty($custom_error)) echo $custom_error; ?>
        <form action="<?php echo current_url(); ?>" id="formGrupo" method="post" class="form-horizontal">
            <div class="control-group">
                <label for="gpu_nome" class="control-label">Nome<span class="required">*</span></label>
                <div class="controls">
                    <input id="gpu_nome" type="text" name="gpu_nome" value="<?php echo set_value('gpu_nome'); ?>" maxlength="100" />
                </div>
            </div>
            <div class="control-group">
                <label for="gpu_descricao" class="control-label">Descrição</label>
                <div class="controls">
                    <input id="gpu_descricao" type="text" name="gpu_descricao" value="<?php echo set_value('gpu_descricao'); ?>" maxlength="255" />
                </div>
            </div>
            <div class="control-group">
                <label for="gpu_situacao" class="control-label">Situação<span class="required">*</span></label>
                <div class="controls">
                    <select id="gpu_situacao" name="gpu_situacao">
                        <option value="1" <?php echo set_select('gpu_situacao', '1', true); ?>>Ativo</option>
                        <option value="0" <?php echo set_select('gpu_situacao', '0'); ?>>Inativo</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <div class="span12">
                    <div class="span6 offset3" style="display:flex;justify-content: center">
                        <button type="submit" class="button btn btn-success">
                            <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span>
                        </button>
                        <a title="Voltar" class="button btn btn-mini btn-warning" href="<?php echo site_url('gruposUsuario/gerenciar'); ?>">
                            <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formGrupo').validate({
            rules: { gpu_nome: { required: true } },
            messages: { gpu_nome: { required: 'Campo obrigatório.' } },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) { $(element).parents('.control-group').addClass('error'); },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error').addClass('success');
            }
        });
    });
</script>
