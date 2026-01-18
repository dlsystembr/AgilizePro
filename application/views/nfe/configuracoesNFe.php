<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-alt"></i>
                </span>
                <h5>Configurações de NFe</h5>
            </div>
            <div class="widget-content">
                <div class="span12" id="divConfiguracoes">
                    <form action="<?php echo base_url(); ?>index.php/nfe/saveConfiguracoesNFe" id="formConfiguracoes" method="post" class="form-horizontal">
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
                            <label for="versao_nfe" class="control-label">Versão NFe<span class="required">*</span></label>
                            <div class="controls">
                                <select name="versao_nfe" id="versao_nfe" class="input-large">
                                    <option value="4.00" <?php echo isset($config->versao_nfe) && $config->versao_nfe == '4.00' ? 'selected' : ''; ?>>4.00</option>
                                </select>
                                <span class="help-inline">Versão do layout da NFe</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="tipo_impressao_danfe" class="control-label">Tipo de Impressão DANFE<span class="required">*</span></label>
                            <div class="controls">
                                <select name="tipo_impressao_danfe" id="tipo_impressao_danfe" class="input-large">
                                    <option value="1" <?php echo isset($config->tipo_impressao_danfe) && $config->tipo_impressao_danfe == 1 ? 'selected' : ''; ?>>Normal</option>
                                    <option value="2" <?php echo isset($config->tipo_impressao_danfe) && $config->tipo_impressao_danfe == 2 ? 'selected' : ''; ?>>DANFE Simplificado</option>
                                </select>
                                <span class="help-inline">Tipo de impressão do DANFE</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="orientacao_danfe" class="control-label">Orientação DANFE<span class="required">*</span></label>
                            <div class="controls">
                                <select name="orientacao_danfe" id="orientacao_danfe" class="input-large">
                                    <option value="P" <?php echo isset($config->orientacao_danfe) && $config->orientacao_danfe == 'P' ? 'selected' : ''; ?>>Retrato</option>
                                    <option value="L" <?php echo isset($config->orientacao_danfe) && $config->orientacao_danfe == 'L' ? 'selected' : ''; ?>>Paisagem</option>
                                </select>
                                <span class="help-inline">Orientação de impressão do DANFE</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="sequencia_nota" class="control-label">Sequência de Número de Nota</label>
                            <div class="controls">
                                <input type="number" name="sequencia_nota" id="sequencia_nota" value="<?php echo isset($config->sequencia_nota) ? $config->sequencia_nota : 1; ?>" min="1" required>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Imprimir Logo na NFe</label>
                            <div class="controls">
                                <input type="checkbox" name="imprimir_logo_nfe" id="imprimir_logo_nfe" value="1" <?php echo (isset($config->imprimir_logo_nfe) && $config->imprimir_logo_nfe) ? 'checked' : ''; ?>>
                                <span class="help-inline">Se marcado, a logo do emitente será exibida na DANFE.</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label class="control-label">Preview NFe</label>
                            <div class="controls">
                                <input type="checkbox" name="preview_nfe" id="preview_nfe" value="1" <?php echo (isset($config->preview_nfe) && $config->preview_nfe) ? 'checked' : ''; ?>>
                                <span class="help-inline">Se marcado, ao emitir a NFe o preview de impressão será aberto automaticamente.</span>
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