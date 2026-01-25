<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/maskmoney.js"></script>
<!-- SweetAlert2 -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>

<style>
.content-wrapper {
    margin-top: 80px !important;
    padding-top: 15px !important;
    min-height: calc(100vh - 80px) !important;
}

.content-header {
    margin-bottom: 10px !important;
    padding-bottom: 8px !important;
}

.content {
    padding-top: 10px !important;
    margin-bottom: 15px !important;
}

.box-primary {
    margin-top: 10px !important;
}

.box-header {
    padding: 6px 12px !important;
}

.box-header .box-title {
    font-size: 18px !important;
    font-weight: 600 !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.2 !important;
}

.box-body {
    padding: 10px !important;
}

.form-group {
    margin-bottom: 8px !important;
}

.form-group label {
    display: block !important;
    margin-bottom: 3px !important;
    font-weight: 600 !important;
    font-size: 11px !important;
}

.form-control {
    height: 22px !important;
    padding: 2px 6px !important;
    border-radius: 3px !important;
    font-size: 11px !important;
    line-height: 1.2 !important;
}

.input-group {
    width: 100% !important;
}

.input-group .form-control {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.input-group-btn {
    width: auto !important;
}

.input-group-btn .btn {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
    height: 22px !important;
    padding: 2px 6px !important;
    font-size: 11px !important;
    line-height: 1.2 !important;
}

/* Ajuste para campos específicos */
#chave_acesso {
    width: 100% !important;
    min-width: 300px !important;
    font-family: monospace !important;
    letter-spacing: 0.5px !important;
}

.datepicker {
    width: 100px !important;
}

.money {
    width: 100px !important;
}

.quantidade {
    width: 70px !important;
}

.cfop, .cst {
    width: 70px !important;
}

.aliquota, .aliquota_st {
    width: 50px !important;
}

/* Ajuste para a tabela de itens */
.table-responsive {
    margin-bottom: 10px !important;
}

.table > thead > tr > th {
    padding: 4px !important;
    font-size: 11px !important;
}

.table > tbody > tr > td {
    padding: 4px !important;
    font-size: 11px !important;
}

/* Ajuste para os botões */
.btn-group {
    margin-top: 5px !important;
}

.btn-group .btn {
    margin-right: 3px !important;
    padding: 2px 5px !important;
    font-size: 11px !important;
    height: 22px !important;
    line-height: 1.2 !important;
}

.btn-sm {
    padding: 2px 5px !important;
    font-size: 11px !important;
    line-height: 1.2 !important;
    height: 22px !important;
}

/* Ajuste para o layout responsivo */
.row {
    margin-left: -5px !important;
    margin-right: -5px !important;
}

.col-lg-3, .col-md-6, .col-sm-12 {
    padding-left: 5px !important;
    padding-right: 5px !important;
}

/* Ajuste para campos de total */
#total_nota, #total_base_icms, #total_icms, #total_base_icms_st, #total_icms_st {
    font-weight: bold !important;
    background-color: #f4f4f4 !important;
    color: #333 !important;
    border-color: #ddd !important;
    width: 100px !important;
}

/* Ajuste para o campo de fornecedor */
#fornecedor {
    width: 100% !important;
    min-width: 200px !important;
}

/* Ajuste para o select de operação comercial */
#operacao_comercial_id {
    width: 100% !important;
    min-width: 150px !important;
}

/* Ajuste para o campo NSU */
#nsu {
    width: 150px !important;
}

/* Ajuste para o botão de salvar */
.btn-primary {
    margin-right: 8px !important;
}

/* Ajuste para o container principal */
.container-fluid {
    padding-left: 10px !important;
    padding-right: 10px !important;
}

/* Ajuste para o box */
.box {
    margin-bottom: 15px !important;
    border-radius: 3px !important;
}

/* Ajuste para o título do box */
.box-title {
    line-height: 1.3 !important;
}

/* Ajuste para o alerta de erro */
.alert {
    margin-bottom: 10px !important;
    padding: 6px 12px !important;
}

/* Estilos para a tabela de produtos do XML */
#tabela-produtos-xml {
    width: 100%;
    margin-bottom: 0;
    background-color: #fff;
    border: 1px solid #ddd;
    table-layout: fixed;
}

#tabela-produtos-xml thead th {
    background-color: #f4f4f4;
    border-bottom: 2px solid #ddd;
    color: #333;
    font-weight: 600;
    padding: 10px;
    text-align: left;
    white-space: nowrap;
}

#tabela-produtos-xml thead th:nth-child(1) { width: 26%; } /* Produto XML */
#tabela-produtos-xml thead th:nth-child(2) { width: 7%; }  /* Quantidade */
#tabela-produtos-xml thead th:nth-child(3) { width: 8%; }  /* Valor Unit. */
#tabela-produtos-xml thead th:nth-child(4) { width: 8%; }  /* Total */
#tabela-produtos-xml thead th:nth-child(5) { width: 34%; } /* Produto Sistema */
#tabela-produtos-xml thead th:nth-child(6) { width: 17%; } /* Ação */

#tabela-produtos-xml tbody tr {
    border-bottom: 1px solid #ddd;
}

#tabela-produtos-xml tbody tr:hover {
    background-color: #f9f9f9;
}

#tabela-produtos-xml .table-cell {
    padding: 8px;
    vertical-align: middle;
    color: #333;
    font-size: 12px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

#tabela-produtos-xml .product-description {
    color: #333;
    font-weight: normal;
    text-align: left;
    white-space: normal;
    word-wrap: break-word;
}

#tabela-produtos-xml .btn-mapear {
    min-width: 85px;
    margin: 0;
    white-space: nowrap;
    display: inline-block;
    padding: 4px 6px;
    text-align: center;
}

/* Garantir que a última coluna (ação) tenha espaço suficiente */
#tabela-produtos-xml td:last-child {
    min-width: 140px;
    padding: 8px 2px 8px 20px;
    text-align: left;
}

/* Ajuste para o container da tabela */
.table-responsive {
    width: 100%;
    margin-bottom: 15px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

/* Ajustar z-index do SweetAlert para aparecer na frente dos modais */
.swal2-container {
    z-index: 99999 !important;
}

.swal2-popup {
    z-index: 99999 !important;
}

/* Garantir que o backdrop do SweetAlert também tenha z-index alto */
.swal2-backdrop-show {
    z-index: 99998 !important;
}

/* Classe personalizada para SweetAlert sobre modais */
.swal-on-top {
    z-index: 100000 !important;
}

.swal-on-top .swal2-popup {
    z-index: 100001 !important;
}

/* Ajustar modais do Bootstrap para terem z-index menor que SweetAlert */
.modal {
    z-index: 1050 !important;
}

.modal-backdrop {
    z-index: 1040 !important;
}

.swal2-container-custom {
    z-index: 9999 !important;
}

/* Estilos para o autocomplete dentro do modal */
.ui-autocomplete {
    max-height: none !important;
    overflow-y: visible !important;
    overflow-x: hidden;
    width: 100% !important;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 9999 !important;
}

.ui-autocomplete .ui-menu-item {
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    font-size: 13px;
    white-space: normal;
    word-wrap: break-word;
}

.ui-autocomplete .ui-menu-item:last-child {
    border-bottom: none;
}

.ui-autocomplete .ui-menu-item:hover {
    background-color: #f5f5f5;
}

.ui-autocomplete .ui-menu-item div {
    padding: 0;
    margin: 0;
}

#modalMapearProduto .ui-autocomplete {
    z-index: 9999 !important;
    position: absolute !important;
    width: 100% !important;
    max-width: 100% !important;
}

#modalMapearProduto .modal-body {
    position: relative;
    overflow: visible !important;
}

#modalMapearProduto input:focus {
    outline: none;
    border-color: #66afe9;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
}

/* Ajuste para o container do autocomplete */
.ui-front {
    z-index: 9999 !important;
    position: relative !important;
}

/* Garantir que o dropdown fique visível */
.ui-autocomplete.ui-widget-content {
    border: 1px solid #ddd;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-height: none !important;
    overflow: visible !important;
}

/* Ajuste para o posicionamento do dropdown */
.ui-autocomplete {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    margin-top: 2px !important;
}

/* Desabilitar autocomplete nativo do browser */
#produto_sistema {
    autocomplete: off;
}

/* Fix para z-index do autocomplete */
.ui-front {
    z-index: 9999 !important;
}

/* Aumentar tamanho do modal de importar XML */
#modalImportarXML .modal-dialog {
    width: 99.5%;
    max-width: 2700px;
    margin: 5px auto;
}

#modalImportarXML .modal-content {
    min-height: 600px;
    width: 100%;
}

#modalImportarXML .modal-body {
    max-height: 75vh;
    overflow-y: auto;
    padding: 20px;
}

/* Adicionar estilos específicos para o input file */
#arquivo_xml {
    position: relative;
    z-index: 9999;
    cursor: pointer;
    opacity: 1 !important;
    pointer-events: auto !important;
}

#modalImportarXML .modal-body {
    position: relative;
    z-index: 9999;
}

#modalImportarXML .form-group {
    position: relative;
    z-index: 9999;
}

