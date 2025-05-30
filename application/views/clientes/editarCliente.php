<script src="<?php echo base_url() ?>assets/js/jquery.mask.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<script src="<?php echo base_url() ?>assets/js/funcoes.js"></script>
<style>
    #imgSenha {
        width: 18px;
        cursor: pointer;
    }

    /* Hiding the checkbox, but allowing it to be focused */
    .badgebox {
        opacity: 0;
    }

    .badgebox+.badge {
        /* Move the check mark away when unchecked */
        text-indent: -999999px;
        /* Makes the badge's width stay the same checked and unchecked */
        width: 27px;
    }

    .badgebox:focus+.badge {
        /* Set something to make the badge looks focused */
        /* This really depends on the application, in my case it was: */
        /* Adding a light border */
        box-shadow: inset 0px 0px 5px;
        /* Taking the difference out of the padding */
    }

    .badgebox:checked+.badge {
        /* Move the check mark back when checked */
        text-indent: 0;
    }

    .control-group.error .help-inline {
        display: flex;
    }

    .form-horizontal .control-group {
        border-bottom: 1px solid #ffffff;
    }

    .form-horizontal .controls {
        margin-left: 20px;
        padding-bottom: 8px 0;
    }

    .form-horizontal .control-label {
        text-align: left;
        padding-top: 15px;
    }

    .nopadding {
        padding: 0 20px !important;
        margin-right: 20px;
    }

    .widget-title h5 {
        padding-bottom: 30px;
        text-align-last: left;
        font-size: 2em;
        font-weight: 500;
    }

    @media (max-width: 480px) {
        form {
            display: contents !important;
        }

        .form-horizontal .control-label {
            margin-bottom: -6px;
        }

        .btn-xs {
            position: initial !important;
        }
    }
