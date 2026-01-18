<style>
    select {
        width: 70px;
    }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-shopping-cart"></i>
        </span>
        <h5>Pedidos de Compra</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/pedidoscompra/gerenciar">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aPedidoCompra')) { ?>
                <div class="span3">
                    <a href="<?php echo base_url(); ?>index.php/pedidoscompra/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Novo Pedido</span>
                    </a>
                </div>
            <?php } ?>
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome do fornecedor a pesquisar" class="span12" value="">
            </div>
            <div class="span2">
                <select name="status" class="span12">
                    <option value="">Selecione status</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Aprovado">Aprovado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
            </div>
            <div class="span2">
                <input type="text" name="data" class="span12 datepicker" placeholder="Data inicial" autocomplete="off">
            </div>
            <div class="span2">
                <input type="text" name="data2" class="span12 datepicker" placeholder="Data final" autocomplete="off">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                </button>
            </div>
        </form>
    </div>
    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Fornecedor</th>
                        <th>Responsável</th>
                        <th>Data</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!$results) {
                            echo '<tr>
                                    <td colspan="7">Nenhum Pedido de Compra Cadastrado</td>
                                </tr>';
                        }
                        foreach ($results as $r) {
                            $dataPedido = date(('d/m/Y'), strtotime($r->data_pedido));
                            
                            $corStatus = match($r->status) {
                                'Pendente' => '#FF7F00',
                                'Aprovado' => '#4d9c79',
                                'Cancelado' => '#CD0000',
                                default => '#E0E4CC',
                            };

                            echo '<tr>';
                            echo '<td>' . $r->idPedido . '</td>';
                            echo '<td><a href="' . base_url() . 'index.php/fornecedores/visualizar/' . $r->idFornecedores . '">' . $r->nomeFornecedor . '</a></td>';
                            echo '<td>' . $r->nome . '</td>';
                            echo '<td>' . $dataPedido . '</td>';
                            echo '<td>R$ ' . number_format($r->totalProdutos ?: 0, 2, ',', '.') . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '">' . $r->status . '</span></td>';
                            echo '<td>';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPedidoCompra')) {
                                echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/pedidoscompra/visualizar/' . $r->idPedido . '" class="btn-nwe" title="Ver mais detalhes"><i class="bx bx-show bx-xs"></i></a>';
                            }
                            if ($r->status != 'Aprovado') {
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
                                    echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/pedidoscompra/editar/' . $r->idPedido . '" class="btn-nwe3" title="Editar pedido"><i class="bx bx-edit bx-xs"></i></a>';
                                }
                                if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dPedidoCompra')) {
                                    echo '<a href="#modal-excluir" role="button" data-toggle="modal" pedido="' . $r->idPedido . '" class="btn-nwe4" title="Excluir Pedido"><i class="bx bx-trash-alt bx-xs"></i></a>';
                                }
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->pagination->create_links(); ?>

    <!-- Modal -->
    <div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <form action="<?php echo base_url() ?>index.php/pedidoscompra/excluir" method="post">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h5 id="myModalLabel">Excluir Pedido de Compra</h5>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idPedido" name="id" value="" />
                <h5 style="text-align: center">Deseja realmente excluir este pedido de compra?</h5>
            </div>
            <div class="modal-footer" style="display:flex;justify-content: center">
                <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                    <span class="button__icon"><i class="bx bx-x"></i></span>
                    <span class="button__text2">Cancelar</span>
                </button>
                <button class="button btn btn-danger">
                    <span class="button__icon"><i class='bx bx-trash'></i></span>
                    <span class="button__text2">Excluir</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    var baseUrl = '<?php echo base_url(); ?>';
</script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pedidoscompra.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var pedido = $(this).attr('pedido');
            $('#idPedido').val(pedido);
        });
        
        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });
    });
</script> 