/* Garantir que o input file seja visível e clicável */
#modalImportarXML input[type="file"] {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    position: relative !important;
    z-index: 9999 !important;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Faturamento de Entrada
            <small>Adicionar Novo</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/faturamentoEntrada">Faturamento de Entrada</a></li>
            <li class="active">Adicionar</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-8">
                        <h3 class="box-title">Adicionar Faturamento de Entrada</h3>
                    </div>
                            <div class="col-md-4 text-right">
                                <button type="button" class="btn btn-info" id="btnMonitorarNotas">
                                    <i class="fa fa-search"></i> Monitor de Notas
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if ($custom_error != '') {
                            echo '<div class="alert alert-danger">' . $custom_error . '</div>';
                        } ?>
                        <form action="<?php echo current_url(); ?>" id="formFaturamentoEntrada" method="post" class="form">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="fornecedor">Fornecedor<span class="required">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="fornecedor" name="fornecedor" required readonly>
                                        <input type="hidden" id="fornecedor_id" name="fornecedor_id">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnBuscarFornecedor"><i class="fa fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="transportadora">Transportadora</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="transportadora" name="transportadora" readonly>
                                        <input type="hidden" id="transportadora_id" name="transportadora_id">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button" id="btnBuscarTransportadora"><i class="fa fa-search"></i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label for="modalidade_frete">Modalidade do Frete</label>
                                    <select class="form-control" id="modalidade_frete" name="modalidade_frete">
                                        <option value="">Selecione...</option>
                                        <option value="0">0 - Emitente</option>
                                        <option value="1">1 - Destinatário</option>
                                        <option value="2">2 - Terceiros</option>
                                        <option value="9">9 - Sem Frete</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="peso_bruto">Peso Bruto (kg)</label>
                                    <input type="text" class="form-control" id="peso_bruto" name="peso_bruto">
                                </div>
                                <div class="col-md-4">
                                    <label for="peso_liquido">Peso Líquido (kg)</label>
                                    <input type="text" class="form-control" id="peso_liquido" name="peso_liquido">
                                </div>
                                <div class="col-md-4">
                                    <label for="volume">Volume (m³)</label>
                                    <input type="text" class="form-control" id="volume" name="volume">
                                </div>
                                <div class="col-md-4">
                                    <label for="operacao_comercial_id">Operação Comercial<span class="required">*</span></label>
                                    <select class="form-control" id="operacao_comercial_id" name="operacao_comercial_id" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($operacoes as $o) { ?>
                                            <option value="<?php echo $o->opc_id; ?>"><?php echo $o->opc_nome; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <div class="col-md-4">
                                    <label for="chave_acesso">Chave de Acesso</label>
                                    <input type="text" class="form-control" id="chave_acesso" name="chave_acesso" maxlength="44">
                                </div>
                                    </div>
                            <div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="numero_nfe" style="margin-bottom: 5px;">
                                            <i class="fa fa-file-text"></i> Número da NFe
                                        </label>
                                        <input type="text" class="form-control input-sm" id="numero_nfe" name="numero_nfe" value="<?php echo set_value('numero_nfe'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="data_entrada" style="margin-bottom: 5px;">
                                            <i class="fa fa-calendar"></i> Data de Entrada
                                        </label>
                                        <input type="text" class="form-control input-sm datepicker" id="data_entrada" name="data_entrada" value="<?php echo date('d/m/Y'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="data_emissao" style="margin-bottom: 5px;">
                                            <i class="fa fa-calendar-o"></i> Data de Emissão
                                        </label>
                                        <input type="text" class="form-control input-sm datepicker" id="data_emissao" name="data_emissao" value="<?php echo date('d/m/Y'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="despesas" style="margin-bottom: 5px;">
                                            <i class="fa fa-money"></i> Despesas
                                        </label>
                                        <input type="text" class="form-control input-sm money" id="despesas" name="despesas" value="<?php echo set_value('despesas', '0,00'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="frete" style="margin-bottom: 5px;">
                                            <i class="fa fa-truck"></i> Frete
                                        </label>
                                        <input type="text" class="form-control input-sm money" id="frete" name="frete" value="<?php echo set_value('frete', '0,00'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-xs-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label style="margin-bottom: 5px;">
                                            <i class="fa fa-list"></i> Itens
                                        </label>
                                        <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
                                            <table class="table table-bordered table-condensed" id="tblItens" style="min-width: 1500px;">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width: 200px;">Produto</th>
                                                        <th style="min-width: 100px;">Quantidade</th>
                                                        <th style="min-width: 120px;">Valor Unitário</th>
                                                        <th style="min-width: 100px;">Desconto</th>
                                                        <th style="min-width: 80px;">CFOP</th>
                                                        <th style="min-width: 80px;">CST</th>
                                                        <th style="min-width: 120px;">Base ICMS</th>
                                                        <th style="min-width: 120px;">Alíquota ICMS</th>
                                                        <th style="min-width: 120px;">Valor ICMS</th>
                                                        <th style="min-width: 120px;">Base ICMS ST</th>
                                                        <th style="min-width: 120px;">Alíquota ICMS ST</th>
                                                        <th style="min-width: 120px;">Valor ICMS ST</th>
                                                        <th style="min-width: 120px;">Valor IPI</th>
                                                        <th style="min-width: 120px;">Total</th>
                                                        <th style="min-width: 50px;">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr id="row-edicao">
                                                        <td>
                                                            <input type="text" class="form-control input-sm produto_nome" name="produto_nome[]" style="width: 100%" />
                                                            <input type="hidden" class="produto_id" name="produtos[]" />
                                                        </td>
                                                        <td><input type="text" class="form-control input-sm quantidade" name="quantidades[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money valor" name="valores[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money desconto" name="descontos[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm cfop" name="cfop[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm cst" name="cst[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money base_icms" name="bases_icms[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm aliquota" name="aliquotas[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money valor_icms" name="valores_icms[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money base_icms_st" name="bases_icms_st[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm aliquota_st" name="aliquotas_st[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money valor_icms_st" name="valores_icms_st[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money valor_ipi" name="valores_ipi[]" style="width: 100%" /></td>
                                                        <td><input type="text" class="form-control input-sm money total" name="totais[]" readonly style="width: 100%" /></td>
                                                        <td>
                                                            <button type="button" class="btn btn-success btn-xs btn-adicionar">
                                                                <i class="fa fa-plus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div id="lista-itens" class="table-responsive" style="max-width: 100%; overflow-x: auto; margin-top: 15px;">
                                            <table class="table table-bordered table-condensed" style="min-width: 1500px;">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width: 200px;">Produto</th>
                                                        <th style="min-width: 100px;">Quantidade</th>
                                                        <th style="min-width: 120px;">Valor Unitário</th>
                                                        <th style="min-width: 100px;">Desconto</th>
                                                        <th style="min-width: 80px;">CFOP</th>
                                                        <th style="min-width: 80px;">CST</th>
                                                        <th style="min-width: 120px;">Base ICMS</th>
                                                        <th style="min-width: 120px;">Alíquota ICMS</th>
                                                        <th style="min-width: 120px;">Valor ICMS</th>
                                                        <th style="min-width: 120px;">Base ICMS ST</th>
                                                        <th style="min-width: 120px;">Alíquota ICMS ST</th>
                                                        <th style="min-width: 120px;">Valor ICMS ST</th>
                                                        <th style="min-width: 120px;">Valor IPI</th>
                                                        <th style="min-width: 120px;">Total</th>
                                                        <th style="min-width: 100px;">Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="btn-group" style="margin-top: 5px;">
                                            <button type="button" class="btn btn-info btn-xs" id="btn-limpar-itens">
                                                <i class="fa fa-eraser"></i> Limpar Itens
                                            </button>
                                            <button type="button" class="btn btn-warning btn-xs" id="btn-recalcular">
                                                <i class="fa fa-calculator"></i> Recalcular Totais
                                        </button>
                                            <button type="button" class="btn btn-success btn-xs" id="btn-importar-xml">
                                                <i class="fa fa-upload"></i> Importar XML
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="total_base_icms" style="margin-bottom: 5px;">Total Base ICMS</label>
                                        <input type="text" class="form-control input-sm money" id="total_base_icms" name="total_base_icms" readonly style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="total_icms" style="margin-bottom: 5px;">Total ICMS</label>
                                        <input type="text" class="form-control input-sm money" id="total_icms" name="total_icms" readonly style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="total_base_icms_st" style="margin-bottom: 5px;">Total Base ICMS ST</label>
                                        <input type="text" class="form-control input-sm money" id="total_base_icms_st" name="total_base_icms_st" readonly style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="total_icms_st" style="margin-bottom: 5px;">Total ICMS ST</label>
                                        <input type="text" class="form-control input-sm money" id="total_icms_st" name="total_icms_st" readonly style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="total_ipi" style="margin-bottom: 5px;">Total IPI</label>
                                        <input type="text" class="form-control input-sm money" id="total_ipi" name="total_ipi" readonly style="width: 100%" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12" style="padding: 5px;">
                                    <div class="form-group">
                                        <label for="total_produtos">Valor dos Produtos<span class="required">*</span></label>
                                        <input type="text" class="form-control money" id="total_produtos" name="total_produtos" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12" style="padding: 5px;">
                                    <div class="form-group">
                                        <label for="total_nota">Valor Total da Nota<span class="required">*</span></label>
                                        <input type="text" class="form-control money" id="total_nota" name="total_nota" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12" style="padding: 5px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Salvar
                                    </button>
                                    <a href="<?php echo base_url() ?>index.php/faturamentoEntrada" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Monitor de Notas -->
<div class="modal fade" id="modalMonitorarNotas" tabindex="-1" role="dialog" aria-labelledby="modalMonitorarNotasLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalMonitorarNotasLabel">Monitor de Notas</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <input type="text" class="form-control" id="searchNota" placeholder="Buscar por chave ou número...">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="tabelaNotas">
                        <thead>
                            <tr>
                                <th>Fornecedor</th>
                                <th>Número</th>
                                <th>Série</th>
                                <th>Data Emissão</th>
                                <th>Valor</th>
                                <th>Chave</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center">Carregando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Importar XML -->
<div class="modal fade" id="modalImportarXML" tabindex="-1" role="dialog" aria-labelledby="modalImportarXMLLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalImportarXMLLabel">Importar XML da NFe</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="arquivo_xml">Selecione o arquivo XML:</label>
                            <input type="file" class="form-control" id="arquivo_xml" name="arquivo_xml" accept=".xml" required style="position: relative; z-index: 9999;">
                        </div>
                    </div>
                </div>
                <div id="xml-preview" style="display:none;">
                    <h4>Informações da NFe:</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Fornecedor:</strong> <span id="xml-fornecedor"></span></p>
                            <p><strong>CNPJ/CPF:</strong> <span id="xml-documento"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Número NFe:</strong> <span id="xml-numero"></span></p>
                            <p><strong>Valor Total:</strong> <span id="xml-valor"></span></p>
                        </div>
                    </div>
                    <h4>Produtos da NFe:</h4>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="btn-group" style="margin-bottom: 10px;">
                                <button type="button" class="btn btn-success btn-sm" id="btn-mapear-todos">
                                    <i class="fa fa-check-square-o"></i> Cadastrar Todos como Novos Produtos
                                </button>
                                <button type="button" class="btn btn-info btn-sm" id="btn-mapear-nao-mapeados">
                                    <i class="fa fa-plus-circle"></i> Mapear Produtos Não Mapeados como Novo
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="tabela-produtos-xml">
                            <thead>
                                <tr>
                                    <th>Produto XML</th>
                                    <th>Quantidade</th>
                                    <th>Valor Unit.</th>
                                    <th>Total</th>
                                    <th>Produto Sistema</th>
                                    <th>Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-processar-xml" style="display:none;">Processar XML</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Mapear Produto -->
<div class="modal fade" id="modalMapearProduto" tabindex="-1" role="dialog" aria-labelledby="modalMapearProdutoLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalMapearProdutoLabel">Mapear Produto</h4>
            </div>
            <div class="modal-body">
                <p><strong>Produto do XML:</strong> <span id="produto-xml-nome"></span></p>
                <div class="form-group">
                    <label>Escolha uma opção:</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="opcao_produto" value="existente" checked>
                            Mapear para produto existente
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="opcao_produto" value="novo">
                            Cadastrar novo produto
                        </label>
                    </div>
                </div>
                <div id="div-produto-existente">
                    <div class="form-group">
                        <label for="produto_sistema">Produto do Sistema:</label>
                        <input type="text" class="form-control" id="produto_sistema" name="produto_sistema" placeholder="Digite para buscar...">
                        <input type="hidden" id="produto_sistema_id" name="produto_sistema_id">
                    </div>
                </div>
                <div id="div-produto-novo" style="display:none;">
                    <div class="alert alert-info">
                        <strong><i class="fa fa-info-circle"></i> Cadastro Automático</strong><br>
                        O produto será cadastrado automaticamente com o nome do XML: <br>
                        <strong id="nome-produto-xml-preview"></strong>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-mapeamento">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Inicializar datepicker
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });

        // Inicializar máscaras de moeda
        $('.money').maskMoney({
            prefix: 'R$ ',
            thousands: '.',
            decimal: ',',
            allowZero: true,
            allowNegative: false
        });

        // Função para converter valor formatado em número
        function converterParaNumero(valor) {
            if (!valor) return '0.00';
            // Remove R$, pontos e substitui vírgula por ponto
            return valor.toString().replace('R$ ', '').replace(/\./g, '').replace(',', '.');
        }

        // Função para formatar valores antes de enviar o formulário
        function formatarValoresFormulario() {
            // Formatar valores dos itens
            $('.valor').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            $('.desconto').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            $('.base_icms').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            $('.valor_icms').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            $('.base_icms_st').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            $('.valor_icms_st').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            $('.total').each(function() {
                var valor = $(this).val();
                $(this).val(converterParaNumero(valor));
            });

            // Formatar valores gerais
            $('#despesas').val(converterParaNumero($('#despesas').val()));
            $('#frete').val(converterParaNumero($('#frete').val()));
            $('#total_nota').val(converterParaNumero($('#total_nota').val()));
            $('#total_base_icms').val(converterParaNumero($('#total_base_icms').val()));
            $('#total_icms').val(converterParaNumero($('#total_icms').val()));
            $('#total_base_icms_st').val(converterParaNumero($('#total_base_icms_st').val()));
            $('#total_icms_st').val(converterParaNumero($('#total_icms_st').val()));
            $('#total_ipi').val(converterParaNumero($('#total_ipi').val()));
        }

        // Adicionar evento de submit do formulário
        $('#formFaturamentoEntrada').on('submit', function(e) {
            e.preventDefault(); // Prevenir o envio padrão do formulário
            
            console.log('=== INÍCIO DO PROCESSO DE SUBMISSÃO ===');
            
            // Validar campos obrigatórios
            if (!$('#fornecedor_id').val()) {
                console.error('Erro: Fornecedor não selecionado');
                alert('Por favor, selecione um fornecedor.');
                return false;
            }

            if (!$('#operacao_comercial_id').val()) {
                console.error('Erro: Operação comercial não selecionada');
                alert('Por favor, selecione uma operação comercial.');
                return false;
            }

            // Verificar se há pelo menos um item
            if ($('#lista-itens tbody tr').length === 0) {
                console.error('Erro: Nenhum item adicionado');
                alert('Por favor, adicione pelo menos um item.');
                return false;
            }

            // Verificar se todos os itens têm produto selecionado
            var temProdutoInvalido = false;
            $('#lista-itens tbody tr').each(function() {
                var produto = $(this).find('td:first').text().trim();
                if (!produto) {
                    console.error('Erro: Item sem produto selecionado');
                    temProdutoInvalido = true;
                    return false;
                }
            });

            if (temProdutoInvalido) {
                alert('Por favor, selecione um produto para todos os itens.');
                return false;
            }

            console.log('Validações básicas passaram...');

            // Coletar dados dos itens
            var produtos = [];
            var quantidades = [];
            var valores = [];
            var descontos = [];
            var cfops = [];
            var csts = [];
            var bases_icms = [];
            var aliquotas = [];
            var valores_icms = [];
            var bases_icms_st = [];
            var aliquotas_st = [];
            var valores_icms_st = [];
            var valores_ipi = [];
            var totais = [];
            
            // Adicionar arrays para dados específicos dos produtos
            var produtos_descricao = [];
            var produtos_codigo = [];
            var produtos_ncm = [];

            console.log('Coletando dados dos itens...');
            $('#lista-itens tbody tr').each(function(index) {
                var row = $(this);
                var data = row.find('.btn-editar').data('row');
                
                console.log('Processando item ' + (index + 1) + ':', data);
                
                produtos.push(data.produto_id);
                quantidades.push(data.quantidade);
                valores.push(converterParaNumero(data.valor));
                descontos.push(converterParaNumero(data.desconto));
                cfops.push(data.cfop);
                csts.push(data.cst);
                bases_icms.push(converterParaNumero(data.base_icms));
                aliquotas.push(converterParaNumero(data.aliquota));
                valores_icms.push(converterParaNumero(data.valor_icms));
                bases_icms_st.push(converterParaNumero(data.base_icms_st));
                aliquotas_st.push(converterParaNumero(data.aliquota_st));
                valores_icms_st.push(converterParaNumero(data.valor_icms_st));
                valores_ipi.push(converterParaNumero(data.valor_ipi));
                totais.push(converterParaNumero(data.total));
                
                // Adicionar dados específicos do produto
                produtos_descricao.push(data.produto_descricao || data.produto || '');
                produtos_codigo.push(data.produto_codigo || '');
                produtos_ncm.push(data.produto_ncm || '');
            });

            console.log('Dados dos itens coletados:', {
                produtos: produtos,
                quantidades: quantidades,
                valores: valores,
                descontos: descontos,
                cfops: cfops,
                csts: csts,
                bases_icms: bases_icms,
                aliquotas: aliquotas,
                valores_icms: valores_icms,
                bases_icms_st: bases_icms_st,
                aliquotas_st: aliquotas_st,
                valores_icms_st: valores_icms_st,
                valores_ipi: valores_ipi,
                totais: totais,
                produtos_descricao: produtos_descricao,
                produtos_codigo: produtos_codigo,
                produtos_ncm: produtos_ncm
            });

            // Adicionar campos hidden com os dados dos itens
            var form = $(this);
            console.log('Removendo campos hidden existentes...');
            form.find('input[name="produtos[]"]').remove();
            form.find('input[name="quantidades[]"]').remove();
            form.find('input[name="valores[]"]').remove();
            form.find('input[name="descontos[]"]').remove();
            form.find('input[name="cfop[]"]').remove();
            form.find('input[name="cst[]"]').remove();
            form.find('input[name="bases_icms[]"]').remove();
            form.find('input[name="aliquotas[]"]').remove();
            form.find('input[name="valores_icms[]"]').remove();
            form.find('input[name="bases_icms_st[]"]').remove();
            form.find('input[name="aliquotas_st[]"]').remove();
            form.find('input[name="valores_icms_st[]"]').remove();
            form.find('input[name="valores_ipi[]"]').remove();
            form.find('input[name="totais[]"]').remove();
            form.find('input[name="produtos_descricao[]"]').remove();
            form.find('input[name="produtos_codigo[]"]').remove();
            form.find('input[name="produtos_ncm[]"]').remove();

            // Adicionar despesas e frete com valores formatados
            var despesas = converterParaNumero($('#despesas').val()) || '0.00';
            var frete = converterParaNumero($('#frete').val()) || '0.00';
            var peso_bruto = converterParaNumero($('#peso_bruto').val()) || '0.00';
            var peso_liquido = converterParaNumero($('#peso_liquido').val()) || '0.00';
            
            console.log('Valores de despesas e frete:', { despesas: despesas, frete: frete });
            
            form.append('<input type="hidden" name="despesas" value="' + despesas + '">');
            form.append('<input type="hidden" name="frete" value="' + frete + '">');
            form.append('<input type="hidden" name="peso_bruto" value="' + peso_bruto + '">');
            form.append('<input type="hidden" name="peso_liquido" value="' + peso_liquido + '">');
            form.append('<input type="hidden" name="transportadora_id" value="' + $('#transportadora_id').val() + '">');
            form.append('<input type="hidden" name="modalidade_frete" value="' + $('#modalidade_frete').val() + '">');

            // Adicionar outros campos obrigatórios
            console.log('Adicionando campos obrigatórios...');
            form.append('<input type="hidden" name="data_entrada" value="' + $('#data_entrada').val() + '">');
            form.append('<input type="hidden" name="data_emissao" value="' + $('#data_emissao').val() + '">');
            form.append('<input type="hidden" name="chave_acesso" value="' + $('#chave_acesso').val() + '">');
            form.append('<input type="hidden" name="numero_nfe" value="' + $('#numero_nfe').val() + '">');

            console.log('Adicionando dados dos itens ao formulário...');
            produtos.forEach(function(valor) {
                form.append('<input type="hidden" name="produtos[]" value="' + valor + '">');
            });
            quantidades.forEach(function(valor) {
                form.append('<input type="hidden" name="quantidades[]" value="' + valor + '">');
            });
            valores.forEach(function(valor) {
                form.append('<input type="hidden" name="valores[]" value="' + valor + '">');
            });
            descontos.forEach(function(valor) {
                form.append('<input type="hidden" name="descontos[]" value="' + valor + '">');
            });
            cfops.forEach(function(valor) {
                form.append('<input type="hidden" name="cfop[]" value="' + valor + '">');
            });
            csts.forEach(function(valor) {
                form.append('<input type="hidden" name="cst[]" value="' + valor + '">');
            });
            bases_icms.forEach(function(valor) {
                form.append('<input type="hidden" name="bases_icms[]" value="' + valor + '">');
            });
            aliquotas.forEach(function(valor) {
                form.append('<input type="hidden" name="aliquotas[]" value="' + valor + '">');
            });
            valores_icms.forEach(function(valor) {
                form.append('<input type="hidden" name="valores_icms[]" value="' + valor + '">');
            });
            bases_icms_st.forEach(function(valor) {
                form.append('<input type="hidden" name="bases_icms_st[]" value="' + valor + '">');
            });
            aliquotas_st.forEach(function(valor) {
                form.append('<input type="hidden" name="aliquotas_st[]" value="' + valor + '">');
            });
            valores_icms_st.forEach(function(valor) {
                form.append('<input type="hidden" name="valores_icms_st[]" value="' + valor + '">');
            });
            valores_ipi.forEach(function(valor) {
                form.append('<input type="hidden" name="valores_ipi[]" value="' + valor + '">');
            });
            totais.forEach(function(valor) {
                form.append('<input type="hidden" name="totais[]" value="' + valor + '">');
            });
            
            // Adicionar dados específicos dos produtos
            produtos_descricao.forEach(function(valor) {
                form.append('<input type="hidden" name="produtos_descricao[]" value="' + valor + '">');
            });
            produtos_codigo.forEach(function(valor) {
                form.append('<input type="hidden" name="produtos_codigo[]" value="' + valor + '">');
            });
            produtos_ncm.forEach(function(valor) {
                form.append('<input type="hidden" name="produtos_ncm[]" value="' + valor + '">');
            });

            console.log('Formulário preparado para envio...');
            console.log('Dados do formulário:', form.serialize());

            // Enviar formulário via AJAX para melhor tratamento de erros
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log('Resposta do servidor:', response);
                    if (response.success) {
                        window.location.href = '<?php echo base_url(); ?>index.php/faturamentoEntrada';
                    } else {
                        console.error('Erro retornado pelo servidor:', response);
                        let errorMessage = response.message || 'Erro ao adicionar faturamento de entrada.';
                        if (response.error) {
                            errorMessage += '\nDetalhes: ' + response.error;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: errorMessage,
                            footer: 'Verifique o console (F12) para mais detalhes'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro na requisição:', {
                        status: status,
                        error: error,
                        response: xhr.responseText,
                        xhr: xhr
                    });
                    
                    let errorMessage = 'Erro ao adicionar faturamento de entrada.';
                    let errorDetails = '';
                    
                    // Verificar se a resposta é HTML
                    if (xhr.responseText && xhr.responseText.trim().startsWith('<!DOCTYPE')) {
                        // Tentar extrair mensagem de erro do HTML
                        const tempDiv = document.createElement('div');
                        tempDiv.innerHTML = xhr.responseText;
                        
                        // Procurar por mensagens de erro comuns no HTML
                        const errorElement = tempDiv.querySelector('.alert-danger, .error, .alert');
                        if (errorElement) {
                            errorDetails = errorElement.textContent.trim();
                        } else {
                            // Se não encontrar uma mensagem específica, mostrar o título da página
                            const titleElement = tempDiv.querySelector('title');
                            if (titleElement) {
                                errorDetails = titleElement.textContent.trim();
                            }
            }

                        // Se não encontrou nenhuma mensagem específica, mostrar uma mensagem genérica
                        if (!errorDetails) {
                            errorDetails = 'O servidor retornou uma página de erro. Verifique os logs do servidor para mais detalhes.';
                        }
                    } else {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                            if (response.error) {
                                errorDetails = response.error;
                            }
                        } catch (e) {
                            console.error('Erro ao parsear resposta:', e);
                            errorDetails = 'Erro ao processar resposta do servidor. Verifique os logs do servidor.';
                        }
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro no Servidor',
                        text: errorMessage,
                        footer: errorDetails,
                        width: '600px'
                    });
                }
            });
        });

        // Autocomplete para fornecedor com melhorias
        $("#fornecedor").autocomplete({
            source: "<?php echo base_url(); ?>index.php/faturamentoEntrada/autoCompleteFornecedor",
            minLength: 1,
            select: function(event, ui) {
                event.preventDefault();
                if (ui.item.id === null) {
                    window.location.href = "<?php echo base_url(); ?>index.php/fornecedores/adicionar";
                    return false;
                }
                $("#fornecedor_id").val(ui.item.id);
                $(this).val(ui.item.label);
                $(this).trigger('change');
                return false;
            }
        });

        // Autocomplete para transportadora
        $("#transportadora").autocomplete({
            source: "<?php echo base_url(); ?>index.php/faturamentoEntrada/autoCompleteTransportadora",
            minLength: 1,
            select: function(event, ui) {
                event.preventDefault();
                if (ui.item.id === null) {
                    window.location.href = "<?php echo base_url(); ?>index.php/fornecedores/adicionar";
                    return false;
                }
                $("#transportadora_id").val(ui.item.id);
                $(this).val(ui.item.label);
                $(this).trigger('change');
                return false;
            }
        });

        // Função para verificar se é operação interestadual
        function isOperacaoInterestadual(fornecedorEstado) {
            // Buscar estado do emitente
            return $.get('<?= base_url(); ?>index.php/mapos/getEstadoEmitente')
                .then(function(response) {
                    if (response && response.estado) {
                        return fornecedorEstado !== response.estado;
                    }
                    return false;
                })
                .fail(function() {
                    return false;
                });
        }

        // Função para buscar classificação fiscal
        function getClassificacaoFiscal(operacaoId, fornecedorId) {
            return $.get('<?= base_url(); ?>index.php/faturamentoEntrada/getClassificacaoFiscal', {
                operacao_id: operacaoId,
                fornecedor_id: fornecedorId
            });
        }

        // Evento para buscar tributação quando operação ou fornecedor mudar
        $('#operacao_comercial_id, #fornecedor_id').on('change', function() {
            var operacaoId = $('#operacao_comercial_id').val();
            var fornecedorId = $('#fornecedor_id').val();
            
            if (operacaoId && fornecedorId) {
                // Primeiro, buscar a classificação fiscal
                getClassificacaoFiscal(operacaoId, fornecedorId)
                    .then(function(response) {
                        if (response.success && response.data && response.data.length > 0) {
                            // Se encontrou classificação fiscal, usar os valores dela
                            window.tributacaoAtual = {
                                cfop: response.data[0].cfop,
                                cst: response.data[0].cst,
                                aliquota_icms: response.data[0].aliquota_icms || '0',
                                aliquota_icms_st: response.data[0].aliquota_icms_st || '0'
                            };
                        } else {
                            // Se não encontrou classificação fiscal, buscar estado do fornecedor
                            $.get('<?= base_url(); ?>index.php/faturamentoEntrada/getFornecedorEstado/' + fornecedorId)
                                .then(function(estadoResponse) {
                                    if (estadoResponse && estadoResponse.estado) {
                                        return isOperacaoInterestadual(estadoResponse.estado)
                                            .then(function(isInterestadual) {
                                                // Definir CFOP padrão baseado na operação interestadual
                                                window.tributacaoAtual = {
                                                    cfop: isInterestadual ? '2102' : '1102', // CFOP para entrada
                                                    cst: '00',
                                                    aliquota_icms: '0',
                                                    aliquota_icms_st: '0'
                                                };
                                            });
                                    }
                                })
                                .fail(function() {
                                    // Em caso de erro, usa valores padrão para operação estadual
                                    window.tributacaoAtual = {
                                        cfop: '1102', // CFOP padrão para entrada estadual
                                        cst: '00',
                                        aliquota_icms: '0',
                                        aliquota_icms_st: '0'
                                    };
                                });
                        }
                    })
                    .fail(function() {
                        // Em caso de erro, usa valores padrão para operação estadual
                        window.tributacaoAtual = {
                            cfop: '1102', // CFOP padrão para entrada estadual
                            cst: '00',
                            aliquota_icms: '0',
                            aliquota_icms_st: '0'
                        };
                    });
            }
        });

        // Função para calcular o total do item
        function calcularTotalItem(row) {
            var quantidade = parseFloat(row.find('.quantidade').val().replace(',', '.')) || 0;
            var valorUnitario = parseFloat(row.find('.valor').val().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
            var desconto = parseFloat(row.find('.desconto').val().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
            var cst = row.find('.cst').val();
            
            var subtotal = quantidade * valorUnitario;
            var total = subtotal - desconto;
            
            // Atualizar o campo total com formatação correta
            row.find('.total').val(total.toFixed(2).replace('.', ','));
            
            // Atualizar a base do ICMS
            row.find('.base_icms').val(subtotal.toFixed(2).replace('.', ','));
            
            // Calcular ICMS
            var aliquota = parseFloat(row.find('.aliquota').val().replace(',', '.')) || 0;
            var valorICMS = subtotal * (aliquota / 100);
            row.find('.valor_icms').val(valorICMS.toFixed(2).replace('.', ','));
            
            // Calcular ICMS ST apenas para CST 10, 30 ou 70
            if (cst === '10' || cst === '30' || cst === '70') {
                var aliquotaST = parseFloat(row.find('.aliquota_st').val().replace(',', '.')) || 0;
                var baseICMSST = subtotal * 1.4; // Aumenta 40% para base ST
                var valorICMSST = (baseICMSST * (aliquotaST / 100)) - valorICMS;
                
                row.find('.base_icms_st').val(baseICMSST.toFixed(2).replace('.', ','));
                row.find('.valor_icms_st').val(valorICMSST.toFixed(2).replace('.', ','));
            } else {
                // Zerar campos de ST para outros CSTs
                row.find('.base_icms_st').val('0,00');
                row.find('.valor_icms_st').val('0,00');
                row.find('.aliquota_st').val('0,00');
            }
        }

        // Evento para calcular total quando CST mudar
        $(document).on('change', '.cst', function() {
            var row = $(this).closest('tr');
            var cst = $(this).val();
            
            // Habilitar/desabilitar campos de ST baseado no CST
            if (cst === '10' || cst === '30' || cst === '70') {
                row.find('.aliquota_st').prop('disabled', false);
            } else {
                row.find('.aliquota_st').prop('disabled', true);
                row.find('.aliquota_st').val('0,00');
            }
            
            calcularTotalItem(row);
        });

        // Evento para calcular total quando quantidade ou valor mudar
        $(document).on('change', '.quantidade, .valor, .desconto', function() {
            var row = $(this).closest('tr');
            calcularTotalItem(row);
        });

        // Evento para calcular total quando alíquota mudar
        $(document).on('change', '.aliquota, .aliquota_st', function() {
            var row = $(this).closest('tr');
            calcularTotalItem(row);
        });

        // Modificar a função de autocomplete do produto
        $(document).on('focus', '.produto_nome', function() {
            // Verificar se fornecedor foi selecionado
            if (!$('#fornecedor_id').val()) {
                alert('Por favor, selecione um fornecedor antes de adicionar produtos.');
                $(this).blur();
                return false;
            }

            // Verificar se operação comercial foi selecionada
            if (!$('#operacao_comercial_id').val()) {
                alert('Por favor, selecione uma operação comercial antes de adicionar produtos.');
                $(this).blur();
                return false;
            }

            var input = $(this);
            input.autocomplete({
                source: "<?php echo base_url(); ?>index.php/faturamentoEntrada/autoCompleteProduto",
                minLength: 1,
                appendTo: input.parent(),
                select: function(event, ui) {
                    event.preventDefault();
                    if (ui.item) {
                        var row = input.closest('tr');
                        
                        // Atualizar o nome do produto
                        input.val(ui.item.label);
                        
                        // Atualizar o ID do produto no campo hidden
                        row.find('.produto_id').val(ui.item.id);
                        
                        // Atualizar quantidade e valor usando preço de compra
                        if(ui.item.estoque) {
                            row.find('.quantidade').val('1').attr('max', ui.item.estoque);
                        }
                        if(ui.item.precoCompra) {
                            row.find('.valor').val(ui.item.precoCompra).trigger('change');
                        }

                        // Calcular o total inicial
                        calcularTotalItem(row);

                        // Se tiver tributação configurada, aplicar
                        if (window.tributacaoAtual) {
                            console.log('Aplicando tributação:', window.tributacaoAtual);
                            
                            row.find('.cfop').val(window.tributacaoAtual.cfop);
                            row.find('.cst').val(window.tributacaoAtual.cst);
                            row.find('.aliquota').val(window.tributacaoAtual.aliquota_icms);
                            
                            // Recalcular totais após aplicar tributação
                            calcularTotalItem(row);
                        } else {
                            console.log('Usando tributação padrão');
                            // Buscar regime tributário
                            $.get('<?= base_url(); ?>index.php/mapos/getRegimeTributario', function(regimeResponse) {
                                let regimeTributario = regimeResponse.regime_tributario || 'normal';
                                let cstPadrao = regimeTributario === 'simples_nacional' ? '41' : '00';
                                
                                // Verificar se é operação interestadual
                                $.get('<?= base_url(); ?>index.php/faturamentoEntrada/getFornecedorEstado/' + $('#fornecedor_id').val())
                                    .then(function(estadoResponse) {
                                        if (estadoResponse && estadoResponse.estado) {
                                            return isOperacaoInterestadual(estadoResponse.estado)
                                                .then(function(isInterestadual) {
                                                    let cfopPadrao = isInterestadual ? '2102' : '1102';
                                                    
                                                    row.find('.cfop').val(cfopPadrao);
                                row.find('.cst').val(cstPadrao);
                                row.find('.aliquota').val('0');
                                
                                                    // Recalcular totais após aplicar tributação padrão
                                                    calcularTotalItem(row);
                                                });
                                        }
                            })
                            .fail(function() {
                                        // Em caso de erro, usa valores padrão para operação estadual
                                        row.find('.cfop').val('1102');
                                        row.find('.cst').val(cstPadrao);
                                        row.find('.aliquota').val('0');
                                        calcularTotalItem(row);
                                    });
                            })
                            .fail(function() {
                                // Em caso de erro, usa valores padrão para operação estadual
                                row.find('.cfop').val('1102');
                                row.find('.cst').val('00');
                                row.find('.aliquota').val('0');
                                calcularTotalItem(row);
                            });
                        }
                        
                        // Focar no próximo campo
                        row.find('.quantidade').focus();
                    }
                    return false;
                }
            });
        });

        // Função para calcular totais gerais
        function calcularTotaisGerais() {
            var totalBaseICMS = 0;
            var totalICMS = 0;
            var totalBaseICMSST = 0;
            var totalICMSST = 0;
            var totalProdutos = 0;
            var totalIPI = 0;
            var totalNota = 0;

            // Calcular totais dos itens na lista
            $('#lista-itens tbody tr').each(function() {
                var row = $(this);
                var data = row.find('.btn-editar').data('row');
                
                var baseICMS = parseFloat(converterParaNumero(data.base_icms)) || 0;
                var valorICMS = parseFloat(converterParaNumero(data.valor_icms)) || 0;
                var baseICMSST = parseFloat(converterParaNumero(data.base_icms_st)) || 0;
                var valorICMSST = parseFloat(converterParaNumero(data.valor_icms_st)) || 0;
                var valorIPI = parseFloat(converterParaNumero(data.valor_ipi)) || 0;
                var totalItem = parseFloat(converterParaNumero(data.total)) || 0;

                totalBaseICMS += baseICMS;
                totalICMS += valorICMS;
                totalBaseICMSST += baseICMSST;
                totalICMSST += valorICMSST;
                totalIPI += valorIPI;
                totalProdutos += totalItem;
            });

            // Calcular total da nota
            var frete = parseFloat(converterParaNumero($('#frete').val())) || 0;
            var despesas = parseFloat(converterParaNumero($('#despesas').val())) || 0;

            totalNota = totalProdutos + frete + despesas + totalIPI;

            // Atualizar campos de total com formatação correta
            $('#total_base_icms').val(totalBaseICMS.toFixed(2).replace('.', ','));
            $('#total_icms').val(totalICMS.toFixed(2).replace('.', ','));
            $('#total_base_icms_st').val(totalBaseICMSST.toFixed(2).replace('.', ','));
            $('#total_icms_st').val(totalICMSST.toFixed(2).replace('.', ','));
            $('#total_ipi').val(totalIPI.toFixed(2).replace('.', ','));
            $('#total_produtos').val(totalProdutos.toFixed(2).replace('.', ','));
            $('#total_nota').val(totalNota.toFixed(2).replace('.', ','));
        }

        // Função para adicionar item à lista
        function adicionarItemALista() {
            var row = $('#row-edicao');
            var produto = row.find('.produto_nome').val();
            var produtoId = row.find('.produto_id').val();
            
            if (!produto || !produtoId) {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Por favor, selecione um produto.',
                    icon: 'warning'
                });
                return false;
            }

            var quantidade = row.find('.quantidade').val();
            if (!quantidade || parseFloat(quantidade) <= 0) {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Por favor, informe uma quantidade válida.',
                    icon: 'warning'
                });
                return false;
            }

            var newRow = $('<tr>');
            newRow.html(`
                <td>${produto}</td>
                <td>${quantidade}</td>
                <td>${row.find('.valor').val()}</td>
                <td>${row.find('.desconto').val()}</td>
                <td>${row.find('.cfop').val()}</td>
                <td>${row.find('.cst').val()}</td>
                <td>${row.find('.base_icms').val()}</td>
                <td>${row.find('.aliquota').val()}</td>
                <td>${row.find('.valor_icms').val()}</td>
                <td>${row.find('.base_icms_st').val()}</td>
                <td>${row.find('.aliquota_st').val()}</td>
                <td>${row.find('.valor_icms_st').val()}</td>
                <td>${row.find('.valor_ipi').val()}</td>
                <td>${row.find('.total').val()}</td>
                <td>
                    <button type="button" class="btn btn-primary btn-xs btn-editar" data-row='${JSON.stringify({
                        produto: produto,
                        produto_id: produtoId,
                        quantidade: quantidade,
                        valor: row.find('.valor').val(),
                        desconto: row.find('.desconto').val(),
                        cfop: row.find('.cfop').val(),
                        cst: row.find('.cst').val(),
                        base_icms: row.find('.base_icms').val(),
                        aliquota: row.find('.aliquota').val(),
                        valor_icms: row.find('.valor_icms').val(),
                        base_icms_st: row.find('.base_icms_st').val(),
                        aliquota_st: row.find('.aliquota_st').val(),
                        valor_icms_st: row.find('.valor_icms_st').val(),
                        valor_ipi: row.find('.valor_ipi').val(),
                        total: row.find('.total').val()
                    })}'>
                        <i class="fa fa-pencil"></i>
                    </button>
                    <button type="button" class="btn btn-danger btn-xs btn-remover">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `);

            $('#lista-itens tbody').append(newRow);
            limparCamposEdicao();
            calcularTotaisGerais(); // Calcular totais após adicionar item
        }

        // Função para limpar campos de edição
        function limparCamposEdicao() {
            var row = $('#row-edicao');
            row.find('input').val('');
            row.find('.produto_nome').focus();
        }

        // Evento para adicionar item
        $(document).on('click', '.btn-adicionar', function() {
            adicionarItemALista();
        });

        // Evento para editar item
        $(document).on('click', '.btn-editar', function() {
            var data = $(this).data('row');
            var row = $('#row-edicao');
            
            row.find('.produto_nome').val(data.produto);
            row.find('.produto_id').val(data.produto_id);
            row.find('.quantidade').val(data.quantidade);
            row.find('.valor').val(data.valor);
            row.find('.desconto').val(data.desconto);
            row.find('.cfop').val(data.cfop);
            row.find('.cst').val(data.cst);
            row.find('.base_icms').val(data.base_icms);
            row.find('.aliquota').val(data.aliquota);
            row.find('.valor_icms').val(data.valor_icms);
            row.find('.base_icms_st').val(data.base_icms_st);
            row.find('.aliquota_st').val(data.aliquota_st);
            row.find('.valor_icms_st').val(data.valor_icms_st);
            row.find('.valor_ipi').val(data.valor_ipi);
            row.find('.total').val(data.total);

            // Remover a linha da lista
            $(this).closest('tr').remove();
            
            // Recalcular totais
            calcularTotaisGerais();
            
            // Focar no campo de produto
            row.find('.produto_nome').focus();
        });

        // Evento para remover item
        $(document).on('click', '.btn-remover', function() {
            if ($('#lista-itens tbody tr').length > 1) {
                Swal.fire({
                    title: 'Tem certeza?',
                    text: "Este item será removido!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sim, remover!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                $(this).closest('tr').remove();
                        calcularTotaisGerais(); // Calcular totais após remover item
                        Swal.fire(
                            'Removido!',
                            'O item foi removido com sucesso.',
                            'success'
                        );
                    }
                });
            } else {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Não é possível remover o último item.',
                    icon: 'warning'
                });
            }
        });

        // Limpar todos os itens
        $('#btn-limpar-itens').click(function() {
            Swal.fire({
                title: 'Tem certeza?',
                text: "Todos os itens serão removidos!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, limpar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#lista-itens tbody').empty();
                    limparCamposEdicao();
                    calcularTotaisGerais(); // Calcular totais após limpar itens
                    Swal.fire(
                        'Limpo!',
                        'Todos os itens foram removidos.',
                        'success'
                    );
            }
        });
    });

        // Recalcular totais manualmente
        $('#btn-recalcular').click(function() {
            calcularTotaisGerais();
        Swal.fire({
                title: 'Recalculado!',
                text: 'Os totais foram atualizados.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
        });
    });

    $('#btnMonitorarNotas').click(function() {
        $('#modalMonitorarNotas').modal('show');
        consultarNotas();
    });

        function consultarNotas() {
            $.ajax({
            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/monitorarNotas',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        if (response.notas && response.notas.length > 0) {
                        var html = '';
                            response.notas.forEach(function(nota) {
                            html += '<tr>';
                            html += '<td>' + nota.fornecedor + '</td>';
                            html += '<td>' + nota.numero + '</td>';
                            html += '<td>' + nota.serie + '</td>';
                            html += '<td>' + nota.dataEmissao + '</td>';
                            html += '<td>R$ ' + nota.valor + '</td>';
                            html += '<td>' + nota.chave + '</td>';
                            html += '<td><button type="button" class="btn btn-primary btn-sm btnSelecionarNota" data-nota=\'' + JSON.stringify(nota) + '\'><i class="fa fa-check"></i> Selecionar</button></td>';
                            html += '</tr>';
                        });
                        $('#tabelaNotas tbody').html(html);
                        } else {
                        $('#tabelaNotas tbody').html('<tr><td colspan="7" class="text-center">Nenhuma nota encontrada</td></tr>');
                        }
                    } else {
                    alert('Erro ao consultar notas: ' + response.message);
                    }
                },
            error: function() {
                alert('Erro ao consultar notas. Tente novamente.');
                }
            });
        }

    // Adicionar evento para selecionar nota
    $(document).on('click', '.btnSelecionarNota', function() {
        var nota = $(this).data('nota');
        
        // Preencher o formulário com os dados da nota
        $('#chave_acesso').val(nota.chave);
        $('#numero_nfe').val(nota.numero);
        $('#data_emissao').val(nota.dataEmissao);
        
        // Fechar o modal
        $('#modalMonitorarNotas').modal('hide');
        
        // Exibir mensagem de sucesso
        alert('Nota selecionada com sucesso!');
    });

    // Funcionalidade de Importar XML
    var xmlData = null;
    var produtoAtualMapeamento = null;

    // Abrir modal de importar XML
    $('#btn-importar-xml').click(function() {
        $('#arquivo_xml').val('');
        $('#modalImportarXML').modal('show');
    });

    // Exibe o nome do arquivo selecionado
    $('#arquivo_xml').on('change', function() {
        var file = this.files[0];
        if (file) {
            $('#nome-arquivo-xml').text(file.name);
            // Aqui você pode chamar a função de leitura/processamento do XML se desejar
        } else {
            $('#nome-arquivo-xml').text('');
        }
    });

    // Processar arquivo XML selecionado
    $('#arquivo_xml').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                try {
                    var xmlContent = e.target.result;
                    
                    // Debug: Log the first few characters of the XML
                    console.log('XML Content Preview:', xmlContent.substring(0, 100));
                    
                    // Limpar o conteúdo XML de caracteres especiais
                    xmlContent = xmlContent.replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, '');
                    
                    // Remover BOM se existir
                    xmlContent = xmlContent.replace(/^\uFEFF/, '');
                    
                    // Garantir que o XML está bem formatado
                    xmlContent = xmlContent.trim();
                    
                    // Codificar caracteres especiais
                    xmlContent = xmlContent.replace(/&/g, '&amp;')
                                         .replace(/</g, '&lt;')
                                         .replace(/>/g, '&gt;')
                                         .replace(/"/g, '&quot;')
                                         .replace(/'/g, '&apos;');
                    
                    processarXML(xmlContent);
                } catch (error) {
                    console.error('Erro ao processar XML:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao ler o arquivo XML: ' + error.message
                    });
                }
            };
            reader.onerror = function(error) {
                console.error('Erro ao ler arquivo:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao ler o arquivo. Por favor, tente novamente.'
                });
            };
            reader.readAsText(file, 'UTF-8');
        }
    });

    // Função para processar o XML
    function processarXML(xmlContent) {
        // Debug: Log antes do envio
        console.log('Enviando XML para processamento:', xmlContent.substring(0, 100));
        
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/processarXML',
            type: 'POST',
            data: {
                xml_content: xmlContent
            },
            dataType: 'json',
            success: function(response) {
                console.log('Resposta do servidor:', response);
                if (response.success) {
                    xmlData = response.data;
                    exibirPreviewXML(xmlData);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao processar XML: ' + response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na requisição:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao processar XML. Tente novamente.',
                    footer: 'Detalhes: ' + error
                });
            }
        });
    }

    // Função para exibir preview do XML
    function exibirPreviewXML(data) {
        $('#xml-fornecedor').text(data.fornecedor.nome);
        $('#xml-documento').text(data.fornecedor.documento);
        $('#xml-numero').text(data.numero_nfe);
        $('#xml-valor').text('R$ ' + data.valor_total);

        // NOVO: Exibir transportadora
        let transp = data.transportadora;
        let transpHtml = '';
        if (transp) {
            transpHtml = `
            <h4>Informações da Transportadora:</h4>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Transportadora:</strong> <span id="xml-transportadora-nome">${transp.nome}</span></p>
                    <p><strong>CNPJ/CPF:</strong> <span id="xml-transportadora-doc">${transp.documento}</span></p>
                    <p><strong>IE:</strong> <span id="xml-transportadora-ie">${transp.dados_completos.IE || ''}</span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Endereço:</strong> <span id="xml-transportadora-ender">${transp.dados_completos.xEnder || ''}</span></p>
                    <p><strong>Município:</strong> <span id="xml-transportadora-mun">${transp.dados_completos.xMun || ''}</span></p>
                    <p><strong>UF:</strong> <span id="xml-transportadora-uf">${transp.dados_completos.UF || ''}</span></p>
                </div>
            </div>`;
        }
        $('#xml-preview').find('.xml-transportadora-preview').remove();
        $('#xml-preview').prepend('<div class="xml-transportadora-preview">'+transpHtml+'</div>');

        // Preencher campos do formulário automaticamente
        if (transp) {
            $('#transportadora').val(transp.nome);
            $('#transportadora_id').val(transp.id);
        }
        if (data.modalidade_frete) {
            $('#modalidade_frete').val(data.modalidade_frete);
        }
        if (data.peso_bruto) {
            $('#peso_bruto').val(data.peso_bruto);
        }
        if (data.peso_liquido) {
            $('#peso_liquido').val(data.peso_liquido);
        }
        if (data.volume) {
            $('#volume').val(data.volume);
        }

        var tbody = $('#tabela-produtos-xml tbody');
        tbody.empty();

        data.produtos.forEach(function(produto, index) {
            var row = `
                <tr data-index="${index}" class="table-row">
                    <td class="table-cell product-description">${produto.descricao}</td>
                    <td class="table-cell">${produto.quantidade}</td>
                    <td class="table-cell">R$ ${produto.valor_unitario}</td>
                    <td class="table-cell">R$ ${produto.valor_total}</td>
                    <td class="table-cell" id="produto-mapeado-${index}">
                        <span class="label label-default">Não mapeado</span>
                    </td>
                    <td class="table-cell">
                        <button type="button" class="btn btn-primary btn-xs btn-mapear" data-index="${index}">
                            <i class="fa fa-link"></i> Mapear
                        </button>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        $('#xml-preview').show();
        $('#btn-processar-xml').show();
    }

    // Mapear produto
    $(document).on('click', '.btn-mapear', function() {
        var index = $(this).data('index');
        produtoAtualMapeamento = index;
        var produto = xmlData.produtos[index];
        
        $('#produto-xml-nome').text(produto.descricao);
        $('#nome-produto-xml-preview').text(produto.descricao);
        
        // Destruir autocomplete existente antes de abrir o modal
        if ($('#produto_sistema').data('ui-autocomplete')) {
            $('#produto_sistema').autocomplete('destroy');
        }
        
        $('#modalMapearProduto').modal('show');
    });

    // Alternar opções de mapeamento
    $('input[name="opcao_produto"]').change(function() {
        if ($(this).val() === 'existente') {
            $('#div-produto-existente').show();
            $('#div-produto-novo').hide();
        } else {
            $('#div-produto-existente').hide();
            $('#div-produto-novo').show();
        }
    });

    // Confirmar mapeamento
    $('#btn-confirmar-mapeamento').click(function() {
        var opcao = $('input[name="opcao_produto"]:checked').val();
        var index = produtoAtualMapeamento;
        
        if (opcao === 'existente') {
            var produtoId = $('#produto_sistema_id').val();
            var produtoNome = $('#produto_sistema').val();
            
            if (!produtoId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'Por favor, selecione um produto do sistema.',
                    customClass: {
                        container: 'swal-on-top'
                    }
                });
                return;
            }
            
            xmlData.produtos[index].produto_sistema_id = produtoId;
            xmlData.produtos[index].produto_sistema_nome = produtoNome;
            
            $('#produto-mapeado-' + index).html(
                '<span class="label label-success">' + produtoNome + '</span>'
            );
        } else {
            // Usar o nome do produto do XML para o novo produto
            var nomeProdutoXML = xmlData.produtos[index].descricao;
            
            xmlData.produtos[index].produto_sistema_id = 'novo';
            xmlData.produtos[index].produto_sistema_nome = nomeProdutoXML;
            
            $('#produto-mapeado-' + index).html(
                '<span class="label label-info">Novo: ' + nomeProdutoXML + '</span>'
            );
        }
        
        $('#modalMapearProduto').modal('hide');
        
        // Limpar campos
        $('#produto_sistema').val('');
        $('#produto_sistema_id').val('');
        $('input[name="opcao_produto"][value="existente"]').prop('checked', true);
        $('#div-produto-existente').show();
        $('#div-produto-novo').hide();
    });

    // Mapear todos os produtos como novos
    $('#btn-mapear-todos').click(function() {
        if (!xmlData || !xmlData.produtos) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Nenhum produto carregado para mapear.'
            });
            return;
        }

        Swal.fire({
            title: 'Confirmar Mapeamento',
            text: 'Todos os produtos serão cadastrados como novos produtos com os nomes do XML. Deseja continuar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, cadastrar todos!',
            cancelButtonText: 'Cancelar',
            customClass: {
                container: 'swal-on-top'
            },
            backdrop: true,
            allowOutsideClick: true
        }).then((result) => {
            if (result.isConfirmed) {
                xmlData.produtos.forEach(function(produto, index) {
                    produto.produto_sistema_id = 'novo';
                    produto.produto_sistema_nome = produto.descricao; // Usar o nome do XML
                    
                    $('#produto-mapeado-' + index).html(
                        '<span class="label label-info">Novo: ' + produto.descricao + '</span>'
                    );
                });
                
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Todos os produtos foram marcados para cadastro.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        container: 'swal-on-top'
                    }
                });
            }
        });
    });

    // Mapear todos os produtos não mapeados como novos
    $('#btn-mapear-nao-mapeados').click(function() {
        if (!xmlData || !xmlData.produtos) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Nenhum produto carregado para mapear.'
            });
            return;
        }

        let produtosNaoMapeados = xmlData.produtos.filter(function(produto, index) {
            return !produto.produto_sistema_id;
        });

        if (produtosNaoMapeados.length === 0) {
            Swal.fire({
                icon: 'info',
                title: 'Informação',
                text: 'Não há produtos não mapeados para processar.'
            });
            return;
        }

        Swal.fire({
            title: 'Confirmar Mapeamento',
            text: 'Os produtos não mapeados serão cadastrados como novos produtos. Deseja continuar?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, cadastrar!',
            cancelButtonText: 'Cancelar',
            customClass: {
                container: 'swal-on-top'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                xmlData.produtos.forEach(function(produto, index) {
                    if (!produto.produto_sistema_id) {
                        produto.produto_sistema_id = 'novo';
                        produto.produto_sistema_nome = produto.descricao;
                        
                        $('#produto-mapeado-' + index).html(
                            '<span class="label label-info">Novo: ' + produto.descricao + '</span>'
                        );
                    }
                });
                
                Swal.fire({
                    title: 'Sucesso!',
                    text: 'Produtos não mapeados foram marcados para cadastro.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        container: 'swal-on-top'
                    }
                });
            }
        });
    });

    // Remover o botão antigo de mapear existentes
    $('#btn-mapear-existentes').remove();

    // Processar XML final
    $('#btn-processar-xml').click(function() {
        // Verificar se todos os produtos foram mapeados
        var produtosNaoMapeados = xmlData.produtos.filter(function(produto) {
            return !produto.produto_sistema_id;
        });
        
        if (produtosNaoMapeados.length > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Por favor, mapeie todos os produtos antes de continuar.',
                customClass: {
                    container: 'swal-on-top'
                }
            });
            return;
        }
        
        // Preencher formulário com dados do XML
        preencherFormularioComXML();
        
        $('#modalImportarXML').modal('hide');
        
        Swal.fire({
            title: 'Sucesso!',
            text: 'XML importado com sucesso!',
            icon: 'success',
            customClass: {
                container: 'swal-on-top'
            }
        });
    });

    // Função para preencher formulário com dados do XML
    function preencherFormularioComXML() {
        // Verificar se precisa criar fornecedor primeiro
        if (!xmlData.fornecedor.existe) {
            Swal.fire({
                title: 'Criando Fornecedor...',
                text: 'Aguarde enquanto o fornecedor é cadastrado no sistema.',
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-on-top'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Criar fornecedor primeiro
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/criarFornecedor',
                type: 'POST',
                data: {
                    fornecedor: {
                        nome: xmlData.fornecedor.nome,
                        documento: xmlData.fornecedor.documento,
                        dados_completos: xmlData.fornecedor.dados_completos
                    }
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        xmlData.fornecedor.id = response.fornecedor_id;
                        xmlData.fornecedor.nome = response.fornecedor_nome;
                        xmlData.fornecedor.existe = true;
                        continuarPreenchimentoFormulario();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao criar fornecedor: ' + response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erro ao criar fornecedor:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: 'Erro ao criar fornecedor. Tente novamente.'
                    });
                }
            });
        } else {
            continuarPreenchimentoFormulario();
        }
    }
    
    function continuarPreenchimentoFormulario() {
        // Preencher dados do fornecedor
        $('#fornecedor').val(xmlData.fornecedor.nome);
        $('#fornecedor_id').val(xmlData.fornecedor.id);
        
        // Preencher outros campos
        $('#numero_nfe').val(xmlData.numero_nfe);
        $('#chave_acesso').val(xmlData.chave_acesso);
        $('#data_emissao').val(xmlData.data_emissao);
        
        // Limpar itens existentes
        $('#lista-itens tbody').empty();
        
        // Processar produtos que precisam ser criados
        var produtosParaCriar = [];
        
        xmlData.produtos.forEach(function(produto, index) {
            if (produto.produto_sistema_id === 'novo') {
                produtosParaCriar.push({
                    index: index,
                    produto: produto
                });
            }
        });
        
        // Se há produtos para criar, criar eles primeiro
        if (produtosParaCriar.length > 0) {
            Swal.fire({
                title: 'Criando Produtos...',
                text: 'Aguarde enquanto os produtos são cadastrados no sistema.',
                allowOutsideClick: false,
                customClass: {
                    container: 'swal-on-top'
                },
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            criarProdutosSequencialmente(produtosParaCriar, 0, function() {
                finalizarPreenchimentoFormulario();
            });
        } else {
            finalizarPreenchimentoFormulario();
        }
    }
    
    function criarProdutosSequencialmente(produtosParaCriar, indiceAtual, callback) {
        if (indiceAtual >= produtosParaCriar.length) {
            callback();
            return;
        }
        
        var item = produtosParaCriar[indiceAtual];
        
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/criarProduto',
            type: 'POST',
            data: {
                produto: item.produto
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    xmlData.produtos[item.index].produto_sistema_id = response.produto_id;
                    xmlData.produtos[item.index].produto_sistema_nome = item.produto.descricao;
                    log_message('debug', 'Produto criado com ID: ' + response.produto_id);
                } else {
                    console.error('Erro ao criar produto:', response.message);
                }
                
                // Processar próximo produto
                criarProdutosSequencialmente(produtosParaCriar, indiceAtual + 1, callback);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao criar produto:', error);
                // Continuar com o próximo produto mesmo em caso de erro
                criarProdutosSequencialmente(produtosParaCriar, indiceAtual + 1, callback);
            }
        });
    }
    
    function finalizarPreenchimentoFormulario() {
        Swal.close();
        
        // Adicionar produtos à lista
        xmlData.produtos.forEach(function(produto) {
            var produtoId = produto.produto_sistema_id === 'novo' ? produto.produto_sistema_id : produto.produto_sistema_id;
            var produtoNome = produto.produto_sistema_nome || produto.descricao;
            
            var newRow = $(`
                <tr>
                    <td>${produtoNome}</td>
                    <td>${produto.quantidade}</td>
                    <td>R$ ${produto.valor_unitario}</td>
                    <td>R$ ${produto.desconto || '0,00'}</td>
                    <td>${produto.cfop || ''}</td>
                    <td>${produto.cst || ''}</td>
                    <td>R$ ${produto.base_icms || '0,00'}</td>
                    <td>${produto.aliquota_icms || '0,00'}%</td>
                    <td>R$ ${produto.valor_icms || '0,00'}</td>
                    <td>R$ ${produto.base_icms_st || '0,00'}</td>
                    <td>${produto.aliquota_icms_st || '0,00'}%</td>
                    <td>R$ ${produto.valor_icms_st || '0,00'}</td>
                    <td>R$ ${produto.valor_ipi || '0,00'}</td>
                    <td>R$ ${produto.valor_total}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-xs btn-editar" data-row='${JSON.stringify({
                            produto: produtoNome,
                            produto_id: produtoId,
                            produto_descricao: produto.descricao,
                            produto_codigo: produto.codigo,
                            produto_ncm: produto.ncm,
                            quantidade: produto.quantidade,
                            valor: 'R$ ' + produto.valor_unitario,
                            desconto: 'R$ ' + (produto.desconto || '0,00'),
                            cfop: produto.cfop || '',
                            cst: produto.cst || '',
                            base_icms: 'R$ ' + (produto.base_icms || '0,00'),
                            aliquota: produto.aliquota_icms || '0,00',
                            valor_icms: 'R$ ' + (produto.valor_icms || '0,00'),
                            base_icms_st: 'R$ ' + (produto.base_icms_st || '0,00'),
                            aliquota_st: produto.aliquota_icms_st || '0,00',
                            valor_icms_st: 'R$ ' + (produto.valor_icms_st || '0,00'),
                            valor_ipi: 'R$ ' + (produto.valor_ipi || '0,00'),
                            total: 'R$ ' + produto.valor_total
                        })}'>
                            <i class="fa fa-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-xs btn-remover">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
            
            $('#lista-itens tbody').append(newRow);
        });
        
        // Recalcular totais
        calcularTotaisGerais();
    }

    // Auto-complete para produtos no modal de mapeamento
    // Inicializar apenas quando o modal for mostrado para evitar conflitos
    $('#modalMapearProduto').on('shown.bs.modal', function () {
        // Pequeno delay para garantir que o modal esteja completamente renderizado
        setTimeout(function() {
            // Destruir instância anterior se existir
            if ($('#produto_sistema').data('ui-autocomplete')) {
                $('#produto_sistema').autocomplete('destroy');
            }
            
            // Criar nova instância do autocomplete
            $('#produto_sistema').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/autoCompleteProduto',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.label,
                                    value: item.label,
                                    id: item.id,
                                    preco: item.preco
                                };
                            }));
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    event.preventDefault();
                    $('#produto_sistema').val(ui.item.label);
                    $('#produto_sistema_id').val(ui.item.id);
                    return false;
                },
                focus: function(event, ui) {
                    event.preventDefault();
                    return false;
                },
                appendTo: "#modalMapearProduto .modal-body",
                position: {
                    my: "left top",
                    at: "left bottom",
                    collision: "flip"
                },
                open: function() {
                    $('.ui-autocomplete').css('z-index', 9999);
                }
            });
        }, 100);
    });
    
    // Limpar e destruir autocomplete quando o modal for fechado
    $('#modalMapearProduto').on('hidden.bs.modal', function () {
        if ($('#produto_sistema').data('ui-autocomplete')) {
            $('#produto_sistema').autocomplete('close');
            $('#produto_sistema').autocomplete('destroy');
        }
        $('#produto_sistema').val('');
        $('#produto_sistema_id').val('');
    });
    
    // Prevenir propagação de eventos de foco no modal
    $('#modalMapearProduto').on('focus', '#produto_sistema', function(e) {
        e.stopPropagation();
    });

    // Função para fechar documento
    $('#btn-fechar-documento').click(function() {
        // Verificar se há itens na lista
        if ($('#lista-itens tbody tr').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Adicione pelo menos um item antes de fechar o documento.'
            });
            return;
        }

        // Verificar se o formulário está válido
        if (!$('#formFaturamentoEntrada').valid()) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Preencha todos os campos obrigatórios antes de fechar o documento.'
            });
            return;
        }

        // Abrir modal de fechamento
        $('#modalFecharDocumento').modal('show');
    });

    // Confirmar fechamento do documento
    $('#btn-confirmar-fechamento').click(function() {
        var formData = {
            fornecedor_id: $('#fornecedor_id').val(),
            fornecedor_nome: $('#fornecedor').val(),
            valor_total: $('#total_nota').val(),
            forma_pgto: $('#forma_pgto').val(),
            data_vencimento: $('#data_vencimento').val(),
            observacoes: $('#observacoes').val(),
            itens: []
        };

        // Coletar dados dos itens
        $('#lista-itens tbody tr').each(function() {
            var row = $(this);
            formData.itens.push({
                produto: row.find('td:first').text(),
                quantidade: row.find('td:eq(1)').text(),
                valor: row.find('td:eq(12)').text().replace('R$ ', '').replace('.', '').replace(',', '.')
            });
        });

        // Enviar dados para o servidor
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/fecharDocumento',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Documento fechado com sucesso!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = '<?php echo base_url(); ?>index.php/faturamentoEntrada';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.message || 'Erro ao fechar documento.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao processar a requisição.'
                });
            }
        });
    });

    // Inicializar datepicker para data de vencimento
    $('#data_vencimento').datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: 0
    });

    function calcularTotais() {
        var totalProdutos = 0;
        var totalICMS = 0;
        var totalICMSST = 0;
        var totalIPI = 0;
        
        $('#tblItens tbody tr').each(function() {
            var valor = converterParaNumero($(this).find('.valor').val());
            var quantidade = converterParaNumero($(this).find('.quantidade').val());
            var desconto = converterParaNumero($(this).find('.desconto').val());
            var valorICMS = converterParaNumero($(this).find('.valor_icms').val());
            var valorICMSST = converterParaNumero($(this).find('.valor_icms_st').val());
            var valorIPI = converterParaNumero($(this).find('.valor_ipi').val());
            
            var totalItem = (valor * quantidade) - desconto;
            totalProdutos += totalItem;
            totalICMS += valorICMS;
            totalICMSST += valorICMSST;
            totalIPI += valorIPI;
            
            $(this).find('.total_item').val(totalItem.toFixed(2).replace('.', ','));
        });
        
        var frete = converterParaNumero($('#frete').val());
        var despesas = converterParaNumero($('#despesas').val());
        
        var totalNota = totalProdutos + frete + despesas + totalIPI;
        
        $('#total_produtos').val(totalProdutos.toFixed(2).replace('.', ','));
        $('#total_icms').val(totalICMS.toFixed(2).replace('.', ','));
        $('#total_icms_st').val(totalICMSST.toFixed(2).replace('.', ','));
        $('#total_ipi').val(totalIPI.toFixed(2).replace('.', ','));
        // Removido: $('#total_nota').val(totalNota.toFixed(2).replace('.', ','));
    }
});
</script> 

