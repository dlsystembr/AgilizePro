<div class="span12" style="margin-left: 0">
    <form action="<?php echo base_url() ?>index.php/tiposPessoa/editar/<?php echo $result->idTipoPessoa; ?>" id="formTipoPessoa" method="post">
        <div class="span12" style="margin-left: 0">
            <div class="widget-box">
                <div class="widget-title" style="margin: -20px 0 0">
                    <span class="icon">
                        <i class="fas fa-users"></i>
                    </span>
                    <h5>Editar Tipo de Pessoa</h5>
                </div>
                <div class="widget-content">
                    <div class="span6">
                        <label for="nome">Nome<span class="required">*</span></label>
                        <input type="text" name="nome" id="nome" class="span12" value="<?php echo $result->nome; ?>" />
                    </div>
                    <div class="span6">
                        <label for="descricao">Descrição</label>
                        <input type="text" name="descricao" id="descricao" class="span12" value="<?php echo $result->descricao; ?>" />
                    </div>
                    <div class="span6">
                        <label for="situacao">Situação</label>
                        <select name="situacao" id="situacao" class="span12">
                            <?php if ($result->situacao == 1) {
                                $sim = 'selected';
                                $nao = '';
                            } else {
                                $sim = '';
                                $nao = 'selected';
                            } ?>
                            <option value="1" <?php echo $sim; ?>>Ativo</option>
                            <option value="0" <?php echo $nao; ?>>Inativo</option>
                        </select>
                    </div>
                    <div class="span12" style="padding: 1%;">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-success">
                                <span class="button__icon"><i class='bx bx-save'></i></span> <span class="button__text2">Salvar</span>
                            </button>
                            <a href="<?php echo base_url() ?>index.php/tiposPessoa" class="button btn btn-mini btn-warning">
                                <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="<?php echo base_url() ?>assets/js/validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formTipoPessoa').validate({
            rules: {
                nome: {
                    required: true
                }
            },
            messages: {
                nome: {
                    required: 'Campo obrigatório'
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