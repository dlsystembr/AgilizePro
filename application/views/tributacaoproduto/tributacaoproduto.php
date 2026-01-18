<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-balance-scale"></i>
                </span>
                <h5>Tributação de Produtos</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <table id="tabela" class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Nome da Configuração</th>
                            <th>CST IPI</th>
                            <th>Alíquota IPI</th>
                            <th>CST PIS</th>
                            <th>Alíquota PIS</th>
                            <th>CST COFINS</th>
                            <th>Alíquota COFINS</th>
                            <th>Regime Fiscal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$results) { ?>
                            <tr>
                                <td colspan="9">Nenhuma Tributação Cadastrada</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($results as $r) { ?>
                                <tr>
                                    <td><?= $r->nome_configuracao ?></td>
                                    <td><?= $r->cst_ipi_saida ?></td>
                                    <td><?= number_format($r->aliq_ipi_saida, 2, ',', '.') ?>%</td>
                                    <td><?= $r->cst_pis_saida ?></td>
                                    <td><?= number_format($r->aliq_pis_saida, 2, ',', '.') ?>%</td>
                                    <td><?= $r->cst_cofins_saida ?></td>
                                    <td><?= number_format($r->aliq_cofins_saida, 2, ',', '.') ?>%</td>
                                    <td><?= $r->regime_fiscal_tributario ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eTributacaoProduto')) { ?>
                                            <a href="<?= base_url() ?>index.php/tributacaoproduto/editar/<?= $r->id ?>" class="btn btn-info tip-top" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dTributacaoProduto')) { ?>
                                            <a href="#modal-excluir" role="button" data-toggle="modal" tributacao="<?= $r->id ?>" class="btn btn-danger tip-top" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
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
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aTributacaoProduto')) { ?>
            <a href="<?= base_url() ?>index.php/tributacaoproduto/adicionar" class="btn btn-success"><i class="fas fa-plus"></i> Adicionar Tributação</a>
        <?php } ?>
    </div>
</div>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?= base_url() ?>index.php/tributacaoproduto/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Tributação</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idTributacao" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta tributação?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var tributacao = $(this).attr('tributacao');
            $('#idTributacao').val(tributacao);
        });
    });
</script> 