<style>
.swal2-container-custom {
    z-index: 9999 !important;
}

/* Estilos para o autocomplete dentro do modal */
.ui-autocomplete {
    max-height: none !important;
    overflow-y: visible !important;
    overflow-x: hidden;
    width: 100% !important;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 9999 !important;
}

.ui-autocomplete .ui-menu-item {
    padding: 8px 12px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    font-size: 13px;
    white-space: normal;
    word-wrap: break-word;
}

.ui-autocomplete .ui-menu-item:last-child {
    border-bottom: none;
}

.ui-autocomplete .ui-menu-item:hover {
    background-color: #f5f5f5;
}

.ui-autocomplete .ui-menu-item div {
    padding: 0;
    margin: 0;
}

#modalMapearProduto .ui-autocomplete {
    z-index: 9999 !important;
    position: absolute !important;
    width: 100% !important;
    max-width: 100% !important;
}

#modalMapearProduto .modal-body {
    position: relative;
    overflow: visible !important;
}

#modalMapearProduto input:focus {
    outline: none;
    border-color: #66afe9;
    box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102,175,233,.6);
}

/* Ajuste para o container do autocomplete */
.ui-front {
    z-index: 9999 !important;
    position: relative !important;
}

/* Garantir que o dropdown fique visível */
.ui-autocomplete.ui-widget-content {
    border: 1px solid #ddd;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    max-height: none !important;
    overflow: visible !important;
}

