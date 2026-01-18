<style>
    /* Estilos para validação */
    .control-group.error input,
    .control-group.error select {
        border-color: #b94a48;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    }

    .control-group.error .help-inline {
        color: #b94a48;
        display: inline-block;
        margin-left: 10px;
        position: relative;
        padding: 5px 10px;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        font-size: 12px;
        line-height: 1.4;
        white-space: nowrap;
    }

    .control-group.success input,
    .control-group.success select {
        border-color: #468847;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
    }

    .help-inline {
        display: none;
        color: #b94a48;
        font-size: 12px;
    }

    .control-group.error .help-inline {
        display: inline-block;
    }

    /* Seções organizadas */
    .form-section {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin-bottom: 20px;
        background: #fff;
    }

    .form-section-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-section-content {
        padding: 15px;
    }

    /* Alinhamento dos campos */
    .form-section .control-label {
        width: 120px;
        text-align: right;
    }

    .form-section .controls {
        margin-left: 140px;
    }

    /* Garantir que inputs não estourem o container */
    .form-section input[type="text"],
    .form-section input[type="email"],
    .form-section input[type="file"],
    .form-section select {
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        height: 30px;
        padding: 4px 8px;
        line-height: 20px;
        font-size: 14px;
    }

    /* Ajuste específico para o campo código que tem largura fixa */
    .form-section input#EMP_CODIGO {
        width: 150px;
        max-width: 150px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-building"></i>
                </span>
                <h5>Cadastro de Empresa</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formEmpresa" method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">

                    <!-- Seção Dados Gerais -->
                    <div class="form-section" style="margin-top: 20px;">
                        <div class="form-section-header">
                            <i class="fas fa-edit"></i>
                            <span>Dados Gerais</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Linha 1: Código e Situação -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_CODIGO" class="control-label">Código</label>
                                        <div class="controls">
                                            <input id="EMP_CODIGO" type="text" name="EMP_CODIGO"
                                                value="<?php echo set_value('EMP_CODIGO'); ?>"
                                                placeholder="Em branco = gerar" readonly
                                                style="width: 150px;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_SITUACAO" class="control-label">Situação</label>
                                        <div class="controls">
                                            <select id="EMP_SITUACAO" name="EMP_SITUACAO">
                                                <option value="1" <?php echo set_value('EMP_SITUACAO', '1') == '1' ? 'selected' : ''; ?>>Ativo</option>
                                                <option value="0" <?php echo set_value('EMP_SITUACAO') == '0' ? 'selected' : ''; ?>>Inativo</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 2: CNPJ -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span12">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_CNPJ" class="control-label">CNPJ<span class="required">*</span></label>
                                        <div class="controls">
                                            <input id="EMP_CNPJ" type="text" name="EMP_CNPJ"
                                                value="<?php echo set_value('EMP_CNPJ'); ?>"
                                                inputmode="numeric" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 3: Razão Social -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span12">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_RAZAO_SOCIAL" class="control-label">Razão Social<span class="required">*</span></label>
                                        <div class="controls">
                                            <input id="EMP_RAZAO_SOCIAL" type="text" name="EMP_RAZAO_SOCIAL"
                                                value="<?php echo set_value('EMP_RAZAO_SOCIAL'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 4: Nome Fantasia -->
                            <div class="row-fluid">
                                <div class="span12">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_NOME_FANTASIA" class="control-label">Nome Fantasia</label>
                                        <div class="controls">
                                            <input id="EMP_NOME_FANTASIA" type="text" name="EMP_NOME_FANTASIA"
                                                value="<?php echo set_value('EMP_NOME_FANTASIA'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Endereço -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Endereço</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Linha 1: CEP -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_CEP" class="control-label">CEP</label>
                                        <div class="controls">
                                            <input id="EMP_CEP" type="text" name="EMP_CEP"
                                                value="<?php echo set_value('EMP_CEP'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 2: Logradouro e Número -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span8">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_LOGRADOURO" class="control-label">Logradouro</label>
                                        <div class="controls">
                                            <input id="EMP_LOGRADOURO" type="text" name="EMP_LOGRADOURO"
                                                value="<?php echo set_value('EMP_LOGRADOURO'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_NUMERO" class="control-label">Número</label>
                                        <div class="controls">
                                            <input id="EMP_NUMERO" type="text" name="EMP_NUMERO"
                                                value="<?php echo set_value('EMP_NUMERO'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 3: Complemento -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span12">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_COMPLEMENTO" class="control-label">Complemento</label>
                                        <div class="controls">
                                            <input id="EMP_COMPLEMENTO" type="text" name="EMP_COMPLEMENTO"
                                                value="<?php echo set_value('EMP_COMPLEMENTO'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 4: Bairro -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span12">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_BAIRRO" class="control-label">Bairro</label>
                                        <div class="controls">
                                            <input id="EMP_BAIRRO" type="text" name="EMP_BAIRRO"
                                                value="<?php echo set_value('EMP_BAIRRO'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 5: Cidade e UF -->
                            <div class="row-fluid">
                                <div class="span8">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_CIDADE" class="control-label">Cidade</label>
                                        <div class="controls">
                                            <input id="EMP_CIDADE" type="text" name="EMP_CIDADE"
                                                value="<?php echo set_value('EMP_CIDADE'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span4">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_UF" class="control-label">UF</label>
                                        <div class="controls">
                                            <select id="EMP_UF" name="EMP_UF">
                                                <option value="">Selecione</option>
                                                <?php foreach ($estados as $estado): ?>
                                                    <option value="<?php echo $estado->EST_UF; ?>" 
                                                        <?php echo set_value('EMP_UF') == $estado->EST_UF ? 'selected' : ''; ?>>
                                                        <?php echo $estado->EST_UF; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seção Contato e Fiscal -->
                    <div class="form-section">
                        <div class="form-section-header">
                            <i class="fas fa-phone"></i>
                            <span>Contato e Informações Fiscais</span>
                        </div>
                        <div class="form-section-content">
                            <!-- Linha 1: Telefone e Email -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_TELEFONE" class="control-label">Telefone</label>
                                        <div class="controls">
                                            <input id="EMP_TELEFONE" type="text" name="EMP_TELEFONE"
                                                value="<?php echo set_value('EMP_TELEFONE'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_EMAIL" class="control-label">Email</label>
                                        <div class="controls">
                                            <input id="EMP_EMAIL" type="email" name="EMP_EMAIL"
                                                value="<?php echo set_value('EMP_EMAIL'); ?>" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 2: IE e Regime Tributário -->
                            <div class="row-fluid" style="margin-bottom: 15px;">
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_IE" class="control-label">Inscrição Estadual</label>
                                        <div class="controls">
                                            <input id="EMP_IE" type="text" name="EMP_IE"
                                                value="<?php echo set_value('EMP_IE'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                <div class="span6">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_REGIME_TRIBUTARIO" class="control-label">Regime Tributário</label>
                                        <div class="controls">
                                            <select id="EMP_REGIME_TRIBUTARIO" name="EMP_REGIME_TRIBUTARIO">
                                                <option value="">Selecione</option>
                                                <option value="Simples Nacional" <?php echo set_value('EMP_REGIME_TRIBUTARIO') == 'Simples Nacional' ? 'selected' : ''; ?>>Simples Nacional</option>
                                                <option value="Lucro Presumido" <?php echo set_value('EMP_REGIME_TRIBUTARIO') == 'Lucro Presumido' ? 'selected' : ''; ?>>Lucro Presumido</option>
                                                <option value="Lucro Real" <?php echo set_value('EMP_REGIME_TRIBUTARIO') == 'Lucro Real' ? 'selected' : ''; ?>>Lucro Real</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Linha 3: Logo -->
                            <div class="row-fluid">
                                <div class="span12">
                                    <div class="control-group" style="margin-bottom: 0;">
                                        <label for="EMP_LOGO_PATH" class="control-label">Caminho do Logo</label>
                                        <div class="controls">
                                            <input id="EMP_LOGO_PATH" type="text" name="EMP_LOGO_PATH"
                                                value="<?php echo set_value('EMP_LOGO_PATH'); ?>"
                                                placeholder="Ex: assets/logo.png" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                                    <span class="button__text2">Adicionar</span>
                                </button>
                                <a href="<?php echo base_url() ?>index.php/empresas" id=""
                                    class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class="bx bx-undo"></i></span>
                                    <span class="button__text2">Voltar</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Máscara para CNPJ
        $('#EMP_CNPJ').mask('00.000.000/0000-00');
        
        // Máscara para CEP
        $('#EMP_CEP').mask('00000-000');
        
        // Máscara para Telefone
        $('#EMP_TELEFONE').mask('(00) 00000-0000');
    });
</script>
