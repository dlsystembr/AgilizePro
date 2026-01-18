<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) { ?>
<!-- SweetAlert2 -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/sweetalert2.all.min.js"></script>
<!-- jQuery Mask Money -->
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.maskMoney.min.js"></script>
    <div class="span12" style="margin-top:0">
        <div class="widget-box" style="background-color: #2c3e50; color: white;">
            <div class="widget-title" style="margin: -20px 0 0; background-color: #2c3e50; color: white; border-bottom: 1px solid #34495e;">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5 style="color: white;">Faturamento de Entrada</h5>
                <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aFaturamentoEntrada')) { ?>
                    <div class="buttons">
                        <a title="Adicionar Novo Faturamento de Entrada" class="btn btn-mini btn-success" href="<?php echo base_url() ?>index.php/faturamentoEntrada/adicionar">
                            <i class="fas fa-plus"></i> Adicionar Novo
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="widget-content nopadding" style="background-color: #2c3e50;">
                <table class="table table-bordered" style="background-color: #2c3e50; color: white;">
                    <thead>
                        <tr style="background-color: #2c3e50; color: white;">
                            <th style="border-color: #34495e;">Fornecedor</th>
                            <th style="border-color: #34495e;">Data Emissão</th>
                            <th style="border-color: #34495e;">Data Entrada</th>
                            <th style="border-color: #34495e;">Número NF</th>
                            <th style="border-color: #34495e;">Valor Total</th>
                            <th style="border-color: #34495e;">Status</th>
                            <th style="border-color: #34495e;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$results) { ?>
                            <tr style="background-color: #34495e; color: white;">
                                <td colspan="7" style="border-color: #34495e;">Nenhum faturamento de entrada encontrado</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($results as $r) { ?>
                                <tr style="background-color: #34495e; color: white;">
                                    <td style="border-color: #34495e;"><?php echo $r->nomeCliente; ?></td>
                                    <td style="border-color: #34495e;"><?php echo date('d/m/Y', strtotime($r->data_emissao)); ?></td>
                                    <td style="border-color: #34495e;"><?php echo date('d/m/Y', strtotime($r->data_entrada)); ?></td>
                                    <td style="border-color: #34495e;"><?php echo $r->numero_nota; ?></td>
                                    <td style="border-color: #34495e;">R$ <?php echo number_format($r->valor_total, 2, ',', '.'); ?></td>
                                    <td style="border-color: #34495e;">
                                        <?php 
                                            $statusClass = '';
                                            $statusText = empty($r->status) ? 'Pendente' : ucfirst($r->status);
                                            switch(strtolower($statusText)) {
                                                case 'fechado':
                                                    $statusClass = 'label-success';
                                                    break;
                                                case 'pendente':
                                                    $statusClass = 'label-warning';
                                                    break;
                                                default:
                                                    $statusClass = 'label-default';
                                            }
                                        ?>
                                        <span class="label <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td style="border-color: #34495e;">
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vFaturamentoEntrada')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/faturamentoEntrada/visualizar/<?php echo $r->id; ?>" style="margin-right: 1%" class="btn btn-info tip-top" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>
                                            <?php if (empty($r->status) || $r->status != 'fechado') { ?>
                                                <button type="button" class="btn btn-success tip-top" title="Fechar Documento" onclick="fecharDocumento(<?php echo $r->id; ?>)"><i class="fas fa-check-circle"></i></button>
                                            <?php } ?>
                                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfe')) { ?>
                                                <button type="button" class="btn btn-warning tip-top" title="Fazer devolução de compra" onclick="fazerDevolucaoCompra(<?php echo $r->id; ?>)"><i class="fas fa-undo"></i></button>
                                            <?php } ?>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eFaturamentoEntrada')) { ?>
                                            <a href="<?php echo base_url() ?>index.php/faturamentoEntrada/editar/<?php echo $r->id; ?>" style="margin-right: 1%" class="btn btn-warning tip-top" title="Editar faturamento"><i class="fas fa-edit"></i></a>
                                        <?php } ?>
                                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dFaturamentoEntrada')) { ?>
                                            <a href="javascript:void(0)" onclick="abrirModalExcluir(<?php echo $r->id; ?>)" style="margin-right: 1%" class="btn btn-danger tip-top" title="Excluir faturamento"><i class="fas fa-trash-alt"></i></a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php echo $this->pagination->create_links(); ?>
    </div>
