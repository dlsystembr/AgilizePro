L<style>
    .switch-container {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .switch-label {
        font-weight: 600;
        font-size: 14px;
        color: #333;
        transition: color 0.3s ease;
        min-width: 50px;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
        flex-shrink: 0;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 20px;
        box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
        overflow: hidden;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .toggle-switch input:checked + .toggle-slider {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .toggle-switch input:checked + .toggle-slider:before {
        content: "✓";
        color: #28a745;
        font-weight: bold;
        font-size: 12px;
    }

    .toggle-switch input:not(:checked) + .toggle-slider:before {
        content: "✗";
        color: #dc3545;
        font-weight: bold;
        font-size: 12px;
    }

    /* Estilos para o modal */
    .modal {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }

    #modalNcm {
        width: 900px !important;
        max-width: 900px !important;
    }

    #modalNcmDialog {
        width: 900px !important;
        max-width: 900px !important;
        margin: 0 auto;
    }
    
    .modal-content {
        border-radius: 6px;
        box-shadow: 0 3px 9px rgba(0,0,0,.5);
        background: #fff;
    }
    
    .modal-header {
        border-radius: 6px 6px 0 0;
        padding: 15px;
        background-color: #2c3e50;
        color: white;
        border-bottom: none;
    }
    
    .modal-body {
        padding: 20px;
        background: #fff;
    }
    
    .modal-footer {
        padding: 15px;
        background-color: #f5f5f5;
        border-top: 1px solid #ddd;
        border-radius: 0 0 6px 6px;
    }
    
    .table-responsive {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 0;
    }
    
    .table > thead > tr > th {
        padding: 12px;
        background-color: #f5f5f5;
        border-bottom: 2px solid #ddd;
    }
    
    .table > tbody > tr > td {
        padding: 12px;
        vertical-align: middle;
    }
    
    .table > tbody > tr > td:last-child {
        text-align: center;
    }

    /* Estilo para o botão Selecionar */
    .selecionarNcm {
        padding: 2px 8px !important;
        font-size: 12px !important;
        line-height: 1.5 !important;
        white-space: nowrap !important;
    }

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

    /* Estilos para mostrar/ocultar campos baseado no tipo */
    .field-produto {
        display: block;
    }

    .field-servico {
        display: none;
    }

    /* Estilos para mostrar/ocultar campos baseado no tipo */
    .field-produto {
        display: block;
    }

    .field-servico {
        display: none;
    }

    .control-group.error .help-inline:before {
        content: '';
        position: absolute;
        left: -6px;
        top: 50%;
        transform: translateY(-50%);
        border-style: solid;
        border-width: 6px 6px 6px 0;
        border-color: transparent #f5c6cb transparent transparent;
    }

    .control-group.error .help-inline:after {
        content: '';
        position: absolute;
        left: -5px;
        top: 50%;
        transform: translateY(-50%);
        border-style: solid;
        border-width: 6px 6px 6px 0;
        border-color: transparent #f8d7da transparent transparent;
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

    /* Ajuste para o layout dos campos */
    .control-group .controls {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .control-group .controls input,
    .control-group .controls select {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        #modalNcm,
        #modalNcmDialog {
            width: 95% !important;
            max-width: 95% !important;
            margin: 10px auto;
        }
    }

    #modalNcmSearchInputGroup {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
        align-items: stretch;
    }

    #pesquisaNcm {
        width: 100%;
        height: 38px;
        padding: 8px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    #btnPesquisarNcm {
        height: 38px;
        padding: 8px 16px;
        font-size: 14px;
        line-height: 1.42857143;
        white-space: nowrap;
        border-radius: 4px;
        background-color: #2c3e50;
        border-color: #2c3e50;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
    }

    #btnPesquisarNcm:hover {
        background-color: #34495e;
        border-color: #34495e;
    }
