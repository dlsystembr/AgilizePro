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
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-certificate"></i>
                </span>
                <h5>Novo Certificado Digital</h5>
            </div>
            <div class="widget-content nopadding">
                <?php echo $custom_error; ?>

                <form action="<?php echo current_url(); ?>" id="formCertificado" method="post" class="form-horizontal"
                    enctype="multipart/form-data">
                    <div class="form-section" style="padding: 20px;">
                        <h4 class="section-title"><i class="fas fa-file-upload"></i> Arquivo do Certificado</h4>

                        <div class="control-group">
                            <label for="CER_ARQUIVO" class="control-label">Arquivo (.pfx)<span
                                    class="required">*</span></label>
                            <div class="controls">
                                <input id="CER_ARQUIVO" type="file" name="CER_ARQUIVO" accept=".pfx" required />
                                <span class="help-block">Selecione o arquivo do certificado digital (formato
                                    PFX/P12).</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="CER_SENHA" class="control-label">Senha<span class="required">*</span></label>
                            <div class="controls">
                                <div class="input-append">
                                    <input id="CER_SENHA" type="password" name="CER_SENHA" required />
                                    <span class="add-on" style="cursor: pointer;" onclick="togglePassword()"><i
                                            id="eyeIcon" class="fas fa-eye"></i></span>
                                </div>
                                <span class="help-block">Senha do certificado digital fornecida pela autoridade
                                    certificadora.</span>
                            </div>
                        </div>

                        <div class="control-group">
                            <label for="CER_TIPO" class="control-label">Tipo de Certificado<span
                                    class="required">*</span></label>
                            <div class="controls">
                                <select name="CER_TIPO" id="CER_TIPO">
                                    <option value="A1">Certificado A1 (Arquivo)</option>
                                    <option value="A3">Certificado A3 (Cartão/Token)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Manual input removed as requested -->

                    <div class="form-actions" style="background-color: #f9f9f9; text-align: right;">
                        <div class="span12">
                            <a href="<?php echo base_url(); ?>index.php/certificados" class="btn btn-default">
                                <i class="fas fa-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Fazer Upload e Salvar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function togglePassword() {
        var x = document.getElementById("CER_SENHA");
        var icon = document.getElementById("eyeIcon");
        if (x.type === "password") {
            x.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            x.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

    $(document).ready(function () {
        $(".cnpj").mask("99.999.999/9999-99");

        $('#formCertificado').validate({
            rules: {
                CER_SENHA: { required: true },
                CER_ARQUIVO: { required: true }
            },
            messages: {
                CER_SENHA: { required: 'Campo Requerido.' },
                CER_ARQUIVO: { required: 'Selecione o arquivo do certificado.' }
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

<script src="<?php echo base_url(); ?>assets/js/jquery.mask.min.js"></script>
<?php $this->load->view('tema/rodape'); ?>