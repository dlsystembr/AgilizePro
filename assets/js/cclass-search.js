// Função para pesquisar cClass
function pesquisarCClass(termo) {
    if (!termo || termo.length < 2) {
        $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center">Digite pelo menos 2 caracteres</td></tr>');
        $('#totalResultadosCClass').text('');
        return;
    }

    $.ajax({
        url: '<?php echo base_url(); ?>index.php/produtos/pesquisarCClass',
        type: 'POST',
        data: { termo: termo },
        dataType: 'json',
        success: function (response) {
            var tbody = $('#tabelaCClass tbody');
            tbody.empty();

            if (response.length > 0) {
                $.each(response, function (i, cclass) {
                    tbody.append(
                        '<tr>' +
                        '<td style="padding: 12px;">' + cclass.codigo + '</td>' +
                        '<td style="padding: 12px;">' + cclass.descricao + '</td>' +
                        '<td style="padding: 12px; text-align: center;">' +
                        '<button type="button" class="btn btn-success btn-sm selecionarCClass" ' +
                        'data-codigo="' + cclass.codigo + '" ' +
                        'data-descricao="' + cclass.descricao + '">Selecionar</button>' +
                        '</td>' +
                        '</tr>'
                    );
                });
                $('#totalResultadosCClass').text('Total: ' + response.length + ' resultado(s)');
            } else {
                tbody.append('<tr><td colspan="3" class="text-center">Nenhum cClass encontrado</td></tr>');
                $('#totalResultadosCClass').text('');
            }
        },
        error: function () {
            $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center text-danger">Erro na busca</td></tr>');
            $('#totalResultadosCClass').text('');
        }
    });
}

// Evento de digitação no campo de pesquisa cClass
$('#pesquisaCClass').on('input', function () {
    var termo = $(this).val();
    if (termo.length >= 2) {
        pesquisarCClass(termo);
    } else if (termo.length === 0) {
        $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center">Digite algo para pesquisar</td></tr>');
        $('#totalResultadosCClass').text('');
    }
});

// Evento de clique no botão pesquisar cClass
$('#btnPesquisarCClass').on('click', function () {
    pesquisarCClass($('#pesquisaCClass').val());
});

// Evento de seleção do cClass
$(document).on('click', '.selecionarCClass', function () {
    var codigo = $(this).data('codigo');
    var descricao = $(this).data('descricao');

    $('#PRO_CCLASS_SERV').val(codigo);
    $('#modalCClass').modal('hide');
});

// Limpar pesquisa ao abrir modal cClass
$('#modalCClass').on('show.bs.modal', function () {
    $('#pesquisaCClass').val('');
    $('#tabelaCClass tbody').html('<tr><td colspan="3" class="text-center">Digite algo para pesquisar</td></tr>');
    $('#totalResultadosCClass').text('');
});
