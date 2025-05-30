<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Classificação Fiscal
            <small>Adicionar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/classificacaoFiscal">Classificação Fiscal</a></li>
            <li class="active">Adicionar</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Adicionar Classificação Fiscal</h3>
                    </div>
                    <div class="box-body">
                        <form method="post" action="<?php echo base_url(); ?>index.php/classificacaoFiscal/salvar">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="operacao_comercial_id">Operação Comercial</label>
                                        <select class="form-control" name="operacao_comercial_id" id="operacao_comercial_id" required>
                                            <option value="">Selecione uma operação</option>
                                            <?php foreach ($operacoes as $o) { ?>
                                                <option value="<?php echo $o->id; ?>"><?php echo $o->nome; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cst">CST</label>
                                        <input type="text" class="form-control" name="cst" id="cst" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="natureza_contribuinte">Natureza do Contribuinte</label>
                                        <select class="form-control" name="natureza_contribuinte" id="natureza_contribuinte" required>
                                            <option value="nao_inscrito">Não Inscrito</option>
                                            <option value="inscrito">Inscrito</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="cfop">CFOP</label>
                                        <input type="text" class="form-control" name="cfop" id="cfop" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="destinacao">Destinação</label>
                                        <input type="text" class="form-control" name="destinacao" id="destinacao" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                                    <a href="<?php echo base_url(); ?>index.php/classificacaoFiscal" class="btn btn-default"><i class="fa fa-arrow-left"></i> Voltar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 