</style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <h5>Editar Produto</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <?php echo $custom_error; ?>
                <form action="<?php echo current_url(); ?>" id="formProduto" method="post" class="form-horizontal">
                    <div class="row-fluid">
                        <div class="span12">
                            <div class="control-group">
                                <label for="PRO_TIPO" class="control-label">Tipo de Item<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <div class="switch-container">
                                        <label class="toggle-switch">
                                            <input type="checkbox" id="PRO_TIPO_TOGGLE" <?= $result->PRO_TIPO == '2' ? 'checked' : '' ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <input type="hidden" name="PRO_TIPO" id="PRO_TIPO"
                                            value="<?= $result->PRO_TIPO ?: '1' ?>">
                                        <span id="tipo_label"
                                            class="switch-label"><?= $result->PRO_TIPO == '2' ? 'Serviço' : 'Produto' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <!-- Coluna 1: Informações Básicas -->
                    <div class="control-group">
                        <?php echo form_hidden('PRO_ID', $result->PRO_ID) ?>
                        <label for="codigo" class="control-label" id="codigo-label">Código do Produto</label>
                        <div class="controls">
                            <input id="codigo" type="text" name="codigo" value="<?php echo $result->PRO_ID; ?>" readonly />
                        </div>
                    </div>
                    <div class="control-group field-produto">
                        <label for="codDeBarra" class="control-label">Código de Barra</label>
                        <div class="controls">
                            <div class="input-group" style="display: flex; gap: 5px;">
                            <input id="codDeBarra" type="text" name="codDeBarra" value="<?php echo $result->PRO_COD_BARRA; ?>" />
                                <button type="button" class="btn btn-info" id="btnGerarCodigo" style="border-radius: 4px;">
                                    <i class="fas fa-barcode"></i> Gerar
                                </button>
                            </div>
                            <span id="codigoBarraStatus" class="help-inline"></span>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="descricao" class="control-label">Descrição<span class="required">*</span></label>
                        <div class="controls">
                            <input id="descricao" type="text" name="descricao" value="<?php echo $result->PRO_DESCRICAO; ?>" />
                        </div>
                    </div>
                    <div class="control-group field-produto">
                        <label for="PRO_NCM" class="control-label">NCM<span class="required">*</span></label>
                        <div class="controls">
                            <div class="input-group" style="display: flex; gap: 5px;">
                                <input id="PRO_NCM" class="form-control" type="text" name="PRO_NCM" 
                                    value="<?php echo (isset($result->PRO_NCM) && $result->PRO_NCM !== null && $result->PRO_NCM !== '') ? htmlspecialchars($result->PRO_NCM) : ''; ?>" 
                                    readonly />
                                <button type="button" class="btn btn-success" id="btnBuscarNcm" style="border-radius: 4px;" data-toggle="modal" data-target="#modalNcm"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-warning" id="btnDescricaoNcm" style="border-radius: 4px;" title="Nomenclatura Comum do Mercosul - Código de 8 dígitos que classifica produtos para fins fiscais e aduaneiros"><i class="fas fa-info-circle"></i></button>
                            </div>
                            <input id="NCM_ID" class="form-control" type="hidden" name="NCM_ID" 
                                value="<?php echo (isset($result->NCM_ID) && $result->NCM_ID !== null && $result->NCM_ID !== '') ? htmlspecialchars($result->NCM_ID) : ''; ?>" />
                        </div>
                    </div>
                    <?php
                        $finalidadeSelecionada = $result->PRO_FINALIDADE ?? 'Comercialização';
                        // Normalizar valores antigos
                        if ($finalidadeSelecionada === 'COMERCIALIZACAO') {
                            $finalidadeSelecionada = 'Comercialização';
                        }
                    ?>
                    <div class="control-group field-produto">
                        <label for="PRO_FINALIDADE" class="control-label">Finalidade<span class="required">*</span></label>
                        <div class="controls">
                            <select id="PRO_FINALIDADE" name="PRO_FINALIDADE">
                                <?php foreach ($finalidadesProduto as $valor => $rotulo) : ?>
                                    <option value="<?php echo $valor; ?>" <?php echo $finalidadeSelecionada === $valor ? 'selected' : ''; ?>>
                                        <?php echo $rotulo; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline">Indique se o item é para revenda, consumo ou ativo.</span>
                        </div>
                    </div>
                    <div class="control-group field-servico" style="display: none;">
                        <label for="PRO_UNID_MEDIDA" class="control-label">Unidade<span class="required">*</span></label>
                        <div class="controls">
                            <select id="PRO_UNID_MEDIDA" name="PRO_UNID_MEDIDA"></select>
                        </div>
                    </div>
                    <div class="control-group field-servico" style="display: none;">
                        <label for="PRO_CCLASS_SERV" class="control-label">cClass (Serviço)</label>
                        <div class="controls">
                            <div class="input-group" style="display: flex; gap: 5px;">
                                <input id="PRO_CCLASS_SERV" class="form-control" type="text" name="PRO_CCLASS_SERV"
                                    value="<?php echo $result->PRO_CCLASS_SERV; ?>" maxlength="7" />
                                <button type="button" class="btn btn-success" id="btnBuscarCClass"
                                    style="border-radius: 4px;" data-toggle="modal" data-target="#modalCClass"><i
                                        class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-warning" id="btnDescricaoCClass"
                                    style="border-radius: 4px;" title="Código de Classificação de Serviços - Código de 7 dígitos usado para classificar serviços de telecomunicações"><i class="fas fa-info-circle"></i></button>
                            </div>
                        </div>
                    </div>
                    <!-- Preço de Venda para Serviços (aparece na coluna esquerda) -->
                    <div class="control-group field-servico" style="display: none;">
                        <label for="precoVenda" class="control-label">Preço Serviço<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoVenda" class="preco-simples" type="text" name="precoVenda" value="<?php echo $result->PRO_PRECO_VENDA; ?>" placeholder="0,00" />
                            <!-- Campo hidden para garantir que o valor seja enviado mesmo se o campo estiver oculto -->
                            <input type="hidden" name="precoVenda_servico" id="precoVenda_servico" value="<?php echo $result->PRO_PRECO_VENDA; ?>" />
                        </div>
                    </div>
                            <div class="control-group field-produto">
                                <label for="unidade" class="control-label">Unidade<span class="required">*</span></label>
                                <div class="controls">
                                    <select id="unidade" name="unidade" style="width: 15em;"></select>
                                </div>
                        </div>
                    </div>

                        <div class="span6">
                            <!-- Coluna 2: Preços e Estoque -->
                    <div class="control-group field-produto">
                        <label for="precoCompra" class="control-label">Preço de Compra<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoCompra" class="preco-simples" type="text" name="precoCompra" value="<?php echo $result->PRO_PRECO_COMPRA; ?>" placeholder="0,00" />
                            <strong><span style="color: red" id="errorAlert"></span><strong>
                        </div>
                    </div>
                    <div class="control-group field-produto">
                        <label for="Lucro" class="control-label">Lucro</label>
                        <div class="controls">
                            <select id="selectLucro" name="selectLucro" style="width: 10.5em;">
                              <option value="markup">Markup</option>
                            </select>
                            <input style="width: 4em;" id="Lucro" name="Lucro" type="text" placeholder="%" maxlength="3" size="2" />
                            <i class="icon-info-sign tip-left" title="Markup: Porcentagem aplicada ao valor de compra | Margem de Lucro: Porcentagem aplicada ao valor de venda"></i>
                        </div>
                    </div>
                    <!-- Preço de Venda para Produtos (aparece na coluna direita) -->
                    <div class="control-group field-produto">
                        <label for="precoVenda_produto" class="control-label">Preço de Venda<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoVenda_produto" class="preco-simples" type="text" name="precoVenda" value="<?php echo $result->PRO_PRECO_VENDA; ?>" placeholder="0,00" />
                        </div>
                    </div>
                            <div class="control-group field-produto">
                                <label for="estoqueMinimo" class="control-label">Estoque Mínimo</label>
                                <div class="controls">
                                    <input id="estoqueMinimo" type="number" name="estoqueMinimo" value="<?php echo $result->PRO_ESTOQUE_MINIMO; ?>" />
                                </div>
                            </div>
                    <div class="control-group field-produto">
                        <label for="PRO_ORIGEM" class="control-label">Origem do Produto<span class="required">*</span></label>
                        <div class="controls">
                            <select id="PRO_ORIGEM" name="PRO_ORIGEM">
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
                            <span class="help-inline">Selecione a origem do produto conforme a tabela de códigos</span>
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
                            <div class="control-group field-produto">
                                <label for="peso_bruto" class="control-label">Peso Bruto (kg)</label>
                                <div class="controls">
                                    <input id="peso_bruto" type="text" name="peso_bruto" value="<?php echo $result->PRO_PESO_BRUTO; ?>" class="decimal" style="width: 100px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="peso_liquido" class="control-label">Peso Líquido (kg)</label>
                                <div class="controls">
                                    <input id="peso_liquido" type="text" name="peso_liquido" value="<?php echo $result->PRO_PESO_LIQUIDO; ?>" class="decimal" style="width: 100px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group field-produto">
                                <label for="largura" class="control-label">Largura (cm)</label>
                                <div class="controls">
                                    <input id="largura" type="text" name="largura" value="<?php echo $result->PRO_LARGURA; ?>" class="decimal" style="width: 80px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="altura" class="control-label">Altura (cm)</label>
                                <div class="controls">
                                    <input id="altura" type="text" name="altura" value="<?php echo $result->PRO_ALTURA; ?>" class="decimal" style="width: 80px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                    <div class="control-group">
                                <label for="comprimento" class="control-label">Comprimento (cm)</label>
                        <div class="controls">
                                    <input id="comprimento" type="text" name="comprimento" value="<?php echo $result->PRO_COMPRIMENTO; ?>" class="decimal" style="width: 80px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-primary" style="max-width: 160px">
                                  <span class="button__icon"><i class="bx bx-sync"></i></span><span class="button__text2">Atualizar</span></button>
                                <a href="<?php echo base_url() ?>index.php/produtos" id="" class="button btn btn-mini btn-warning">
                                  <span class="button__icon"><i class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal de Pesquisa NCM -->
