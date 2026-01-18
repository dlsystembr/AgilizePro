<style>
    /* Adjust select2 size */
    .select2-container .select2-choice {
        height: 30px;
        line-height: 30px;
        width: 100%;
    }
    .select2-container .select2-choice .select2-arrow b {
        background-position: 0 3px;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </span>
                <h5>Cadastro de Pedido de Compra</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes do Pedido</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarOs">
                                <?php if ($custom_error == true) { ?>
                                    <div class="span12 alert alert-danger" id="divInfo" style="padding: 1%;">
                                        <?php echo form_error('fornecedor_id', '<p>', '</p>'); ?>
                                        <?php echo form_error('usuario_id', '<p>', '</p>'); ?>
                                        <?php echo form_error('data_pedido', '<p>', '</p>'); ?>
                                        <?php if (!form_error('fornecedor_id') && !form_error('usuario_id') && !form_error('data_pedido')) { ?>
                                            <p>Dados incompletos, verifique os campos com asterisco ou se selecionou corretamente fornecedor e responsável.</p>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <form action="<?php echo current_url(); ?>" method="post" id="formPedido">
                                    <input type="hidden" id="fornecedor_id" name="fornecedor_id" value="">
                                    <input type="hidden" id="usuario_id" name="usuario_id" value="">
                                    <input type="hidden" id="idProduto" name="idProduto" value="">
                                    <div class="span12" style="padding: 1%">
                                        <div class="span3">
                                            <label for="dataPedido">Data do Pedido<span class="required">*</span></label>
                                            <input id="dataPedido" class="span12 datepicker" type="text" name="data_pedido" value="<?php echo date('d/m/Y'); ?>" />
                                        </div>
                                        <div class="span5">
                                            <label for="fornecedor">Fornecedor<span class="required">*</span></label>
                                            <select class="span12" name="fornecedor_id" id="fornecedor" required>
                                                <option value="">Selecione o Fornecedor</option>
                                            </select>
                                        </div>
                                        <div class="span4">
                                            <label for="usuario">Responsável<span class="required">*</span></label>
                                            <select class="span12" name="usuario_id" id="usuario" required>
                                                <option value="">Selecione o Responsável</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span12">
                                            <label for="observacoes">Observações</label>
                                            <textarea class="span12" name="observacoes" id="observacoes" rows="3"></textarea>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span8">
                                            <label for="produto">Produto</label>
                                            <input type="text" class="span12" name="produto" id="produto" placeholder="Digite o nome do produto" />
                                        </div>
                                        <div class="span2">
                                            <label for="quantidade">Quantidade</label>
                                            <input type="text" placeholder="Quantidade" id="quantidade" name="quantidade" class="span12" />
                                        </div>
                                        <div class="span2">
                                            <label for="preco">Preço Unitário</label>
                                            <input type="text" placeholder="Preço" id="preco" name="preco" class="span12 money" />
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="display: flex;justify-content: center">
                                            <button class="button btn btn-success" id="btnAdicionarProduto">
                                                <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                                                <span class="button__text2">Adicionar Produto</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <table class="table table-bordered" id="tblProdutos">
                                            <thead>
                                                <tr>
                                                    <th>Produto</th>
                                                    <th width="8%">Quantidade</th>
                                                    <th width="10%">Preço Unit.</th>
                                                    <th width="10%">Subtotal</th>
                                                    <th width="8%">Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td colspan="5">Nenhum produto adicionado</td>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" style="text-align: right; font-weight: bold">Total:</td>
                                                    <td>
                                                        <span id="total">R$ 0,00</span>
                                                        <input type="hidden" id="total-input" name="total" value="0.00">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span6 offset3" style="text-align: center">
                                            <button class="btn btn-success" id="btnContinuar"><i class="fas fa-share"></i> Continuar</button>
                                            <a href="<?php echo base_url() ?>index.php/pedidoscompra" class="btn"><i class="fas fa-backward"></i> Voltar</a>
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

<script type="text/javascript">
    $(document).ready(function() {
        $("#fornecedor").autocomplete({
            source: "<?php echo base_url(); ?>index.php/pedidoscompra/autoCompleteFornecedor",
            minLength: 1,
            select: function(event, ui) {
                $("#fornecedor_id").val(ui.item.id);
            }
        });

        $("#usuario").autocomplete({
            source: "<?php echo base_url(); ?>index.php/pedidoscompra/autoCompleteUsuario",
            minLength: 1,
            select: function(event, ui) {
                $("#usuario_id").val(ui.item.id);
            }
        });

        $("#produto").autocomplete({
            source: "<?php echo base_url(); ?>index.php/pedidoscompra/autoCompleteProduto",
            minLength: 1,
            select: function(event, ui) {
                $("#idProduto").val(ui.item.id);
                $("#preco").val(ui.item.preco);
                $("#quantidade").focus();
            }
        });

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $(".money").maskMoney({
            prefix: 'R$ ',
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        });

        $('#btnAdicionarProduto').on('click', function(e) {
            e.preventDefault();
            
            var produto = $("#produto").val();
            var quantidade = $("#quantidade").val();
            var preco = $("#preco").val();
            
            if (produto === '' || quantidade === '' || preco === '') {
                Swal.fire({
                    type: "error",
                    title: "Atenção",
                    text: "Preencha todos os campos para adicionar um produto."
                });
                return false;
            }
            
            preco = preco.replace('R$ ', '').replace('.', '').replace(',', '.');
            quantidade = parseInt(quantidade);
            var subtotal = parseFloat(preco) * parseInt(quantidade);
            
            var row = $('<tr>');
            row.append($('<td>').text(produto));
            row.append($('<td>').text(quantidade));
            row.append($('<td>').text('R$ ' + parseFloat(preco).toFixed(2).replace('.', ',')));
            row.append($('<td>').text('R$ ' + subtotal.toFixed(2).replace('.', ',')));
            row.append($('<td>').html('<button class="btn-nwe4 excluir" title="Excluir Produto"><i class="bx bx-trash-alt"></i></button>'));
            
            if ($('#tblProdutos tbody tr:first').find('td').length === 1) {
                $('#tblProdutos tbody').html('');
            }
            
            $('#tblProdutos tbody').append(row);
            
            // Limpa os campos
            $("#produto").val('');
            $("#quantidade").val('');
            $("#preco").val('');
            
            atualizarTotal();
        });

        $('#tblProdutos').on('click', '.excluir', function(e) {
            e.preventDefault();
            $(this).closest('tr').remove();
            
            if ($('#tblProdutos tbody tr').length === 0) {
                $('#tblProdutos tbody').html('<tr><td colspan="5">Nenhum produto adicionado</td></tr>');
            }
            
            atualizarTotal();
        });

        function atualizarTotal() {
            var total = 0;
            
            $('#tblProdutos tbody tr').each(function() {
                if ($(this).find('td').length > 1) {
                    var subtotal = $(this).find('td').eq(3).text().replace('R$ ', '').replace('.', '').replace(',', '.');
                    total += parseFloat(subtotal);
                }
            });
            
            $('#total').text('R$ ' + total.toFixed(2).replace('.', ','));
            $('#total-input').val(total.toFixed(2));
        }

        $('#formPedido').submit(function(e) {
            e.preventDefault();
            
            if ($('#fornecedor').val() === '' || $('#fornecedor_id').val() === '') {
                Swal.fire({
                    type: "error",
                    title: "Atenção",
                    text: "Por favor, selecione um fornecedor."
                });
                return false;
            }
            
            if ($('#usuario').val() === '' || $('#usuario_id').val() === '') {
                Swal.fire({
                    type: "error",
                    title: "Atenção",
                    text: "Por favor, selecione um responsável."
                });
                return false;
            }
            
            if ($('#tblProdutos tbody tr:first').find('td').length === 1) {
                Swal.fire({
                    type: "error",
                    title: "Atenção",
                    text: "Adicione pelo menos um produto ao pedido."
                });
                return false;
            }
            
            var form = this;
            form.submit();
        });
    });
</script> 