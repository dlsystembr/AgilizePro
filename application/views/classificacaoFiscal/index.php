<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </span>
        <h5>Classificações Fiscais</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/classificacaoFiscal" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'aClassificacaoFiscal')){ ?>
                    <a href="<?= base_url('index.php/classificacaoFiscal/adicionar') ?>" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Nova Classificação</span>
                    </a>
                <?php } ?>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 300px;">
                    <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Operação ou CST" class="span12" value="<?php echo isset($search) ? $search : ''; ?>">
                </div>
                <div style="display: flex; gap: 5px;">
                    <button class="button btn btn-mini btn-warning" style="min-width: 30px;">
                        <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                    </button>
                    <?php if(isset($search) && $search): ?>
                        <a href="<?php echo base_url() ?>index.php/classificacaoFiscal" class="button btn btn-mini btn-danger" style="min-width: 30px;">
                            <span class="button__icon"><i class='bx bx-x'></i></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Operação Comercial</th>
                        <th>CST/CSOSN</th>
                        <th>Natureza do Contribuinte</th>
                        <th>CFOP</th>
                        <th>Destinação</th>
                        <th>Objetivo</th>
                        <th>Situação</th>
                        <th style="text-align:center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$classificacoes) { ?>
                        <tr>
                            <td colspan="8">Nenhuma Classificação Fiscal Cadastrada</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($classificacoes as $c) { ?>
                            <tr>
                                <td><?php echo $c->nome_operacao; ?></td>
                                <td><?php echo $c->cst ?: $c->csosn; ?></td>
                                <td>
                                    <span class="badge <?php
                                        if ($c->natureza_contribuinte == 'Contribuinte') echo 'badge-success';
                                        elseif ($c->natureza_contribuinte == 'Não Contribuinte') echo 'badge-important';
                                        elseif ($c->natureza_contribuinte == 'Orgão Público') echo 'badge-info';
                                        else echo 'badge-default';
                                    ?>">
                                        <?php echo $c->natureza_contribuinte; ?>
                                    </span>
                                </td>
                                <td><?php echo $c->cfop; ?></td>
                                <td><?php echo $c->destinacao; ?></td>
                                <td><?php echo $c->objetivo_comercial; ?></td>
                                <td>
                                    <span class="badge <?php echo $c->situacao == 1 ? 'badge-success' : 'badge-important'; ?>">
                                        <?php echo $c->situacao == 1 ? 'Ativa' : 'Inativa'; ?>
                                    </span>
                                </td>
                                <td style="text-align:center">
                                    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vClassificacaoFiscal')){ ?>
                                        <a href="<?php echo base_url() ?>index.php/classificacaoFiscal/visualizar/<?php echo $c->id; ?>" class="btn-nwe" title="Visualizar Classificação">
                                            <i class="bx bx-show bx-xs"></i>
                                        </a>
                                    <?php } ?>
                                    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'eClassificacaoFiscal')){ ?>
                                        <a href="<?php echo base_url() ?>index.php/classificacaoFiscal/editar/<?php echo $c->id; ?>" class="btn-nwe3" title="Editar Classificação">
                                            <i class="bx bx-edit-alt bx-xs"></i>
                                        </a>
                                    <?php } ?>
                                    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'dClassificacaoFiscal')){ ?>
                                        <a href="#modal-excluir" role="button" data-toggle="modal" classificacao="<?php echo $c->id; ?>" class="btn-nwe4" title="Excluir Classificação">
                                            <i class="bx bx-trash-alt bx-xs"></i>
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if(isset($pagination_links)) echo $pagination_links; ?>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h5 id="myModalLabel">Excluir Classificação Fiscal</h5>
    </div>
    <div class="modal-body">
        <h5 style="text-align: center">Deseja realmente excluir esta classificação fiscal?</h5>
        <p style="text-align: center; color: #d9534f;">
            <strong>Atenção!</strong> Esta ação não poderá ser revertida.
        </p>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
        <form action="<?php echo base_url() ?>index.php/classificacaoFiscal/excluir" method="post">
            <input type="hidden" id="idClassificacao" name="id" value="" />
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger">
                <span class="button__icon"><i class="bx bx-trash-alt"></i></span>
                <span class="button__text2">Excluir</span>
            </button>
        </form>
    </div>
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
        $(document).on('click', 'a', function(event) {
            var classificacao = $(this).attr('classificacao');
            $('#idClassificacao').val(classificacao);
        });
    });
</script> 