<div class="modal fade" id="modalNcm" tabindex="-1" role="dialog" aria-labelledby="modalNcmLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modalNcmDialog">
        <div class="modal-content" id="modalNcmContent">
            <div class="modal-header" id="modalNcmHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalNcmLabel"><i class="fas fa-search"></i> Pesquisar NCM</h4>
            </div>
            <div class="modal-body" id="modalNcmBody">
                <div class="row-fluid" id="modalNcmSearchRow">
                    <div class="span12" id="modalNcmSearchCol">
                        <div class="control-group" id="modalNcmSearchGroup">
                            <div class="controls" id="modalNcmSearchControls">
                                <div class="input-group" id="modalNcmSearchInputGroup">
                                    <input type="text" id="pesquisaNcm" class="form-control" placeholder="Digite o código ou descrição do NCM" style="width: 100%; height: 34px;">
                                    <button type="button" class="btn btn-info" id="btnPesquisarNcm" style="height: 34px;">
                                        <i class="fas fa-search"></i> Pesquisar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid" id="modalNcmTableRow">
                    <div class="span12" id="modalNcmTableCol">
                        <div class="table-responsive" id="modalNcmTableContainer" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-striped" id="tabelaNcm">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Código</th>
                                        <th style="width: 70%;">Descrição</th>
                                        <th style="width: 15%;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="row-fluid" style="margin-top: 15px;">
                            <div class="span6">
                                <div id="totalResultados" style="font-weight: bold; color: #666; padding: 8px 0;"></div>
                            </div>
                            <div class="span6 text-right">
                                <div class="btn-group" id="paginacaoNcm" style="display: none;">
                                    <button type="button" class="btn btn-default btn-sm" id="btnAnteriorNcm" disabled>
                                        <i class="fas fa-chevron-left"></i> Anterior
                                    </button>
                                    <button type="button" class="btn btn-default btn-sm" id="btnProximoNcm">
                                        Próximo <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modalNcmFooter">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pesquisa cClass -->