/* Ajuste para o posicionamento do dropdown */
.ui-autocomplete {
    position: absolute !important;
    top: 100% !important;
    left: 0 !important;
    right: 0 !important;
    margin-top: 2px !important;
}

/* Desabilitar autocomplete nativo do browser */
#produto_sistema {
    autocomplete: off;
}

/* Fix para z-index do autocomplete */
.ui-front {
    z-index: 9999 !important;
}

/* Aumentar tamanho do modal de importar XML */
#modalImportarXML .modal-dialog {
    width: 99.5%;
    max-width: 2700px;
    margin: 5px auto;
}

#modalImportarXML .modal-content {
    min-height: 600px;
    width: 100%;
}

#modalImportarXML .modal-body {
    max-height: 75vh;
    overflow-y: auto;
    padding: 20px;
}

/* Adicionar estilos específicos para o input file */
#arquivo_xml {
    position: relative;
    z-index: 9999;
    cursor: pointer;
    opacity: 1 !important;
    pointer-events: auto !important;
}

#modalImportarXML .modal-body {
    position: relative;
    z-index: 9999;
}

#modalImportarXML .form-group {
    position: relative;
    z-index: 9999;
}

/* Garantir que o input file seja visível e clicável */
#modalImportarXML input[type="file"] {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    pointer-events: auto !important;
    position: relative !important;
    z-index: 9999 !important;
}
</style> 

