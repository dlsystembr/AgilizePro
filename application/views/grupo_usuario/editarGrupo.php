<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon"><i class="fas fa-users-cog"></i></span>
        <h5>Editar Grupo de Usuário</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <?php if (!empty($custom_error)) echo $custom_error; ?>
        <form action="<?= current_url(); ?>" id="formGrupo" method="post" class="form-horizontal">
            <div class="control-group">
                <label for="codigo" class="control-label">Código</label>
                <div class="controls">
                    <input id="codigo" type="text" value="<?= (int) $result->gpu_id ?>" readonly />
                </div>
            </div>
            <div class="control-group">
                <label for="gpu_nome" class="control-label">Nome<span class="required">*</span></label>
                <div class="controls">
                    <input id="gpu_nome" type="text" name="gpu_nome" value="<?= set_value('gpu_nome', $result->gpu_nome) ?>" maxlength="100" />
                </div>
            </div>
            <div class="control-group">
                <label for="gpu_descricao" class="control-label">Descrição</label>
                <div class="controls">
                    <input id="gpu_descricao" type="text" name="gpu_descricao" value="<?= set_value('gpu_descricao', $result->gpu_descricao) ?>" maxlength="255" />
                </div>
            </div>
            <div class="control-group">
                <label for="gpu_situacao" class="control-label">Situação<span class="required">*</span></label>
                <div class="controls">
                    <select id="gpu_situacao" name="gpu_situacao">
                        <option value="1" <?= set_select('gpu_situacao', '1', (int)$result->gpu_situacao === 1); ?>>Ativo</option>
                        <option value="0" <?= set_select('gpu_situacao', '0', (int)$result->gpu_situacao === 0); ?>>Inativo</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <div class="span12">
                    <div class="span6 offset3" style="display:flex;justify-content: center">
                        <button type="submit" class="button btn btn-primary">
                            <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span>
                        </button>
                        <a title="Voltar" class="button btn btn-mini btn-warning" href="<?= site_url('gruposUsuario/gerenciar') ?>">
                            <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url() ?>assets/js/jquery.validate.js"></script>
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
