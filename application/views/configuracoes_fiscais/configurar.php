<style>
    .form-section {
        border-bottom: 1px solid #e5e5e5;
        margin-bottom: 20px;
        padding-bottom: 10px;
    }

    .section-title {
        color: #2d3436;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 10px;
        color: #0984e3;
    }

    .help-inline {
        color: #999;
        font-size: 11px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon"><i class="fas fa-cog"></i></span>
                <h5>Configurando:
                    <?php echo $tipoNome; ?>
                </h5>
            </div>
            <div class="widget-content nopadding">
                <?php echo $custom_error; ?>

                <form action="<?php echo current_url(); ?>" id="formConfigFiscal" method="post" class="form-horizontal">

                    <div class="form-section" style="padding: 20px;">
                        <h4 class="section-title"><i class="fas fa-sliders-h"></i> Parâmetros de Emissão</h4>

                        <div class="control-group">
                            <label class="control-label">Status</label>
                            <div class="controls">
                                <label class="radio inline">
                                    <input type="radio" name="cfg_ativo" value="1" <?php echo (!$config || $config->cfg_ativo == 1) ? 'checked' : ''; ?>> Ativo
                                </label>
                                <label class="radio inline">
                                    <input type="radio" name="cfg_ativo" value="0" <?php echo ($config && $config->cfg_ativo == 0) ? 'checked' : ''; ?>> Inativo
                                </label>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="cfg_ambiente" class="control-label">Ambiente<span
                                    class="required">*</span></label>
                            <div class="controls">
                                <select name="cfg_ambiente" id="cfg_ambiente">
                                    <option value="2" <?php echo ($config && $config->cfg_ambiente == 2) ? 'selected' : ''; ?>>Homologação (Testes)</option>
                                    <option value="1" <?php echo ($config && $config->cfg_ambiente == 1) ? 'selected' : ''; ?>>Produção (Válido)</option>
                                </select>
                            </div>
                        </div>

                        <div class="row-fluid">
                            <div class="span6">
                                <div class="control-group">
                                    <label for="cfg_serie" class="control-label">Série<span
                                            class="required">*</span></label>
                                    <div class="controls">
                                        <input id="cfg_serie" type="text" name="cfg_serie"
                                            value="<?php echo $config ? $config->cfg_serie : '1'; ?>"
                                            style="width: 80px;" />
                                    </div>
                                </div>
                            </div>
                            <div class="span6">
                                <div class="control-group">
                                    <label for="cfg_numero_atual" class="control-label">Próximo Número<span
                                            class="required">*</span></label>
                                    <div class="controls">
                                        <input id="cfg_numero_atual" type="number" name="cfg_numero_atual"
                                            value="<?php echo $config ? $config->cfg_numero_atual : '1'; ?>"
                                            style="width: 120px;" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="cer_id" class="control-label">Certificado Digital</label>
                            <div class="controls">
                                <select name="cer_id" id="cer_id" class="span8">
                                    <option value="">-- Sem Certificado (Não recomendado) --</option>
                                    <?php foreach ($certificados as $cert): ?>
                                        <option value="<?php echo $cert->cer_id; ?>" <?php echo ($config && $config->cer_id == $cert->cer_id) ? 'selected' : ''; ?>>
                                            <?php echo "Certificado ID: {$cert->cer_id} - CNPJ: {$cert->cer_cnpj} - Validade: " . date('d/m/Y', strtotime($cert->cer_validade_fim)); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <a href="<?php echo base_url(); ?>index.php/certificados/adicionar"
                                    class="btn btn-mini btn-info" style="margin-top: 5px;"><i class="fas fa-plus"></i>
                                    Novo Certificado</a>
                            </div>
                        </div>
                    </div>

                    <?php if ($tipoDocumento === 'NFCE'): ?>
                        <div class="form-section" style="padding: 20px;">
                            <h4 class="section-title"><i class="fas fa-key"></i> Configurações Específicas NFC-e (CSC)</h4>
                            <div class="control-group">
                                <label for="cfg_csc_id" class="control-label">ID do Token (CSC)</label>
                                <div class="controls">
                                    <input id="cfg_csc_id" type="text" name="cfg_csc_id"
                                        value="<?php echo $config ? $config->cfg_csc_id : ''; ?>"
                                        placeholder="Ex: 000001" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="cfg_csc_token" class="control-label">Código do Token (CSC)</label>
                                <div class="controls">
                                    <input id="cfg_csc_token" type="text" name="cfg_csc_token" class="span8"
                                        value="<?php echo $config ? $config->cfg_csc_token : ''; ?>"
                                        placeholder="Ex: 12345678-90AB-CDEF-GHIJ-KLMNOPQRSTUV" />
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($tipoDocumento === 'NFCOM'): ?>
                        <div class="form-section" style="padding: 20px;">
                            <h4 class="section-title"><i class="fas fa-key"></i> Configurações Específicas NFCom (CSC)</h4>
                            <div class="control-group">
                                <label for="cfg_csc_id" class="control-label">ID do Token (CSC)</label>
                                <div class="controls">
                                    <input id="cfg_csc_id" type="text" name="cfg_csc_id"
                                        value="<?php echo $config ? $config->cfg_csc_id : ''; ?>"
                                        placeholder="Ex: 000001" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="cfg_csc_token" class="control-label">Código do Token (CSC)</label>
                                <div class="controls">
                                    <input id="cfg_csc_token" type="text" name="cfg_csc_token" class="span8"
                                        value="<?php echo $config ? $config->cfg_csc_token : ''; ?>"
                                        placeholder="Ex: 12345678-90AB-CDEF-GHIJ-KLMNOPQRSTUV" />
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($tipoDocumento === 'NFSE'): ?>
                        <div class="form-section" style="padding: 20px;">
                            <h4 class="section-title"><i class="fas fa-university"></i> Configurações Específicas NFS-e</h4>
                            <div class="row-fluid">
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="cfg_aliquota_iss" class="control-label">Alíquota ISS (%)</label>
                                        <div class="controls">
                                            <input id="cfg_aliquota_iss" type="text" name="cfg_aliquota_iss"
                                                value="<?php echo $config ? $config->cfg_aliquota_iss : ''; ?>"
                                                style="width: 80px;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group">
                                        <label for="cfg_regime_especial" class="control-label">Regime Especial</label>
                                        <div class="controls">
                                            <select name="cfg_regime_especial" id="cfg_regime_especial">
                                                <option value="">Nenhum</option>
                                                <option value="1" <?php echo ($config && $config->cfg_regime_especial == '1') ? 'selected' : ''; ?>>Microempresa Municipal</option>
                                                <option value="2" <?php echo ($config && $config->cfg_regime_especial == '2') ? 'selected' : ''; ?>>Estimativa</option>
                                                <option value="3" <?php echo ($config && $config->cfg_regime_especial == '3') ? 'selected' : ''; ?>>Sociedade de Profissionais</option>
                                                <option value="4" <?php echo ($config && $config->cfg_regime_especial == '4') ? 'selected' : ''; ?>>Cooperativa</option>
                                                <option value="5" <?php echo ($config && $config->cfg_regime_especial == '5') ? 'selected' : ''; ?>>MEI - Simples Nacional</option>
                                                <option value="6" <?php echo ($config && $config->cfg_regime_especial == '6') ? 'selected' : ''; ?>>ME ou EPP - Simples Nacional</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-section" style="padding: 20px;">
                        <h4 class="section-title"><i class="fas fa-print"></i> Opções de Impressão</h4>
                        <div class="control-group">
                            <label for="cfg_formato_impressao" class="control-label">Formato Padrão</label>
                            <div class="controls">
                                <select name="cfg_formato_impressao" id="cfg_formato_impressao">
                                    <option value="A4" <?php echo (!$config || $config->cfg_formato_impressao == 'A4') ? 'selected' : ''; ?>>Papel A4 (Padrão)</option>
                                    <option value="80mm" <?php echo ($config && $config->cfg_formato_impressao == '80mm') ? 'selected' : ''; ?>>Térmica 80mm</option>
                                    <option value="58mm" <?php echo ($config && $config->cfg_formato_impressao == '58mm') ? 'selected' : ''; ?>>Térmica 58mm</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions" style="background-color: #f9f9f9; text-align: right;">
                        <div class="span12">
                            <a href="<?php echo base_url(); ?>index.php/configuracoesfiscais" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Salvar Configurações
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#formConfigFiscal').validate({
            rules: {
                cfg_serie: { required: true },
                cfg_numero_atual: { required: true, number: true }
            },
            messages: {
                cfg_serie: { required: 'Campo Requerido.' },
                cfg_numero_atual: { required: 'Campo Requerido.', number: 'Digite apenas números.' }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script>
