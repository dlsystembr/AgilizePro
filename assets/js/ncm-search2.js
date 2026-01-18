$(document).ready(function() {
    var $ncmInput = $('#NCMs');
    var $ncmId = $('#ncm_id');
    var baseUrl = window.location.origin + '/mapos/';

    // Busca o NCM inicial se houver um código
    if ($ncmInput.val()) {
        buscarNcm($ncmInput.val());
    }

    // Função para buscar NCM
    function buscarNcm(codigo) {
        if (!codigo) return;
        
        // Remove caracteres não numéricos
        codigo = codigo.replace(/[^0-9]/g, '');
        console.log('Buscando NCM:', codigo);
        
        $.ajax({
            url: baseUrl + 'index.php/ncms/buscar',
            type: 'GET',
            data: { termo: codigo },
            dataType: 'json',
            success: function(response) {
                console.log('Resposta:', response);
                
                if (response && response.length > 0) {
                    // Procura exatamente pelo código digitado
                    var ncmEncontrado = response.find(function(ncm) {
                        return ncm.codigo === codigo;
                    });
                    
                    if (ncmEncontrado) {
                        console.log('NCM encontrado:', ncmEncontrado);
                        $ncmInput.val(ncmEncontrado.codigo);
                        $ncmId.val(ncmEncontrado.id);
                        // Armazena a descrição no botão
                        $('#btnDescricaoNcm').data('descricao', ncmEncontrado.descricao);
                        console.log('ID do NCM:', ncmEncontrado.id);
                    } else {
                        console.log('NCM não encontrado - código não corresponde');
                        $ncmId.val('');
                        $('#btnDescricaoNcm').data('descricao', '');
                    }
                } else {
                    console.log('Nenhum NCM encontrado');
                    $ncmId.val('');
                    $('#btnDescricaoNcm').data('descricao', '');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na busca:', error);
                $ncmId.val('');
                $('#btnDescricaoNcm').data('descricao', '');
            }
        });
    }

    // Aceita apenas números no campo
    $ncmInput.on('input', function() {
        var valor = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(valor);
        
        // Se tiver 8 dígitos, busca automaticamente
        if (valor.length === 8) {
            buscarNcm(valor);
        }
    });

    // Busca quando sair do campo
    $ncmInput.on('blur', function() {
        var valor = $(this).val();
        if (valor.length > 0) {
            buscarNcm(valor);
        }
    });

    // Limpar campo ao focar
    $ncmInput.on('focus', function() {
        if ($(this).val() === '') {
            $ncmId.val('');
            $('#btnDescricaoNcm').data('descricao', '');
        }
    });

    // Mostrar descrição ao clicar no botão
    $('#btnDescricaoNcm').on('click', function() {
        var descricao = $(this).data('descricao');
        if (descricao) {
            // Remove alerta anterior se existir
            $('#ncm_alert').remove();
            
            // Cria novo alerta
            var alert = $('<div id="ncm_alert" class="alert alert-info" style="margin-top: 10px;">' + descricao + '</div>');
            $ncmInput.parent().after(alert);
            
            // Remove o alerta após 5 segundos
            setTimeout(function() {
                $('#ncm_alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        } else {
            // Remove alerta anterior se existir
            $('#ncm_alert').remove();
            
            // Cria novo alerta de erro
            var alert = $('<div id="ncm_alert" class="alert alert-warning" style="margin-top: 10px;">Nenhum NCM selecionado</div>');
            $ncmInput.parent().after(alert);
            
            // Remove o alerta após 5 segundos
            setTimeout(function() {
                $('#ncm_alert').fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }
    });

    // Validação do formulário
    $('#formProduto').on('submit', function(e) {
        if (!$ncmId.val() || $ncmId.val() === '0') {
            e.preventDefault();
            // Remove alerta anterior se existir
            $('#ncm_alert').remove();
            
            // Cria novo alerta de erro
            var alert = $('<div id="ncm_alert" class="alert alert-warning" style="margin-top: 10px;">Selecione um NCM válido</div>');
            $ncmInput.parent().after(alert);
            
            $ncmInput.focus();
            return false;
        }
    });

    // Se estiver na página de visualização e tiver um NCM, carregar a descrição
    if ($('#btnDescricaoNcm').length && $('#ncm_id').val()) {
        $.get(baseUrl + 'index.php/ncms/buscar', { termo: $('#NCMs').val() }, function(data) {
            if (data && data.length > 0) {
                var ncm = data.find(n => n.id == $('#ncm_id').val());
                if (ncm) {
                    $('#btnDescricaoNcm').data('descricao', ncm.descricao);
                }
            }
        });
    }

    // Função para pesquisar NCM
    function pesquisarNcm(termo) {
        $.ajax({
            url: baseUrl + 'produtos/pesquisarNcm',
            type: 'POST',
            data: { termo: termo },
            dataType: 'json',
            beforeSend: function() {
                $('#tabelaNcm tbody').html('<tr><td colspan="3" class="text-center"><i class="fas fa-spinner fa-spin"></i> Pesquisando...</td></tr>');
                $('#totalResultados').html('');
            },
            success: function(response) {
                if (response.length > 0) {
                    let html = '';
                    response.forEach(function(item) {
                        html += `
                            <tr>
                                <td>${item.codigo}</td>
                                <td>${item.descricao}</td>
                                <td>
                                    <button type="button" class="btn btn-success btn-sm selecionar-ncm" 
                                            data-codigo="${item.codigo}" 
                                            data-descricao="${item.descricao}">
                                        <i class="fas fa-check"></i> Selecionar
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tabelaNcm tbody').html(html);
                    $('#totalResultados').html(`Total de resultados: ${response.length}`);
                } else {
                    $('#tabelaNcm tbody').html('<tr><td colspan="3" class="text-center">Nenhum resultado encontrado</td></tr>');
                    $('#totalResultados').html('');
                }
            },
            error: function() {
                $('#tabelaNcm tbody').html('<tr><td colspan="3" class="text-center text-danger">Erro ao pesquisar NCM</td></tr>');
                $('#totalResultados').html('');
            }
        });
    }

    // Evento de clique no botão de pesquisa
    $('#btnPesquisarNcm').click(function() {
        const termo = $('#pesquisaNcm').val().trim();
        if (termo) {
            pesquisarNcm(termo);
        }
    });

    // Evento de tecla Enter no campo de pesquisa
    $('#pesquisaNcm').keypress(function(e) {
        if (e.which == 13) {
            const termo = $(this).val().trim();
            if (termo) {
                pesquisarNcm(termo);
            }
        }
    });

    // Evento de seleção do NCM
    $(document).on('click', '.selecionar-ncm', function() {
        const codigo = $(this).data('codigo');
        const descricao = $(this).data('descricao');
        
        $('#ncm').val(codigo);
        $('#descricaoNcm').val(descricao);
        
        $('#modalNcm').modal('hide');
    });

    // Limpar campos ao fechar o modal
    $('#modalNcm').on('hidden.bs.modal', function() {
        $('#pesquisaNcm').val('');
        $('#tabelaNcm tbody').html('');
        $('#totalResultados').html('');
    });
}); 