</style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-user"></i>
                </span>
                <h5>Editar Cliente</h5>
            </div>
            <?php if ($custom_error != '') {
                echo '<div class="alert alert-danger">' . $custom_error . '</div>';
            } ?>
            <form action="<?php echo current_url(); ?>" id="formCliente" method="post" class="form-horizontal">
                <div class="widget-content nopadding tab-content">
                    <div class="span6">
                        <div class="control-group">
                            <label for="documento" class="control-label">CPF/CNPJ</label>
                            <div class="controls">
                                <input id="documento" class="cpfcnpj" type="text" name="documento" value="<?php echo $result->documento; ?>" />
                                <button id="buscar_info_cnpj" class="btn btn-xs" type="button">Buscar(CNPJ)</button>
                            </div>
                        </div>
                        <div class="control-group">
                            <?php echo form_hidden('idClientes', $result->idClientes) ?>
                            <label for="nomeCliente" class="control-label">Nome/Razão Social<span class="required">*</span></label>
                            <div class="controls">
                                <input id="nomeCliente" type="text" name="nomeCliente" value="<?php echo $result->nomeCliente; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="inscricao" class="control-label">Inscrição</label>
                            <div class="controls">
                                <input id="inscricao" type="text" name="inscricao" value="<?php echo $result->inscricao; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="natureza_contribuinte" class="control-label">Natureza do Contribuinte</label>
                            <div class="controls">
                                <select name="natureza_contribuinte" id="natureza_contribuinte">
                                    <option value="">Selecione</option>
                                    <option value="inscrito" <?php echo $result->natureza_contribuinte === 'inscrito' ? 'selected' : ''; ?>>Inscrito</option>
                                    <option value="nao_inscrito" <?php echo $result->natureza_contribuinte === 'nao_inscrito' ? 'selected' : ''; ?>>Não Inscrito</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="contato" class="control-label">Contato:</label>
                            <div class="controls">
                                <input class="contato" type="text" name="contato" value="<?php echo $result->contato; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="telefone" class="control-label">Telefone</label>
                            <div class="controls">
                                <input id="telefone" type="text" name="telefone" value="<?php echo $result->telefone; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="celular" class="control-label">Celular</label>
                            <div class="controls">
                                <input id="celular" type="text" name="celular" value="<?php echo $result->celular; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="email" class="control-label">Email</label>
                            <div class="controls">
                                <input id="email" type="text" name="email" value="<?php echo $result->email; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="senha" class="control-label">Senha</label>
                            <div class="controls">
                                <input id="senha" type="password" name="senha" value="" placeholder="Não preencha se não quiser alterar." />
                                <img id="imgSenha" src="<?php echo base_url() ?>assets/img/eye.svg" alt="">
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="objetivo_comercial" class="control-label">Objetivo Comercial<span class="required">*</span></label>
                            <div class="controls">
                                <select name="objetivo_comercial" id="objetivo_comercial" required>
                                    <option value="">Selecione</option>
                                    <option value="consumo" <?= $result->objetivo_comercial == 'consumo' ? 'selected' : '' ?>>Consumo</option>
                                    <option value="revenda" <?= $result->objetivo_comercial == 'revenda' ? 'selected' : '' ?>>Revenda</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="span6">
                        <div class="control-group" class="control-label">
                            <label for="cep" class="control-label">CEP</label>
                            <div class="controls">
                                <input id="cep" type="text" name="cep" value="<?php echo $result->cep; ?>" />
                            </div>
                        </div>
                        <div class="control-group" class="control-label">
                            <label for="rua" class="control-label">Rua</label>
                            <div class="controls">
                                <input id="rua" type="text" name="rua" value="<?php echo $result->rua; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="numero" class="control-label">Número</label>
                            <div class="controls">
                                <input id="numero" type="text" name="numero" value="<?php echo $result->numero; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="complemento" class="control-label">Complemento</label>
                            <div class="controls">
                                <input id="complemento" type="text" name="complemento" value="<?php echo $result->complemento; ?>" />
                            </div>
                        </div>
                        <div class="control-group" class="control-label">
                            <label for="bairro" class="control-label">Bairro</label>
                            <div class="controls">
                                <input id="bairro" type="text" name="bairro" value="<?php echo $result->bairro; ?>" />
                            </div>
                        </div>
                        <div class="control-group" class="control-label">
                            <label for="cidade" class="control-label">Cidade</label>
                            <div class="controls">
                                <input id="cidade" type="text" name="cidade" value="<?php echo $result->cidade; ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="estado" class="control-label">Estado<span class="required">*</span></label>
                            <div class="controls">
                                <select name="estado" id="estado" required>
                                    <option value="">Selecione...</option>
                                    <option value="AC" <?php echo ($result->estado == 'AC') ? 'selected' : ''; ?>>Acre</option>
                                    <option value="AL" <?php echo ($result->estado == 'AL') ? 'selected' : ''; ?>>Alagoas</option>
                                    <option value="AP" <?php echo ($result->estado == 'AP') ? 'selected' : ''; ?>>Amapá</option>
                                    <option value="AM" <?php echo ($result->estado == 'AM') ? 'selected' : ''; ?>>Amazonas</option>
                                    <option value="BA" <?php echo ($result->estado == 'BA') ? 'selected' : ''; ?>>Bahia</option>
                                    <option value="CE" <?php echo ($result->estado == 'CE') ? 'selected' : ''; ?>>Ceará</option>
                                    <option value="DF" <?php echo ($result->estado == 'DF') ? 'selected' : ''; ?>>Distrito Federal</option>
                                    <option value="ES" <?php echo ($result->estado == 'ES') ? 'selected' : ''; ?>>Espírito Santo</option>
                                    <option value="GO" <?php echo ($result->estado == 'GO') ? 'selected' : ''; ?>>Goiás</option>
                                    <option value="MA" <?php echo ($result->estado == 'MA') ? 'selected' : ''; ?>>Maranhão</option>
                                    <option value="MT" <?php echo ($result->estado == 'MT') ? 'selected' : ''; ?>>Mato Grosso</option>
                                    <option value="MS" <?php echo ($result->estado == 'MS') ? 'selected' : ''; ?>>Mato Grosso do Sul</option>
                                    <option value="MG" <?php echo ($result->estado == 'MG') ? 'selected' : ''; ?>>Minas Gerais</option>
                                    <option value="PA" <?php echo ($result->estado == 'PA') ? 'selected' : ''; ?>>Pará</option>
                                    <option value="PB" <?php echo ($result->estado == 'PB') ? 'selected' : ''; ?>>Paraíba</option>
                                    <option value="PR" <?php echo ($result->estado == 'PR') ? 'selected' : ''; ?>>Paraná</option>
                                    <option value="PE" <?php echo ($result->estado == 'PE') ? 'selected' : ''; ?>>Pernambuco</option>
                                    <option value="PI" <?php echo ($result->estado == 'PI') ? 'selected' : ''; ?>>Piauí</option>
                                    <option value="RJ" <?php echo ($result->estado == 'RJ') ? 'selected' : ''; ?>>Rio de Janeiro</option>
                                    <option value="RN" <?php echo ($result->estado == 'RN') ? 'selected' : ''; ?>>Rio Grande do Norte</option>
                                    <option value="RS" <?php echo ($result->estado == 'RS') ? 'selected' : ''; ?>>Rio Grande do Sul</option>
                                    <option value="RO" <?php echo ($result->estado == 'RO') ? 'selected' : ''; ?>>Rondônia</option>
                                    <option value="RR" <?php echo ($result->estado == 'RR') ? 'selected' : ''; ?>>Roraima</option>
                                    <option value="SC" <?php echo ($result->estado == 'SC') ? 'selected' : ''; ?>>Santa Catarina</option>
                                    <option value="SP" <?php echo ($result->estado == 'SP') ? 'selected' : ''; ?>>São Paulo</option>
                                    <option value="SE" <?php echo ($result->estado == 'SE') ? 'selected' : ''; ?>>Sergipe</option>
                                    <option value="TO" <?php echo ($result->estado == 'TO') ? 'selected' : ''; ?>>Tocantins</option>
                                </select>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="ibge" class="control-label">Código IBGE</label>
                            <div class="controls">
                                <input id="ibge" type="text" name="ibge" value="<?php echo $result->ibge; ?>" readonly />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                            <button type="submit" class="button btn btn-primary" style="max-width: 160px">
                                <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                            <a title="Voltar" class="button btn btn-warning" href="<?php echo site_url() ?>/clientes"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span></a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        let container = document.querySelector('div');
        let input = document.querySelector('#senha');
        let icon = document.querySelector('#imgSenha');

        // Lógica para natureza do contribuinte baseado na inscrição
        $('#inscricao').on('change blur', function() {
            var inscricao = $(this).val().trim();
            var naturezaContribuinte = $('#natureza_contribuinte');
            
            if (inscricao === '') {
                // Se não houver inscrição, define como não inscrito
                naturezaContribuinte.val('nao_inscrito');
            } else {
                // Se houver inscrição, define como inscrito
                naturezaContribuinte.val('inscrito');
            }
        });

        // Verifica o valor inicial da inscrição ao carregar a página
        $('#inscricao').trigger('blur');

        // Adiciona evento para quando o select de natureza_contribuinte mudar
        $('#natureza_contribuinte').on('change', function() {
            var natureza = $(this).val();
            var inscricao = $('#inscricao').val().trim();
            
            // Se mudar para inscrito e não tiver inscrição, limpa a inscrição
            if (natureza === 'inscrito' && inscricao === '') {
                $('#inscricao').val('');
            }
            // Se mudar para não inscrito, limpa a inscrição
            else if (natureza === 'nao_inscrito') {
                $('#inscricao').val('');
            }
        });

        icon.addEventListener('click', function() {
            container.classList.toggle('visible');
            if (container.classList.contains('visible')) {
                icon.src = '<?php echo base_url() ?>assets/img/eye-off.svg';
                input.type = 'text';
            } else {
                icon.src = '<?php echo base_url() ?>assets/img/eye.svg'
                input.type = 'password';
            }
        });

        // Busca CEP
        $("#cep").blur(function() {
            var cep = $(this).val().replace(/\D/g, '');
            if (cep != "") {
                var validacep = /^[0-9]{8}$/;
                if (validacep.test(cep)) {
                    $.getJSON("https://viacep.com.br/ws/" + cep + "/json/?callback=?", function(dados) {
                        if (!("erro" in dados)) {
                            $("#rua").val(dados.logradouro);
                            $("#bairro").val(dados.bairro);
                            $("#cidade").val(dados.localidade);
                            $("#estado").val(dados.uf);
                            
                            // Busca o código IBGE do município
                            $.getJSON("https://servicodados.ibge.gov.br/api/v1/localidades/estados/" + dados.uf + "/municipios", function(municipios) {
                                for (var i = 0; i < municipios.length; i++) {
                                    if (municipios[i].nome.toLowerCase() === dados.localidade.toLowerCase()) {
                                        $("#ibge").val(municipios[i].id);
                                        break;
                                    }
                                }
                            });
                        }
                    });
                }
            }
        });

        $.getJSON('<?php echo base_url() ?>assets/json/estados.json', function(data) {
            for (i in data.estados) {
                $('#estado').append(new Option(data.estados[i].nome, data.estados[i].sigla));
            }
            var curState = '<?php echo $result->estado; ?>';
            if (curState) {
                $("#estado option[value=" + curState + "]").prop("selected", true);
            }
        });
        $('#formCliente').validate({
            rules: {
                nomeCliente: {
                    required: true
                },
                objetivo_comercial: {
                    required: true
                }
            },
            messages: {
                nomeCliente: {
                    required: 'Campo Nome é obrigatório.'
                },
                objetivo_comercial: {
                    required: 'Campo Objetivo Comercial é obrigatório.'
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
