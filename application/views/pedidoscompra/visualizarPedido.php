<?php $totalProdutos = 0; ?>
<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <h5>Pedido de Compra #<?php echo $result->idPedido ?></h5>
                <div class="buttons">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra') && $result->status != 'Aprovado') { ?>
                        <a title="Editar Pedido" class="button btn btn-mini btn-success" href="<?php echo base_url() . 'index.php/pedidoscompra/editar/' . $result->id; ?>">
                            <span class="button__icon"><i class="bx bx-edit"></i></span>
                            <span class="button__text">Editar</span>
                        </a>
                    <?php } ?>
                    <a target="_blank" title="Imprimir" class="button btn btn-mini btn-inverse" href="<?php echo site_url() . '/pedidoscompra/imprimir/' . $result->id; ?>">
                        <span class="button__icon"><i class="bx bx-printer"></i></span>
                        <span class="button__text">Imprimir</span>
                    </a>
                </div>
            </div>
            <div class="widget-content" style="padding: 20px">
                <div class="row-fluid">
                    <div class="span12" style="margin-left: 0">
                        <div class="span6">
                            <h4>Fornecedor</h4>
                            <p><strong>Nome:</strong> <?php echo $result->nomeFornecedor ?></p>
                            <p><strong>Telefone:</strong> <?php echo $result->telefone_fornecedor ?></p>
                            <p><strong>Email:</strong> <?php echo $result->email_fornecedor ?></p>
                        </div>
                        <div class="span6">
                            <h4>Responsável</h4>
                            <p><strong>Nome:</strong> <?php echo $result->nome ?></p>
                            <p><strong>Telefone:</strong> <?php echo $result->telefone_usuario ?></p>
                            <p><strong>Email:</strong> <?php echo $result->email_usuario ?></p>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row-fluid">
                    <div class="span12" style="margin-left: 0">
                        <div class="span4">
                            <label for="status"><strong>Status:</strong></label>
                            <?php
                            $corStatus = match($result->status) {
                                'Pendente' => '#FF7F00',
                                'Aprovado' => '#4d9c79',
                                'Cancelado' => '#CD0000',
                                default => '#E0E4CC',
                            };
                            ?>
                            <span class="badge" style="background-color: <?php echo $corStatus; ?>; border-color: <?php echo $corStatus; ?>"><?php echo $result->status ?></span>
                        </div>
                        <div class="span4">
                            <label for="data"><strong>Data do Pedido:</strong></label>
                            <?php echo date('d/m/Y', strtotime($result->data_pedido)) ?>
                        </div>
                        <?php if ($result->data_aprovacao) { ?>
                            <div class="span4">
                                <label for="dataAprovacao"><strong>Data de Aprovação:</strong></label>
                                <?php echo date('d/m/Y', strtotime($result->data_aprovacao)) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <hr>
                <div class="row-fluid">
                    <div class="span12" style="margin-left: 0">
                        <div class="span12">
                            <label for="observacoes"><strong>Observações:</strong></label>
                            <?php echo $result->observacoes ?>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row-fluid" style="margin-top: 0">
                    <div class="span12">
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </span>
                                <h5>Produtos do Pedido</h5>
                            </div>
                            <div class="widget-content nopadding">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Produto</th>
                                            <th width="8%">Quantidade</th>
                                            <th width="10%">Preço Unit.</th>
                                            <th width="10%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach ($produtos as $p) {
                                            $totalProdutos = $totalProdutos + $p->subtotal;
                                            echo '<tr>';
                                            echo '<td>' . $p->descricao . '</td>';
                                            echo '<td>' . $p->quantidade . '</td>';
                                            echo '<td>R$ ' . number_format($p->preco_unitario, 2, ',', '.') . '</td>';
                                            echo '<td>R$ ' . number_format($p->subtotal, 2, ',', '.') . '</td>';
                                            echo '</tr>';
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="3" style="text-align: right; font-weight: bold">Total:</td>
                                            <td><strong>R$ <?php echo number_format($totalProdutos, 2, ',', '.'); ?></strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aprovar -->
<div id="modal-aprovar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/pedidoscompra/aprovar" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Aprovar Pedido de Compra</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idPedido" name="id" value="" />
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
$(document).ready(function() {
    $(document).on('click', 'a', function(event) {
        var pedido = $(this).attr('pedido');
        $('#idPedido').val(pedido);
    });
});
</script>

<script type="text/javascript">
    var baseUrl = '<?php echo base_url(); ?>';
</script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pedidoscompra.js"></script> 