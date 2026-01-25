<style>
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

    /* Estilo para campos desabilitados */
    .form-control:disabled,
    .form-control[readonly] {
        background-color: #f8f9fa;
        opacity: 1;
        cursor: not-allowed;
    }

    .btn-edit {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    .btn-edit:hover {
        background-color: #0056b3;
    }

    /* Estilos para mostrar/ocultar campos baseado no tipo */
    .field-produto {
        display: block;
    }

    .field-servico {
        display: none;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <h5>Visualizar Produto</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php if (isset($custom_error) && $custom_error != ''): ?>
                    <?php echo $custom_error; ?>
                <?php endif; ?>
                <form id="formProduto" class="form-horizontal">
                    <div class="row-fluid">
                        <div class="span6">
                            <!-- Coluna 1: Informações Básicas -->
                            <div class="control-group">
                                <?php echo form_hidden('PRO_ID', $result->PRO_ID) ?>
                                <label for="codigo" class="control-label">Código do Produto</label>
                                <div class="controls">
                                    <input id="codigo" type="text" name="codigo" value="<?php echo $result->PRO_ID; ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="codDeBarra" class="control-label">Código de Barra</label>
                                <div class="controls">
                                    <div style="display: flex; gap: 10px; align-items: center;">
                                        <input id="codDeBarra" type="text" name="codDeBarra" value="<?php echo $result->PRO_COD_BARRA; ?>" readonly style="width: 200px;" />
                                        <?php if (!empty($result->codDeBarra)): ?>
                                            <span class="badge badge-info" style="padding: 5px 10px; font-size: 12px; background-color: #17a2b8; color: white; border-radius: 4px;">
                                                GTIN-<?php echo strlen(preg_replace('/[^0-9]/', '', $result->PRO_COD_BARRA)); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="descricao" class="control-label">Descrição<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="descricao" type="text" name="descricao" value="<?php echo $result->PRO_DESCRICAO; ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="NCMs" class="control-label">NCM<span class="required">*</span></label>
                                <div class="controls">
                                    <div class="input-group" style="display: flex; gap: 5px;">
                                        <input id="NCMs" class="form-control" type="text" name="NCMs" value="<?php echo $result->PRO_NCM; ?>" readonly />
                                        <button type="button" class="btn btn-warning" id="btnDescricaoNcm" style="border-radius: 4px;" title="Nomenclatura Comum do Mercosul - Código de 8 dígitos que classifica produtos para fins fiscais e aduaneiros"><i class="fas fa-info-circle"></i></button>
                                    </div>
                                    <input id="ncm_id" class="form-control" type="hidden" name="ncm_id" value="<?php echo $result->NCM_ID; ?>" />
                                </div>
                            </div>
                            <?php
                                $finalidadeSelecionada = $result->PRO_FINALIDADE ?? 'Comercialização';
                                if ($finalidadeSelecionada === 'COMERCIALIZACAO') {
                                    $finalidadeSelecionada = 'Comercialização';
                                }
                            ?>
                            <div class="control-group field-produto">
                                <label for="finalidade" class="control-label">Finalidade<span class="required">*</span></label>
                                <div class="controls">
                                    <select id="finalidade" name="finalidade" disabled>
                                        <?php foreach ($finalidadesProduto as $valor => $rotulo) : ?>
                                            <option value="<?php echo $valor; ?>" <?php echo $finalidadeSelecionada === $valor ? 'selected' : ''; ?>>
                                                <?php echo $rotulo; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group field-servico">
                                <label for="cclass_serv" class="control-label">cClass (Serviço)</label>
                                <div class="controls">
                                    <input id="cclass_serv" type="text" name="cclass_serv" value="<?php echo $result->PRO_CCLASS_SERV; ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="unidade" class="control-label">Unidade<span class="required">*</span></label>
                                <div class="controls">
                                    <select id="unidade" name="unidade" style="width: 15em;" disabled></select>
                                </div>
                            </div>
                            <div class="control-group field-servico">
                                <label for="precoVenda_servico" class="control-label">Preço Serviço<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="precoVenda_servico" type="text" name="precoVenda_servico" value="<?php echo number_format($result->PRO_PRECO_VENDA, 2, ',', '.'); ?>" readonly />
                                </div>
                            </div>
                        </div>

                        <div class="span6">
                            <!-- Coluna 2: Preços e Estoque -->
                            <div class="control-group field-produto">
                                <label for="precoCompra" class="control-label">Preço de Compra<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="precoCompra" type="text" name="precoCompra" value="<?php echo number_format($result->PRO_PRECO_COMPRA, 2, ',', '.'); ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="precoVenda" class="control-label">Preço de Venda<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="precoVenda" type="text" name="precoVenda" value="<?php echo number_format($result->PRO_PRECO_VENDA, 2, ',', '.'); ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="estoque" class="control-label">Estoque<span class="required">*</span></label>
                                <div class="controls">
                                    <input id="estoque" type="number" name="estoque" value="<?php echo $result->PRO_ESTOQUE; ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="estoqueMinimo" class="control-label">Estoque Mínimo</label>
                                <div class="controls">
                                    <input id="estoqueMinimo" type="number" name="estoqueMinimo" value="<?php echo $result->PRO_ESTOQUE_MINIMO; ?>" readonly />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="origem" class="control-label">Origem do Produto<span class="required">*</span></label>
                                <div class="controls">
                                    <select id="origem" name="origem" disabled>
                                        <option value="0" <?php if (!isset($result->PRO_ORIGEM) || $result->PRO_ORIGEM == 0) echo 'selected'; ?>>0 - Nacional (exceto as indicadas nos códigos 3, 4, 5 e 8)</option>
                                        <option value="1" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 1) echo 'selected'; ?>>1 - Estrangeira – Importação direta</option>
                                        <option value="2" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 2) echo 'selected'; ?>>2 - Estrangeira – Adquirida no mercado interno</option>
                                        <option value="3" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 3) echo 'selected'; ?>>3 - Nacional – Conteúdo de importação superior a 40% e inferior ou igual a 70%</option>
                                        <option value="4" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 4) echo 'selected'; ?>>4 - Nacional – Produzido conforme os processos produtivos básicos (PPB)</option>
                                        <option value="5" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 5) echo 'selected'; ?>>5 - Nacional – Conteúdo de importação inferior ou igual a 40%</option>
                                        <option value="6" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 6) echo 'selected'; ?>>6 - Estrangeira – Importação direta sem similar nacional, constante da CAMEX</option>
                                        <option value="7" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 7) echo 'selected'; ?>>7 - Estrangeira – Adquirida no mercado interno, sem similar nacional</option>
                                        <option value="8" <?php if (isset($result->PRO_ORIGEM) && $result->PRO_ORIGEM == 8) echo 'selected'; ?>>8 - Nacional – Conteúdo de importação superior a 70%</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid field-produto">
                        <div class="span12">
                            <h5 style="margin: 20px 0 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; padding-left: 20px;">Dimensões e Peso</h5>
                        </div>
                    </div>

                    <div class="row-fluid field-produto" style="margin-left: 0;">
                        <div class="span2">
                            <div class="control-group">
                                <label for="peso_bruto" class="control-label">Peso Bruto (kg)</label>
                                <div class="controls">
                                        <input id="peso_bruto" type="text" name="peso_bruto" value="<?php echo number_format($result->PRO_PESO_BRUTO, 3, ',', '.'); ?>" readonly style="width: 100px;" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="peso_liquido" class="control-label">Peso Líquido (kg)</label>
                                <div class="controls">
                                        <input id="peso_liquido" type="text" name="peso_liquido" value="<?php echo number_format($result->PRO_PESO_LIQUIDO, 3, ',', '.'); ?>" readonly style="width: 100px;" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="largura" class="control-label">Largura (cm)</label>
                                <div class="controls">
                                        <input id="largura" type="text" name="largura" value="<?php echo number_format($result->PRO_LARGURA, 3, ',', '.'); ?>" readonly style="width: 100px;" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="altura" class="control-label">Altura (cm)</label>
                                <div class="controls">
                                        <input id="altura" type="text" name="altura" value="<?php echo number_format($result->PRO_ALTURA, 3, ',', '.'); ?>" readonly style="width: 100px;" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="comprimento" class="control-label">Comprimento (cm)</label>
                                <div class="controls">
                                        <input id="comprimento" type="text" name="comprimento" value="<?php echo number_format($result->PRO_COMPRIMENTO, 3, ',', '.'); ?>" readonly style="width: 100px;" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <a href="<?php echo base_url() ?>index.php/produtos/adicionar" class="button btn btn-success" style="max-width: 160px">
                                    <span class="button__icon"><i class="fas fa-plus"></i></span>
                                    <span class="button__text2">Novo</span>
                                </a>
                                <a href="<?php echo base_url() ?>index.php/produtos/editar/<?php echo $result->PRO_ID; ?>" class="button btn btn-primary" style="max-width: 160px">
                                    <span class="button__icon"><i class="fas fa-edit"></i></span>
                                    <span class="button__text2">Editar</span>
                                </a>
                                <a href="<?php echo base_url() ?>index.php/produtos" class="button btn btn-mini btn-warning">
                                    <span class="button__icon"><i class="fas fa-undo"></i></span>
                                    <span class="button__text2">Voltar</span>
                                </a>
                            </div>
            </div>
        </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/ncm-search.js"></script>
