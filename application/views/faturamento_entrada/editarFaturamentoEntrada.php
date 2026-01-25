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
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Faturamento de Entrada
            <small>Editar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/faturamentoEntrada">Faturamento de Entrada</a></li>
            <li class="active">Editar</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <div class="row">
                            <div class="col-md-8">
                        <h3 class="box-title">Editar Faturamento de Entrada</h3>
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
                                    <label for="operacao_comercial_id">Operação Comercial<span class="required">*</span></label>
                                    <select class="form-control" id="operacao_comercial_id" name="operacao_comercial_id" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach ($operacoes as $o) { ?>
                                            <option value="<?php echo $o->opc_id; ?>" <?php echo (isset($faturamento->operacao_comercial_id) && $o->opc_id == $faturamento->operacao_comercial_id) ? 'selected' : ''; ?>><?php echo $o->opc_nome; ?></option>
                                            <?php } ?>
                                        </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="chave_acesso">Chave de Acesso</label>
                                    <input type="text" class="form-control" id="chave_acesso" name="chave_acesso" value="<?php echo isset($faturamento->chave_acesso) ? $faturamento->chave_acesso : ''; ?>" maxlength="44">
                                </div>
                            </div>
                            <div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="numero_nfe" style="margin-bottom: 5px;">
                                            <i class="fa fa-file-text"></i> Número da NFe
                                        </label>
                                        <input type="text" class="form-control input-sm" id="numero_nfe" name="numero_nfe" value="<?php echo isset($faturamento->numero_nfe) ? $faturamento->numero_nfe : ''; ?>" style="width: 100%" />
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="data_entrada" style="margin-bottom: 5px;">
                                            <i class="fa fa-calendar"></i> Data de Entrada
                                        </label>
                                        <input type="text" class="form-control input-sm datepicker" id="data_entrada" name="data_entrada" value="<?php echo isset($faturamento->data_entrada) ? date('d/m/Y', strtotime($faturamento->data_entrada)) : date('d/m/Y'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="data_emissao" style="margin-bottom: 5px;">
                                            <i class="fa fa-calendar-o"></i> Data de Emissão
                                        </label>
                                        <input type="text" class="form-control input-sm datepicker" id="data_emissao" name="data_emissao" value="<?php echo isset($faturamento->data_emissao) ? date('d/m/Y', strtotime($faturamento->data_emissao)) : date('d/m/Y'); ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="despesas" style="margin-bottom: 5px;">
                                            <i class="fa fa-money"></i> Despesas
                                        </label>
                                        <input type="text" class="form-control input-sm money" id="despesas" name="despesas" value="<?php echo isset($faturamento->despesas) ? number_format($faturamento->despesas, 2, ',', '.') : '0,00'; ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="frete" style="margin-bottom: 5px;">
                                            <i class="fa fa-truck"></i> Frete
                                        </label>
                                        <input type="text" class="form-control input-sm money" id="frete" name="frete" value="<?php echo isset($faturamento->frete) ? number_format($faturamento->frete, 2, ',', '.') : '0,00'; ?>" style="width: 100%" />
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
                            </div>

                            <div class="row" style="margin-bottom: 15px;">
                                <div class="col-xs-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="total_nota" style="margin-bottom: 5px;">Total da Nota</label>
                                        <input type="text" class="form-control input-sm money" id="total_nota" name="total_nota" readonly style="width: 100%" />
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: flex; flex-wrap: wrap; margin-bottom: 15px;">
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="peso_bruto" style="margin-bottom: 5px;">
                                            <i class="fa fa-balance-scale"></i> Peso Bruto (kg)
                                        </label>
                                        <input type="text" class="form-control input-sm" id="peso_bruto" name="peso_bruto" value="<?php echo isset($faturamento->peso_bruto) ? number_format($faturamento->peso_bruto, 3, ',', '.') : ''; ?>" style="width: 100%" />
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-12" style="padding: 5px;">
                                    <div class="form-group" style="margin-bottom: 5px;">
                                        <label for="peso_liquido" style="margin-bottom: 5px;">
                                            <i class="fa fa-balance-scale"></i> Peso Líquido (kg)
                                        </label>
                                        <input type="text" class="form-control input-sm" id="peso_liquido" name="peso_liquido" value="<?php echo isset($faturamento->peso_liquido) ? number_format($faturamento->peso_liquido, 3, ',', '.') : ''; ?>" style="width: 100%" />
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

        // Carregar itens existentes
        <?php if (isset($itens) && !empty($itens)): ?>
        <?php foreach ($itens as $item): ?>
        var row = '<tr>' +
            '<td><?php echo $item->descricao; ?><input type="hidden" name="produtos[]" value="<?php echo $item->produto_id; ?>"></td>' +
            '<td><?php echo $item->quantidade; ?><input type="hidden" name="quantidades[]" value="<?php echo $item->quantidade; ?>"></td>' +
            '<td><?php echo number_format($item->valor_unitario, 2, ',', '.'); ?><input type="hidden" name="valores[]" value="<?php echo number_format($item->valor_unitario, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->desconto, 2, ',', '.'); ?><input type="hidden" name="descontos[]" value="<?php echo number_format($item->desconto, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo $item->cfop; ?><input type="hidden" name="cfop[]" value="<?php echo $item->cfop; ?>"></td>' +
            '<td><?php echo $item->cst; ?><input type="hidden" name="cst[]" value="<?php echo $item->cst; ?>"></td>' +
            '<td><?php echo number_format($item->base_calculo_icms, 2, ',', '.'); ?><input type="hidden" name="bases_icms[]" value="<?php echo number_format($item->base_calculo_icms, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->aliquota_icms, 2, ',', '.'); ?><input type="hidden" name="aliquotas[]" value="<?php echo number_format($item->aliquota_icms, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->valor_icms, 2, ',', '.'); ?><input type="hidden" name="valores_icms[]" value="<?php echo number_format($item->valor_icms, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->base_calculo_icms_st, 2, ',', '.'); ?><input type="hidden" name="bases_icms_st[]" value="<?php echo number_format($item->base_calculo_icms_st, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->aliquota_icms_st, 2, ',', '.'); ?><input type="hidden" name="aliquotas_st[]" value="<?php echo number_format($item->aliquota_icms_st, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->valor_icms_st, 2, ',', '.'); ?><input type="hidden" name="valores_icms_st[]" value="<?php echo number_format($item->valor_icms_st, 2, ',', '.'); ?>"></td>' +
            '<td><?php echo number_format($item->total_item, 2, ',', '.'); ?><input type="hidden" name="totais[]" value="<?php echo number_format($item->total_item, 2, ',', '.'); ?>"></td>' +
            '<td>' +
            '<button type="button" class="btn btn-primary btn-xs btn-editar" data-row=\'' + JSON.stringify({
                produto: '<?php echo $item->descricao; ?>',
                produto_id: '<?php echo $item->produto_id; ?>',
                quantidade: '<?php echo $item->quantidade; ?>',
                valor: '<?php echo number_format($item->valor_unitario, 2, ',', '.'); ?>',
                desconto: '<?php echo number_format($item->desconto, 2, ',', '.'); ?>',
                cfop: '<?php echo $item->cfop; ?>',
                cst: '<?php echo $item->cst; ?>',
                base_icms: '<?php echo number_format($item->base_calculo_icms, 2, ',', '.'); ?>',
                aliquota: '<?php echo number_format($item->aliquota_icms, 2, ',', '.'); ?>',
                valor_icms: '<?php echo number_format($item->valor_icms, 2, ',', '.'); ?>',
                base_icms_st: '<?php echo number_format($item->base_calculo_icms_st, 2, ',', '.'); ?>',
                aliquota_st: '<?php echo number_format($item->aliquota_icms_st, 2, ',', '.'); ?>',
                valor_icms_st: '<?php echo number_format($item->valor_icms_st, 2, ',', '.'); ?>',
                total: '<?php echo number_format($item->total_item, 2, ',', '.'); ?>'
            }) + '\'><i class="fa fa-pencil"></i></button>' +
            '<button type="button" class="btn btn-danger btn-xs btn-remover"><i class="fa fa-trash"></i></button>' +
            '</td>' +
            '</tr>';
        $('#lista-itens tbody').append(row);
        <?php endforeach; ?>
        calcularTotaisGerais();
        <?php endif; ?>

        // Função para calcular totais gerais
        function calcularTotaisGerais() {
            var totalBaseICMS = 0;
            var totalICMS = 0;
            var totalBaseICMSST = 0;
            var totalICMSST = 0;
            var totalNota = 0;

            $('#lista-itens tbody tr').each(function() {
                var row = $(this);
                var baseICMS = parseFloat(row.find('td:eq(6)').text().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
                var valorICMS = parseFloat(row.find('td:eq(8)').text().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
                var baseICMSST = parseFloat(row.find('td:eq(9)').text().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
                var valorICMSST = parseFloat(row.find('td:eq(11)').text().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
                var totalItem = parseFloat(row.find('td:eq(12)').text().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;

                totalBaseICMS += baseICMS;
                totalICMS += valorICMS;
                totalBaseICMSST += baseICMSST;
                totalICMSST += valorICMSST;
                totalNota += totalItem;
            });

            $('#total_base_icms').val(totalBaseICMS.toFixed(2).replace('.', ','));
            $('#total_icms').val(totalICMS.toFixed(2).replace('.', ','));
            $('#total_base_icms_st').val(totalBaseICMSST.toFixed(2).replace('.', ','));
            $('#total_icms_st').val(totalICMSST.toFixed(2).replace('.', ','));
            $('#total_nota').val(totalNota.toFixed(2).replace('.', ','));
        }

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
                        calcularTotaisGerais();
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

        // Adicionar item
        $('.btn-adicionar').click(function() {
            var row = $(this).closest('tr');
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
                <td>${produto}<input type="hidden" name="produtos[]" value="${produtoId}"></td>
                <td>${quantidade}<input type="hidden" name="quantidades[]" value="${quantidade}"></td>
                <td>${row.find('.valor').val()}<input type="hidden" name="valores[]" value="${row.find('.valor').val()}"></td>
                <td>${row.find('.desconto').val()}<input type="hidden" name="descontos[]" value="${row.find('.desconto').val()}"></td>
                <td>${row.find('.cfop').val()}<input type="hidden" name="cfop[]" value="${row.find('.cfop').val()}"></td>
                <td>${row.find('.cst').val()}<input type="hidden" name="cst[]" value="${row.find('.cst').val()}"></td>
                <td>${row.find('.base_icms').val()}<input type="hidden" name="bases_icms[]" value="${row.find('.base_icms').val()}"></td>
                <td>${row.find('.aliquota').val()}<input type="hidden" name="aliquotas[]" value="${row.find('.aliquota').val()}"></td>
                <td>${row.find('.valor_icms').val()}<input type="hidden" name="valores_icms[]" value="${row.find('.valor_icms').val()}"></td>
                <td>${row.find('.base_icms_st').val()}<input type="hidden" name="bases_icms_st[]" value="${row.find('.base_icms_st').val()}"></td>
                <td>${row.find('.aliquota_st').val()}<input type="hidden" name="aliquotas_st[]" value="${row.find('.aliquota_st').val()}"></td>
                <td>${row.find('.valor_icms_st').val()}<input type="hidden" name="valores_icms_st[]" value="${row.find('.valor_icms_st').val()}"></td>
                <td>${row.find('.total').val()}<input type="hidden" name="totais[]" value="${row.find('.total').val()}"></td>
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
            calcularTotaisGerais();
        });

        // Função para limpar campos de edição
        function limparCamposEdicao() {
            var row = $('#row-edicao');
            row.find('input').val('');
            row.find('.produto_nome').focus();
        }

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
                    calcularTotaisGerais();
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

        // Função para calcular o total do item
        function calcularTotalItem(row) {
            var quantidade = parseFloat(row.find('.quantidade').val().replace(',', '.')) || 0;
            var valorUnitario = parseFloat(row.find('.valor').val().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
            var desconto = parseFloat(row.find('.desconto').val().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
            var cst = row.find('.cst').val();
            
            var subtotal = quantidade * valorUnitario;
            var total = subtotal - desconto;
            
            row.find('.total').val(total.toFixed(2).replace('.', ','));
            row.find('.base_icms').val(subtotal.toFixed(2).replace('.', ','));
            
            var aliquota = parseFloat(row.find('.aliquota').val().replace(',', '.')) || 0;
            var valorICMS = subtotal * (aliquota / 100);
            row.find('.valor_icms').val(valorICMS.toFixed(2).replace('.', ','));
            
            if (cst === '10' || cst === '30' || cst === '70') {
                var aliquotaST = parseFloat(row.find('.aliquota_st').val().replace(',', '.')) || 0;
                var baseICMSST = subtotal * 1.4;
                var valorICMSST = (baseICMSST * (aliquotaST / 100)) - valorICMS;
                
                row.find('.base_icms_st').val(baseICMSST.toFixed(2).replace('.', ','));
                row.find('.valor_icms_st').val(valorICMSST.toFixed(2).replace('.', ','));
            } else {
                row.find('.base_icms_st').val('0,00');
                row.find('.valor_icms_st').val('0,00');
                row.find('.aliquota_st').val('0,00');
            }
        }

        // Autocomplete para fornecedor
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

        // Autocomplete para produto
        $(document).on('focus', '.produto_nome', function() {
            if (!$('#fornecedor_id').val()) {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Por favor, selecione um fornecedor antes de adicionar produtos.',
                    icon: 'warning'
                });
                $(this).blur();
                return false;
            }

            if (!$('#operacao_comercial_id').val()) {
                Swal.fire({
                    title: 'Atenção!',
                    text: 'Por favor, selecione uma operação comercial antes de adicionar produtos.',
                    icon: 'warning'
                });
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
                        input.val(ui.item.label);
                        row.find('.produto_id').val(ui.item.id);
                        
                        if(ui.item.estoque) {
                            row.find('.quantidade').val('1').attr('max', ui.item.estoque);
                        }
                        if(ui.item.precoCompra) {
                            row.find('.valor').val(ui.item.precoCompra).trigger('change');
                        }

                        calcularTotalItem(row);
                        row.find('.quantidade').focus();
                    }
                    return false;
                }
            });
        });

        // Monitor de Notas
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

        // Selecionar nota
        $(document).on('click', '.btnSelecionarNota', function() {
            var nota = $(this).data('nota');
            $('#chave_acesso').val(nota.chave);
            $('#numero_nfe').val(nota.numero);
            $('#data_emissao').val(nota.dataEmissao);
            $('#modalMonitorarNotas').modal('hide');
            Swal.fire({
                title: 'Sucesso!',
                text: 'Nota selecionada com sucesso!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        });
    });
</script> 

<style>
.swal2-container-custom {
    z-index: 9999 !important;
}
</style> 