<style>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <h5>Editar Pedido de Compra</h5>
            </div>
            <div class="widget-content nopadding">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes do Pedido</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divEditarPedido">
                                <?php if ($custom_error == true) { ?>
                                    <div class="span12 alert alert-danger"><?php echo $custom_error; ?></div>
                                <?php } ?>
                                <form action="<?php echo base_url() ?>index.php/pedidoscompra/editar" method="post" id="formPedido">
                                    <input type="hidden" name="idPedido" value="<?php echo $result->idPedido; ?>" />
                                    <div class="span12" style="padding: 1%;">
                                        <div class="span6">
                                            <label for="data_pedido">Data do Pedido</label>
                                            <input id="data_pedido" class="span12 datepicker" type="text" name="data_pedido" value="<?php echo date('d/m/Y', strtotime($result->data_pedido)); ?>" required />
                                        </div>
                                        <div class="span6">
                                            <label for="fornecedor">Fornecedor</label>
                                            <select class="span12" name="fornecedor_id" id="fornecedor" required>
                                                <option value="">Selecione o Fornecedor</option>
                                                <?php foreach ($fornecedores as $f) {
                                                    $selected = ($f->idFornecedores == $result->fornecedor_id) ? 'selected' : '';
                                                    echo '<option value="' . $f->idFornecedores . '" ' . $selected . '>' . $f->nomeFornecedor . '</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span12">
                                            <label for="observacoes">Observações</label>
                                            <textarea class="span12" name="observacoes" id="observacoes" rows="3"><?php echo $result->observacoes; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span8">
                                            <label for="produto">Produto</label>
                                            <select class="span12" name="produto_id" id="produto">
                                                <option value="">Selecione o Produto</option>
                                                <?php foreach ($produtos as $p) {
                                                    echo '<option value="' . $p->idProdutos . '">' . $p->descricao . '</option>';
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="span2">
                                            <label for="quantidade">Quantidade</label>
                                            <input id="quantidade" class="span12" type="number" min="1" placeholder="Quantidade" />
                                        </div>
                                        <div class="span2">
                                            <label for="preco">Preço Unitário</label>
                                            <input id="preco" class="span12 money" type="text" placeholder="Preço" />
                                        </div>
                                        <div class="span12" style="margin-left: 0; padding-top: 10px;">
                                            <button class="btn btn-success" id="btnAdicionarProduto">
                                                <i class="fas fa-plus"></i> Adicionar Produto
                                            </button>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="widget-box">
                                            <div class="widget-title">
                                                <span class="icon">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </span>
                                                <h5>Produtos do Pedido</h5>
                                            </div>
                                            <div class="widget-content nopadding">
                                                <table class="table table-bordered" id="tblProdutos">
                                                    <thead>
                                                        <tr>
                                                            <th>Produto</th>
                                                            <th width="10%">Quantidade</th>
                                                            <th width="10%">Preço Unit.</th>
                                                            <th width="10%">Subtotal</th>
                                                            <th width="5%">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $total = 0;
                                                        foreach ($produtos as $p) {
                                                            $preco = number_format($p->preco_unitario, 2, ',', '.');
                                                            $subtotal = number_format($p->subtotal, 2, ',', '.');
                                                            $total += $p->subtotal;
                                                            
                                                            echo '<tr>';
                                                            echo '<td>' . $p->descricao . '<input type="hidden" name="produto_id[]" value="' . $p->produto_id . '" /></td>';
                                                            echo '<td>' . $p->quantidade . '<input type="hidden" name="quantidade[]" value="' . $p->quantidade . '" /></td>';
                                                            echo '<td>R$ ' . $preco . '<input type="hidden" name="preco[]" value="' . $preco . '" /></td>';
                                                            echo '<td>R$ ' . $subtotal . '<input type="hidden" name="subtotal[]" value="' . $p->subtotal . '" /></td>';
                                                            echo '<td><button class="btn btn-danger" onclick="removerProduto(this)" type="button"><i class="fas fa-trash-alt"></i></button></td>';
                                                            echo '</tr>';
                                                        }
                                                        
                                                        if (count($produtos) == 0) {
                                                            echo '<tr><td colspan="5">Nenhum produto adicionado</td></tr>';
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td colspan="3" style="text-align: right; font-weight: bold">Total:</td>
                                                            <td>
                                                                <span id="total">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                                                                <input type="hidden" id="total-input" name="total" value="<?php echo number_format($total, 2, '.', ''); ?>" />
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="text-align: center">
                                            <button class="btn btn-primary" id="btnSalvar">
                                                <i class="fas fa-sync-alt"></i> Atualizar
                                            </button>
                                            <a href="<?php echo base_url() ?>index.php/pedidoscompra" class="btn">
                                                <i class="fas fa-times"></i> Cancelar
                                            </a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var baseUrl = '<?php echo base_url(); ?>';
</script>
<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo base_url(); ?>assets/js/maskmoney.js"></script>
<script src="<?php echo base_url(); ?>assets/js/pedidoscompra.js"></script>

$(document).ready(function() {
    $('.datepicker').datepicker({
        dateFormat: 'dd/mm/yy'
    });
    $('.money').maskMoney({
        prefix: 'R$ ',
        allowNegative: false,
        thousands: '.',
        decimal: ',',
        affixesStay: false
    });

    $('#btnAdicionarProduto').on('click', function(e) {
        e.preventDefault();
        
        var produto = $('#produto').val();
        var quantidade = $('#quantidade').val();
        var preco = $('#preco').val();
        
        if (produto === '' || quantidade === '' || preco === '') {
            alert('Por favor, preencha todos os campos.');
            return false;
        }
        
        var produtoText = $('#produto option:selected').text();
        preco = preco.replace('R$ ', '');
        var subtotal = parseFloat(quantidade) * parseFloat(preco.replace('.', '').replace(',', '.'));
        
        if ($('#tblProdutos tbody tr:first td').length === 1) {
            $('#tblProdutos tbody').html('');
        }
        
        var html = '<tr>';
        html += '<td>' + produtoText + '<input type="hidden" name="produto_id[]" value="' + produto + '" /></td>';
        html += '<td>' + quantidade + '<input type="hidden" name="quantidade[]" value="' + quantidade + '" /></td>';
        html += '<td>R$ ' + preco + '<input type="hidden" name="preco[]" value="' + preco + '" /></td>';
        html += '<td>R$ ' + subtotal.toFixed(2).replace('.', ',') + '<input type="hidden" name="subtotal[]" value="' + subtotal.toFixed(2) + '" /></td>';
        html += '<td><button class="btn btn-danger" onclick="removerProduto(this)" type="button"><i class="fas fa-trash-alt"></i></button></td>';
        html += '</tr>';
        
        $('#tblProdutos tbody').append(html);
        
        atualizarTotal();
        
        $('#produto').val('');
        $('#quantidade').val('');
        $('#preco').val('');
    });

    $('#formPedido').submit(function(e) {
        e.preventDefault();
        
        if ($('#tblProdutos tbody tr:first td').length === 1) {
            alert('Por favor, adicione pelo menos um produto ao pedido.');
            return false;
        }
        
        var produtos = [];
        $('#tblProdutos tbody tr').each(function() {
            var produto = {
                produto: $(this).find('input[name="produto_id[]"]').val(),
                quantidade: $(this).find('input[name="quantidade[]"]').val(),
                preco: $(this).find('input[name="preco[]"]').val(),
                subtotal: $(this).find('input[name="subtotal[]"]').val()
            };
            produtos.push(produto);
        });
        
        $('<input>').attr({
            type: 'hidden',
            name: 'produtos',
            value: JSON.stringify(produtos)
        }).appendTo(this);
        
        this.submit();
    });
});

function removerProduto(btn) {
    if (confirm('Deseja realmente excluir este produto?')) {
        $(btn).closest('tr').remove();
        
        if ($('#tblProdutos tbody tr').length === 0) {
            $('#tblProdutos tbody').html('<tr><td colspan="5">Nenhum produto adicionado</td></tr>');
        }
        
        atualizarTotal();
    }
}

function atualizarTotal() {
    var total = 0;
    $('#tblProdutos tbody tr').each(function() {
        if ($(this).find('td').length > 1) {
            var subtotal = $(this).find('input[name="subtotal[]"]').val();
            total += parseFloat(subtotal);
        }
    });
    
    $('#total').html('R$ ' + total.toFixed(2).replace('.', ','));
    $('#total-input').val(total.toFixed(2));
}
</script> 