<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Faturamento de Entrada
            <small>Visualizar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/faturamentoEntrada">Faturamento de Entrada</a></li>
            <li class="active">Visualizar</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Visualizar Faturamento de Entrada</h3>
                        <div class="box-tools">
                            <a href="<?php echo base_url() ?>index.php/faturamentoEntrada/editar/<?php echo $faturamento->id; ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Editar</a>
                            <a href="<?php echo base_url() ?>index.php/faturamentoEntrada" class="btn btn-default btn-xs"><i class="fa fa-arrow-left"></i> Voltar</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Operação Comercial</label>
                                    <p><?php echo $faturamento->nome_operacao; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Chave de Acesso</label>
                                    <p><?php echo $faturamento->chave_acesso; ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Número da NFe</label>
                                    <p><?php echo $faturamento->numero_nfe; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Data de Entrada</label>
                                    <p><?php echo date('d/m/Y', strtotime($faturamento->data_entrada)); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Data de Emissão</label>
                                    <p><?php echo date('d/m/Y', strtotime($faturamento->data_emissao)); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Fornecedor</label>
                                    <p><?php echo $faturamento->nome_fornecedor; ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Itens</label>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" style="width: 100%; white-space: nowrap;">
                                            <thead>
                                                <tr>
                                                    <th style="min-width: 200px;">Produto</th>
                                                    <th style="min-width: 100px;">Quantidade</th>
                                                    <th style="min-width: 120px;">Valor Unitário</th>
                                                    <th style="min-width: 100px;">Desconto</th>
                                                    <th style="min-width: 120px;">Base ICMS</th>
                                                    <th style="min-width: 120px;">Alíquota ICMS</th>
                                                    <th style="min-width: 120px;">Valor ICMS</th>
                                                    <th style="min-width: 120px;">Base ICMS ST</th>
                                                    <th style="min-width: 120px;">Alíquota ICMS ST</th>
                                                    <th style="min-width: 120px;">Valor ICMS ST</th>
                                                    <th style="min-width: 120px;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($itens as $i) { ?>
                                                    <tr>
                                                        <td style="min-width: 200px;"><?php echo $i->nome_produto; ?></td>
                                                        <td style="min-width: 100px;"><?php echo number_format($i->quantidade, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->valor_unitario, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 100px;">R$ <?php echo number_format($i->desconto, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->base_calculo_icms, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;"><?php echo number_format($i->aliquota_icms, 2, ',', '.'); ?>%</td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->valor_icms, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->base_calculo_icms_st, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;"><?php echo number_format($i->aliquota_icms_st, 2, ',', '.'); ?>%</td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->valor_icms_st, 2, ',', '.'); ?></td>
                                                        <td style="min-width: 120px;">R$ <?php echo number_format($i->total_item, 2, ',', '.'); ?></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Despesas</label>
                                    <p>R$ <?php echo number_format($faturamento->despesas, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Frete</label>
                                    <p>R$ <?php echo number_format($faturamento->frete, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total Base ICMS</label>
                                    <p>R$ <?php echo number_format($faturamento->total_base_icms, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total ICMS</label>
                                    <p>R$ <?php echo number_format($faturamento->total_icms, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total Base ICMS ST</label>
                                    <p>R$ <?php echo number_format($faturamento->total_base_icms_st, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Total ICMS ST</label>
                                    <p>R$ <?php echo number_format($faturamento->total_icms_st, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Total da Nota</label>
                                    <p>R$ <?php echo number_format($faturamento->total_nota, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 