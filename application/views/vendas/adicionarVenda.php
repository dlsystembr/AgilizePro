<link rel="stylesheet" href="<?php echo base_url(); ?>assets/js/jquery-ui/css/smoothness/jquery-ui-1.9.2.custom.css" />
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery-ui/js/jquery-ui-1.9.2.custom.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<link rel="stylesheet" href="<?php echo base_url() ?>assets/trumbowyg/ui/trumbowyg.css">
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/trumbowyg.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/trumbowyg/langs/pt_br.js"></script>
<script src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css" />

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-cash-register"></i>
                </span>
                <h5>Adicionar Pedido</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" id="divProdutosServicos" style=" margin-left: 0">
                    <ul class="nav nav-tabs">
                        <li class="active" id="tabDetalhes"><a href="#tab1" data-toggle="tab">Detalhes do Pedido</a></li>
                        <li id="tabObservacoes"><a href="#tab2" data-toggle="tab">Observações</a></li>
                    </ul>
                    <div class="tab-content">
                        <!-- TAB 1: Detalhes do Pedido + Produtos -->
                        <div class="tab-pane active" id="tab1">
                            <div class="span12" id="divCadastrarPedido">
                                <?php if (isset($custom_error) && $custom_error == true) { ?>
                                    <div class="span12 alert alert-danger" id="divInfo" style="padding: 1%;">Dados incompletos, verifique os campos com asterisco ou se selecionou corretamente cliente e responsável.</div>
                                <?php } ?>
                                <form id="formPedido">
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span2" style="margin-left: 0">
                                            <label for="dataVenda">Data do Pedido<span class="required">*</span></label>
                                            <input id="dataVenda" class="span12 datepicker" type="text" name="dataVenda" value="<?php echo date('d/m/Y'); ?>" />
                                        </div>
                                        <div class="span3">
                                            <label for="cliente">Cliente<span class="required">*</span></label>
                                            <input id="cliente" class="span12" type="text" name="cliente" value="" />
                                            <input id="clientes_id" class="span12" type="hidden" name="clientes_id" value="" />
                                            <div class="addclient"><?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aCliente')) { ?>
                                              <a href="<?php echo base_url(); ?>index.php/clientes/adicionar" class="btn btn-success"><i class="fas fa-plus"></i> Adicionar Cliente</a><?php } ?></div>
                                        </div>
                                        <div class="span3">
                                            <label for="operacao_comercial">Operação Comercial<span class="required">*</span></label>
                                            <select name="operacao_comercial_id" id="operacao_comercial" class="span12" required>
                                                <option value="">Selecione...</option>
                                                <?php foreach ($operacoes as $o) { ?>
                                                    <option value="<?php echo $o->OPC_ID; ?>"><?php echo $o->OPC_NOME; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="span3">
                                            <label for="tecnico">Vendedor<span class="required">*</span></label>
                                            <input id="tecnico" class="span12" type="text" name="tecnico" value="<?= $this->session->userdata('nome_admin'); ?>" />
                                            <input id="usuarios_id" class="span12" type="hidden" name="usuarios_id" value="<?= $this->session->userdata('id_admin'); ?>" />
                                        </div>
                                    </div>
                                    <div class="span12" style="padding: 1%; margin-left: 0">
                                        <div class="span2" style="margin-left: 0">
                                            <label for="status">Status<span class="required">*</span></label>
                                            <select class="span12" name="status" id="status" value="">
                                                <option value="Orçamento">Orçamento</option>
                                                <option value="Aberto">Aberto</option>
                                                <option value="Faturado">Faturado</option>
                                                <option value="Em Andamento">Em Andamento</option>
                                                <option value="Finalizado">Finalizado</option>
                                                <option value="Cancelado">Cancelado</option>
                                                <option value="Aguardando Peças">Aguardando Peças</option>
                                                <option value="Aprovado">Aprovado</option>
                                            </select>
                                        </div>
                                        <div class="span2">
                                            <label for="garantia">Garantia (dias)</label>
                                            <input id="garantia" type="number" placeholder="Em Dias" min="0" max="9999" class="span12" name="garantia" value="" />
                                        </div>
                                    </div>
                                </form>

                                <!-- SEÇÃO DE PRODUTOS (na mesma aba) -->
                                <div class="span12" style="margin-top: 20px; margin-left: 0">
                                    <h4 style="margin-left: 10px;">Produtos</h4>
                                    <div class="span12 well" style="padding: 1%; margin-left: 0">
                                        <div class="span11">
                                            <form id="formAdicionarProduto">
                                                <div class="span6">
                                                    <input type="hidden" name="idProduto" id="idProduto" />
                                                    <input type="hidden" name="estoque" id="estoque" value="" />
                                                    <label for="">Produto</label>
                                                    <input type="text" class="span12" name="produto" id="produto" placeholder="Digite o nome do produto" />
                                                </div>
                                                <div class="span2">
                                                    <label for="">Preço</label>
                                                    <input type="text" placeholder="Preço" id="preco" name="preco" class="span12" />
                                                </div>
                                                <div class="span2">
                                                    <label for="">Quantidade</label>
                                                    <input type="text" placeholder="Quantidade" id="quantidade" name="quantidade" class="span12" />
                                                </div>
                                                <div class="span2">
                                                    <label for="">&nbsp</label>
                                                    <button type="button" class="button btn btn-success" id="btnAdicionarProdutoTemp">
                                                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar</span></button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="span12" id="divProdutosLista" style="margin-left: 0">
                                        <table class="table table-bordered" id="tblProdutosTemp">
                                            <thead>
                                                <tr>
                                                    <th>Produto</th>
                                                    <th width="8%">Quantidade</th>
                                                    <th width="10%">Preço</th>
                                                    <th width="6%">Ações</th>
                                                    <th width="10%">Sub-total</th>
                                                </tr>
                                            </thead>
                                            <tbody id="produtosTableBody">
                                                <!-- Produtos serão adicionados aqui via JavaScript -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" style="text-align: right"><strong>Total:</strong></td>
                                                    <td>
                                                        <div align="center"><strong>R$: <span id="totalPedido">0.00</span></strong></div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Observações -->
                        <div class="tab-pane" id="tab2">
                            <div class="span12" style="padding: 1%; margin-left: 0">
                                <div class="span6" style="margin-left: 0">
                                    <label for="observacoes">
                                        <h4>Observações Internas</h4>
                                    </label>
                                    <textarea class="editor" name="observacoes" id="observacoes" cols="30" rows="5"></textarea>
                                </div>

                                <div class="span6" style="margin-left: 0">
                                    <label for="observacoes_cliente">
                                        <h4>Observações ao Cliente</h4>
                                    </label>
                                    <textarea class="editor" name="observacoes_cliente" id="observacoes_cliente" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botões de ação -->
                    <div class="span12" style="padding: 1%; margin-left: 0">
                        <div class="span6 offset3" style="display:flex;justify-content: center">
                          <button class="button btn btn-success" id="btnGravarPedido">
                            <span class="button__icon"><i class='bx bx-save'></i></span>
                            <span class="button__text2">Gravar Pedido</span>
                          </button>
                          <a href="<?php echo base_url() ?>index.php/vendas" class="button btn btn-mini btn-warning">
                            <span class="button__icon"><i class="bx bx-undo"></i></span> 
                            <span class="button__text2">Voltar</span>
                          </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    // Array temporário para armazenar produtos antes de gravar
    var produtosTemp = [];

    $(document).ready(function() {
        $('.addclient').hide();
        
        // Autocomplete Cliente
        $("#cliente").autocomplete({
            source: "<?php echo base_url(); ?>index.php/vendas/autoCompleteCliente",
            minLength: 1,
            close: function(ui) { if(ui.label == 'Adicionar cliente...')ui.target.value = '';},
            select: function(event, ui) {
                if(ui.item.label == 'Adicionar cliente...')
                    $('.addclient').show();
                else {
                    $("#clientes_id").val(ui.item.id);
                    $('.addclient').hide();
                }
            }
        });

        // Autocomplete Vendedor
        $("#tecnico").autocomplete({
            source: "<?php echo base_url(); ?>index.php/vendas/autoCompleteUsuario",
            minLength: 1,
            select: function(event, ui) {
                $("#usuarios_id").val(ui.item.id);
            }
        });

        // Autocomplete Produto
        $("#produto").autocomplete({
            source: "<?php echo base_url(); ?>index.php/vendas/autoCompleteProduto",
            minLength: 2,
            select: function(event, ui) {
                event.preventDefault();
                if (ui.item) {
                    $("#idProduto").val(ui.item.id);
                    $("#produto").val(ui.item.label);
                    $("#estoque").val(ui.item.estoque);
                    $("#preco").val(ui.item.preco);
                    $("#preco").focus();
                }
                return false;
            }
        });

        $('#preco').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#quantidade').focus();
            }
        });

        $('#quantidade').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $('#btnAdicionarProdutoTemp').click();
            }
        });

        // Adicionar produto ao array temporário
        $("#btnAdicionarProdutoTemp").click(function(e) {
            e.preventDefault();
            
            var idProduto = $("#idProduto").val();
            var nomeProduto = $("#produto").val();
            var preco = parseFloat($("#preco").val()) || 0;
            var quantidade = parseInt($("#quantidade").val()) || 0;
            var estoque = parseInt($("#estoque").val()) || 0;

            if (!idProduto || !nomeProduto) {
                Swal.fire({
                    type: "warning",
                    title: "Atenção",
                    text: "Selecione um produto válido."
                });
                return;
            }

            if (quantidade <= 0) {
                Swal.fire({
                    type: "warning",
                    title: "Atenção",
                    text: "Informe uma quantidade válida."
                });
                return;
            }

            <?php if ($this->config->item('control_estoque')) { ?>
            if (estoque < quantidade) {
                Swal.fire({
                    type: "warning",
                    title: "Atenção",
                    text: "Você não possui estoque suficiente."
                });
                return;
            }
            <?php } ?>

            var subtotal = preco * quantidade;

            produtosTemp.push({
                idProduto: idProduto,
                nomeProduto: nomeProduto,
                preco: preco,
                quantidade: quantidade,
                subtotal: subtotal
            });

            atualizarTabelaProdutos();
            limparFormProduto();
        });

        // Atualizar tabela de produtos
        function atualizarTabelaProdutos() {
            var tbody = $("#produtosTableBody");
            tbody.empty();
            var total = 0;

            produtosTemp.forEach(function(produto, index) {
                total += produto.subtotal;
                var row = '<tr>' +
                    '<td>' + produto.nomeProduto + '</td>' +
                    '<td><div align="center">' + produto.quantidade + '</div></td>' +
                    '<td><div align="center">R$: ' + produto.preco.toFixed(2) + '</div></td>' +
                    '<td><div align="center"><a href="#" class="btn-nwe4 remover-produto" data-index="' + index + '"><i class="bx bx-trash-alt"></i></a></div></td>' +
                    '<td><div align="center">R$: ' + produto.subtotal.toFixed(2) + '</div></td>' +
                    '</tr>';
                tbody.append(row);
            });

            $("#totalPedido").text(total.toFixed(2));
        }

        // Remover produto do array temporário
        $(document).on('click', '.remover-produto', function(e) {
            e.preventDefault();
            var index = $(this).data('index');
            produtosTemp.splice(index, 1);
            atualizarTabelaProdutos();
        });

        // Limpar formulário de produto
        function limparFormProduto() {
            $("#idProduto").val('');
            $("#produto").val('');
            $("#preco").val('');
            $("#quantidade").val('');
            $("#estoque").val('');
            $("#produto").focus();
        }

        // Gravar pedido completo
        $("#btnGravarPedido").click(function(e) {
            e.preventDefault();

            if (produtosTemp.length === 0) {
                Swal.fire({
                    type: "warning",
                    title: "Atenção",
                    text: "Adicione pelo menos um produto ao pedido."
                });
                return;
            }

            if (!$("#clientes_id").val()) {
                Swal.fire({
                    type: "warning",
                    title: "Atenção",
                    text: "Selecione um cliente."
                });
                return;
            }

            if (!$("#operacao_comercial").val()) {
                Swal.fire({
                    type: "warning",
                    title: "Atenção",
                    text: "Selecione uma operação comercial."
                });
                return;
            }

            var dados = {
                dataVenda: $("#dataVenda").val(),
                clientes_id: $("#clientes_id").val(),
                usuarios_id: $("#usuarios_id").val(),
                operacao_comercial_id: $("#operacao_comercial").val(),
                status: $("#status").val(),
                garantia: $("#garantia").val(),
                observacoes: $("#observacoes").val(),
                observacoes_cliente: $("#observacoes_cliente").val(),
                produtos: JSON.stringify(produtosTemp)
            };

            $.ajax({
                type: "POST",
                url: "<?php echo base_url(); ?>index.php/vendas/salvarPedidoCompleto",
                data: dados,
                dataType: 'json',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Processando',
                        text: 'Salvando pedido...',
                        icon: 'info',
                        showCloseButton: false,
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                },
                success: function(data) {
                    if (data.result == true) {
                        Swal.fire({
                            type: "success",
                            title: "Sucesso",
                            text: data.message
                        }).then(function() {
                            window.location.href = "<?php echo base_url(); ?>index.php/vendas/editar/" + data.pedidoId;
                        });
                    } else {
                        Swal.fire({
                            type: "error",
                            title: "Erro",
                            text: data.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        type: "error",
                        title: "Erro",
                        text: "Ocorreu um erro ao salvar o pedido."
                    });
                }
            });
        });

        $(".datepicker").datepicker({
            dateFormat: 'dd/mm/yy'
        });

        $('.editor').trumbowyg({
            lang: 'pt_br',
            semantic: { 'strikethrough': 's', }
        });

        $('.addclient').hide();
    });
</script>
