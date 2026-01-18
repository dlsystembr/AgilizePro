<!--start-top-serch-->
<div id="content">
    <div class="container-fluid">
        <div class="row-fluid" style="margin-top:0">
            <div class="span12">
                <div class="widget-box">
                    <div class="widget-title" style="margin: -20px 0 0">
                        <span class="icon">
                            <i class="fas fa-percentage"></i>
                        </span>
                        <h5>Alíquotas</h5>
                        <div class="buttons">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aAliquota')) { ?>
                                <a title="Adicionar Nova Alíquota" class="button btn btn-mini btn-success" href="<?php echo base_url() ?>index.php/aliquotas/adicionar">
                                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar Alíquota</span></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="widget-content nopadding tab-content">
                        <table id="tabela" class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>UF Origem</th>
                                    <th>UF Destino</th>
                                    <th>Alíquota Origem (%)</th>
                                    <th>Alíquota Destino (%)</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($aliquotas == null) { ?>
                                    <tr>
                                        <td colspan="5">Nenhuma alíquota cadastrada</td>
                                    </tr>
                                <?php } else {
                                    foreach ($aliquotas as $a) { ?>
                                        <tr>
                                            <td><?php echo $a->uf_origem; ?></td>
                                            <td><?php echo $a->uf_destino; ?></td>
                                            <td><?php echo number_format($a->aliquota_origem, 2, ',', '.'); ?></td>
                                            <td><?php echo number_format($a->aliquota_destino, 2, ',', '.'); ?></td>
                                            <td>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eAliquota')) { ?>
                                                    <a href="<?php echo base_url() ?>index.php/aliquotas/editar/<?php echo $a->id; ?>" class="button btn btn-mini btn-primary"><span class="button__icon"><i class='bx bx-edit'></i></span><span class="button__text2">Editar</span></a>
                                                <?php } ?>
                                                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dAliquota')) { ?>
                                                    <a href="#modal-excluir" role="button" data-toggle="modal" aliquota="<?php echo $a->id; ?>" class="button btn btn-mini btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span><span class="button__text2">Excluir</span></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                <?php }
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/aliquotas/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Alíquota</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idAliquota" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta alíquota?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button type="button" class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class='bx bx-x'></i></span><span class="button__text2">Cancelar</span></button>
            <button type="submit" class="button btn btn-danger">
                <span class="button__icon"><i class='bx bx-trash'></i></span><span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Quando clicar no botão de excluir
        $(document).on('click', 'a[href="#modal-excluir"]', function(event) {
            var aliquota = $(this).attr('aliquota');
            $('#idAliquota').val(aliquota);
        });
    });
</script> 