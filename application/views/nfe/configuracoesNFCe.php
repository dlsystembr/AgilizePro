<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-alt"></i>
                </span>
                <h5>Configurações de NFC-e</h5>
            </div>
            <div class="widget-content">
                <div class="span12" id="divConfiguracoes">
                    <form action="<?php echo base_url(); ?>index.php/nfe/saveConfiguracoesNFCe" id="formConfiguracoes" method="post" class="form-horizontal">
                        <div class="control-group">
                            <label for="ambiente" class="control-label">Ambiente<span class="required">*</span></label>
                            <div class="controls">
                                <select name="ambiente" id="ambiente" class="input-large">
                                    <option value="1" <?php echo isset($config->ambiente) && $config->ambiente == 1 ? 'selected' : ''; ?>>Produção</option>
                                    <option value="2" <?php echo isset($config->ambiente) && $config->ambiente == 2 ? 'selected' : ''; ?>>Homologação (Testes)</option>
                                </select>
                                <span class="help-inline">Ambiente onde as notas serão emitidas</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="csc" class="control-label">CSC (Código de Segurança do Contribuinte)<span class="required">*</span></label>
                            <div class="controls">
                                <input type="text" name="csc" id="csc" value="<?php echo isset($config->csc) ? $config->csc : ''; ?>" class="input-large" />
                                <span class="help-inline">Código fornecido pela SEFAZ</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="csc_id" class="control-label">ID do CSC<span class="required">*</span></label>
                            <div class="controls">
                                <input type="text" name="csc_id" id="csc_id" value="<?php echo isset($config->csc_id) ? $config->csc_id : ''; ?>" class="input-large" />
                                <span class="help-inline">Identificador do CSC fornecido pela SEFAZ</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="sequencia_nfce" class="control-label">Sequência de Número de Nota</label>
                            <div class="controls">
                                <input type="number" name="sequencia_nfce" id="sequencia_nfce" value="<?= isset($config->sequencia_nfce) ? $config->sequencia_nfce : 1 ?>" min="1" required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <div class="span12">
                                <div class="span6 offset3" style="display: flex;justify-content: center">
                                    <button type="submit" class="button btn btn-primary">
                                        <span class="button__icon"><i class='bx bx-save'></i></span><span class="button__text2">Salvar</span></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 