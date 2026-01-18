L<style>
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
                        <div class="span6">
                            <!-- Coluna 1: Informações Básicas -->
                    <div class="control-group">
                        <?php echo form_hidden('PRO_ID', $result->PRO_ID) ?>
                        <label for="codigo" class="control-label">Código do Produto</label>
                        <div class="controls">
                            <input id="codigo" type="text" name="codigo" value="<?php echo $result->PRO_ID; ?>" readonly />
                        </div>
                    </div>
                    <div class="control-group">
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
                    <div class="control-group">
                        <label for="tributacao_produto_id" class="control-label">Tributação de Produto</label>
                        <div class="controls">
                            <select name="tributacao_produto_id" id="tributacao_produto_id">
                                <option value="">Selecione</option>
                                <?php foreach ($tributacoes as $t): ?>
                                    <option value="<?= $t->id ?>" <?= (set_value('tributacao_produto_id', isset($result->tributacao_produto_id) ? $result->tributacao_produto_id : '') == $t->id) ? 'selected' : '' ?>><?= $t->nome_configuracao ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-inline">Vincule uma tributação ao produto.</span>
                        </div>
                    </div>
                    <div class="control-group field-produto">
                        <label for="NCMs" class="control-label">NCM<span class="required">*</span></label>
                        <div class="controls">
                            <div class="input-group" style="display: flex; gap: 5px;">
                                <input id="NCMs" class="form-control" type="text" name="NCMs" value="<?php echo $result->PRO_NCM; ?>" readonly />
                                <button type="button" class="btn btn-success" id="btnBuscarNcm" style="border-radius: 4px;" data-toggle="modal" data-target="#modalNcm"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-warning" id="btnDescricaoNcm" style="border-radius: 4px;"><i class="fas fa-info-circle"></i></button>
                            </div>
                            <input id="ncm_id" class="form-control" type="hidden" name="ncm_id" value="<?php echo $result->NCM_ID; ?>" />
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
                                    style="border-radius: 4px;"><i class="fas fa-info-circle"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Tipo de Movimento</label>
                        <div class="controls">
                            <label for="entrada" class="btn btn-default" style="margin-top: 5px;">Entrada
                                <input type="checkbox" id="entrada" name="entrada" class="badgebox" value="1" <?= ($result->PRO_ENTRADA == 1) ? 'checked' : '' ?>>
                                <span class="badge">&check;</span>
                            </label>
                            <label for="saida" class="btn btn-default" style="margin-top: 5px;">Saída
                                <input type="checkbox" id="saida" name="saida" class="badgebox" value="1" <?= ($result->PRO_SAIDA == 1) ? 'checked' : '' ?>>
                                <span class="badge">&check;</span>
                            </label>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="unidade" class="control-label">Unidade<span class="required">*</span></label>
                                <div class="controls">
                                    <select id="unidade" name="unidade" style="width: 15em;"></select>
                                </div>
                        </div>
                    </div>

                        <div class="span6">
                            <!-- Coluna 2: Preços e Estoque -->
                    <div class="control-group">
                        <label for="precoCompra" class="control-label">Preço de Compra<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoCompra" class="preco-simples" type="text" name="precoCompra" value="<?php echo $result->PRO_PRECO_COMPRA; ?>" placeholder="0,00" />
                            <strong><span style="color: red" id="errorAlert"></span><strong>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="Lucro" class="control-label">Lucro</label>
                        <div class="controls">
                            <select id="selectLucro" name="selectLucro" style="width: 10.5em;">
                              <option value="markup">Markup</option>
                            </select>
                            <input style="width: 4em;" id="Lucro" name="Lucro" type="text" placeholder="%" maxlength="3" size="2" />
                            <i class="icon-info-sign tip-left" title="Markup: Porcentagem aplicada ao valor de compra | Margem de Lucro: Porcentagem aplicada ao valor de venda"></i>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="precoVenda" class="control-label">Preço de Venda<span class="required">*</span></label>
                        <div class="controls">
                            <input id="precoVenda" class="preco-simples" type="text" name="precoVenda" value="<?php echo $result->PRO_PRECO_VENDA; ?>" placeholder="0,00" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="estoque" class="control-label">Estoque<span class="required">*</span></label>
                        <div class="controls">
                            <input id="estoque" type="number" name="estoque" value="<?php echo $result->PRO_ESTOQUE; ?>" />
                        </div>
                    </div>
                            <div class="control-group">
                                <label for="estoqueMinimo" class="control-label">Estoque Mínimo</label>
                                <div class="controls">
                                    <input id="estoqueMinimo" type="number" name="estoqueMinimo" value="<?php echo $result->PRO_ESTOQUE_MINIMO; ?>" />
                                </div>
                            </div>
                    <div class="control-group">
                        <label for="origem" class="control-label">Origem do Produto<span class="required">*</span></label>
                        <div class="controls">
                            <select id="origem" name="origem">
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

                    <div class="row-fluid">
                        <div class="span12">
                            <h5 style="margin: 20px 0 10px; border-bottom: 1px solid #ddd; padding-bottom: 5px; padding-left: 20px;">Dimensões e Peso</h5>
                        </div>
                    </div>

                    <div class="row-fluid" style="margin-left: 0;">
                        <div class="span2">
                            <div class="control-group">
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
                            <div class="control-group">
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
                        <div class="table-responsive" id="modalNcmTableContainer" style="max-height: 600px; overflow-y: auto;">
                            <table class="table table-bordered table-striped" id="tabelaNcm">
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
                        <div id="totalResultados" class="text-right" style="margin-top: 10px;"></div>
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
<script src="<?php echo base_url(); ?>assets/js/ncm-search.js"></script>
<script src="<?php echo base_url(); ?>assets/js/modal-ncm.js"></script>
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

        // Carregar unidades
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

        // Validação dos campos decimais
        $('#formProduto').validate({
            rules: {
                descricao: {
                    required: true
                },
                unidade: {
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

        // Validar código inicial
        validarCodigoBarra();

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

    });
</script>