<!-- Modal Fechar Documento -->
<div class="modal fade" id="modalFecharDocumento" tabindex="-1" role="dialog" aria-labelledby="modalFecharDocumentoLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalFecharDocumentoLabel">Fechar Documento</h4>
            </div>
            <div class="modal-body">
                <form id="formFecharDocumento">
                    <div class="form-group">
                        <label for="forma_pgto">Forma de Pagamento<span class="required">*</span></label>
                        <select class="form-control" id="forma_pgto" name="forma_pgto" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Transferência">Transferência</option>
                            <option value="PIX">PIX</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="data_vencimento">Data de Vencimento<span class="required">*</span></label>
                        <input type="text" class="form-control datepicker" id="data_vencimento" name="data_vencimento" required>
                    </div>
                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn-confirmar-fechamento">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
// ... existing code ...

    // Função para fechar documento
    $('#btn-fechar-documento').click(function() {
        // Verificar se há itens na lista
        if ($('#lista-itens tbody tr').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Adicione pelo menos um item antes de fechar o documento.'
            });
            return;
        }

        // Verificar se o formulário está válido
        if (!$('#formFaturamentoEntrada').valid()) {
            Swal.fire({
                icon: 'warning',
                title: 'Atenção',
                text: 'Preencha todos os campos obrigatórios antes de fechar o documento.'
            });
            return;
        }

        // Abrir modal de fechamento
        $('#modalFecharDocumento').modal('show');
    });

    // Confirmar fechamento do documento
    $('#btn-confirmar-fechamento').click(function() {
        var formData = {
            fornecedor_id: $('#fornecedor_id').val(),
            fornecedor_nome: $('#fornecedor').val(),
            valor_total: $('#total_nota').val(),
            forma_pgto: $('#forma_pgto').val(),
            data_vencimento: $('#data_vencimento').val(),
            observacoes: $('#observacoes').val(),
            itens: []
        };

        // Coletar dados dos itens
        $('#lista-itens tbody tr').each(function() {
            var row = $(this);
            formData.itens.push({
                produto: row.find('td:first').text(),
                quantidade: row.find('td:eq(1)').text(),
                valor: row.find('td:eq(12)').text().replace('R$ ', '').replace('.', '').replace(',', '.')
            });
        });

        // Enviar dados para o servidor
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/fecharDocumento',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Documento fechado com sucesso!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = '<?php echo base_url(); ?>index.php/faturamentoEntrada';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro',
                        text: response.message || 'Erro ao fechar documento.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Erro ao processar a requisição.'
                });
            }
        });
    });

    // Inicializar datepicker para data de vencimento
    $('#data_vencimento').datepicker({
        dateFormat: 'dd/mm/yy',
        minDate: 0
    });
