<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                <h5>Editar Usuário</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') {
                    echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                } ?>
                <form action="<?php echo current_url(); ?>" id="formUsuario" method="post" class="form-horizontal">
                    <div class="control-group">
                        <?php echo form_hidden('usu_id', isset($result->usu_id) ? $result->usu_id : $result->idUsuarios) ?>
                        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
                        <div class="controls">
                            <input id="nome" type="text" name="nome" value="<?php echo isset($result->usu_nome) ? $result->usu_nome : $result->nome; ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="email" class="control-label">Email<span class="required">*</span></label>
                        <div class="controls">
                            <input id="email" type="text" name="email" value="<?php echo isset($result->usu_email) ? $result->usu_email : $result->email; ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="senha" class="control-label">Senha</label>
                        <div class="controls">
                            <input id="senha" type="password" name="senha" value="" placeholder="Não preencha se não quiser alterar." />
                            <i class="icon-exclamation-sign tip-top" title="Se não quiser alterar a senha, não preencha esse campo."></i>
                        </div>
                    </div>

                    <div class="control-group">
                        <label for="dataExpiracao" class="control-label">Expira em</label>
                        <div class="controls">
                            <input id="dataExpiracao" type="date" name="dataExpiracao" value="<?php echo isset($result->usu_data_expiracao) ? $result->usu_data_expiracao : ($result->dataExpiracao ?? ''); ?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">Situação*</label>
                        <div class="controls">
                            <select name="situacao" id="situacao">
                                <?php
                                $sit = isset($result->usu_situacao) ? $result->usu_situacao : ($result->situacao ?? 1);
                                $ativo = ($sit == 1) ? 'selected' : '';
                                $inativo = ($sit == 0) ? 'selected' : '';
                                ?>
                                <option value="1" <?php echo $ativo; ?>>Ativo</option>
                                <option value="0" <?php echo $inativo; ?>>Inativo</option>
                            </select>
                        </div>
                    </div>


                    <div class="control-group">
                        <label class="control-label">Grupo de usuário<span class="required">*</span></label>
                        <div class="controls">
                            <select name="gpu_id" id="gpu_id" required>
                                <option value="">Selecione um grupo</option>
                                <?php
                                $gpu_atual = isset($gpu_id_atual) ? (int) $gpu_id_atual : 0;
                                foreach ($grupos as $g) {
                                    $selected = ((int) $g->gpu_id === $gpu_atual) ? ' selected' : '';
                                    echo '<option value="' . (int) $g->gpu_id . '"' . $selected . '>' . htmlspecialchars($g->gpu_nome, ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex">
                                <button type="submit" class="button btn btn-primary">
                                  <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/usuarios" id="" class="button btn btn-mini btn-warning">
                                  <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text">Voltar</span></a>
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

        $('#formUsuario').validate({
            rules: {
                nome: { required: true },
                email: { required: true }
            },
            messages: {
                nome: { required: 'Campo Requerido.' },
                email: { required: 'Campo Requerido.' }
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
