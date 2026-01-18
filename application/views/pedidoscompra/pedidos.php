<?php $permissoes = unserialize($this->session->userdata('permissoes')); ?>

<div class="widget-box">
    <div class="widget-title">
        <span class="icon">
            <i class="fas fa-shopping-cart"></i>
        </span>
        <h5>Pedidos de Compra</h5>
    </div>

    <div class="widget-content nopadding tab-content">
        <table id="tabela" class="table table-bordered ">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Fornecedor</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    if (!$results) {
                        echo '<tr><td colspan="6">Nenhum pedido de compra cadastrado.</td></tr>';
                    }
                    foreach ($results as $r) {
                        $dataCompra = date('d/m/Y', strtotime($r->data_pedido));
                        echo '<tr>';
                        echo '<td>' . $r->idPedido . '</td>';
                        echo '<td>' . $dataCompra . '</td>';
                        echo '<td>' . $r->fornecedor . '</td>';
                        echo '<td>' . ucfirst($r->status) . '</td>';
                        echo '<td>R$ ' . number_format($r->valor_total, 2, ',', '.') . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPedidoCompra')) {
                            echo '<a href="' . base_url() . 'index.php/pedidoscompra/visualizar/' . $r->idPedido . '" style="margin-right: 1%" class="btn tip-top" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
                            echo '<a href="' . base_url() . 'index.php/pedidoscompra/editar/' . $r->idPedido . '" style="margin-right: 1%" class="btn btn-info tip-top" title="Editar"><i class="fas fa-edit"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dPedidoCompra')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" pedido="' . $r->idPedido . '" style="margin-right: 1%" class="btn btn-danger tip-top" title="Excluir"><i class="fas fa-trash-alt"></i></a>';
                        }
                        if ($r->status == 'pendente' && $this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
                            echo '<a href="#modal-aprovar" role="button" data-toggle="modal" pedido="' . $r->idPedido . '" class="btn btn-success tip-top" title="Aprovar"><i class="fas fa-check"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aPedidoCompra')) { ?>
    <a href="<?php echo base_url(); ?>index.php/pedidoscompra/adicionar" class="btn btn-success"><i class="fas fa-plus"></i> Adicionar Pedido de Compra</a>
<?php } ?>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/pedidoscompra/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Pedido de Compra</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idPedido" name="id" value="" />
            <h5>Deseja excluir este pedido de compra?</h5>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>

<!-- Modal Aprovar -->
<div id="modal-aprovar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/pedidoscompra/aprovar" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Aprovar Pedido de Compra</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idPedidoAprovar" name="id" value="" />
            <h5>Deseja aprovar este pedido de compra?</h5>
            <p class="text-info"><small>Ao aprovar, o estoque dos produtos será atualizado automaticamente.</small></p>
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-success">Aprovar</button>
        </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click', 'a', function(event) {
        var pedido = $(this).attr('pedido');
        $('#idPedido').val(pedido);
        $('#idPedidoAprovar').val(pedido);
    });

    $('#tabela').DataTable({
        "order": [[ 0, "desc" ]],
        "language": {
            "url": "<?php echo base_url(); ?>assets/js/dataTable_pt-br.json"
        }
    }); 
});
</script> 