// ... existing code ...
</script> 

<!-- Modal Busca Fornecedor/Transportadora -->
<div class="modal fade" id="modalBuscaPessoa" tabindex="-1" role="dialog" aria-labelledby="modalBuscaPessoaLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalBuscaPessoaLabel">Buscar <span id="tipoBuscaPessoa">Fornecedor</span></h4>
      </div>
      <div class="modal-body">
        <form class="form-inline" id="formFiltroPessoa" style="margin-bottom:10px;">
          <div class="form-group">
            <input type="text" class="form-control" id="filtroNomePessoa" placeholder="Nome">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" id="filtroDocumentoPessoa" placeholder="Documento">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" id="filtroTelefonePessoa" placeholder="Telefone">
          </div>
          <div class="form-group">
            <label for="limitePessoa">Mostrar</label>
            <select class="form-control" id="limitePessoa">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="200">200</option>
              <option value="500">500</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
        <div class="table-responsive">
          <table class="table table-bordered table-hover" id="tabelaBuscaPessoa">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Documento</th>
                <th>Telefone</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="4" class="text-center">Carregando...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
$(function() {
  let tipoBusca = 'fornecedor';
  let campoNome = '';
  let campoId = '';

  function abrirModalBuscaPessoa(tipo) {
    tipoBusca = tipo;
    if (tipo === 'fornecedor') {
      $('#tipoBuscaPessoa').text('Fornecedor');
      campoNome = '#fornecedor';
      campoId = '#fornecedor_id';
    } else {
      $('#tipoBuscaPessoa').text('Transportadora');
      campoNome = '#transportadora';
      campoId = '#transportadora_id';
    }
    $('#modalBuscaPessoa').modal('show');
    buscarPessoas();
  }

  $('#btnBuscarFornecedor').click(function() {
    abrirModalBuscaPessoa('fornecedor');
  });
  $('#btnBuscarTransportadora').click(function() {
    abrirModalBuscaPessoa('transportadora');
  });

  $('#formFiltroPessoa').submit(function(e) {
    e.preventDefault();
    buscarPessoas();
  });
  $('#limitePessoa').change(function() {
    buscarPessoas();
  });

  function buscarPessoas() {
    let url = tipoBusca === 'fornecedor'
      ? '<?php echo base_url(); ?>index.php/faturamentoEntrada/buscarFornecedores'
      : '<?php echo base_url(); ?>index.php/faturamentoEntrada/buscarTransportadoras';
    let params = {
      nome: $('#filtroNomePessoa').val(),
      documento: $('#filtroDocumentoPessoa').val(),
      telefone: $('#filtroTelefonePessoa').val(),
      limite: $('#limitePessoa').val()
    };
    $('#tabelaBuscaPessoa tbody').html('<tr><td colspan="4" class="text-center">Carregando...</td></tr>');
    $.get(url, params, function(res) {
      let html = '';
      if (res && res.length > 0) {
        res.forEach(function(p) {
          html += '<tr>' +
            '<td>' + p.nome + '</td>' +
            '<td>' + p.documento + '</td>' +
            '<td>' + (p.telefone || '') + '</td>' +
            '<td><button class="btn btn-success btn-xs btnSelecionarPessoa" data-id="' + p.id + '" data-nome="' + p.nome + '">Selecionar</button></td>' +
            '</tr>';
        });
      } else {
        html = '<tr><td colspan="4" class="text-center">Nenhum registro encontrado</td></tr>';
      }
      $('#tabelaBuscaPessoa tbody').html(html);
    }, 'json');
  }

  $(document).on('click', '.btnSelecionarPessoa', function() {
    let id = $(this).data('id');
    let nome = $(this).data('nome');
    $(campoNome).val(nome);
    $(campoId).val(id);
    $('#modalBuscaPessoa').modal('hide');
  });
});
</script> 

