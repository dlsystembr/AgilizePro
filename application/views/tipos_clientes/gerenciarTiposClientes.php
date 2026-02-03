<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-users"></i>
        </span>
        <h5>Tipos de Clientes</h5>
    </div>

    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aTipoCliente')) { ?>
            <div class="span3">
                <a href="<?= base_url() ?>index.php/tipos_clientes/adicionar" class="button btn btn-mini btn-success" style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                    <span class="button__text2">Novo Tipo</span>
                </a>
            </div>
        <?php } ?>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Cod.</th>
                        <th>Nome</th>
                        <th>Cod. Cliente</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        // DataTables exige o mesmo número de <td> que <th>; uma linha com colspan quebra _DT_CellIndex
                        echo '<tr><td></td><td style="text-align:center">Nenhum Tipo de Cliente Cadastrado</td><td></td><td></td></tr>';
                    }
                    foreach ($results as $r) {
                        echo '<tr>';
                        echo '<td>' . $r->tpc_id . '</td>';
                        echo '<td>' . $r->tpc_nome . '</td>';
                        echo '<td>' . $r->tpc_codigo_cliente . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eTipoCliente')) {
                            echo '<a href="' . base_url() . 'index.php/tipos_clientes/editar/' . $r->tpc_id . '" style="margin-right: 1%" class="btn-nwe3" title="Editar Tipo de Cliente"><i class="bx bx-edit bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dTipoCliente')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" tipoCliente="' . $r->tpc_id . '" class="btn-nwe4" title="Excluir Tipo de Cliente"><i class="bx bx-trash-alt bx-xs"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/tipos_clientes/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Tipo de Cliente</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idExcluir" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este tipo de cliente?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var tipoCliente = $(this).attr('tipoCliente');
            $('#idExcluir').val(tipoCliente);
        });
    });
</script>