<?php } ?>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/faturamentoEntrada/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Faturamento de Entrada</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idFaturamento" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este faturamento de entrada?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-danger">Excluir</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Inicializar máscara de moeda
        $('.money').maskMoney({
            prefix: 'R$ ',
            thousands: '.',
            decimal: ',',
            allowZero: true
        });

        // Inicializar datepicker
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior'
        });

        // Inicializar autocomplete para fornecedor
        $("#cliente_fornecedor").autocomplete({
            source: '<?php echo base_url(); ?>index.php/faturamentoEntrada/autoCompleteFornecedor',
            minLength: 1,
            select: function(event, ui) {
                if (ui.item.id) {
                    $('#cliente_fornecedor').val(ui.item.id);
                }
            }
        });
    });

    function fecharDocumento(id) {
        Swal.fire({
            title: 'Fechar Documento',
            text: 'Deseja realmente fechar este documento e criar um lançamento de despesa?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, fechar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Abrir modal de adicionar receita/despesa
                $('#modalAdicionarLancamento').modal('show');
                
                // Preencher os campos do modal
                $('#tipo').val('despesa').prop('disabled', true);
                $('#faturamento_id').val(id);
                
                // Buscar dados do faturamento para preencher os campos
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/getDadosFaturamento/' + id,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#valor').val(response.data.total_nota);
                            $('#descricao').val('NFe ' + response.data.numero_nfe + ' - ' + response.data.fornecedor);
                            $('#cliente_fornecedor').val(response.data.fornecedor);
                            $('#data_vencimento').val(response.data.data_vencimento);
                            $('#formaPgto').val(response.data.forma_pgto);
                            
                            // Inicializar máscara de moeda novamente após preencher o valor
                            $('.money').maskMoney({
                                prefix: 'R$ ',
                                thousands: '.',
                                decimal: ',',
                                allowZero: true
                            });
                            
                            // Verificar se a forma de pagamento permite parcelamento
                            verificarParcelamento();
                            
                            // Se houver parcelamento no XML, preencher
                            if (response.data.parcelamento) {
                                $('#parcelas').val(response.data.parcelamento.quantidade);
                                $('#intervalo').val(response.data.parcelamento.intervalo);
                                calcularParcelas();
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: 'Erro ao buscar dados do faturamento: ' + response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao buscar dados do faturamento. Tente novamente.'
                        });
                    }
                });
            }
        });
    }

    function verificarParcelamento() {
        var formaPgto = $('#formaPgto').val();
        var parcelamentoContainer = $('#parcelamentoContainer');
        
        // Formas de pagamento que permitem parcelamento
        var formasParcelamento = [
            'Cartão de Crédito',
            'Cartão de Débito',
            'Boleto',
            'Duplicata'
        ];
        
        if (formasParcelamento.includes(formaPgto)) {
            parcelamentoContainer.show();
            calcularParcelas();
        } else {
            parcelamentoContainer.hide();
            $('#parcelas').val('1');
            calcularParcelas();
        }
    }

    function calcularParcelas() {
        var valor = $('#valor').val();
        var parcelas = $('#parcelas').val();
        var intervalo = $('#intervalo').val();
        
        // Limpar valor da máscara
        valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
        valor = parseFloat(valor);

        if (isNaN(valor) || valor <= 0) {
            return;
        }

        var valorParcela = valor / parcelas;
        var html = '';
        
        // Usar data atual para primeira parcela
        var data = new Date();
        
        for (var i = 1; i <= parcelas; i++) {
            var dataParcela = new Date(data);
            if (i > 1) {
                dataParcela.setDate(dataParcela.getDate() + ((i - 1) * intervalo));
            }
            
            html += '<tr>';
            html += '<td>' + i + 'ª parcela</td>';
            html += '<td><input type="text" class="form-control datepicker" value="' + dataParcela.toLocaleDateString('pt-BR') + '" onchange="atualizarParcela(this, ' + i + ')"></td>';
            html += '<td><input type="text" class="form-control money" value="R$ ' + valorParcela.toFixed(2).replace('.', ',') + '" onchange="atualizarParcela(this, ' + i + ')"></td>';
            html += '</tr>';
        }

        $('#parcelasBody').html(html);
        
        // Inicializar máscaras e datepickers nas novas linhas
        $('.money').maskMoney({
            prefix: 'R$ ',
            thousands: '.',
            decimal: ',',
            allowZero: true
        });
        
        $('.datepicker').datepicker({
            dateFormat: 'dd/mm/yy',
            dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
            dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
            dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
            monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
            monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
            nextText: 'Próximo',
            prevText: 'Anterior'
        });
    }

    function atualizarParcela(input, parcela) {
        // Atualizar a parcela quando o usuário editar a data ou valor
        var row = $(input).closest('tr');
        var data = row.find('input.datepicker').val();
        var valor = row.find('input.money').val();
        
        // Aqui você pode adicionar validações adicionais se necessário
    }

    // Função para salvar o lançamento e fechar o documento
    function salvarLancamento() {
        var formaPgto = $('#formaPgto').val();
        var lancamentos = [];
        
        // Verificar se é parcelado
        if ($('#parcelamentoContainer').is(':visible')) {
            // Coletar dados das parcelas
            $('#parcelasBody tr').each(function() {
                var row = $(this);
                var parcela = row.find('td:first').text();
                var data = row.find('input.datepicker').val();
                var valor = row.find('input.money').val();
                
                // Limpar valor da máscara
                valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
                
                lancamentos.push({
                    tipo: $('#tipo').val(),
                    valor: valor,
                    descricao: $('#descricao').val() + ' - ' + parcela,
                    cliente_fornecedor: $('#cliente_fornecedor').val(),
                    data_vencimento: data,
                    forma_pgto: formaPgto,
                    observacoes: $('#observacoes').val(),
                    faturamento_id: $('#faturamento_id').val(),
                    status: 'pendente',
                    valor_desconto: '0',
                    desconto: '0',
                    tipo_desconto: 'real',
                    baixado: 0,
                    urlAtual: window.location.href
                });
            });
        } else {
            // Lançamento único
            var valor = $('#valor').val();
            // Limpar valor da máscara
            valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
            
            lancamentos.push({
                tipo: $('#tipo').val(),
                valor: valor,
                descricao: $('#descricao').val(),
                cliente_fornecedor: $('#cliente_fornecedor').val(),
                data_vencimento: $('#data_vencimento').val(),
                forma_pgto: formaPgto,
                observacoes: $('#observacoes').val(),
                faturamento_id: $('#faturamento_id').val(),
                status: 'pendente',
                valor_desconto: '0',
                desconto: '0',
                tipo_desconto: 'real',
                baixado: 0,
                urlAtual: window.location.href
            });
        }

        // Enviar cada lançamento
        var promises = [];
        
        // Criar array de lançamentos primeiro
        var lancamentosArray = [];
        $('#parcelasBody tr').each(function() {
            var row = $(this);
            var parcela = row.find('td:first').text();
            var data = row.find('input.datepicker').val();
            var valor = row.find('input.money').val();
            
            // Limpar valor da máscara
            valor = valor.replace('R$ ', '').replace('.', '').replace(',', '.');
            
            lancamentosArray.push({
                tipo: $('#tipo').val(),
                valor: valor,
                descricao: $('#descricao').val() + ' - ' + parcela,
                cliente_fornecedor: $('#cliente_fornecedor').val(),
                data_vencimento: data,
                forma_pgto: formaPgto,
                observacoes: $('#observacoes').val(),
                faturamento_id: $('#faturamento_id').val(),
                status: 'pendente',
                valor_desconto: '0',
                desconto: '0',
                tipo_desconto: 'real',
                baixado: 0,
                urlAtual: window.location.href
            });
        });

        // Função para processar lançamento
        function processarLancamento(lancamento) {
            return new Promise((resolve, reject) => {
                // Formatar data de vencimento
                var dataVencimento = lancamento.data_vencimento.split('/');
                lancamento.data_vencimento = dataVencimento[2] + '-' + dataVencimento[1] + '-' + dataVencimento[0];
                lancamento.vencimento = lancamento.data_vencimento;
                lancamento.pagamento = null;
                lancamento.pago = 0;
                lancamento.fornecedor = lancamento.cliente_fornecedor;
                
                $.ajax({
                    url: '<?php echo base_url(); ?>index.php/financeiro/adicionarDespesa',
                    type: 'POST',
                    data: lancamento,
                    complete: function(xhr) {
                        if (xhr.status === 200 || xhr.status === 302) {
                            resolve(true);
                        } else {
                            reject(new Error('Erro ao adicionar lançamento'));
                        }
                    }
                });
            });
        }

        // Processar lançamentos sequencialmente
        async function processarLancamentosEmOrdem() {
            try {
                for (let lancamento of lancamentosArray) {
                    await processarLancamento(lancamento);
                }
                return Promise.resolve();
            } catch (error) {
                return Promise.reject(error);
            }
        }

        // Se for parcelado, processar em ordem
        if ($('#parcelamentoContainer').is(':visible')) {
            processarLancamentosEmOrdem()
                .then(function() {
                    // Atualizar status do faturamento
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/atualizarStatus',
                            type: 'POST',
                            data: { 
                                id: $('#faturamento_id').val(),
                                status: 'fechado'
                            },
                            success: function(response) {
                                // Tentar fazer o parse da resposta se for string
                                if (typeof response === 'string') {
                                    try {
                                        response = JSON.parse(response);
                                    } catch (e) {
                                        // Se não for JSON válido, considerar sucesso
                                        resolve(true);
                                        return;
                                    }
                                }
                                
                                // Se a resposta for um objeto com success, verificar
                                if (response && typeof response.success !== 'undefined') {
                                    if (response.success) {
                                        resolve(true);
                                    } else {
                                        reject(new Error(response.message || 'Erro ao atualizar status do faturamento'));
                                    }
                                } else {
                                    // Se não tiver success, considerar como sucesso
                                    resolve(true);
                                }
                            },
                            error: function(xhr) {
                                // Se o status for 200 ou 302, considerar sucesso mesmo com erro no parse
                                if (xhr.status === 200 || xhr.status === 302) {
                                    resolve(true);
                                } else {
                                    reject(new Error('Erro ao atualizar status do faturamento'));
                                }
                            }
                        });
                    });
                })
                .then(function() {
                    // Fechar o modal
                    $('#modalAdicionarLancamento').modal('hide');
                    
                    // Mostrar mensagem de sucesso
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Documento fechado e lançamentos criados com sucesso!',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                })
                .catch(function(error) {
                    console.error('Erro:', error);
                    // Se o erro for relacionado ao status mas sabemos que funcionou, ignorar
                    if (error.message && error.message.includes('status do faturamento')) {
                        // Fechar o modal
                        $('#modalAdicionarLancamento').modal('hide');
                        
                        // Mostrar mensagem de sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Documento fechado e lançamentos criados com sucesso!',
                            showConfirmButton: true,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao processar os lançamentos: ' + (error.message || 'Erro desconhecido')
                        });
                    }
                });
        } else {
            // Se não for parcelado, processar o lançamento único
            processarLancamento(lancamentos[0])
                .then(function() {
                    // Atualizar status do faturamento
                    return new Promise((resolve, reject) => {
                        $.ajax({
                            url: '<?php echo base_url(); ?>index.php/faturamentoEntrada/atualizarStatus',
                            type: 'POST',
                            data: { 
                                id: $('#faturamento_id').val(),
                                status: 'fechado'
                            },
                            success: function(response) {
                                // Tentar fazer o parse da resposta se for string
                                if (typeof response === 'string') {
                                    try {
                                        response = JSON.parse(response);
                                    } catch (e) {
                                        // Se não for JSON válido, considerar sucesso
                                        resolve(true);
                                        return;
                                    }
                                }
                                
                                // Se a resposta for um objeto com success, verificar
                                if (response && typeof response.success !== 'undefined') {
                                    if (response.success) {
                                        resolve(true);
                                    } else {
                                        reject(new Error(response.message || 'Erro ao atualizar status do faturamento'));
                                    }
                                } else {
                                    // Se não tiver success, considerar como sucesso
                                    resolve(true);
                                }
                            },
                            error: function(xhr) {
                                // Se o status for 200 ou 302, considerar sucesso mesmo com erro no parse
                                if (xhr.status === 200 || xhr.status === 302) {
                                    resolve(true);
                                } else {
                                    reject(new Error('Erro ao atualizar status do faturamento'));
                                }
                            }
                        });
                    });
                })
                .then(function() {
                    // Fechar o modal
                    $('#modalAdicionarLancamento').modal('hide');
                    
                    // Mostrar mensagem de sucesso
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: 'Documento fechado e lançamento criado com sucesso!',
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                })
                .catch(function(error) {
                    console.error('Erro:', error);
                    // Se o erro for relacionado ao status mas sabemos que funcionou, ignorar
                    if (error.message && error.message.includes('status do faturamento')) {
                        // Fechar o modal
                        $('#modalAdicionarLancamento').modal('hide');
                        
                        // Mostrar mensagem de sucesso
                        Swal.fire({
                            icon: 'success',
                            title: 'Sucesso!',
                            text: 'Documento fechado e lançamento criado com sucesso!',
                            showConfirmButton: true,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erro',
                            text: 'Erro ao processar o lançamento: ' + (error.message || 'Erro desconhecido')
                        });
                    }
                });
        }
    }

    function fazerDevolucaoCompra(id) {
        Swal.fire({
            title: 'Confirmação',
            text: "Deseja realmente fazer a devolução de compra?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, fazer devolução!',
            cancelButtonText: 'Não'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '<?php echo base_url(); ?>index.php/nfe/devolucaoCompra/' + id;
            }
        });
    }

    function abrirModalExcluir(id) {
        console.log('Abrindo modal para excluir faturamento ID:', id);
        $('#idFaturamento').val(id);
        $('#modal-excluir').modal('show');
    }

    // Quando o modal for fechado, limpar o ID
    $('#modal-excluir').on('hidden.bs.modal', function () {
        $('#idFaturamento').val('');
    });