<!-- Ajuste na tabela de itens para adicionar a lupa -->
<script type="text/javascript">
$(function() {
  // Adiciona a lupa ao lado do campo produto_nome na linha de edição
  function addLupaProduto() {
    var $input = $('#row-edicao .produto_nome');
    if ($input.next('.input-group-btn').length === 0) {
      $input.wrap('<div class="input-group" style="width: 100%"></div>');
      $input.after('<span class="input-group-btn"><button class="btn btn-default btnBuscarProduto" type="button" tabindex="-1"><i class="fa fa-search"></i></button></span>');
    }
  }
  addLupaProduto();
  // Se adicionar linhas dinamicamente, chame addLupaProduto() novamente
  $(document).on('focus', '.produto_nome', function() {
    addLupaProduto();
  });

  // Modal de busca de produto
  $(document).on('click', '.btnBuscarProduto', function() {
    $('#modalBuscaProduto').data('input', $(this).closest('td').find('.produto_nome')).modal('show');
    buscarProdutos();
  });

  $('#formFiltroProduto').submit(function(e) {
    e.preventDefault();
    buscarProdutos();
  });
  $('#limiteProduto').change(function() {
    buscarProdutos();
  });

  function buscarProdutos() {
    let params = {
      nome: $('#filtroNomeProduto').val(),
      codigo: $('#filtroCodigoProduto').val(),
      barras: $('#filtroBarrasProduto').val(),
      limite: $('#limiteProduto').val()
    };
    $('#tabelaBuscaProduto tbody').html('<tr><td colspan="5" class="text-center">Carregando...</td></tr>');
    $.get('<?php echo base_url(); ?>index.php/faturamentoEntrada/buscarProdutos', params, function(res) {
      let html = '';
      if (res && res.length > 0) {
        res.forEach(function(p) {
          html += '<tr>' +
            '<td>' + p.descricao + '</td>' +
            '<td>' + (p.codDeBarra || '') + '</td>' +
            '<td>' + (p.precoVenda ? 'R$ ' + p.precoVenda : '') + '</td>' +
            '<td>' + (p.estoque || '') + '</td>' +
            '<td><button class="btn btn-success btn-xs btnSelecionarProduto" data-id="' + p.idProdutos + '" data-label="' + p.descricao.replace(/'/g, '\'') + '" data-preco="' + p.precoVenda + '">Selecionar</button></td>' +
            '</tr>';
        });
      } else {
        html = '<tr><td colspan="5" class="text-center">Nenhum produto encontrado</td></tr>';
      }
      $('#tabelaBuscaProduto tbody').html(html);
    }, 'json');
  }

  $(document).on('click', '.btnSelecionarProduto', function() {
    let $input = $('#modalBuscaProduto').data('input');
    let label = $(this).data('label');
    let id = $(this).data('id');
    let preco = $(this).data('preco');
    $input.val(label);
    $input.closest('tr').find('.produto_id').val(id);
    if (preco) {
      $input.closest('tr').find('.valor').val(preco).trigger('change');
    }
    $('#modalBuscaProduto').modal('hide');
    $input.focus();
  });
});
</script>

