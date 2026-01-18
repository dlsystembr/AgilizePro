$(document).ready(function() {
    // Inicialização do autocomplete para fornecedor
    $("#fornecedor").autocomplete({
        source: baseUrl + "index.php/pedidoscompra/autoCompleteFornecedor",
        minLength: 1,
        select: function(event, ui) {
            $("#fornecedor_id").val(ui.item.id);
        }
    });

    // Inicialização do autocomplete para usuário
    $("#usuario").autocomplete({
        source: baseUrl + "index.php/pedidoscompra/autoCompleteUsuario",
        minLength: 1,
        select: function(event, ui) {
            $("#usuario_id").val(ui.item.id);
        }
    });

    // Inicialização do autocomplete para produto
    $("#produto").autocomplete({
        source: baseUrl + "index.php/pedidoscompra/autoCompleteProduto",
        minLength: 1,
        select: function(event, ui) {
            $("#idProduto").val(ui.item.id);
            $("#preco").val(ui.item.preco);
            $("#quantidade").focus();
        }
    });

    // Inicialização do datepicker
    $(".datepicker").datepicker({
        dateFormat: 'dd/mm/yy'
    });

    // Inicialização da máscara monetária
    $(".money").maskMoney({
        prefix: 'R$ ',
        allowNegative: false,
        thousands: '.',
        decimal: ',',
        affixesStay: false
    });

    // Validação do formulário
    $('#formPedido').validate({
        rules: {
            data_pedido: {
                required: true
            },
            fornecedor_id: {
                required: true
            },
            usuario_id: {
                required: true
            }
        },
        messages: {
            data_pedido: {
                required: 'Por favor, informe a data do pedido'
            },
            fornecedor_id: {
                required: 'Por favor, selecione um fornecedor'
            },
            usuario_id: {
                required: 'Por favor, selecione um responsável'
            }
        },
        errorClass: "help-inline",
        errorElement: "span",
        highlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').addClass('error');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).parents('.control-group').removeClass('error');
        },
        submitHandler: function(form) {
            var produtos = $('#tblProdutos tbody tr').length;
            if (produtos === 0 || ($('#tblProdutos tbody tr').length === 1 && $('#tblProdutos tbody tr:first td').length === 1)) {
                Swal.fire({
                    type: "error",
                    title: "Atenção",
                    text: "Adicione pelo menos um produto ao pedido."
                });
                return false;
            }
            form.submit();
        }
    });

    // Adicionar produto ao pedido
    $('#btnAdicionarProduto').on('click', function(e) {
        e.preventDefault();
        
        var produto = $("#produto").val();
        var quantidade = $("#quantidade").val();
        var preco = $("#preco").val();
        var idProduto = $("#idProduto").val();
        
        if (produto === '' || quantidade === '' || preco === '' || !idProduto) {
            Swal.fire({
                type: "error",
                title: "Atenção",
                text: "Preencha todos os campos para adicionar um produto."
            });
            return false;
        }
        
        if (parseInt(quantidade) <= 0) {
            Swal.fire({
                type: "error",
                title: "Atenção",
                text: "A quantidade deve ser maior que zero."
            });
            return false;
        }
        
        preco = preco.replace('R$ ', '').replace('.', '').replace(',', '.');
        quantidade = parseInt(quantidade);
        var subtotal = parseFloat(preco) * parseInt(quantidade);
        
        var row = $('<tr>');
        row.append($('<td>').text(produto).append($('<input>').attr({
            type: 'hidden',
            name: 'produtos[]',
            value: idProduto
        })));
        row.append($('<td>').text(quantidade).append($('<input>').attr({
            type: 'hidden',
            name: 'quantidades[]',
            value: quantidade
        })));
        row.append($('<td>').text('R$ ' + parseFloat(preco).toFixed(2).replace('.', ',')).append($('<input>').attr({
            type: 'hidden',
            name: 'precos[]',
            value: preco
        })));
        row.append($('<td>').text('R$ ' + subtotal.toFixed(2).replace('.', ',')).append($('<input>').attr({
            type: 'hidden',
            name: 'subtotais[]',
            value: subtotal.toFixed(2)
        })));
        row.append($('<td>').html('<button class="btn-nwe4 excluir" title="Excluir Produto"><i class="bx bx-trash-alt"></i></button>'));
        
        if ($('#tblProdutos tbody tr:first').find('td').length === 1) {
            $('#tblProdutos tbody').html('');
        }
        
        $('#tblProdutos tbody').append(row);
        
        // Limpa os campos
        $("#produto").val('');
        $("#idProduto").val('');
        $("#quantidade").val('');
        $("#preco").val('');
        
        atualizarTotal();
    });

    // Excluir produto do pedido
    $('#tblProdutos').on('click', '.excluir', function(e) {
        e.preventDefault();
        $(this).closest('tr').remove();
        
        if ($('#tblProdutos tbody tr').length === 0) {
            $('#tblProdutos tbody').html('<tr><td colspan="5">Nenhum produto adicionado</td></tr>');
        }
        
        atualizarTotal();
    });

    // Atualizar total do pedido
    function atualizarTotal() {
        var total = 0;
        
        $('#tblProdutos tbody tr').each(function() {
            if ($(this).find('td').length > 1) {
                var subtotal = $(this).find('input[name="subtotais[]"]').val();
                total += parseFloat(subtotal);
            }
        });
        
        $('#total').text('R$ ' + total.toFixed(2).replace('.', ','));
        $('#total-input').val(total.toFixed(2));
    }
}); 