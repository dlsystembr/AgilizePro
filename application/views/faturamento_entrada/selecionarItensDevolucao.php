<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Selecionar Itens para Devolução
            <small>Selecione os itens que deseja devolver</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="<?php echo base_url(); ?>index.php/faturamentoEntrada">Faturamento de Entrada</a></li>
            <li class="active">Selecionar Itens</li>
        </ol>
    </section>

    <!-- Adiciona SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Itens da Entrada #<?php echo $entrada->id; ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-success btn-sm" id="btn-selecionar-todos">
                                <i class="fa fa-check-square-o"></i> Selecionar Todos
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <form id="formDevolucao" action="<?php echo base_url(); ?>index.php/devolucaoCompra/devolucaoCompra/<?php echo $entrada->id; ?>" method="post">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-dark">
                                    <thead>
                                        <tr>
                                            <th>Selecionar</th>
                                            <th>Produto</th>
                                            <th>Quantidade Original</th>
                                            <th>Quantidade a Devolver</th>
                                            <th>Valor Unitário</th>
                                            <th>Base ICMS</th>
                                            <th>Alíquota ICMS (%)</th>
                                            <th>Valor ICMS</th>
                                            <th>IPI Devolução</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($produtos as $produto): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="itens_selecionados[]" value="<?php echo $produto->produto_id; ?>" class="item-checkbox">
                                            </td>
                                            <td><?php echo $produto->descricao; ?></td>
                                            <td><?php echo number_format($produto->quantidade, 2, ',', '.'); ?></td>
                                            <td>
                                                <input type="number" name="quantidades[<?php echo $produto->produto_id; ?>]" 
                                                       class="form-control quantidade" 
                                                       value="<?php echo $produto->quantidade; ?>" 
                                                       min="0" 
                                                       max="<?php echo $produto->quantidade; ?>" 
                                                       step="0.01" 
                                                       disabled>
                                            </td>
                                            <td>R$ <?php echo number_format($produto->valor_unitario, 2, ',', '.'); ?></td>
                                            <td>
                                                <input type="number" name="base_icms[<?php echo $produto->produto_id; ?>]" 
                                                       class="form-control base-icms" 
                                                       value="<?php echo number_format($produto->base_calculo_icms, 2, '.', ''); ?>" 
                                                       step="0.01" 
                                                       disabled>
                                            </td>
                                            <td>
                                                <input type="number" name="aliquota_icms[<?php echo $produto->produto_id; ?>]" 
                                                       class="form-control aliquota-icms" 
                                                       value="<?php echo number_format($produto->aliquota_icms, 2, '.', ''); ?>" 
                                                       step="0.01" 
                                                       disabled>
                                            </td>
                                            <td>
                                                <input type="number" name="valor_icms[<?php echo $produto->produto_id; ?>]" 
                                                       class="form-control valor-icms" 
                                                       value="<?php echo number_format($produto->valor_icms, 2, '.', ''); ?>" 
                                                       step="0.01" 
                                                       disabled>
                                            </td>
                                            <td>
                                                <input type="number" name="ipi_devolucao[<?php echo $produto->produto_id; ?>]" 
                                                       class="form-control ipi-devolucao" 
                                                       value="<?php echo number_format($produto->valor_ipi, 2, '.', ''); ?>" 
                                                       step="0.01" 
                                                       disabled>
                                            </td>
                                            <td class="total-item">R$ <?php echo number_format($produto->quantidade * $produto->valor_unitario, 2, ',', '.'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Informações de Transporte</h3>
                                        </div>
                                        <div class="box-body" style="padding: 20px;">
                                            <div class="row" style="margin-left: 15px; margin-right: 15px;">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="transportadora">Transportadora:</label>
                                                        <?php
                                                        $nome_transportadora = 'Não informado';
                                                        if (isset($entrada->transportadora_id) && !empty($entrada->transportadora_id)) {
                                                            $this->db->select('nomeCliente');
                                                            $this->db->from('clientes');
                                                            $this->db->where('idClientes', $entrada->transportadora_id);
                                                            $transportadora = $this->db->get()->row();
                                                            if ($transportadora) {
                                                                $nome_transportadora = $transportadora->nomeCliente;
                                                            }
                                                        }
                                                        ?>
                                                        <input type="text" class="form-control" id="transportadora" name="transportadora" value="<?php echo $nome_transportadora; ?>" readonly>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="modalidade_frete">Modalidade do Frete:</label>
                                                        <select class="form-control" id="modalidade_frete" name="modalidade_frete">
                                                            <option value="0" <?php echo (isset($entrada->modalidade_frete) && $entrada->modalidade_frete == '0') ? 'selected' : ''; ?>>0 - Contratação do Frete por conta do Remetente (CIF)</option>
                                                            <option value="1" <?php echo (isset($entrada->modalidade_frete) && $entrada->modalidade_frete == '1') ? 'selected' : ''; ?>>1 - Contratação do Frete por conta do Destinatário (FOB)</option>
                                                            <option value="2" <?php echo (isset($entrada->modalidade_frete) && $entrada->modalidade_frete == '2') ? 'selected' : ''; ?>>2 - Contratação do Frete por conta de Terceiros</option>
                                                            <option value="3" <?php echo (isset($entrada->modalidade_frete) && $entrada->modalidade_frete == '3') ? 'selected' : ''; ?>>3 - Transporte Próprio por conta do Remetente</option>
                                                            <option value="4" <?php echo (isset($entrada->modalidade_frete) && $entrada->modalidade_frete == '4') ? 'selected' : ''; ?>>4 - Transporte Próprio por conta do Destinatário</option>
                                                            <option value="9" <?php echo (isset($entrada->modalidade_frete) && $entrada->modalidade_frete == '9') ? 'selected' : ''; ?>>9 - Sem Ocorrência de Transporte</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="peso_liquido">Peso Líquido</label>
                                                        <input type="text" class="form-control" id="peso_liquido" name="peso_liquido" value="<?php echo $entrada->peso_liquido ?? ''; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="peso_bruto">Peso Bruto</label>
                                                        <input type="text" class="form-control" id="peso_bruto" name="peso_bruto" value="<?php echo $entrada->peso_bruto ?? ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-left: 15px; margin-right: 15px;">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="volume">Volume</label>
                                                        <input type="text" class="form-control" id="volume" name="volume" value="<?php echo $entrada->volume ?? '1'; ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="especie">Espécie</label>
                                                        <input type="text" class="form-control" id="especie" name="especie" value="volume">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="devolver_todos" name="devolver_todos" value="true">
                                            Devolver todos os itens
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12 text-right">
                                    <button type="submit" class="btn btn-primary" id="btn-confirmar">
                                        <i class="fa fa-check"></i> Confirmar Devolução
                                    </button>
                                    <a href="<?php echo base_url(); ?>index.php/faturamentoEntrada" class="btn btn-default">
                                        <i class="fa fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
.table-dark {
    background-color: #2b2b2b;
    color: #fff;
}

.table-dark thead th {
    background-color: #1a1a1a;
    border-color: #454d55;
    color: #fff;
}

.table-dark tbody tr {
    background-color: #2b2b2b;
    color: #fff;
}

.table-dark tbody tr:hover {
    background-color: #3a3a3a;
}

.table-dark .form-control {
    background-color: #1a1a1a;
    border-color: #454d55;
    color: #fff;
}

/* Estilo para campos desabilitados */
.table-dark .form-control:disabled {
    background-color: #1a1a1a !important;
    border-color: #454d55 !important;
    color: #666 !important;
    cursor: not-allowed !important;
    opacity: 0.7 !important;
}

/* Estilo para campos desabilitados no hover */
.table-dark .form-control:disabled:hover {
    background-color: #1a1a1a !important;
    border-color: #454d55 !important;
    cursor: not-allowed !important;
}

.table-dark .checkbox {
    background-color: #2b2b2b;
}

.table-dark .checkbox input[type="checkbox"] {
    background-color: #1a1a1a;
    border-color: #454d55;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-content {
    background: #2b2b2b;
    padding: 20px;
    border-radius: 5px;
    text-align: center;
    color: #fff;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
}

.loading-spinner {
    border: 4px solid #3a3a3a;
    border-top: 4px solid #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Ajustes para o checkbox */
.item-checkbox {
    background-color: #1a1a1a !important;
    border-color: #454d55 !important;
}

.item-checkbox:checked {
    background-color: #3498db !important;
    border-color: #3498db !important;
}

/* Ajuste para o texto dos itens */
.table-dark td {
    background-color: #2b2b2b !important;
    color: #fff !important;
}

/* Ajuste para o box */
.box-primary {
    background-color: #2b2b2b !important;
    border-color: #454d55 !important;
}

.box-header {
    background-color: #1a1a1a !important;
    border-bottom-color: #454d55 !important;
}

.box-title {
    color: #fff !important;
}

/* Estilos para os campos de transporte */
.form-group label {
    color: #fff !important;
}

.form-control[readonly] {
    background-color: #2b2b2b !important;
    border-color: #454d55 !important;
    color: #6c757d !important;
}

.form-control {
    background-color: #1a1a1a !important;
    border-color: #454d55 !important;
    color: #fff !important;
}

.box-primary .box-header {
    background-color: #1a1a1a !important;
    border-bottom-color: #454d55 !important;
}

.box-primary .box-body {
    background-color: #2b2b2b !important;
}
</style>

<div class="loading-overlay">
    <div class="loading-content">
        <div class="loading-spinner"></div>
        <h4>Emitindo Devolução...</h4>
        <p>Por favor, aguarde.</p>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Prevenir o envio do formulário ao pressionar Enter
    $(document).on('keypress', 'input, select', function(e) {
        if (e.which == 13) { // 13 é o código da tecla Enter
            e.preventDefault();
            var inputs = $(this).closest('form').find(':input:visible');
            var index = inputs.index(this);
            if (index > -1 && index < inputs.length - 1) {
                inputs.eq(index + 1).focus();
            }
            return false;
        }
    });

    // Manipular a tecla ESC para voltar um campo
    $(document).on('keydown', 'input, select', function(e) {
        if (e.which == 27) { // 27 é o código da tecla ESC
            e.preventDefault();
            var inputs = $(this).closest('form').find(':input:visible');
            var index = inputs.index(this);
            if (index > 0) {
                inputs.eq(index - 1).focus();
            }
            return false;
        }
    });

    // Armazenar valores originais
    var valoresOriginais = {
        volume: $('#volume').val(),
        peso_bruto: $('#peso_bruto').val(),
        peso_liquido: $('#peso_liquido').val()
    };

    // Função para controlar campos de transporte
    function controlarCamposTransporte() {
        var modalidade = $('#modalidade_frete').val();
        
        if (modalidade === '9') {
            // Desabilita e limpa os campos
            $('#volume, #peso_bruto, #peso_liquido').prop('disabled', true).val('0.000');
        } else {
            // Reabilita os campos e restaura valores originais
            $('#volume, #peso_bruto, #peso_liquido').prop('disabled', false);
            $('#volume').val(valoresOriginais.volume);
            $('#peso_bruto').val(valoresOriginais.peso_bruto);
            $('#peso_liquido').val(valoresOriginais.peso_liquido);
        }
    }

    // Executa ao carregar a página
    controlarCamposTransporte();

    // Executa quando mudar a modalidade
    $('#modalidade_frete').change(function() {
        controlarCamposTransporte();
    });

    // Função para calcular valores do ICMS e IPI
    function calcularValoresICMS(row) {
        var quantidade = parseFloat(row.find('.quantidade').val()) || 0;
        var quantidadeOriginal = parseFloat(row.find('.quantidade').attr('max')) || 0;
        var baseOriginal = parseFloat(row.find('.base-icms').attr('data-base-original')) || 0;
        var valorOriginal = parseFloat(row.find('.valor-icms').attr('data-valor-original')) || 0;
        var ipiOriginal = parseFloat(row.find('.ipi-devolucao').attr('data-ipi-original')) || 0;
        
        // Calcula os valores proporcionais
        var fator = quantidade / quantidadeOriginal;
        
        // Atualiza base e valor do ICMS
        var novaBase = baseOriginal * fator;
        var novoValor = valorOriginal * fator;
        var novoIPI = ipiOriginal * fator;
        
        row.find('.base-icms').val(novaBase.toFixed(2));
        row.find('.valor-icms').val(novoValor.toFixed(2));
        row.find('.ipi-devolucao').val(novoIPI.toFixed(2));
        
        // Atualiza o total do item
        atualizarTotalItem(row);
    }

    // Função para calcular valor do ICMS baseado na base e alíquota
    function calcularValorICMS(row) {
        var base = parseFloat(row.find('.base-icms').val()) || 0;
        var aliquota = parseFloat(row.find('.aliquota-icms').val()) || 0;
        var valorICMS = (base * aliquota) / 100;
        row.find('.valor-icms').val(valorICMS.toFixed(2));
    }

    // Função para atualizar o total do item
    function atualizarTotalItem(row) {
        var quantidade = parseFloat(row.find('.quantidade').val()) || 0;
        var valorUnitario = parseFloat(row.find('td:eq(4)').text().replace('R$ ', '').replace('.', '').replace(',', '.')) || 0;
        var ipi = parseFloat(row.find('.ipi-devolucao').val()) || 0;
        
        // Calcula o valor total do produto
        var valorProduto = quantidade * valorUnitario;
        
        // O total é o valor do produto mais o IPI
        var total = valorProduto + ipi;
        
        row.find('.total-item').text('R$ ' + total.toFixed(2).replace('.', ','));
    }

    // Função para validar campos numéricos
    function validarCampoNumerico(campo, valor) {
        // Verifica se está vazio
        if (campo.val() === '' || campo.val() === null) {
            Swal.fire({
                title: 'Atenção!',
                text: 'O campo não pode ficar vazio',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                background: '#2b2b2b',
                color: '#fff'
            }).then((result) => {
                campo.focus();
            });
            return false;
        }

        // Verifica se é negativo
        if (valor < 0) {
            Swal.fire({
                title: 'Atenção!',
                text: 'O valor não pode ser negativo',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                background: '#2b2b2b',
                color: '#fff'
            }).then((result) => {
                campo.val('');
                campo.focus();
            });
            return false;
        }
        return true;
    }

    // Adiciona validação no blur (quando o campo perde o foco)
    $(document).on('blur', '.base-icms, .aliquota-icms, .valor-icms, .ipi-devolucao, .quantidade', function() {
        var valor = parseFloat($(this).val()) || 0;
        if (!validarCampoNumerico($(this), valor)) {
            return;
        }
    });

    // Evento de mudança na base do ICMS
    $(document).on('change', '.base-icms', function() {
        var valor = parseFloat($(this).val()) || 0;
        if (!validarCampoNumerico($(this), valor)) {
            return;
        }
        var row = $(this).closest('tr');
        calcularValorICMS(row);
        atualizarTotalItem(row);
    });

    // Evento de mudança na alíquota do ICMS
    $(document).on('change', '.aliquota-icms', function() {
        var valor = parseFloat($(this).val()) || 0;
        if (!validarCampoNumerico($(this), valor)) {
            return;
        }
        var row = $(this).closest('tr');
        calcularValorICMS(row);
        atualizarTotalItem(row);
    });

    // Evento de mudança no valor do ICMS
    $(document).on('change', '.valor-icms', function() {
        var valor = parseFloat($(this).val()) || 0;
        if (!validarCampoNumerico($(this), valor)) {
            return;
        }
        var row = $(this).closest('tr');
        atualizarTotalItem(row);
    });

    // Evento de mudança no IPI
    $(document).on('change', '.ipi-devolucao', function() {
        var valor = parseFloat($(this).val()) || 0;
        if (!validarCampoNumerico($(this), valor)) {
            return;
        }
        var row = $(this).closest('tr');
        atualizarTotalItem(row);
    });

    // Evento de mudança na quantidade
    $(document).on('change', '.quantidade', function() {
        var row = $(this).closest('tr');
        var quantidade = parseFloat($(this).val()) || 0;
        var quantidadeOriginal = parseFloat($(this).attr('max')) || 0;
        var $this = $(this);
        
        // Validação da quantidade
        if ($this.val() === '' || $this.val() === null) {
            Swal.fire({
                title: 'Atenção!',
                text: 'A quantidade não pode ficar vazia',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                background: '#2b2b2b',
                color: '#fff'
            }).then((result) => {
                $this.focus();
            });
            return;
        }
        
        if (quantidade <= 0) {
            Swal.fire({
                title: 'Atenção!',
                text: 'A quantidade deve ser maior que zero',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                background: '#2b2b2b',
                color: '#fff'
            }).then((result) => {
                $this.val('');
                $this.focus();
            });
            return;
        }

        if (quantidade > quantidadeOriginal) {
            Swal.fire({
                title: 'Atenção!',
                text: 'A quantidade deve ser igual ou menor que a quantidade original da nota (' + quantidadeOriginal + ')',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                background: '#2b2b2b',
                color: '#fff'
            }).then((result) => {
                $this.val(quantidadeOriginal);
                quantidade = quantidadeOriginal;
                
                var ipiOriginal = parseFloat(row.find('.ipi-devolucao').attr('data-ipi-original')) || 0;
                
                // Calcula o IPI proporcional
                var fator = quantidade / quantidadeOriginal;
                var novoIPI = ipiOriginal * fator;
                
                // Atualiza o valor do IPI
                row.find('.ipi-devolucao').val(novoIPI.toFixed(2));
                
                // Recalcula os outros valores
                calcularValoresICMS(row);

                // Retorna o foco para o campo de quantidade
                $this.focus();
                // Seleciona todo o texto do campo
                $this.select();
            });
        } else {
            var ipiOriginal = parseFloat(row.find('.ipi-devolucao').attr('data-ipi-original')) || 0;
            
            // Calcula o IPI proporcional
            var fator = quantidade / quantidadeOriginal;
            var novoIPI = ipiOriginal * fator;
            
            // Atualiza o valor do IPI
            row.find('.ipi-devolucao').val(novoIPI.toFixed(2));
            
            // Recalcula os outros valores
            calcularValoresICMS(row);
        }
    });

    // Evento de mudança nos checkboxes individuais
    $('.item-checkbox').change(function() {
        var row = $(this).closest('tr');
        var campos = row.find('.quantidade, .base-icms, .aliquota-icms, .valor-icms, .ipi-devolucao');
        campos.prop('disabled', !$(this).is(':checked'));
        
        if (!$(this).is(':checked')) {
            // Apenas atualiza o total, mantém os valores
            atualizarTotalItem(row);
        } else {
            row.find('.quantidade').val(row.find('.quantidade').attr('max'));
            // Restaura os valores originais
            var baseOriginal = parseFloat(row.find('.base-icms').attr('data-base-original')) || 0;
            var valorOriginal = parseFloat(row.find('.valor-icms').attr('data-valor-original')) || 0;
            var ipiOriginal = parseFloat(row.find('.ipi-devolucao').attr('data-ipi-original')) || 0;
            var aliquotaOriginal = parseFloat(row.find('.aliquota-icms').attr('data-aliquota-original')) || 0;
            
            row.find('.base-icms').val(baseOriginal.toFixed(2));
            row.find('.valor-icms').val(valorOriginal.toFixed(2));
            row.find('.ipi-devolucao').val(ipiOriginal.toFixed(2));
            row.find('.aliquota-icms').val(aliquotaOriginal.toFixed(2));
            
            calcularValoresICMS(row);
        }
    });

    // Função para habilitar/desabilitar campos
    function toggleCampos() {
        var devolverTodos = $('#devolver_todos').is(':checked');
        $('.quantidade, .base-icms, .aliquota-icms, .valor-icms, .ipi-devolucao').prop('disabled', devolverTodos);
        $('.item-checkbox').prop('checked', devolverTodos);
        if (devolverTodos) {
            $('.quantidade').each(function() {
                $(this).val($(this).attr('max'));
                var row = $(this).closest('tr');
                atualizarTotalItem(row);
                calcularValoresICMS(row);
            });
        }
    }

    // Evento de mudança no checkbox de devolver todos
    $('#devolver_todos').change(function() {
        toggleCampos();
    });

    // Evento de mudança nos checkboxes individuais
    $('.item-checkbox').change(function() {
        var row = $(this).closest('tr');
        var campos = row.find('.quantidade, .base-icms, .aliquota-icms, .valor-icms, .ipi-devolucao');
        campos.prop('disabled', !$(this).is(':checked'));
        
        if (!$(this).is(':checked')) {
            // Apenas atualiza o total, mantém os valores
            atualizarTotalItem(row);
        } else {
            row.find('.quantidade').val(row.find('.quantidade').attr('max'));
            // Restaura os valores originais
            var baseOriginal = parseFloat(row.find('.base-icms').attr('data-base-original')) || 0;
            var valorOriginal = parseFloat(row.find('.valor-icms').attr('data-valor-original')) || 0;
            var ipiOriginal = parseFloat(row.find('.ipi-devolucao').attr('data-ipi-original')) || 0;
            var aliquotaOriginal = parseFloat(row.find('.aliquota-icms').attr('data-aliquota-original')) || 0;
            
            row.find('.base-icms').val(baseOriginal.toFixed(2));
            row.find('.valor-icms').val(valorOriginal.toFixed(2));
            row.find('.ipi-devolucao').val(ipiOriginal.toFixed(2));
            row.find('.aliquota-icms').val(aliquotaOriginal.toFixed(2));
            
            calcularValoresICMS(row);
        }
    });

    // Evento do botão Selecionar Todos
    $('#btn-selecionar-todos').click(function() {
        $('.item-checkbox').prop('checked', true).trigger('change');
    });

    // Validação do formulário e exibição do loading
    $('#formDevolucao').submit(function(e) {
        var devolverTodos = $('#devolver_todos').is(':checked');
        var itensSelecionados = $('.item-checkbox:checked').length;
        
        if (!devolverTodos && itensSelecionados === 0) {
            e.preventDefault();
            alert('Selecione pelo menos um item para devolução.');
            return false;
        }

        // Calcula o total do IPI dos itens selecionados
        var totalIPI = 0;
        $('.item-checkbox:checked').each(function() {
            var row = $(this).closest('tr');
            var ipi = parseFloat(row.find('.ipi-devolucao').val()) || 0;
            totalIPI += ipi;
        });

        // Adiciona um campo hidden com o total do IPI
        if ($('#total_ipi').length === 0) {
            $('<input>').attr({
                type: 'hidden',
                id: 'total_ipi',
                name: 'total_ipi',
                value: totalIPI.toFixed(2)
            }).appendTo('#formDevolucao');
        } else {
            $('#total_ipi').val(totalIPI.toFixed(2));
        }
        
        // Mostra o loading
        $('.loading-overlay').css('display', 'flex');
        
        return true;
    });

    // Armazena os valores originais ao carregar a página
    $('.base-icms').each(function() {
        var valor = parseFloat($(this).val()) || 0;
        $(this).attr('data-base-original', valor);
    });

    $('.valor-icms').each(function() {
        var valor = parseFloat($(this).val()) || 0;
        $(this).attr('data-valor-original', valor);
    });

    $('.ipi-devolucao').each(function() {
        var valor = parseFloat($(this).val()) || 0;
        $(this).attr('data-ipi-original', valor);
    });

    $('.aliquota-icms').each(function() {
        var valor = parseFloat($(this).val()) || 0;
        $(this).attr('data-aliquota-original', valor);
    });
});
</script> 