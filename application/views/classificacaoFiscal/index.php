<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Classificação Fiscal
            <small>Gerenciar</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Classificação Fiscal</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">Classificações Fiscais</h3>
                        <div class="box-tools">
                            <a href="<?php echo base_url() ?>index.php/classificacaoFiscal/adicionar" class="btn btn-success"><i class="fa fa-plus"></i> Adicionar Nova Classificação</a>
                        </div>
                    </div>
                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Operação Comercial</th>
                                    <th>CST</th>
                                    <th>Natureza do Contribuinte</th>
                                    <th>CFOP</th>
                                    <th>Destinação</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($classificacoes as $c) { ?>
                                    <tr>
                                        <td><?php echo $c->nome_operacao; ?></td>
                                        <td><?php echo $c->cst; ?></td>
                                        <td><?php echo $c->natureza_contribuinte == 'inscrito' ? 'Inscrito' : 'Não Inscrito'; ?></td>
                                        <td><?php echo $c->cfop; ?></td>
                                        <td><?php echo $c->destinacao; ?></td>
                                        <td>
                                            <a href="<?php echo base_url() ?>index.php/classificacaoFiscal/editar/<?php echo $c->id; ?>" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Editar</a>
                                            <a href="#modal-excluir" role="button" data-toggle="modal" classificacao="<?php echo $c->id; ?>" class="btn btn-danger btn-xs btn-excluir"><i class="fa fa-trash"></i> Excluir</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="modal-excluir" tabindex="-1" role="dialog" aria-labelledby="modal-excluir-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modal-excluir-label">Excluir Classificação Fiscal</h4>
            </div>
            <div class="modal-body">
                <h5>Tem certeza que deseja excluir esta classificação fiscal?</h5>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-default" data-dismiss="modal">Fechar</a>
                <a href="#" class="btn btn-danger" id="confirmar-exclusao">Confirmar Exclusão</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', '.btn-excluir', function(event) {
            var classificacao = $(this).attr('classificacao');
            $('#confirmar-exclusao').attr('href', '<?php echo base_url(); ?>index.php/classificacaoFiscal/excluir/' + classificacao);
        });
    });
</script> 