<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-exchange-alt"></i>
        </span>
        <h5>Operações Comerciais</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/operacaocomercial" style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'aOperacaoComercial')){ ?>
                    <a href="<?= base_url('index.php/operacaocomercial/adicionar') ?>" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Nova Operação</span>
                    </a>
                <?php } ?>
            </div>
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 300px;">
                    <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Sigla ou Nome" class="span12" value="<?php echo $search; ?>">
                </div>
                <div style="width: 200px;">
                    <select name="situacao" class="span12">
                        <option value="">Todas Situações</option>
                        <option value="1" <?php echo (!isset($_GET['situacao']) || $_GET['situacao'] == '1') ? 'selected' : ''; ?>>Ativas</option>
                        <option value="0" <?php echo isset($_GET['situacao']) && $_GET['situacao'] == '0' ? 'selected' : ''; ?>>Inativas</option>
                    </select>
                </div>
                <div style="display: flex; gap: 5px;">
                    <button class="button btn btn-mini btn-warning" style="min-width: 30px;">
                        <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                    </button>
                    <?php if($search || isset($_GET['situacao'])): ?>
                        <a href="<?php echo base_url() ?>index.php/operacaocomercial" class="button btn btn-mini btn-danger" style="min-width: 30px;">
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
                        <th>Sigla</th>
                        <th>Nome</th>
                        <th>Natureza</th>
                        <th>Tipo Movimento</th>
                        <th>Afeta Custo</th>
                        <th>Fato Fiscal</th>
                        <th>Gera Financeiro</th>
                        <th>Movimenta Estoque</th>
                        <th>Situação</th>
                        <th>Finalidade NFe</th>
                        <th style="text-align:center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$results) { ?>
                        <tr>
                            <td colspan="11">Nenhuma Operação Comercial Cadastrada</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($results as $r) { ?>
                            <tr>
                                <td><?= $r->opc_sigla ?></td>
                                <td><?= $r->opc_nome ?></td>
                                <td><?php echo $r->opc_natureza_operacao; ?></td>
                                <td><?php echo $r->opc_tipo_movimento; ?></td>
                                <td>
                                    <span class="badge <?php echo $r->opc_afeta_custo ? 'badge-success' : 'badge-important'; ?>">
                                        <?php echo $r->opc_afeta_custo ? 'Sim' : 'Não'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $r->opc_fato_fiscal ? 'badge-success' : 'badge-important'; ?>">
                                        <?php echo $r->opc_fato_fiscal ? 'Sim' : 'Não'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $r->opc_gera_financeiro ? 'badge-success' : 'badge-important'; ?>">
                                        <?php echo $r->opc_gera_financeiro ? 'Sim' : 'Não'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $r->opc_movimenta_estoque ? 'badge-success' : 'badge-important'; ?>">
                                        <?php echo $r->opc_movimenta_estoque ? 'Sim' : 'Não'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $r->opc_situacao ? 'badge-success' : 'badge-important'; ?>">
                                        <?php echo $r->opc_situacao ? 'Ativo' : 'Inativo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    switch($r->opc_finalidade_nfe) {
                                        case 1: echo '<span class="badge badge-info">NF-e normal</span>'; break;
                                        case 2: echo '<span class="badge badge-info">NF-e complementar</span>'; break;
                                        case 3: echo '<span class="badge badge-info">NF-e de ajuste</span>'; break;
                                        case 4: echo '<span class="badge badge-info">Devolução de mercadoria</span>'; break;
                                        default: echo '<span class="badge badge-default">Não definido</span>';
                                    }
                                    ?>
                                </td>
                                <td style="text-align:center">
                                    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'vOperacaoComercial')){ ?>
                                        <a href="<?php echo base_url() ?>index.php/operacaocomercial/visualizaroperacao/<?php echo $r->opc_id; ?>" class="btn-nwe" title="Visualizar Operação">
                                            <i class="bx bx-show bx-xs"></i>
                                        </a>
                                    <?php } ?>
                                    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'eOperacaoComercial')){ ?>
                                        <a href="<?php echo base_url() ?>index.php/operacaocomercial/editar/<?php echo $r->opc_id; ?>" class="btn-nwe3" title="Editar Operação">
                                            <i class="bx bx-edit-alt bx-xs"></i>
                                        </a>
                                    <?php } ?>
                                    <?php if($this->permission->checkPermission($this->session->userdata('permissao'),'dOperacaoComercial')){ ?>
                                        <a href="#modal-excluir" role="button" data-toggle="modal" operacao="<?php echo $r->opc_id; ?>" class="btn-nwe4" title="Excluir Operação">
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

<?php echo $this->pagination->create_links(); ?>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h5 id="myModalLabel">Excluir Operação Comercial</h5>
    </div>
    <div class="modal-body">
        <h5 style="text-align: center">Deseja realmente excluir esta operação comercial?</h5>
        <p style="text-align: center; color: #d9534f;">
            <strong>Atenção!</strong> Esta ação não poderá ser revertida.
        </p>
    </div>
    <div class="modal-footer" style="display:flex;justify-content: center">
        <form action="<?php echo base_url() ?>index.php/operacaocomercial/excluir" method="post">
            <input type="hidden" id="idOperacao" name="id" value="" />
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

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var operacao = $(this).attr('operacao');
            $('#idOperacao').val(operacao);
        });
    });
</script> 