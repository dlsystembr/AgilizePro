<div class="row-fluid" style="margin-top: 10px;">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="bx bx-block"></i></span>
                <h5>Cancelar NFCom por Chave</h5>
            </div>
            <div class="widget-content nopadding">
                <form action="<?php echo site_url('nfecom/cancelarPorChave'); ?>" method="post" class="form-horizontal">
                    <div class="control-group">
                        <label class="control-label" for="chave_nfcom">Chave de acesso<span class="required">*</span></label>
                        <div class="controls">
                            <input type="text" name="chave_nfcom" id="chave_nfcom" class="span10"
                                   placeholder="Informe a chave de 44 dígitos" maxlength="44" required>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label" for="justificativa">Justificativa<span class="required">*</span></label>
                        <div class="controls">
                            <textarea name="justificativa" id="justificativa" class="span10" rows="4"
                                      placeholder="Mínimo 15 caracteres" required></textarea>
                        </div>
                    </div>

                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>"
                           value="<?php echo $this->security->get_csrf_hash(); ?>">

                    <div class="form-actions">
                        <button type="submit" class="btn btn-danger">Cancelar NFCom</button>
                        <a href="<?php echo site_url('nfecom'); ?>" class="btn btn-warning">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