<!-- Modal Busca Produto -->
<div class="modal fade" id="modalBuscaProduto" tabindex="-1" role="dialog" aria-labelledby="modalBuscaProdutoLabel">
  <div class="modal-dialog" style="max-width: 600px;" role="document">
    <div class="modal-content" style="max-height: 420px; overflow-y: auto;">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalBuscaProdutoLabel">Buscar Produto</h4>
      </div>
      <div class="modal-body" style="padding-bottom: 5px;">
        <form class="form-inline" id="formFiltroProduto" style="margin-bottom:10px;">
          <div class="form-group">
            <input type="text" class="form-control" id="filtroNomeProduto" placeholder="Nome">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" id="filtroCodigoProduto" placeholder="Código">
          </div>
          <div class="form-group">
            <input type="text" class="form-control" id="filtroBarrasProduto" placeholder="Barras">
          </div>
          <div class="form-group">
            <label for="limiteProduto">Mostrar</label>
            <select class="form-control" id="limiteProduto">
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="200">200</option>
              <option value="500">500</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Filtrar</button>
        </form>
        <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
          <table class="table table-bordered table-hover" id="tabelaBuscaProduto" style="font-size:12px;">
            <thead>
              <tr>
                <th>Nome</th>
                <th>Código</th>
                <th>Preço</th>
                <th>Estoque</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tbody>
              <tr><td colspan="5" class="text-center">Carregando...</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer" style="padding: 8px 15px;">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>
</div>

<style>
/* Melhorar autocomplete do produto */
.ui-autocomplete {
  max-width: 350px !important;
  min-width: 180px !important;
  max-height: 180px !important;
  overflow-y: auto !important;
  font-size: 12px !important;
  z-index: 9999 !important;
}
.ui-autocomplete .ui-menu-item {
  padding: 4px 10px !important;
  font-size: 12px !important;
  line-height: 1.2 !important;
}
</style>