<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-edit"></i>
                </span>
                <h5>Editar Operação Comercial</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if ($custom_error != '') { echo $custom_error; } ?>
                <form action="<?php echo current_url(); ?>" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label">Nome da Operação<span class="required">*</span></label>
                        <div class="controls">
                            <input type="text" name="nome" value="<?= isset($result->nome_operacao) ? $result->nome_operacao : ''; ?>" required />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Mensagem Nota Fiscal</label>
                        <div class="controls">
                            <textarea name="mensagem_nota_fiscal" rows="2"><?= isset($result->mensagem_nota) ? $result->mensagem_nota : ''; ?></textarea>
                        </div>
                    </div>
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display:flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary">
                                    <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                                <a href="<?php echo base_url('index.php/operacaocomercial') ?>" class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> 