<div class="modal fade" id="modalCClass" tabindex="-1" role="dialog" aria-labelledby="modalCClassLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modalCClassDialog">
        <div class="modal-content" id="modalCClassContent">
            <div class="modal-header" id="modalCClassHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalCClassLabel"><i class="fas fa-search"></i> Pesquisar cClass</h4>
            </div>
            <div class="modal-body" id="modalCClassBody">
                <div class="row-fluid" id="modalCClassSearchRow">
                    <div class="span12" id="modalCClassSearchCol">
                        <div class="control-group" id="modalCClassSearchGroup">
                            <div class="controls" id="modalCClassSearchControls">
                                <div class="input-group" id="modalCClassSearchInputGroup">
                                    <input type="text" id="pesquisaCClass" class="form-control"
                                        placeholder="Digite o código ou descrição do cClass"
                                        style="width: 100%; height: 34px;">
                                    <button type="button" class="btn btn-info" id="btnPesquisarCClass"
                                        style="height: 34px;">
                                        <i class="fas fa-search"></i> Pesquisar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid" id="modalCClassTableRow">
                    <div class="span12" id="modalCClassTableCol">
                        <div class="table-responsive" id="modalCClassTableContainer"
                            style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-bordered table-striped" id="tabelaCClass">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="3" class="text-center">Digite algo para pesquisar</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="totalResultadosCClass" class="text-right" style="margin-top: 10px;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="modalCClassFooter">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script type="text/javascript">
    function calcLucro(precoCompra, Lucro) {
        var lucroTipo = $('#selectLucro').val();
        var precoVenda;
        
        if (lucroTipo === 'markup') {
            precoVenda = (precoCompra * (1 + Lucro / 100)).toFixed(2);
        } else if (lucroTipo === 'margemLucro') {
            precoVenda = (precoCompra / (1 - (Lucro / 100))).toFixed(2);
        }
        
        return precoVenda;
    }
    
    function atualizarPrecoVenda() {
        var precoCompra = Number($("#precoCompra").val());
        var lucro = Number($("#Lucro").val());
        
        if (precoCompra > 0 && lucro >= 0) {
            $('#precoVenda').val(calcLucro(precoCompra, lucro));
        }
    }
    
    $("#precoCompra, #Lucro, #selectLucro").on('input change', atualizarPrecoVenda);

    $("#precoCompra, #Lucro").on('input change', function() {
        if ($("#precoCompra").val() == '0.00' && $('#precoVenda').val() != '') {
            $('#errorAlert').text('Você não pode preencher valor de compra e depois apagar.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
            $("#precoCompra").focus();
        } else if ($("#precoCompra").val() != '' && $("#Lucro").val() != '') {
            atualizarPrecoVenda();
        }
    });

    $("#Lucro").keyup(function() {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#precoCompra").val() == null || $("#precoCompra").val() == '') {
            $('#errorAlert').text('Preencher valor da compra primeiro.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
            $("#precoCompra").focus();

        } else if (Number($("#Lucro").val()) >= 0) {
            $('#precoVenda').val(calcLucro(Number($("#precoCompra").val()), Number($("#Lucro").val())));
        } else {
            $('#errorAlert').text('Não é permitido número negativo.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#precoVenda').val('');
        }
    });

    $('#precoVenda').focusout(function () {
        if (Number($('#precoVenda').val()) < Number($("#precoCompra").val())) {
            $('#errorAlert').text('Preço de venda não pode ser menor que o preço de compra.').css("display", "inline").fadeOut(6000);
            $('#precoVenda').val('');
        }
    });

    $(document).ready(function() {
        // Debug: Verificar valores iniciais do NCM antes de qualquer manipulação
        console.log('=== DEBUG NCM - INÍCIO ===');
        console.log('PRO_NCM (PHP):', '<?php echo isset($result->PRO_NCM) ? $result->PRO_NCM : "VAZIO"; ?>');
        console.log('NCM_ID (PHP):', '<?php echo isset($result->NCM_ID) ? $result->NCM_ID : "VAZIO"; ?>');
        console.log('PRO_NCM (jQuery):', $('#PRO_NCM').val());
        console.log('NCM_ID (jQuery):', $('#NCM_ID').val());
        console.log('PRO_TIPO:', $('#PRO_TIPO').val());
        console.log('=== FIM DEBUG ===');
        
        // Limpar valores "undefined" que possam ter sido inseridos incorretamente
        if ($('#PRO_NCM').val() === 'undefined' || $('#PRO_NCM').val() === 'null') {
            console.log('Limpando PRO_NCM com valor inválido');
            $('#PRO_NCM').val('');
        }
        if ($('#NCM_ID').val() === 'undefined' || $('#NCM_ID').val() === 'null') {
            console.log('Limpando NCM_ID com valor inválido');
            $('#NCM_ID').val('');
        }
        
        // Adiciona método personalizado para validar números decimais
        $.validator.addMethod("decimal", function(value, element) {
            if (this.optional(element)) {
                return true;
            }
            // Remove pontos de milhar e substitui vírgula por ponto
            value = value.replace(/\./g, '').replace(',', '.');
            // Verifica se é um número válido e positivo
            return !isNaN(parseFloat(value)) && isFinite(value) && parseFloat(value) >= 0;
        }, "Por favor, insira um número decimal válido.");

        $(".money").maskMoney();
        
        // Configuração dos campos monetários
        $(".money").maskMoney({
            thousands: '.',
            decimal: ',',
            allowZero: true,
            precision: 2,
            allowNegative: false,
            suffix: '',
            clearIfNotMatch: true,
            selectAllOnFocus: true,
            formatOnBlur: true,
            insertPlusSign: false,
            insertMinusSign: false,
            allowEmpty: true
        });

        // Configuração dos campos decimais
        $(".decimal").maskMoney({
            thousands: '.',
            decimal: ',',
            allowZero: true,
            precision: 3,
            allowNegative: false,
            suffix: '',
            clearIfNotMatch: true,
            selectAllOnFocus: true,
            formatOnBlur: true,
            insertPlusSign: false,
            insertMinusSign: false,
            allowEmpty: true
        });

        // Permitir digitação livre nos campos monetários
        $('.money').off('keypress').on('keypress', function(e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            var value = $(this).val();
            
            // Permite números, vírgula e backspace
            if (charCode === 44 || // vírgula
                charCode === 8 || // backspace
                (charCode >= 48 && charCode <= 57)) { // números
                return true;
            }
            return false;
        });

        // Permitir digitação livre nos campos decimais
        $('.decimal').off('keypress').on('keypress', function(e) {
            var charCode = (e.which) ? e.which : e.keyCode;
            var value = $(this).val();
            
            // Permite números, vírgula e backspace
            if (charCode === 44 || // vírgula
                charCode === 8 || // backspace
                (charCode >= 48 && charCode <= 57)) { // números
                return true;
            }
            return false;
        });

        // Selecionar todo o conteúdo do campo quando focar
        $('.money, .decimal').on('focus click', function() {
            $(this).select();
        });

        // Carregar valores existentes (sem formatação automática)
        $('.decimal').each(function() {
            var value = $(this).val();
            if (value) {
                // Mantém o valor original, apenas converte ponto para vírgula se necessário
                value = value.replace('.', ',');
                $(this).val(value);
            }
        });

        // Melhorar a usabilidade dos campos decimais
        $('.decimal').on('focus', function() {
            $(this).select();
        }).on('click', function() {
            $(this).select();
        }).on('blur', function() {
            var value = $(this).val();
            if (value === '') {
                $(this).val('');
            } else {
                // Se não tiver vírgula, adiciona ,000
                if (value.indexOf(',') === -1) {
                    $(this).val(value + ',000');
                }
            }
        });

        // Carregar unidades para produtos (campo unidade antigo)
        function carregarUnidadesCampoAntigo() {
            var select = $('#unidade');
            $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function(data) {
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

        // Carregar unidades iniciais para o campo antigo
        carregarUnidadesCampoAntigo();

        // Validação dos campos decimais
        $('#formProduto').validate({
            rules: {
                descricao: {
                    required: true
                },
                unidade: {
                    required: true
                },
                PRO_FINALIDADE: {
                    required: true
                },
                precoCompra: {
                    required: true
                },
                precoVenda: {
                    required: true
                },
                estoque: {
                    required: true
                },
                peso_bruto: {
                    decimal: true
                },
                peso_liquido: {
                    decimal: true
                },
                largura: {
                    decimal: true
                },
                altura: {
                    decimal: true
                },
                comprimento: {
                    decimal: true
                }
            },
            messages: {
                descricao: {
                    required: 'Campo Requerido.'
                },
                unidade: {
                    required: 'Campo Requerido.'
                },
                PRO_FINALIDADE: {
                    required: 'Selecione a finalidade do produto.'
                },
                precoCompra: {
                    required: 'Campo Requerido.'
                },
                precoVenda: {
                    required: 'Campo Requerido.'
                },
                estoque: {
                    required: 'Campo Requerido.'
                },
                peso_bruto: {
                    decimal: "O campo Peso Bruto deve conter um número decimal válido"
                },
                peso_liquido: {
                    decimal: "O campo Peso Líquido deve conter um número decimal válido"
                },
                largura: {
                    decimal: "O campo Largura deve conter um número decimal válido"
                },
                altura: {
                    decimal: "O campo Altura deve conter um número decimal válido"
                },
                comprimento: {
                    decimal: "O campo Comprimento deve conter um número decimal válido"
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

        // Converter valores para formato decimal antes do envio
        $('#formProduto').on('submit', function() {
            $('.decimal').each(function() {
                var value = $(this).val();
                if (value) {
                    // Remove pontos de milhar
                    value = value.replace(/\./g, '');
                    // Se não tiver vírgula, adiciona ,000
                    if (value.indexOf(',') === -1) {
                        value = value + ',000';
                    }
                    // Substitui vírgula por ponto para o banco de dados
                    value = value.replace(',', '.');
                    $(this).val(value);
                }
            });
        });

        // Função para validar código de barra
        function validarCodigoBarra() {
            // Não validar se for serviço
            if ($('#PRO_TIPO').val() == '2') {
                $('#codigoBarraStatus').html('');
                return;
            }

            var codigo = $('#codDeBarra').val();
            if (!codigo) {
                $('#codigoBarraStatus').html('');
                return;
            }
            
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/produtos/validarCodigoBarraAjax',
                type: 'POST',
                data: {codigo: codigo},
                dataType: 'json',
                success: function(response) {
                    if (response.valido) {
                        $('#codigoBarraStatus').html('<span style="color: green;"><i class="fas fa-check"></i> Código válido</span>');
                    } else {
                        $('#codigoBarraStatus').html('<span style="color: red;"><i class="fas fa-times"></i> Código inválido</span>');
                    }
                }
            });
        }

        // Validar ao digitar
        $('#codDeBarra').on('input', function() {
            validarCodigoBarra();
        });

        // Gerar código de barra
        $('#btnGerarCodigo').click(function() {
            // Não gerar se for serviço
            if ($('#PRO_TIPO').val() == '2') {
                return;
            }

            var id = $('#codigo').val();
            if (id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/produtos/gerarCodigoBarraAjax',
                    type: 'GET',
                    data: {id: id},
                    dataType: 'json',
                    success: function(response) {
                        if (response.codigo) {
                            $('#codDeBarra').val(response.codigo);
                            validarCodigoBarra();
                        }
                    }
                });
            }
        });

        // Validar código inicial apenas se for produto
        if ($('#PRO_TIPO').val() != '2') {
            validarCodigoBarra();
        }

        // Função para pesquisar cClass
        function pesquisarCClass(termo) {
            if (!termo || termo.length < 2) {
                $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center">Digite pelo menos 2 caracteres</td></tr>');
                $('#totalResultadosCClass').text('');
                return;
            }

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/produtos/pesquisarCClass',
                type: 'POST',
                data: { termo: termo },
                dataType: 'json',
                success: function (response) {
                    var tbody = $('#tabelaCClass tbody');
                    tbody.empty();

                    if (response.length > 0) {
                        $.each(response, function (i, cclass) {
                            tbody.append(
                                '<tr>' +
                                '<td style="padding: 12px;">' + cclass.codigo + '</td>' +
                                '<td style="padding: 12px;">' + cclass.descricao + '</td>' +
                                '<td style="padding: 12px; text-align: center;">' +
                                '<button type="button" class="btn btn-success btn-sm selecionarCClass" ' +
                                'data-codigo="' + cclass.codigo + '" ' +
                                'data-descricao="' + cclass.descricao + '">Selecionar</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });
                        $('#totalResultadosCClass').text('Total: ' + response.length + ' resultado(s)');
                    } else {
                        tbody.append('<tr><td colspan="3" class="text-center">Nenhum cClass encontrado</td></tr>');
                        $('#totalResultadosCClass').text('');
                    }
                },
                error: function () {
                    $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center text-danger">Erro na busca</td></tr>');
                    $('#totalResultadosCClass').text('');
                }
            });
        }

        // Evento de digitação no campo de pesquisa cClass
        $('#pesquisaCClass').on('input', function () {
            var termo = $(this).val();
            if (termo.length >= 2) {
                pesquisarCClass(termo);
            } else if (termo.length === 0) {
                $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center">Digite algo para pesquisar</td></tr>');
                $('#totalResultadosCClass').text('');
            }
        });

        // Evento de clique no botão pesquisar cClass
        $('#btnPesquisarCClass').on('click', function () {
            pesquisarCClass($('#pesquisaCClass').val());
        });

        // Evento de seleção do cClass
        $(document).on('click', '.selecionarCClass', function () {
            var codigo = $(this).data('codigo');
            var descricao = $(this).data('descricao');

            $('#PRO_CCLASS_SERV').val(codigo);
            $('#modalCClass').modal('hide');
        });

        // Função para carregar unidades de produto
        function carregarUnidadesProduto() {
            var select = $('#PRO_UNID_MEDIDA');
            $.getJSON('<?php echo base_url() ?>assets/json/tabela_medidas.json', function (data) {
                select.empty();
                select.append('<option value="">Selecione</option>');
                $.each(data.medidas, function (i, medida) {
                    select.append($('<option></option>').val(medida.sigla).text(medida.descricao));
                });
                // Selecionar a unidade atual se existir
                if ('<?php echo $result->PRO_UNID_MEDIDA; ?>') {
                    select.val('<?php echo $result->PRO_UNID_MEDIDA; ?>');
                }
            });
        }

        // Função para carregar unidades de serviço
        function carregarUnidadesServico() {
            var select = $('#PRO_UNID_MEDIDA');
            $.getJSON('<?php echo base_url() ?>assets/json/unidades_servico.json', function (data) {
                select.empty();
                select.append('<option value="">Selecione</option>');
                $.each(data.unidades_servico, function (i, unidade) {
                    var texto = unidade.codigo + ' - ' + unidade.descricao;
                    select.append($('<option></option>').val(unidade.valor).text(texto));
                });
                // Selecionar a unidade atual se existir, senão usar Minuto como padrão
                if ('<?php echo $result->PRO_UNID_MEDIDA; ?>') {
                    select.val('<?php echo $result->PRO_UNID_MEDIDA; ?>');
                } else {
                    select.val('1'); // Minuto como padrão para serviços
                }
            });
        }

        // Carregar unidades iniciais baseadas no tipo atual
        if ($('#PRO_TIPO').val() == '2') {
            carregarUnidadesServico();
        } else {
            carregarUnidadesProduto();
            carregarUnidadesCampoAntigo();
        }

        // Variável para controlar se é o primeiro carregamento
        var primeiroCarregamento = true;
        
        function toggleFields(manualChange) {
            var isService = $('#PRO_TIPO_TOGGLE').is(':checked');

            console.log('toggleFields chamado - isService:', isService, 'manualChange:', manualChange);

            if (isService) { // Serviço
                $('.field-produto').hide();
                $('.field-servico').show();
                $('#PRO_TIPO_TOGGLE').prop('checked', true);
                $('#tipo_label').text('Serviço');
                $('#PRO_FINALIDADE option[value="Serviço"]').show();
                $('#PRO_FINALIDADE').val('Serviço').prop('disabled', true);

                // Alterar label do código para "Código do Serviço"
                $('#codigo-label').text('Código do Serviço');

                // Sincronizar preço: copiar valor do campo de produto para o campo de serviço se necessário
                var precoProduto = $('#precoVenda_produto').val();
                if (precoProduto && !$('#precoVenda').val()) {
                    $('#precoVenda').val(precoProduto);
                }
                // Remover name do campo de produto para evitar conflito
                $('#precoVenda_produto').removeAttr('name');
                // Garantir que o campo de serviço tenha o name
                $('#precoVenda').attr('name', 'precoVenda');
                // Sincronizar campo hidden
                $('#precoVenda_servico').val($('#precoVenda').val());

                // Auto-set NCM for Service (apenas se for mudança manual)
                if (manualChange) {
                    $('#PRO_NCM').val('00000000');
                    $('#NCM_ID').val('15142');
                    console.log('NCM definido para serviço: 00000000');
                }

                // Definir valores padrão para campos obrigatórios de produto que não se aplicam a serviço
                if (manualChange) {
                    $('#precoCompra, #estoque, #estoqueMinimo, #codDeBarra').val('');
                    $('#PRO_ORIGEM').val('0');
                    $('#peso_bruto, #peso_liquido, #largura, #altura, #comprimento').val('0.000');
                }

                // Carregar unidades de serviço
                carregarUnidadesServico();

                // Preencher cClass automaticamente com primeiro código disponível
                if (!$('#PRO_CCLASS_SERV').val()) {
                    $('#PRO_CCLASS_SERV').val('0100101'); // Primeiro código disponível
                }

            } else { // Produto
                $('.field-produto').show();
                $('.field-servico').hide();
                $('#PRO_TIPO_TOGGLE').prop('checked', false);
                $('#tipo_label').text('Produto');
                $('#PRO_FINALIDADE').prop('disabled', false);
                $('#PRO_FINALIDADE option[value="Serviço"]').hide();
                if ($('#PRO_FINALIDADE').val() === 'Serviço') {
                    $('#PRO_FINALIDADE').val('Comercialização');
                }

                // Alterar label do código de volta para "Código do Produto"
                $('#codigo-label').text('Código do Produto');

                // Sincronizar preço: copiar valor do campo de serviço para o campo de produto se necessário
                var precoServico = $('#precoVenda').val();
                if (precoServico && !$('#precoVenda_produto').val()) {
                    $('#precoVenda_produto').val(precoServico);
                }
                // Remover name do campo de serviço para evitar conflito
                $('#precoVenda').removeAttr('name');
                // Garantir que o campo de produto tenha o name
                $('#precoVenda_produto').attr('name', 'precoVenda');

                // Clear NCM apenas se for mudança manual e o NCM for o padrão de serviço
                if (manualChange && $('#PRO_NCM').val() === '00000000') {
                    console.log('Limpando NCM de serviço ao mudar para produto');
                    $('#PRO_NCM').val('');
                    $('#NCM_ID').val('');
                }

                // Limpar cClass quando voltar para produto
                $('#PRO_CCLASS_SERV').val('');

                // Carregar unidades de produto
                carregarUnidadesProduto();
                carregarUnidadesCampoAntigo();
            }

            // Garantir que todos os campos estejam sempre habilitados após alternar tipo
            $('input, select, textarea').prop('disabled', false).prop('readonly', false);
            // Manter apenas o campo código como readonly
            $('#codigo').prop('readonly', true);
        }

        // Sincronizar valores de precoVenda quando o usuário digitar
        $('#precoVenda, #precoVenda_produto').on('input change', function() {
            var valor = $(this).val();
            // Sincronizar com o outro campo
            if ($(this).attr('id') === 'precoVenda') {
                $('#precoVenda_produto').val(valor);
                $('#precoVenda_servico').val(valor);
            } else {
                $('#precoVenda').val(valor);
                $('#precoVenda_servico').val(valor);
            }
        });

        // Evento do toggle switch
        $('#PRO_TIPO_TOGGLE').change(function () {
            if ($(this).is(':checked')) {
                $('#PRO_TIPO').val('2');
            } else {
                $('#PRO_TIPO').val('1');
            }
            toggleFields(true); // true = mudança manual do usuário
        });

        // Executa a função no carregamento da página baseado no valor atual
        if ($('#PRO_TIPO').val() == '2') {
            $('#PRO_TIPO_TOGGLE').prop('checked', true);
        }
        
        // Debug: mostrar valores do NCM ao carregar
        console.log('NCM ao carregar:', $('#PRO_NCM').val());
        console.log('NCM_ID ao carregar:', $('#NCM_ID').val());
        
        toggleFields(false); // false = carregamento inicial, não limpar valores
        
        // Debug: verificar valores após toggleFields
        console.log('NCM após toggleFields:', $('#PRO_NCM').val());
        console.log('NCM_ID após toggleFields:', $('#NCM_ID').val());
        
        // Marcar que o primeiro carregamento foi concluído
        primeiroCarregamento = false;

        // Variáveis para controle de paginação NCM
        var paginaAtualNcm = 1;
        var totalPaginasNcm = 1;
        var termoPesquisaAtual = '';

        // Função para pesquisar NCM no modal com paginação
        function pesquisarNcmModal(termo, pagina) {
            pagina = pagina || 1;
            var data = { pagina: pagina, limite: 25 };

            // Só adiciona termo se ele não estiver vazio ou undefined
            if (termo && typeof termo === 'string' && termo.trim().length > 0) {
                data.termo = termo.trim();
            }

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/ncms/buscar',
                type: 'GET',
                data: data,
                dataType: 'json',
                success: function(response) {
                    var tbody = $('#tabelaNcm tbody');
                    tbody.empty();

                    // Verifica se a resposta tem a estrutura com 'resultados' (sem termo) ou é direta (com termo)
                    var ncms = response.resultados || response;
                    var totalRegistros = response.total_registros || (Array.isArray(response) ? response.length : 0);
                    var totalPaginas = response.total_paginas || Math.ceil(totalRegistros / 25);

                    if (ncms && ncms.length > 0) {
                        $.each(ncms, function(i, ncm) {
                            var codigo = ncm.NCM_CODIGO || ncm.codigo || ncm.ncm_codigo || '';
                            var descricao = ncm.NCM_DESCRICAO || ncm.descricao || ncm.ncm_descricao || '';
                            var id = ncm.NCM_ID || ncm.id || ncm.ncm_id || '';

                            tbody.append(
                                '<tr>' +
                                '<td style="padding: 12px;">' + codigo + '</td>' +
                                '<td style="padding: 12px;">' + descricao + '</td>' +
                                '<td style="padding: 12px; text-align: center;">' +
                                '<button type="button" class="btn btn-success btn-sm selecionarNcm" ' +
                                'data-codigo="' + codigo + '" ' +
                                'data-id="' + id + '" ' +
                                'data-descricao="' + descricao + '">Selecionar</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });

                        // Atualizar controles de paginação
                        totalPaginasNcm = totalPaginas;
                        paginaAtualNcm = pagina;

                        if (totalPaginasNcm > 1) {
                            $('#paginacaoNcm').show();
                            $('#btnAnteriorNcm').prop('disabled', paginaAtualNcm <= 1);
                            $('#btnProximoNcm').prop('disabled', paginaAtualNcm >= totalPaginasNcm);
                        } else {
                            $('#paginacaoNcm').hide();
                        }

                        $('#totalResultados').html('<i class="fas fa-info-circle"></i> ' + totalRegistros + ' resultado(s) encontrado(s) - Página ' + paginaAtualNcm + ' de ' + totalPaginasNcm);
                    } else {
                        tbody.append('<tr><td colspan="3" class="text-center">Nenhum NCM encontrado</td></tr>');
                        $('#totalResultados').text('');
                        $('#paginacaoNcm').hide();
                    }
                },
                error: function() {
                    $('#tabelaNcm tbody').html('<tr><td colspan="3" class="text-center text-danger">Erro na busca</td></tr>');
                    $('#totalResultados').text('');
                    $('#paginacaoNcm').hide();
                }
            });
        }

        // Função para carregar os primeiros 25 NCMs automaticamente
        function carregarPrimeirosNcms() {
            termoPesquisaAtual = '';
            pesquisarNcmModal('', 1);
        }

        // Evento de digitação no campo de pesquisa NCM
        $('#pesquisaNcm').on('input', function() {
            var termo = $(this).val();
            termoPesquisaAtual = termo;
            if (termo.length >= 2) {
                pesquisarNcmModal(termo, 1);
            } else if (termo.length === 0) {
                carregarPrimeirosNcms();
            }
        });

        // Evento de clique no botão pesquisar NCM
        $('#btnPesquisarNcm').on('click', function() {
            termoPesquisaAtual = $('#pesquisaNcm').val();
            pesquisarNcmModal(termoPesquisaAtual, 1);
        });

        // Eventos de paginação
        $('#btnAnteriorNcm').on('click', function () {
            if (paginaAtualNcm > 1) {
                pesquisarNcmModal(termoPesquisaAtual, paginaAtualNcm - 1);
            }
        });

        $('#btnProximoNcm').on('click', function () {
            if (paginaAtualNcm < totalPaginasNcm) {
                pesquisarNcmModal(termoPesquisaAtual, paginaAtualNcm + 1);
            }
        });

        // Evento de seleção do NCM
        $(document).on('click', '.selecionarNcm', function() {
            var codigo = $(this).data('codigo');
            var id = $(this).data('id');
            var descricao = $(this).data('descricao');

            $('#PRO_NCM').val(codigo);
            $('#NCM_ID').val(id);
            $('#btnDescricaoNcm').data('descricao', descricao);
            $('#modalNcm').modal('hide');
        });

        $('#modalNcm').on('hidden.bs.modal', function() {
            console.log('Modal NCM fechado - liberando campos');

            // Garantir que os campos do formulário estejam liberados após o modal fechar
            $('input, select, textarea').prop('disabled', false).prop('readonly', false);

            // Garantir especificamente que os campos de preço e estoque estejam liberados
            $('#precoCompra, #precoVenda, #estoque, #estoqueMinimo, #PRO_ORIGEM').prop('disabled', false).prop('readonly', false);

            // Verificar se é produto ou serviço
            var isService = $('#PRO_TIPO_TOGGLE').is(':checked');
            console.log('Tipo atual:', isService ? 'Serviço' : 'Produto');

            // Reaplicar apenas a visibilidade dos campos baseada no tipo (sem afetar disabled/readonly)
            if (isService) {
                $('.field-produto').hide();
                $('.field-servico').show();
            } else {
                $('.field-produto').show();
                $('.field-servico').hide();
            }

            // Manter apenas o campo código como readonly
            $('#codigo').prop('readonly', true);

            // Forçar liberação específica dos campos de produto após um pequeno delay
            setTimeout(function() {
                if (!isService) {
                    console.log('Garantindo que campos de produto estejam liberados');
                    console.log('Antes da liberação - precoCompra disabled:', $('#precoCompra').prop('disabled'), 'readonly:', $('#precoCompra').prop('readonly'));
                    console.log('Antes da liberação - precoVenda disabled:', $('#precoVenda').prop('disabled'), 'readonly:', $('#precoVenda').prop('readonly'));

                    $('#precoCompra, #precoVenda, #estoque, #estoqueMinimo, #PRO_ORIGEM').prop('disabled', false).prop('readonly', false);
                    $('#precoCompra, #precoVenda, #estoque, #estoqueMinimo, #PRO_ORIGEM').removeAttr('disabled');

                    console.log('Após liberação - precoCompra disabled:', $('#precoCompra').prop('disabled'), 'readonly:', $('#precoCompra').prop('readonly'));
                    console.log('Após liberação - precoVenda disabled:', $('#precoVenda').prop('disabled'), 'readonly:', $('#precoVenda').prop('readonly'));

                    // Reaplicar as funcionalidades dos campos após liberá-los
                    $('.preco-simples').off('input').on('input', function() {
                        var value = $(this).val();
                        var cleanValue = value.replace(/[^0-9,]/g, '');
                        if (value !== cleanValue) {
                            $(this).val(cleanValue);
                        }
                    });

                    $('.preco-simples').off('focus click').on('focus click', function() {
                        $(this).select();
                    });
                }
            }, 100);

            // Verificar novamente após um delay maior
            setTimeout(function() {
                if (!isService) {
                    console.log('Verificação final - precoCompra disabled:', $('#precoCompra').prop('disabled'), 'readonly:', $('#precoCompra').prop('readonly'));
                    console.log('Verificação final - precoVenda disabled:', $('#precoVenda').prop('disabled'), 'readonly:', $('#precoVenda').prop('readonly'));

                    // Forçar liberação uma segunda vez se necessário
                    if ($('#precoCompra').prop('disabled') || $('#precoCompra').prop('readonly')) {
                        console.log('Forçando liberação novamente para precoCompra');
                        $('#precoCompra').prop('disabled', false).prop('readonly', false).removeAttr('disabled');
                    }
                    if ($('#precoVenda').prop('disabled') || $('#precoVenda').prop('readonly')) {
                        console.log('Forçando liberação novamente para precoVenda');
                        $('#precoVenda').prop('disabled', false).prop('readonly', false).removeAttr('disabled');
                    }
                }
            }, 500);
        });

        // Limpar pesquisa ao abrir modal e carregar primeiros NCMs
        $('#modalNcm').on('show.bs.modal', function () {
            // Garantir que os campos de preço estejam liberados antes de abrir o modal
            $('#precoCompra, #precoVenda').prop('disabled', false).prop('readonly', false).removeAttr('disabled');
            console.log('Campos liberados antes de abrir modal NCM');

            $('#pesquisaNcm').val('');
            $('#tabelaNcm tbody').empty();
            carregarPrimeirosNcms();
        });

        // Carregar descrição do NCM ao carregar a página
        function carregarDescricaoNcm() {
            var ncmCodigo = $('#PRO_NCM').val();
            var ncmId = $('#NCM_ID').val();
            
            if (ncmCodigo && ncmCodigo !== '' && ncmCodigo !== '00000000') {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/ncms/buscar',
                    type: 'GET',
                    data: { termo: ncmCodigo },
                    dataType: 'json',
                    success: function(response) {
                        if (response && response.length > 0) {
                            // Procura pelo NCM com o código exato
                            var ncmEncontrado = null;
                            $.each(response, function(i, ncm) {
                                var codigo = ncm.NCM_CODIGO || ncm.codigo || ncm.ncm_codigo || '';
                                if (codigo == ncmCodigo) {
                                    ncmEncontrado = ncm;
                                    return false; // break
                                }
                            });
                            
                            if (ncmEncontrado) {
                                var descricao = ncmEncontrado.NCM_DESCRICAO || ncmEncontrado.descricao || ncmEncontrado.ncm_descricao || '';
                                $('#btnDescricaoNcm').data('descricao', descricao);
                            }
                        }
                    },
                    error: function() {
                        console.log('Erro ao carregar descrição do NCM');
                    }
                });
            }
        }

        // Carregar a descrição do NCM quando a página estiver pronta
        carregarDescricaoNcm();

        // Botão para mostrar descrição do NCM
        $('#btnDescricaoNcm').on('click', function() {
            var descricao = $(this).data('descricao');
            if (descricao) {
                // Mostra a descrição usando SweetAlert ou alert
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Descrição do NCM',
                        text: descricao,
                        icon: 'info',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Descrição do NCM:\n\n' + descricao);
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Atenção',
                        text: 'Nenhum NCM selecionado ou descrição não disponível',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Nenhum NCM selecionado ou descrição não disponível');
                }
            }
        });

    });
</script>

