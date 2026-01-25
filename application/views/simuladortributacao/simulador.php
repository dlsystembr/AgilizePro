<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<style>
    /* Melhorar autocomplete */
    .ui-autocomplete {
        max-width: 100% !important;
        max-height: 200px !important;
        overflow-y: auto !important;
        z-index: 9999 !important;
    }
    
    /* Estilo para tabelas dos modais */
    #tabelaBuscaCliente, #tabelaBuscaProduto {
        font-size: 12px;
    }
    #tabelaBuscaCliente th, #tabelaBuscaProduto th {
        background-color: #f5f5f5;
        font-weight: bold;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-calculator"></i>
                </span>
                <h5>Simulador de Tributação</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" style="padding: 20px;">

                    <form id="formSimulador">
                        <div class="row-fluid">
                            <div class="span6">
                                <label for="operacao_comercial_id">Operação Comercial <span
                                        class="required">*</span></label>
                                <select name="operacao_comercial_id" id="operacao_comercial_id" class="span12" required>
                                    <option value="">Selecione uma operação</option>
                                    <?php foreach ($operacoes as $op): ?>
                                        <option value="<?= $op->opc_id ?>">
                                            <?= $op->opc_sigla ?> -
                                            <?= $op->opc_nome ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="span6">
                                <label for="cliente">Cliente <span class="required">*</span></label>
                                <div style="display: flex; width: 100%;">
                                    <input type="hidden" name="cliente_id" id="cliente_id" required>
                                    <input type="text" name="cliente" id="cliente" class="span11"
                                        placeholder="Digite o nome do cliente ou clique no botão de busca" required style="margin-right: 5px;">
                                    <button type="button" class="btn btn-info" id="btnBuscarCliente" title="Buscar Cliente" style="height: 34px;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="row-fluid" style="margin-top: 15px;">
                            <div class="span6">
                                <label for="produto">Produto <span class="required">*</span></label>
                                <div style="display: flex; width: 100%;">
                                    <input type="hidden" name="produto_id" id="produto_id" required>
                                    <input type="text" name="produto" id="produto" class="span11"
                                        placeholder="Digite o nome do produto ou clique no botão de busca" required style="margin-right: 5px;">
                                    <button type="button" class="btn btn-info" id="btnBuscarProduto" title="Buscar Produto" style="height: 34px;">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="span3">
                                <label for="valor_produto">Valor do Produto <span class="required">*</span></label>
                                <input type="text" name="valor_produto" id="valor_produto" class="span12 money"
                                    placeholder="0,00" required>
                            </div>

                            <div class="span3">
                                <label for="quantidade">Quantidade <span class="required">*</span></label>
                                <input type="number" name="quantidade" id="quantidade" class="span12" value="1" min="1"
                                    required>
                            </div>
                        </div>

                        <div class="row-fluid" style="margin-top: 20px;">
                            <div class="span12">
                                <button type="submit" class="btn btn-primary btn-large">
                                    <i class="fas fa-calculator"></i> Simular Tributação
                                </button>
                                <button type="button" class="btn btn-warning btn-large" id="btnLimpar">
                                    <i class="fas fa-eraser"></i> Limpar
                                </button>
                            </div>
                        </div>

                        <!-- CSRF Token -->
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>"
                            value="<?= $this->security->get_csrf_hash(); ?>">
                    </form>

                    <!-- Área de Resultado -->
                    <div id="resultado" style="display: none; margin-top: 30px;">
                        <hr>
                        <h4><i class="fas fa-chart-line"></i> Resultado da Simulação</h4>

                        <div class="alert alert-info">
                            <strong>Atenção:</strong> Esta é apenas uma simulação. Nenhum dado foi salvo no banco de
                            dados.
                        </div>

                        <!-- Classificação Fiscal -->
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="fas fa-file-invoice"></i></span>
                                <h5>Classificação Fiscal</h5>
                            </div>
                            <div class="widget-content">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>ID da Classificação Fiscal:</strong></td>
                                        <td id="result_clf_id"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>CST (Código de Situação Tributária):</strong></td>
                                        <td id="result_cst"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>CSOSN (Simples Nacional):</strong></td>
                                        <td id="result_csosn"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>CFOP (Código Fiscal):</strong></td>
                                        <td id="result_cfop"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tipo de ICMS:</strong></td>
                                        <td id="result_tipo_icms"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Dados da Operação -->
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="fas fa-info-circle"></i></span>
                                <h5>Dados da Operação</h5>
                            </div>
                            <div class="widget-content">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Cliente:</strong></td>
                                        <td id="result_cliente"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>UF do Cliente:</strong></td>
                                        <td id="result_cliente_uf"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Natureza do Contribuinte:</strong></td>
                                        <td id="result_natureza"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Objetivo Comercial:</strong></td>
                                        <td id="result_objetivo"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Destinação:</strong></td>
                                        <td id="result_destinacao"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Produto:</strong></td>
                                        <td id="result_produto"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Valores Calculados -->
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="fas fa-dollar-sign"></i></span>
                                <h5>Valores Calculados</h5>
                            </div>
                            <div class="widget-content">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Quantidade:</strong></td>
                                        <td id="result_quantidade"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Valor Unitário:</strong></td>
                                        <td>R$ <span id="result_valor_unitario"></span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Valor Base (Produtos):</strong></td>
                                        <td>R$ <span id="result_valor_base"></span></td>
                                    </tr>
                                    <tr class="info">
                                        <td><strong>IPI (<span id="result_aliq_ipi"></span>%):</strong></td>
                                        <td>R$ <span id="result_valor_ipi"></span></td>
                                    </tr>
                                    <tr class="info">
                                        <td><strong>PIS (<span id="result_aliq_pis"></span>%):</strong></td>
                                        <td>R$ <span id="result_valor_pis"></span></td>
                                    </tr>
                                    <tr class="info">
                                        <td><strong>COFINS (<span id="result_aliq_cofins"></span>%):</strong></td>
                                        <td>R$ <span id="result_valor_cofins"></span></td>
                                    </tr>
                                    <tr class="warning">
                                        <td><strong>Total de Impostos:</strong></td>
                                        <td><strong>R$ <span id="result_total_impostos"></span></strong></td>
                                    </tr>
                                    <tr class="success">
                                        <td><strong>Valor Total da Nota:</strong></td>
                                        <td><strong>R$ <span id="result_valor_total"></span></strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- CST dos Impostos -->
                        <div class="widget-box">
                            <div class="widget-title">
                                <span class="icon"><i class="fas fa-list"></i></span>
                                <h5>CST dos Impostos</h5>
                            </div>
                            <div class="widget-content">
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>CST IPI:</strong></td>
                                        <td id="result_cst_ipi"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>CST PIS:</strong></td>
                                        <td id="result_cst_pis"></td>
                                    </tr>
                                    <tr>
                                        <td><strong>CST COFINS:</strong></td>
                                        <td id="result_cst_cofins"></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        // Autocomplete para Cliente
        $("#cliente").autocomplete({
            source: "<?= base_url() ?>index.php/simuladortributacao/autoCompleteCliente",
            minLength: 2,
            select: function (event, ui) {
                $("#cliente_id").val(ui.item.id);
                $("#cliente").val(ui.item.label);
                console.log("Cliente selecionado:", ui.item);
                return false;
            }
        }).on('change', function () {
            // Se o usuário digitar manualmente, limpar o ID
            if (!$("#cliente_id").val()) {
                $(this).val('');
            }
        });

        // Autocomplete para Produto
        $("#produto").autocomplete({
            source: "<?= base_url() ?>index.php/simuladortributacao/autoCompleteProduto",
            minLength: 2,
            select: function (event, ui) {
                $("#produto_id").val(ui.item.id);
                $("#produto").val(ui.item.label);
                console.log("Produto selecionado:", ui.item);

                // Se o produto tiver preço, preenche automaticamente
                if (ui.item.precoVenda) {
                    $("#valor_produto").val(ui.item.precoVenda.replace('.', ','));
                }

                return false;
            }
        }).on('change', function () {
            // Se o usuário digitar manualmente, limpar o ID
            if (!$("#produto_id").val()) {
                $(this).val('');
            }
        });

        // Máscara de dinheiro
        $(".money").maskMoney({
            prefix: '',
            thousands: '.',
            decimal: ',',
            allowZero: true
        });

        // Submit do formulário
        $("#formSimulador").submit(function (e) {
            e.preventDefault();

            // Validar se cliente e produto foram selecionados
            if (!$("#cliente_id").val()) {
                alert("Por favor, selecione um cliente da lista.");
                return false;
            }

            if (!$("#produto_id").val()) {
                alert("Por favor, selecione um produto da lista.");
                return false;
            }

            // Mostrar loading
            $("#resultado").hide();
            var btn = $(this).find('button[type="submit"]');
            var btnText = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Calculando...').prop('disabled', true);

            // Enviar dados via AJAX
            $.ajax({
                url: "<?= base_url() ?>index.php/simuladortributacao/simular",
                type: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (response) {
                    btn.html(btnText).prop('disabled', false);

                    if (response.sucesso) {
                        // Preencher os dados do resultado
                        var d = response.dados;

                        // Classificação Fiscal
                        $("#result_clf_id").text(d.classificacao_fiscal_id);
                        $("#result_cst").text(d.cst);
                        $("#result_csosn").text(d.csosn);
                        $("#result_cfop").text(d.cfop);
                        $("#result_tipo_icms").text(d.tipo_icms);

                        // Dados da Operação
                        $("#result_cliente").text(d.cliente_nome);
                        $("#result_cliente_uf").text(d.cliente_uf);
                        $("#result_natureza").text(d.cliente_natureza);
                        $("#result_objetivo").text(d.cliente_objetivo);
                        $("#result_destinacao").text(d.destinacao);
                        $("#result_produto").text(d.produto_descricao);

                        // Valores
                        $("#result_quantidade").text(d.quantidade);
                        $("#result_valor_unitario").text(d.valor_unitario);
                        $("#result_valor_base").text(d.valor_base);
                        $("#result_aliq_ipi").text(d.aliq_ipi);
                        $("#result_valor_ipi").text(d.valor_ipi);
                        $("#result_aliq_pis").text(d.aliq_pis);
                        $("#result_valor_pis").text(d.valor_pis);
                        $("#result_aliq_cofins").text(d.aliq_cofins);
                        $("#result_valor_cofins").text(d.valor_cofins);
                        $("#result_total_impostos").text(d.total_impostos);
                        $("#result_valor_total").text(d.valor_total);

                        // CST dos Impostos
                        $("#result_cst_ipi").text(d.cst_ipi);
                        $("#result_cst_pis").text(d.cst_pis);
                        $("#result_cst_cofins").text(d.cst_cofins);

                        // Mostrar resultado
                        $("#resultado").slideDown();

                        // Scroll suave até o resultado
                        $('html, body').animate({
                            scrollTop: $("#resultado").offset().top - 100
                        }, 500);

                    } else {
                        alert("Erro: " + response.mensagem);
                    }
                },
                error: function () {
                    btn.html(btnText).prop('disabled', false);
                    alert("Erro ao processar a simulação. Tente novamente.");
                }
            });
        });

        // Botão Limpar
        $("#btnLimpar").click(function () {
            $("#formSimulador")[0].reset();
            $("#cliente_id").val('');
            $("#produto_id").val('');
            $("#resultado").slideUp();
        });

        // ========== MODAL DE BUSCA DE CLIENTE ==========
        $("#btnBuscarCliente").click(function() {
            $("#modalBuscaCliente").modal('show');
            buscarClientes();
        });

        $("#formFiltroCliente").submit(function(e) {
            e.preventDefault();
            buscarClientes();
        });

        $("#limiteCliente").change(function() {
            buscarClientes();
        });

        function buscarClientes() {
            let params = {
                nome: $("#filtroNomeCliente").val(),
                documento: $("#filtroDocumentoCliente").val(),
                limite: $("#limiteCliente").val()
            };
            $("#tabelaBuscaCliente tbody").html('<tr><td colspan="4" class="text-center">Carregando...</td></tr>');
            $.get("<?= base_url() ?>index.php/simuladortributacao/buscarClientes", params, function(res) {
                let html = '';
                if (res && res.length > 0) {
                    res.forEach(function(c) {
                        html += '<tr>' +
                            '<td>' + (c.nome || '') + '</td>' +
                            '<td>' + (c.documento || '') + '</td>' +
                            '<td>' + (c.uf || '') + '</td>' +
                            '<td><button class="btn btn-success btn-xs btnSelecionarCliente" data-id="' + c.id + '" data-nome="' + (c.nome || '').replace(/'/g, "\\'") + '">Selecionar</button></td>' +
                            '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center">Nenhum cliente encontrado</td></tr>';
                }
                $("#tabelaBuscaCliente tbody").html(html);
            }, 'json').fail(function() {
                $("#tabelaBuscaCliente tbody").html('<tr><td colspan="4" class="text-center text-danger">Erro ao buscar clientes</td></tr>');
            });
        }

        $(document).on('click', '.btnSelecionarCliente', function() {
            let id = $(this).data('id');
            let nome = $(this).data('nome');
            $("#cliente_id").val(id);
            $("#cliente").val(nome);
            $("#modalBuscaCliente").modal('hide');
        });

        // ========== MODAL DE BUSCA DE PRODUTO ==========
        $("#btnBuscarProduto").click(function() {
            $("#modalBuscaProduto").modal('show');
            buscarProdutos();
        });

        $("#formFiltroProduto").submit(function(e) {
            e.preventDefault();
            buscarProdutos();
        });

        $("#limiteProduto").change(function() {
            buscarProdutos();
        });

        function buscarProdutos() {
            let params = {
                nome: $("#filtroNomeProduto").val(),
                codigo: $("#filtroCodigoProduto").val(),
                barras: $("#filtroBarrasProduto").val(),
                limite: $("#limiteProduto").val()
            };
            $("#tabelaBuscaProduto tbody").html('<tr><td colspan="4" class="text-center">Carregando...</td></tr>');
            $.get("<?= base_url() ?>index.php/simuladortributacao/buscarProdutos", params, function(res) {
                let html = '';
                if (res && res.length > 0) {
                    res.forEach(function(p) {
                        html += '<tr>' +
                            '<td>' + (p.descricao || '') + '</td>' +
                            '<td>' + (p.codDeBarra || '') + '</td>' +
                            '<td>R$ ' + (p.precoVenda || '0,00') + '</td>' +
                            '<td><button class="btn btn-success btn-xs btnSelecionarProduto" data-id="' + p.id + '" data-label="' + (p.descricao || '').replace(/'/g, "\\'") + '" data-preco="' + (p.precoVenda || '0,00') + '">Selecionar</button></td>' +
                            '</tr>';
                    });
                } else {
                    html = '<tr><td colspan="4" class="text-center">Nenhum produto encontrado</td></tr>';
                }
                $("#tabelaBuscaProduto tbody").html(html);
            }, 'json').fail(function() {
                $("#tabelaBuscaProduto tbody").html('<tr><td colspan="4" class="text-center text-danger">Erro ao buscar produtos</td></tr>');
            });
        }

        $(document).on('click', '.btnSelecionarProduto', function() {
            let id = $(this).data('id');
            let label = $(this).data('label');
            let preco = $(this).data('preco');
            $("#produto_id").val(id);
            $("#produto").val(label);
            if (preco && preco !== '0,00' && preco !== '') {
                // Remover R$ e espaços, manter apenas o formato numérico
                preco = preco.replace('R$', '').trim();
                $("#valor_produto").val(preco).trigger('change');
                // Atualizar máscara monetária
                if ($("#valor_produto").data('maskMoney')) {
                    $("#valor_produto").maskMoney('mask', preco);
                }
            }
            $("#modalBuscaProduto").modal('hide');
        });

    });
</script>