<script type="text/javascript">
    // Função para mostrar/ocultar campos baseado no tipo do produto
    function toggleFieldsVisualizar() {
        var isService = '<?php echo $result->PRO_TIPO; ?>' == '2';

        if (isService) {
            // É serviço - mostrar apenas campos de serviço
            $('.field-produto').hide();
            $('.field-servico').show();

            // Alterar label do código
            $('label[for="codigo"]').text('Código do Serviço');
        } else {
            // É produto - mostrar apenas campos de produto
            $('.field-produto').show();
            $('.field-servico').hide();

            // Alterar label do código
            $('label[for="codigo"]').text('Código do Produto');
        }
    }

    $(document).ready(function() {
        // Aplicar lógica de mostrar/ocultar campos na visualização
        toggleFieldsVisualizar();

        // Carregar unidades baseado no tipo
        var isService = '<?php echo $result->PRO_TIPO; ?>' == '2';

        if (isService) {
            // Carregar unidades de serviço
            $.getJSON('<?php echo base_url() ?>assets/json/unidades_servico.json', function(data) {
                var select = $('#unidade');
                select.empty();
                select.append('<option value="">Selecione</option>');
                $.each(data.unidades_servico, function(i, unidade) {
                    var texto = unidade.codigo + ' - ' + unidade.descricao;
                    select.append($('<option></option>').val(unidade.valor).text(texto));
                });
                // Selecionar a unidade atual
                if ('<?php echo $result->PRO_UNID_MEDIDA; ?>') {
                    select.val('<?php echo $result->PRO_UNID_MEDIDA; ?>');
                }
            });
        } else {
            // Carregar unidades de produto
            $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
                var select = $('#unidade');
                select.empty();
                select.append('<option value="">Selecione</option>');
                $.each(data.medidas, function(i, medida) {
                    select.append($('<option></option>').val(medida.sigla).text(medida.descricao));
                });
                // Selecionar a unidade atual
                if ('<?php echo $result->PRO_UNID_MEDIDA; ?>') {
                    select.val('<?php echo $result->PRO_UNID_MEDIDA; ?>');
                }
            });
        }
    });
</script>