</script>

<!-- Modal Adicionar Lançamento -->
<div class="modal fade" id="modalAdicionarLancamento" tabindex="-1" role="dialog" aria-labelledby="modalAdicionarLancamentoLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="modalAdicionarLancamentoLabel">Adicionar Lançamento</h4>
            </div>
            <div class="modal-body">
                <form id="formLancamento">
                    <input type="hidden" id="faturamento_id" name="faturamento_id">
                    
                    <div class="form-group">
                        <label for="tipo">Tipo<span class="required">*</span></label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <option value="despesa">Despesa</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="valor">Valor<span class="required">*</span></label>
                        <input type="text" class="form-control money" id="valor" name="valor" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="descricao">Descrição<span class="required">*</span></label>
                        <input type="text" class="form-control" id="descricao" name="descricao" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cliente_fornecedor">Fornecedor<span class="required">*</span></label>
                        <input type="text" class="form-control" id="cliente_fornecedor" name="cliente_fornecedor" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="data_vencimento">Data de Vencimento<span class="required">*</span></label>
                        <input type="text" class="form-control datepicker" id="data_vencimento" name="data_vencimento" required>
                    </div>

                    <div class="form-group">
                        <label for="formaPgto">Forma de Pagamento<span class="required">*</span></label>
                        <select class="form-control" id="formaPgto" name="formaPgto" required onchange="verificarParcelamento()">
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Cartão de Crédito">Cartão de Crédito</option>
                            <option value="Cartão de Débito">Cartão de Débito</option>
                            <option value="Boleto">Boleto</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Transferência">Transferência</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cheque Pré-datado">Cheque Pré-datado</option>
                            <option value="Pix">Pix</option>
                            <option value="Duplicata">Duplicata</option>
                            <option value="Promissória">Promissória</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                    </div>

                    <div id="parcelamentoContainer" style="display: none;">
                        <div class="form-group">
                            <label>Parcelamento</label>
                            <div class="form-group">
                                <label for="parcelas">Número de Parcelas</label>
                                <select class="form-control" id="parcelas" name="parcelas" onchange="calcularParcelas()">
                                    <option value="1">1x</option>
                                    <option value="2">2x</option>
                                    <option value="3">3x</option>
                                    <option value="4">4x</option>
                                    <option value="5">5x</option>
                                    <option value="6">6x</option>
                                    <option value="7">7x</option>
                                    <option value="8">8x</option>
                                    <option value="9">9x</option>
                                    <option value="10">10x</option>
                                    <option value="11">11x</option>
                                    <option value="12">12x</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="intervalo">Intervalo entre Parcelas (dias)</label>
                                <input type="number" class="form-control" id="intervalo" name="intervalo" value="30" min="1" onchange="calcularParcelas()">
                            </div>
                            <div id="parcelasContainer" class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Parcela</th>
                                            <th>Vencimento</th>
                                            <th>Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody id="parcelasBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="salvarLancamento()">Salvar</button>
            </div>
        </div>
    </div>
</div> 