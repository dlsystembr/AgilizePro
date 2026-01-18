<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-receipt"></i>
                </span>
                <h5>Classificação Fiscal</h5>
                <div class="buttons">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aClassificacaoFiscal')) { ?>
                        <a title="Adicionar Nova Classificação Fiscal" class="btn btn-mini btn-inverse" href="<?= site_url('classificacaofiscal/adicionar') ?>">
                            <i class="fas fa-plus"></i> Adicionar Nova Classificação Fiscal
                        </a>
                    <?php } ?>
                </div>
            </div>
            <div class="widget-content nopadding tab-content">
                <table id="tabela" class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Operação Comercial</th>
                            <th><?= $regime_tributario === 'Simples Nacional' ? 'CSOSN' : 'CST' ?></th>
                            <th>Natureza do Contribuinte</th>
                            <th>Tipo ICMS</th>
                            <th>CFOP</th>
                            <th>Destinação</th>
                            <th>Objetivo Comercial</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$results) { ?>
                            <tr>
                                <td>Sem registros</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($results as $r) { ?>
                                <tr>
                                    <td><?= $r->nome_operacao ?></td>
                                    <td><?= $regime_tributario === 'Simples Nacional' ? $r->csosn : $r->cst ?></td>
                                    <td><?= $r->natureza_contribuinte == 'inscrito' ? 'Inscrito' : 'Não Inscrito' ?></td>
                                    <td><?= ($r->tipo_icms ?? 'normal') === 'st' ? 'ST' : 'Normal' ?></td>
                                    <td><?= $r->cfop ?></td>
                                    <td><?= $r->destinacao ?></td>
                                    <td><?= $r->objetivo_comercial ?></td>
                                    <td>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eClassificacaoFiscal')) { ?>
                                            <a href="<?= site_url('classificacaofiscal/editar/' . $r->id) ?>" class="btn btn-info tip-top" title="Editar Classificação Fiscal">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aClassificacaoFiscal')) { ?>
                                            <a href="<?= site_url('classificacaofiscal/clonar/' . $r->id) ?>" class="btn btn-success tip-top" title="Clonar Classificação Fiscal">
                                                <i class="fas fa-clone"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dClassificacaoFiscal')) { ?>
                                            <a href="#modal-excluir" role="button" data-toggle="modal" class="btn btn-danger tip-top" title="Excluir Classificação Fiscal" classificacao="<?= $r->id ?>">
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
    </div>
</div>

<?php $this->load->view('classificacaofiscal/modal_excluir'); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var classificacao = $(this).attr('classificacao');
            $('#idClassificacao').val(classificacao);
        });
    });
</script> 