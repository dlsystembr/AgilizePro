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
        box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
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

    .table>thead>tr>th {
        padding: 12px;
        background-color: #f5f5f5;
        border-bottom: 2px solid #ddd;
    }

    .table>tbody>tr>td {
        padding: 12px;
        vertical-align: middle;
    }

    .table>tbody>tr>td:last-child {
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

    .switch-container {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        padding: 8px 0;
        margin-bottom: 10px;
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
        font-size: 12px;
        font-weight: bold;
        color: #dc3545;
    }

    .toggle-slider:before {
        content: '📦';
        font-size: 12px;
    }

    input:checked+.toggle-slider {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        box-shadow: 0 2px 6px rgba(40, 167, 69, 0.3);
    }

    input:checked+.toggle-slider:before {
        transform: translateX(24px);
        content: '🔧';
        color: #28a745;
    }

    input:focus+.toggle-slider {
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    }

    input:checked:focus+.toggle-slider {
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
    }

    /* Hover effects */
    .toggle-switch:hover .toggle-slider {
        transform: scale(1.02);
    }

    .toggle-switch:hover input:checked+.toggle-slider {
        box-shadow: 0 3px 8px rgba(40, 167, 69, 0.4);
    }

    .toggle-switch:hover input:not(:checked)+.toggle-slider {
        box-shadow: 0 3px 8px rgba(220, 53, 69, 0.4);
    }
</style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-bag"></i>
                </span>
                <h5>Cadastro de Produto / Serviços</h5>
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
                                            <input type="checkbox" id="PRO_TIPO_TOGGLE" <?= set_value('PRO_TIPO') == '2' ? 'checked' : '' ?>>
                                            <span class="toggle-slider"></span>
                                        </label>
                                        <input type="hidden" name="PRO_TIPO" id="PRO_TIPO"
                                            value="<?= set_value('PRO_TIPO', '1') ?>">
                                        <span id="tipo_label"
                                            class="switch-label"><?= set_value('PRO_TIPO') == '2' ? 'Serviço' : 'Produto' ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span6">
                            <!-- Coluna 1: Informações Básicas -->
                            <div class="control-group">
                                <label for="codigo" class="control-label">Código do Produto</label>
                                <div class="controls">
                                    <input id="codigo" type="text" name="codigo" value="" placeholder="Deixe vazio para gerar automaticamente" />
                                    <span class="help-inline" style="color: #666; font-size: 11px;"><i class="fas fa-info-circle"></i> Se deixar vazio, será gerado automaticamente</span>
                                    <span id="codigoHint" class="help-inline" style="color: #ff9800; font-size: 11px; display: none;"><i class="fas fa-exclamation-triangle"></i> É necessário código para gerar um código de barras manual</span>
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="PRO_COD_BARRA" class="control-label">Código de Barra</label>
                                <div class="controls">
                                    <div class="input-group" style="display: flex; gap: 5px;">
                                        <input id="PRO_COD_BARRA" type="text" name="PRO_COD_BARRA"
                                            value="<?php echo set_value('PRO_COD_BARRA'); ?>" />
                                        <button type="button" class="btn btn-info" id="btnGerarCodigo"
                                            style="border-radius: 4px;" disabled>
                                            <i class="fas fa-barcode"></i> Gerar
                                        </button>
                                    </div>
                                    <span id="codigoBarraStatus" class="help-inline"></span>
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="PRO_REFERENCIA" class="control-label">Referência</label>
                                <div class="controls">
                                    <input id="PRO_REFERENCIA" type="text" name="PRO_REFERENCIA"
                                        value="<?php echo set_value('PRO_REFERENCIA'); ?>"
                                        placeholder="Código de referência do produto" />
                                    <span class="help-inline">Código de referência do produto no sistema do
                                        fornecedor</span>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="PRO_DESCRICAO" class="control-label">Descrição<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <input id="PRO_DESCRICAO" type="text" name="PRO_DESCRICAO"
                                        value="<?php echo set_value('PRO_DESCRICAO'); ?>" />
                                </div>
                            </div>

                            <div class="control-group field-produto">
                                <label for="PRO_NCM" class="control-label">NCM<span class="required">*</span></label>
                                <div class="controls">
                                    <div class="input-group" style="display: flex; gap: 5px;">
                                        <input id="PRO_NCM" class="form-control" type="text" name="PRO_NCM"
                                            value="<?php echo set_value('PRO_NCM'); ?>" maxlength="8" />
                                        <button type="button" class="btn btn-success" id="btnBuscarNcm"
                                            style="border-radius: 4px;" data-toggle="modal" data-target="#modalNcm"><i
                                                class="fas fa-search"></i></button>
                                        <button type="button" class="btn btn-warning" id="btnDescricaoNcm"
                                            style="border-radius: 4px;" title="Nomenclatura Comum do Mercosul - Código de 8 dígitos que classifica produtos para fins fiscais e aduaneiros"><i class="fas fa-info-circle"></i></button>
                                    </div>
                                    <input id="NCM_ID" class="form-control" type="hidden" name="NCM_ID"
                                        value="<?php echo set_value('NCM_ID'); ?>" />
                                </div>
                            </div>
                            <?php $finalidadeSelecionada = set_value('PRO_FINALIDADE', 'Comercialização'); ?>
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
                                    <span class="help-inline">Identifique se o item é para revenda, consumo ou ativo.</span>
                                </div>
                            </div>
                            <div class="control-group field-servico" style="display: none;">
                                <label for="PRO_CCLASS_SERV" class="control-label">cClass (Serviço)</label>
                                <div class="controls">
                                    <div class="input-group" style="display: flex; gap: 5px;">
                                        <input id="PRO_CCLASS_SERV" class="form-control" type="text" name="PRO_CCLASS_SERV"
                                            value="<?php echo set_value('PRO_CCLASS_SERV'); ?>" maxlength="7" />
                                        <button type="button" class="btn btn-success" id="btnBuscarCClass"
                                            style="border-radius: 4px;" data-toggle="modal" data-target="#modalCClass"><i
                                                class="fas fa-search"></i></button>
                                        <button type="button" class="btn btn-warning" id="btnDescricaoCClass"
                                            style="border-radius: 4px;" title="Código de Classificação de Serviços - Código de 7 dígitos usado para classificar serviços de telecomunicações"><i class="fas fa-info-circle"></i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="PRO_UNID_MEDIDA" class="control-label">Unidade<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <select id="PRO_UNID_MEDIDA" name="PRO_UNID_MEDIDA"></select>
                                </div>
                            </div>
                            <!-- Preço de Venda para Serviços (aparece na coluna esquerda) -->
                            <div class="control-group field-servico" style="display: none;">
                                <label for="PRO_PRECO_VENDA" class="control-label" id="label-preco-venda">Preço Serviço<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <input id="PRO_PRECO_VENDA" class="preco-simples" type="text" name="PRO_PRECO_VENDA"
                                        value="<?php echo set_value('PRO_PRECO_VENDA'); ?>" placeholder="0,00" />
                                </div>
                            </div>
                        </div>

                        <div class="span6">
                            <!-- Coluna 2: Preços e Estoque -->
                            <div class="control-group field-produto">
                                <label for="PRO_PRECO_COMPRA" class="control-label">Preço de Compra<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <input id="PRO_PRECO_COMPRA" class="preco-simples" type="text" name="PRO_PRECO_COMPRA"
                                        value="<?php echo set_value('PRO_PRECO_COMPRA'); ?>" placeholder="0,00" />
                                    <strong><span style="color: red" id="errorAlert"></span><strong>
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="Lucro" class="control-label">Lucro</label>
                                <div class="controls">
                                    <select id="selectLucro" name="selectLucro" style="width: 10.5em;">
                                        <option value="markup">Markup</option>
                                        <option value="margemLucro">Margem de Lucro</option>
                                    </select>
                                    <input style="width: 4em;" id="Lucro" name="Lucro" type="text" placeholder="%"
                                        maxlength="3" size="2" />
                                    <i class="icon-info-sign tip-left"
                                        title="Markup: Porcentagem aplicada ao valor de compra | Margem de Lucro: Porcentagem aplicada ao valor de venda"></i>
                                </div>
                            </div>
                            <!-- Preço de Venda para Produtos (aparece na coluna direita) -->
                            <div class="control-group field-produto">
                                <label for="PRO_PRECO_VENDA_PRODUTO" class="control-label">Preço de Venda<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <input id="PRO_PRECO_VENDA_PRODUTO" class="preco-simples" type="text" name="PRO_PRECO_VENDA"
                                        value="<?php echo set_value('PRO_PRECO_VENDA'); ?>" placeholder="0,00" />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="PRO_ESTOQUE_MINIMO" class="control-label">Estoque Mínimo</label>
                                <div class="controls">
                                    <input id="PRO_ESTOQUE_MINIMO" type="number" name="PRO_ESTOQUE_MINIMO"
                                        value="<?php echo set_value('PRO_ESTOQUE_MINIMO'); ?>" />
                                </div>
                            </div>
                            <div class="control-group field-produto">
                                <label for="PRO_ORIGEM" class="control-label">Origem do Produto<span
                                        class="required">*</span></label>
                                <div class="controls">
                                    <select id="PRO_ORIGEM" name="PRO_ORIGEM">
                                        <option value="0" selected>0 - Nacional (exceto as indicadas nos códigos 3, 4, 5
                                            e 8)</option>
                                        <option value="1">1 - Estrangeira – Importação direta</option>
                                        <option value="2">2 - Estrangeira – Adquirida no mercado interno</option>
                                        <option value="3">3 - Nacional – Conteúdo de importação superior a 40% e
                                            inferior ou igual a 70%</option>
                                        <option value="4">4 - Nacional – Produzido conforme os processos produtivos
                                            básicos (PPB)</option>
                                        <option value="5">5 - Nacional – Conteúdo de importação inferior ou igual a 40%
                                        </option>
                                        <option value="6">6 - Estrangeira – Importação direta sem similar nacional,
                                            constante da CAMEX</option>
                                        <option value="7">7 - Estrangeira – Adquirida no mercado interno, sem similar
                                            nacional</option>
                                        <option value="8">8 - Nacional – Conteúdo de importação superior a 70%</option>
                                    </select>
                                    <span class="help-inline">Selecione a origem do produto conforme a tabela de
                                        códigos</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-fluid field-produto">
                        <div class="span12">
                            <h5
                                style="margin: 20px 0 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; padding-left: 20px;">
                                Dimensões e Peso</h5>
                        </div>
                    </div>

                    <div class="row-fluid field-produto" style="margin-left: 0;">
                        <div class="span2">
                            <div class="control-group">
                                <label for="PRO_PESO_BRUTO" class="control-label">Peso Bruto (kg)</label>
                                <div class="controls">
                                    <input id="PRO_PESO_BRUTO" type="text" name="PRO_PESO_BRUTO"
                                        value="<?php echo set_value('PRO_PESO_BRUTO'); ?>" class="decimal"
                                        style="width: 100px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="PRO_PESO_LIQUIDO" class="control-label">Peso Líquido (kg)</label>
                                <div class="controls">
                                    <input id="PRO_PESO_LIQUIDO" type="text" name="PRO_PESO_LIQUIDO"
                                        value="<?php echo set_value('PRO_PESO_LIQUIDO'); ?>" class="decimal"
                                        style="width: 100px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="PRO_LARGURA" class="control-label">Largura (cm)</label>
                                <div class="controls">
                                    <input id="PRO_LARGURA" type="text" name="PRO_LARGURA"
                                        value="<?php echo set_value('PRO_LARGURA'); ?>" class="decimal"
                                        style="width: 80px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="PRO_ALTURA" class="control-label">Altura (cm)</label>
                                <div class="controls">
                                    <input id="PRO_ALTURA" type="text" name="PRO_ALTURA"
                                        value="<?php echo set_value('PRO_ALTURA'); ?>" class="decimal"
                                        style="width: 80px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                        <div class="span2">
                            <div class="control-group">
                                <label for="PRO_COMPRIMENTO" class="control-label">Comprimento (cm)</label>
                                <div class="controls">
                                    <input id="PRO_COMPRIMENTO" type="text" name="PRO_COMPRIMENTO"
                                        value="<?php echo set_value('PRO_COMPRIMENTO'); ?>" class="decimal"
                                        style="width: 80px; padding: 5px;" placeholder="0,000" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <div class="span12">
                            <div class="span6 offset3" style="display: flex;justify-content: center">
                                <button type="submit" class="button btn btn-mini btn-success"
                                    style="max-width: 160px"><span class="button__icon"><i
                                            class='bx bx-plus-circle'></i></span><span
                                        class="button__text2">Adicionar</span></button>
                                <a href="<?php echo base_url() ?>index.php/produtos" id=""
                                    class="button btn btn-mini btn-warning"><span class="button__icon"><i
                                            class="bx bx-undo"></i></span><span class="button__text2">Voltar</span></a>
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
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="modalNcmLabel"><i class="fas fa-search"></i> Pesquisar NCM</h4>
            </div>
            <div class="modal-body" style="max-height: 600px;">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <div class="controls">
                                <div class="input-group">
                                    <input type="text" id="pesquisaNcm" class="form-control"
                                        placeholder="Digite o código ou parte da descrição do NCM (mín. 2 caracteres)">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-info" id="btnPesquisarNcm">
                                        <i class="fas fa-search"></i> Pesquisar
                                    </button>
                                    </span>
                                </div>
                                <div class="help-block" style="margin-top: 5px; font-size: 11px; color: #666;">
                                    <i class="fas fa-info-circle"></i> Busca por código exato ou palavras na descrição
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-striped" id="tabelaNcm">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoTabelaNcm">
                                    <tr>
                                        <td colspan="3" class="text-center">Carregando NCMs...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pesquisa cClass -->
<div class="modal fade" id="modalCClass" role="dialog" data-backdrop="false">
    <div class="modal-dialog modal-lg" style="width: 95%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><i class="fas fa-search"></i> Pesquisar cClass</h4>
            </div>
            <div class="modal-body" style="max-height: 600px;">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="control-group">
                            <div class="controls">
                                <div class="input-group">
                                    <input type="text" id="pesquisaCClass" class="form-control"
                                        placeholder="Digite o código ou parte da descrição do cClass (mín. 2 caracteres)">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-info" id="btnPesquisarCClass">
                                            <i class="fas fa-search"></i> Pesquisar
                                        </button>
                                    </span>
                                </div>
                                <div class="help-block" style="margin-top: 5px; font-size: 11px; color: #666;">
                                    <i class="fas fa-info-circle"></i> Busca por código exato ou palavras na descrição
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-bordered table-striped" id="tabelaCClass">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descrição</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="corpoTabelaCClass">
                                    <tr>
                                        <td colspan="3" class="text-center">Carregando cClass...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
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
        var precoCompraStr = $("#PRO_PRECO_COMPRA").val();
        var lucro = Number($("#Lucro").val());

        // Converte valor com vírgula para número
        var precoCompra = parseFloat(precoCompraStr.replace(',', '.'));

        if (precoCompra > 0 && lucro >= 0 && !isNaN(precoCompra)) {
            var precoCalculado = calcLucro(precoCompra, lucro);
            // Atualiza ambos os campos de preço
            $('#PRO_PRECO_VENDA').val(precoCalculado.toString().replace('.', ','));
            $('#PRO_PRECO_VENDA_PRODUTO').val(precoCalculado.toString().replace('.', ','));
        }
    }

    // Atualizar quando mudar preço de compra também
    $("#PRO_PRECO_COMPRA").on('input change', atualizarPrecoVenda);
    $("#Lucro, #selectLucro").on('input change', atualizarPrecoVenda);

    // Removido: Não limpar preço de venda automaticamente quando editando preço de compra

    $("#Lucro").keyup(function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
        if ($("#PRO_PRECO_COMPRA").val() == null || $("#PRO_PRECO_COMPRA").val() == '') {
            $('#errorAlert').text('Preencher valor da compra primeiro.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#PRO_PRECO_VENDA').val('');
            $('#PRO_PRECO_VENDA_PRODUTO').val('');
            $("#PRO_PRECO_COMPRA").focus();

        } else if (Number($("#Lucro").val()) >= 0) {
            var precoCompraStr = $("#PRO_PRECO_COMPRA").val();
            var precoCompra = parseFloat(precoCompraStr.replace(',', '.'));
            var precoCalculado = calcLucro(precoCompra, Number($("#Lucro").val()));
            $('#PRO_PRECO_VENDA').val(precoCalculado.toString().replace('.', ','));
            $('#PRO_PRECO_VENDA_PRODUTO').val(precoCalculado.toString().replace('.', ','));
        } else {
            $('#errorAlert').text('Não é permitido número negativo.').css("display", "inline").fadeOut(5000);
            $('#Lucro').val('');
            $('#PRO_PRECO_VENDA').val('');
            $('#PRO_PRECO_VENDA_PRODUTO').val('');
        }
    });

    $('#PRO_PRECO_VENDA').focusout(function () {
        var precoVendaStr = $('#PRO_PRECO_VENDA').val();
        var precoCompraStr = $("#PRO_PRECO_COMPRA").val();

        var precoVenda = parseFloat(precoVendaStr.replace(',', '.'));
        var precoCompra = parseFloat(precoCompraStr.replace(',', '.'));

        if (precoVenda < precoCompra) {
            $('#errorAlert').text('Preço de venda não pode ser menor que o preço de compra.').css("display", "inline").fadeOut(6000);
            $('#PRO_PRECO_VENDA').val('');
        }
    });

    // Auto-selecionar conteúdo ao clicar ou focar nos campos de preço, decimais e lucro
    $(document).on('focus click', '.preco-simples, .decimal, #Lucro', function() {
        $(this).select();
    });

    // Sincronizar os dois campos de preço (serviço e produto)
    $('#PRO_PRECO_VENDA').on('input', function() {
        $('#PRO_PRECO_VENDA_PRODUTO').val($(this).val());
    });
    
    $('#PRO_PRECO_VENDA_PRODUTO').on('input', function() {
        $('#PRO_PRECO_VENDA').val($(this).val());
    });


    $(document).ready(function () {
        // Configuração dos campos decimais (mantém maskMoney)
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

        // Configuração dos campos de preço simples (digitação livre)
        $('.preco-simples').on('input', function() {
            // Permite apenas números e vírgula
            var value = $(this).val();
            var cleanValue = value.replace(/[^0-9,]/g, '');
            if (value !== cleanValue) {
                $(this).val(cleanValue);
            }
        });

        // Selecionar todo o conteúdo do campo quando clicar/focar
        $('.preco-simples').on('focus click', function() {
            $(this).select();
        });

        // Permite backspace e delete
        $('.preco-simples').on('keydown', function(e) {
            if (e.keyCode === 8 || e.keyCode === 46) { // backspace or delete
                return true;
            }
        });

        // Permitir digitação livre nos campos decimais
        $('.decimal').off('keypress').on('keypress', function (e) {
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

        // Formatar ao perder o foco
        $('.money, .decimal').on('blur', function () {
            var value = $(this).val();
            if (value === '') {
                $(this).val('');
                return;
            }

            // Se não tiver vírgula, adiciona os zeros necessários
            if (value.indexOf(',') === -1) {
                if ($(this).hasClass('money')) {
                    $(this).val(value + ',00');
                } else {
                    $(this).val(value + ',000');
                }
            }
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
                if ('<?php echo set_value('PRO_UNID_MEDIDA'); ?>') {
                    select.val('<?php echo set_value('PRO_UNID_MEDIDA'); ?>');
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
                // Selecionar unidade padrão para serviço (1=Minuto) se não houver valor definido
                if ('<?php echo set_value('PRO_UNID_MEDIDA'); ?>') {
                    select.val('<?php echo set_value('PRO_UNID_MEDIDA'); ?>');
                } else {
                    select.val('1'); // Minuto como padrão para serviços
                }
            });
        }

        // Carregar unidades iniciais (produto por padrão)
        carregarUnidadesProduto();

        function toggleFields() {
            var isService = $('#PRO_TIPO_TOGGLE').is(':checked');
            if (isService) { // Serviço
                $('.field-produto').hide();
                $('.field-servico').show();
                $('#PRO_TIPO_TOGGLE').prop('checked', true);
                $('#tipo_label').text('Serviço');
                $('#PRO_FINALIDADE option[value="Serviço"]').show();
                $('#PRO_FINALIDADE').val('Serviço').prop('disabled', true);

                // Alterar label do código para "Código do Serviço"
                $('label[for="codigo"]').text('Código do Serviço');

                // Auto-set NCM for Service
                $('#PRO_NCM').val('00000000');
                $('#NCM_ID').val('15142');

                // Definir valores padrão para campos obrigatórios de produto que não se aplicam a serviço
                $('#PRO_PRECO_COMPRA, #PRO_ESTOQUE, #PRO_ESTOQUE_MINIMO').val('0');
                $('#PRO_ORIGEM').val('0');
                $('#PRO_PESO_BRUTO, #PRO_PESO_LIQUIDO, #PRO_LARGURA, #PRO_ALTURA, #PRO_COMPRIMENTO').val('0.000');

                // Carregar unidades de serviço
                carregarUnidadesServico();

                // Deixar cClass vazio por padrão para serviços
                if (!$('#PRO_CCLASS_SERV').val()) {
                    $('#PRO_CCLASS_SERV').val(''); // Vazio por padrão
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
                $('label[for="codigo"]').text('Código do Produto');

                // Clear NCM if it was the default service NCM
                if ($('#PRO_NCM').val() === '00000000') {
                    $('#PRO_NCM').val('');
                    $('#NCM_ID').val('');
                }

                // Limpar cClass quando voltar para produto
                $('#PRO_CCLASS_SERV').val('');

                // Carregar unidades de produto
                carregarUnidadesProduto();
            }
        }

        $('#PRO_TIPO_TOGGLE').change(function () {
            if ($(this).is(':checked')) {
                $('#PRO_TIPO').val('2');
            } else {
                $('#PRO_TIPO').val('1');
            }
            toggleFields();
        });

        toggleFields();

        // Validação dos campos
        $('#formProduto').on('submit', function(e) {
            console.log('Valor PRO_PRECO_COMPRA sendo enviado:', $('#PRO_PRECO_COMPRA').val());
            console.log('Valor PRO_PRECO_VENDA sendo enviado:', $('#PRO_PRECO_VENDA').val());
        });

        $('#formProduto').validate({
            errorElement: 'span',
            errorClass: 'help-inline',
            focusInvalid: true,
            highlight: function (element) {
                $(element).closest('.control-group').addClass('error');
            },
            unhighlight: function (element) {
                $(element).closest('.control-group').removeClass('error');
                $(element).closest('.control-group').addClass('success');
            },
            errorPlacement: function (error, element) {
                error.addClass('help-inline');
                element.closest('.controls').append(error);
            },
            rules: {
                PRO_DESCRICAO: {
                    required: true
                },
                PRO_UNID_MEDIDA: {
                    required: true
                },
                PRO_PRECO_COMPRA: {
                    required: function () {
                        return $('#PRO_TIPO').val() == '1';
                    }
                },
                PRO_PRECO_VENDA: {
                    required: true
                },
                PRO_ESTOQUE: {
                    required: function () {
                        return $('#PRO_TIPO').val() == '1';
                    }
                },
                PRO_NCM: {
                    required: function () {
                        return $('#PRO_TIPO').val() == '1';
                    }
                },
                PRO_ORIGEM: {
                    required: function () {
                        return $('#PRO_TIPO').val() == '1';
                    }
                },
                PRO_FINALIDADE: {
                    required: function () {
                        return $('#PRO_TIPO').val() == '1';
                    }
                },
                PRO_REFERENCIA: {
                    maxlength: 50
                }
            },
            messages: {
                PRO_DESCRICAO: {
                    required: 'Campo obrigatório'
                },
                PRO_UNID_MEDIDA: {
                    required: 'Campo obrigatório'
                },
                PRO_PRECO_COMPRA: {
                    required: 'Campo obrigatório'
                },
                PRO_PRECO_VENDA: {
                    required: 'Campo obrigatório'
                },
                PRO_ESTOQUE: {
                    required: 'Campo obrigatório'
                },
                PRO_NCM: {
                    required: 'Campo obrigatório'
                },
                PRO_ORIGEM: {
                    required: 'Campo obrigatório'
                },
                PRO_FINALIDADE: {
                    required: 'Selecione a finalidade do produto'
                },
                PRO_REFERENCIA: {
                    maxlength: 'A referência não pode ter mais que 50 caracteres'
                }
            },
            invalidHandler: function (event, validator) {
                $('.alert-error').show();
            },
            submitHandler: function (form) {
                form.submit();
            }
        });

        // Adicionar classe de erro ao campo quando perder o foco
        $('input, select').on('blur', function () {
            if ($(this).val() === '') {
                $(this).closest('.control-group').addClass('error');
            } else {
                $(this).closest('.control-group').removeClass('error');
                $(this).closest('.control-group').addClass('success');
            }
        });

        // Remover mensagens de erro ao digitar
        $('input, select').on('keyup change', function () {
            if ($(this).val() !== '') {
                $(this).closest('.control-group').find('.help-inline').remove();
                $(this).closest('.control-group').removeClass('error');
                $(this).closest('.control-group').addClass('success');
            }
        });

        // Função para validar código de barra
        function validarCodigoBarra() {
            var codigo = $('#PRO_COD_BARRA').val();
            if (!codigo) {
                $('#codigoBarraStatus').html('');
                return;
            }

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/produtos/validarCodigoBarraAjax',
                type: 'POST',
                data: { codigo: codigo },
                dataType: 'json',
                success: function (response) {
                    if (response.valido) {
                        $('#codigoBarraStatus').html('<span style="color: green;"><i class="fas fa-check"></i> Código válido</span>');
                    } else {
                        $('#codigoBarraStatus').html('<span style="color: red;"><i class="fas fa-times"></i> Código inválido</span>');
                    }
                }
            });
        }

        // Validar ao digitar
        $('#PRO_COD_BARRA').on('input', function () {
            validarCodigoBarra();
        });

        // Monitorar campo código para habilitar/desabilitar botão Gerar
        $('#codigo').on('input', function () {
            var codigo = $(this).val().trim();
            if (codigo) {
                $('#btnGerarCodigo').prop('disabled', false);
                $('#codigoHint').hide();
            } else {
                $('#btnGerarCodigo').prop('disabled', true);
                $('#codigoHint').show();
            }
        });

        // Gerar código de barra
        $('#btnGerarCodigo').click(function () {
            var id = $('#codigo').val();
            if (id) {
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/produtos/gerarCodigoBarraAjax',
                    type: 'GET',
                    data: { id: id },
                    dataType: 'json',
                    success: function (response) {
                        if (response.codigo) {
                            $('#PRO_COD_BARRA').val(response.codigo);
                            validarCodigoBarra();
                        }
                    }
                });
            }
        });


        // Função para carregar os primeiros 25 NCMs automaticamente
        function carregarPrimeirosNcms() {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/ncms/buscar',
                type: 'GET',
                data: { pagina: 1, limite: 25 },
                dataType: 'json',
                success: function(response) {
                    var tbody = $('#corpoTabelaNcm');
                    tbody.empty();

                    // A API sempre retorna um objeto com "resultados"
                    var ncms = response.resultados || response;

                    if (ncms && ncms.length > 0) {
                        $.each(ncms, function(i, ncm) {
                            var codigo = ncm.NCM_CODIGO || ncm.codigo || ncm.ncm_codigo || '';
                            var descricao = ncm.NCM_DESCRICAO || ncm.descricao || ncm.ncm_descricao || '';
                            var id = ncm.NCM_ID || ncm.id || ncm.ncm_id || '';

                            tbody.append(
                                '<tr>' +
                                '<td>' + codigo + '</td>' +
                                '<td>' + descricao + '</td>' +
                                '<td class="text-center">' +
                                '<button type="button" class="btn btn-success btn-sm selecionarNcm" ' +
                                'data-codigo="' + codigo + '" ' +
                                'data-id="' + id + '">' +
                                '<i class="fas fa-check"></i> Selecionar</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        tbody.html('<tr><td colspan="3" class="text-center">Nenhum NCM encontrado</td></tr>');
                    }
                },
                error: function() {
                    $('#corpoTabelaNcm').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar NCMs</td></tr>');
                }
            });
        }

        // Função simples para pesquisar NCM por código OU descrição
        function pesquisarNcm(termo) {
            if (!termo || termo.trim().length < 2) {
                // Se não há termo suficiente, volta aos primeiros 25 NCMs
                console.log('Termo muito curto, carregando primeiros NCMs');
                carregarPrimeirosNcms();
                return;
            }

            var termoLimpo = termo.trim();
            console.log('Pesquisando NCM por:', termoLimpo);
            console.log('URL da requisição:', '<?php echo base_url(); ?>index.php/ncms/buscar?termo=' + encodeURIComponent(termoLimpo));

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/ncms/buscar',
                type: 'GET',
                data: { termo: termo.trim() },
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta da busca NCM:', response);

                    var tbody = $('#corpoTabelaNcm');
                    tbody.empty();

                    // A API sempre retorna um objeto com "resultados"
                    var ncms = response.resultados || response;

                    if (ncms && ncms.length > 0) {
                        console.log('Encontrados ' + ncms.length + ' NCMs');

                        $.each(ncms, function(i, ncm) {
                            var codigo = ncm.NCM_CODIGO || ncm.codigo || ncm.ncm_codigo || '';
                            var descricao = ncm.NCM_DESCRICAO || ncm.descricao || ncm.ncm_descricao || '';
                            var id = ncm.NCM_ID || ncm.id || ncm.ncm_id || '';

                            tbody.append(
                                '<tr>' +
                                '<td>' + codigo + '</td>' +
                                '<td>' + descricao + '</td>' +
                                '<td class="text-center">' +
                                '<button type="button" class="btn btn-success btn-sm selecionarNcm" ' +
                                'data-codigo="' + codigo + '" ' +
                                'data-id="' + id + '">' +
                                '<i class="fas fa-check"></i> Selecionar</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        console.log('Nenhum NCM encontrado para:', termo.trim());
                        tbody.html('<tr><td colspan="3" class="text-center text-warning"><i class="fas fa-info-circle"></i> Nenhum NCM encontrado para "' + termo.trim() + '"</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na busca NCM:', error);
                    $('#corpoTabelaNcm').html('<tr><td colspan="3" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Erro na busca. Tente novamente.</td></tr>');
                }
            });
        }

        // Evento de digitação no campo de pesquisa NCM
        $('#pesquisaNcm').on('input', function() {
                pesquisarNcm($(this).val());
        });

        // Evento de clique no botão pesquisar NCM
        $('#btnPesquisarNcm').on('click', function() {
            pesquisarNcm($('#pesquisaNcm').val());
        });

        // Evento de seleção do NCM - apenas define o valor no campo
        $(document).on('click', '.selecionarNcm', function() {
            var codigo = $(this).data('codigo');
            var id = $(this).data('id');

            // Apenas define os valores nos campos do formulário
            $('#PRO_NCM').val(codigo);
            $('#NCM_ID').val(id);

            // Fecha o modal
            $('#modalNcm').modal('hide');
        });

        // Carregar primeiros 25 NCMs ao abrir modal
        $('#modalNcm').on('show.bs.modal', function() {
            $('#pesquisaNcm').val('');
            $('#corpoTabelaNcm').html('<tr><td colspan="3" class="text-center">Carregando NCMs...</td></tr>');
            carregarPrimeirosNcms();
        });

        // Limpar qualquer resíduo quando o modal fechar
        $('#modalNcm').on('hidden.bs.modal', function() {
            // Aguardar um pouco para garantir que o modal foi completamente fechado
            setTimeout(function() {
                console.log('Iniciando limpeza completa do modal NCM');

                // 1. Forçar fechamento e remoção completa do modal
                $('#modalNcm').removeClass('in').hide();
                $('#modalNcm').css({
                    'display': 'none',
                    'visibility': 'hidden',
                    'opacity': '0',
                    'pointer-events': 'none'
                });

                // 2. Garantir que não há nenhum elemento bloqueante residual
            $('.modal-backdrop').remove();
                $('.modal').not('#modalNcm').removeClass('in').hide();
            $('body').removeClass('modal-open');
                $('body').css({
                    'overflow': 'auto',
                    'padding-right': '0',
                    'pointer-events': 'auto'
                });

                // 3. Resetar especificamente a área onde o modal estava
                $('#modalNcm').css({
                    'position': 'static',
                    'z-index': 'auto',
                    'pointer-events': 'none'
                });

                // 4. Garantir que todos os campos do formulário estão acessíveis
                $('input, select, textarea, button, a').each(function() {
                    var $element = $(this);
                    $element.prop('disabled', false).prop('readonly', false);
                    $element.removeAttr('disabled').removeAttr('readonly');
                    $element.css({
                        'pointer-events': 'auto',
                        'cursor': 'auto',
                        'z-index': 'auto',
                        'position': 'static'
                    });
                });

                // 5. Resetar containers principais
                $('.widget-box, .widget-content, form, .row-fluid, .span6, .span12').css({
                    'pointer-events': 'auto',
                    'position': 'static',
                    'z-index': 'auto'
                });

                // 6. Verificar se há elementos com pointer-events bloqueantes
                $('*').each(function() {
                    var $el = $(this);
                    var pointerEvents = $el.css('pointer-events');
                    if (pointerEvents === 'none') {
                        $el.css('pointer-events', 'auto');
                    }
                });

                console.log('Modal NCM fechado completamente - área liberada');

                // Verificação adicional após mais tempo
                setTimeout(function() {
                    // Garantir que o modal está completamente fora da área
                    $('#modalNcm').css('display', 'none !important');
                    $('#modalNcm').hide();

                    // Testar se os campos estão realmente funcionais
                    console.log('Verificação final: campos devem estar clicáveis agora');
                }, 300);

            }, 200); // Aumentei o delay para 200ms
        });

        // Função para carregar os primeiros 25 cClass automaticamente
        function carregarPrimeirosCClass() {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/produtos/pesquisarCClass',
                type: 'POST',
                data: { limite: 25 },
                dataType: 'json',
                success: function(response) {
                    var tbody = $('#corpoTabelaCClass');
                    tbody.empty();

                    if (response && response.length > 0) {
                        $.each(response.slice(0, 25), function(i, cclass) { // Limita a 25
                            tbody.append(
                                '<tr>' +
                                '<td>' + cclass.codigo + '</td>' +
                                '<td>' + cclass.descricao + '</td>' +
                                '<td class="text-center">' +
                                '<button type="button" class="btn btn-success btn-sm selecionarCClass" ' +
                                'data-codigo="' + cclass.codigo + '" ' +
                                'data-descricao="' + cclass.descricao + '">' +
                                '<i class="fas fa-check"></i> Selecionar</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        tbody.html('<tr><td colspan="3" class="text-center">Nenhum cClass encontrado</td></tr>');
                    }
                },
                error: function() {
                    $('#corpoTabelaCClass').html('<tr><td colspan="3" class="text-center text-danger">Erro ao carregar cClass</td></tr>');
                }
            });
        }

        // Função simples para pesquisar cClass por código OU descrição
        function pesquisarCClass(termo) {
            if (!termo || termo.trim().length < 2) {
                // Se não há termo suficiente, volta aos primeiros 25 cClass
                carregarPrimeirosCClass();
                return;
            }

            console.log('Pesquisando cClass por:', termo.trim());

            $.ajax({
                url: '<?php echo base_url(); ?>index.php/produtos/pesquisarCClass',
                type: 'POST',
                data: { termo: termo.trim() },
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta da busca cClass:', response);

                    var tbody = $('#corpoTabelaCClass');
                    tbody.empty();

                    if (response && response.length > 0) {
                        console.log('Encontrados ' + response.length + ' cClass');

                        $.each(response, function(i, cclass) {
                            tbody.append(
                                '<tr>' +
                                '<td>' + cclass.codigo + '</td>' +
                                '<td>' + cclass.descricao + '</td>' +
                                '<td class="text-center">' +
                                '<button type="button" class="btn btn-success btn-sm selecionarCClass" ' +
                                'data-codigo="' + cclass.codigo + '" ' +
                                'data-descricao="' + cclass.descricao + '">' +
                                '<i class="fas fa-check"></i> Selecionar</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        console.log('Nenhum cClass encontrado para:', termo.trim());
                        tbody.html('<tr><td colspan="3" class="text-center text-warning"><i class="fas fa-info-circle"></i> Nenhum cClass encontrado para "' + termo.trim() + '"</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na busca cClass:', error);
                    $('#corpoTabelaCClass').html('<tr><td colspan="3" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Erro na busca. Tente novamente.</td></tr>');
                }
            });
        }

        // Evento de digitação no campo de pesquisa cClass
        $('#pesquisaCClass').on('input', function() {
            pesquisarCClass($(this).val());
        });

        // Evento de clique no botão pesquisar cClass
        $('#btnPesquisarCClass').on('click', function() {
            pesquisarCClass($('#pesquisaCClass').val());
        });

        // Carregar primeiros 25 cClass ao abrir modal
        $('#modalCClass').on('show.bs.modal', function() {
            $('#pesquisaCClass').val('');
            $('#corpoTabelaCClass').html('<tr><td colspan="3" class="text-center">Carregando cClass...</td></tr>');
            carregarPrimeirosCClass();
        });

        // Limpar qualquer resíduo quando o modal fechar
        $('#modalCClass').on('hidden.bs.modal', function() {
            // Aguardar um pouco para garantir que o modal foi completamente fechado
            setTimeout(function() {
                console.log('Iniciando limpeza completa do modal cClass');

                // Forçar fechamento e remoção completa do modal
                $('#modalCClass').removeClass('in').hide();
                $('#modalCClass').css({
                    'display': 'none',
                    'visibility': 'hidden',
                    'opacity': '0',
                    'pointer-events': 'none'
                });

                // Garantir que não há nenhum elemento bloqueante residual
                $('.modal-backdrop').remove();
                $('.modal').not('#modalCClass').removeClass('in').hide();
                $('body').removeClass('modal-open');
                $('body').css({
                    'overflow': 'auto',
                    'padding-right': '0',
                    'pointer-events': 'auto'
                });

                // Resetar especificamente a área do modal
                $('#modalCClass').css({
                    'position': 'static',
                    'z-index': 'auto',
                    'pointer-events': 'none'
                });

                // Garantir que todos os campos do formulário estão acessíveis
                $('input, select, textarea, button, a').each(function() {
                    var $element = $(this);
                    $element.prop('disabled', false).prop('readonly', false);
                    $element.removeAttr('disabled').removeAttr('readonly');
                    $element.css({
                        'pointer-events': 'auto',
                        'cursor': 'auto',
                        'z-index': 'auto',
                        'position': 'static'
                    });
                });

                // Resetar containers principais
                $('.widget-box, .widget-content, form, .row-fluid, .span6, .span12').css({
                    'pointer-events': 'auto',
                    'position': 'static',
                    'z-index': 'auto'
                });

                // Verificar e corrigir elementos com pointer-events bloqueantes
                $('*').each(function() {
                    if ($(this).css('pointer-events') === 'none') {
                        $(this).css('pointer-events', 'auto');
                    }
                });

                console.log('Modal cClass fechado completamente - área liberada');

                // Verificação adicional após mais tempo
                setTimeout(function() {
                    $('#modalCClass').css('display', 'none !important').hide();
                    console.log('Verificação final: campos devem estar clicáveis agora');
                }, 300);

            }, 200);
        });

        // Evento de seleção do cClass - apenas define o valor no campo
        $(document).on('click', '.selecionarCClass', function() {
            var codigo = $(this).data('codigo');
            var descricao = $(this).data('descricao');

            // Apenas define os valores nos campos do formulário
            $('#PRO_CCLASS_SERV').val(codigo);

            // Fecha o modal
            $('#modalCClass').modal('hide');
        });

    });
</script>