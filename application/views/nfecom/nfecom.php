<style>
    select {
        width: 70px;
    }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice"></i>
        </span>
        <h5>NFECom</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <form method="get" action="<?php echo base_url(); ?>index.php/nfecom/gerenciar">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aNfecom')) { ?>
                <div class="span3">
                    <a href="<?php echo base_url(); ?>index.php/nfecom/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                        <span class="button__text2">Nova NFECom</span>
                    </a>
                </div>
            <?php } ?>
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Chave, cliente ou número" class="span12" value="<?php echo $this->input->get('pesquisa'); ?>">
            </div>
            <div class="span2">
                <select name="status" class="span12">
                    <option value="">Selecione status</option>
                    <option value="0" <?php echo $this->input->get('status') == '0' ? 'selected' : ''; ?>>Rascunho</option>
                    <option value="1" <?php echo $this->input->get('status') == '1' ? 'selected' : ''; ?>>Salvo</option>
                    <option value="2" <?php echo $this->input->get('status') == '2' ? 'selected' : ''; ?>>Enviado</option>
                    <option value="3" <?php echo $this->input->get('status') == '3' ? 'selected' : ''; ?>>Autorizado</option>
                    <option value="4" <?php echo $this->input->get('status') == '4' ? 'selected' : ''; ?>>Rejeitado</option>
                </select>
            </div>
            <div class="span3">
                <input type="date" name="data" id="data" placeholder="De" class="span6 datepicker" autocomplete="off" value="<?php echo $this->input->get('data'); ?>">
                <input type="date" name="data2" id="data2" placeholder="Até" class="span6 datepicker" autocomplete="off" value="<?php echo $this->input->get('data2'); ?>">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                </button>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nº NF</th>
                        <th>Chave</th>
                        <th>Cliente</th>
                        <th>Data Emissão</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th style="text-align:center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!$results) {
                            echo '<tr>
                                    <td colspan="7">Nenhuma NFECom Cadastrada</td>
                                </tr>';
                        }
                        foreach ($results as $r) {
                            $dataEmissao = date('d/m/Y', strtotime($r->NFC_DHEMI));
                            $valorTotal = number_format($r->NFC_V_NF, 2, ',', '.');

                            $corStatus = match($r->NFC_STATUS) {
                                0 => '#CDB380', // Rascunho
                                1 => '#436eee', // Salvo
                                2 => '#00cd00', // Enviado
                                3 => '#4d9c79', // Autorizado
                                4 => '#f24c6f', // Rejeitado
                                default => '#999'
                            };

                            $statusDesc = match($r->NFC_STATUS) {
                                0 => 'Rascunho',
                                1 => 'Salvo',
                                2 => 'Enviado',
                                3 => 'Autorizado',
                                4 => 'Rejeitado',
                                default => 'Desconhecido'
                            };

                            echo '<tr>';
                            echo '<td>' . $r->NFC_NNF . '</td>';
                            echo '<td>' . $r->NFC_CH_NFCOM . '</td>';
                            echo '<td>' . $r->NFC_X_NOME_DEST . '</td>';
                            echo '<td>' . $dataEmissao . '</td>';
                            echo '<td>R$ ' . $valorTotal . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '">' . $statusDesc . '</span></td>';
                            echo '<td style="text-align:center">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/visualizar/' . $r->NFC_ID . '" style="margin-right: 1%" class="btn-nwe3" title="Visualizar"><i class="bx bx-show"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/danfe/' . $r->NFC_ID . '" style="margin-right: 1%" class="btn-nwe3" title="DANFE"><i class="bx bx-file"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $r->NFC_STATUS < 2) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/gerarXml/' . $r->NFC_ID . '" style="margin-right: 1%" class="btn-nwe3" title="Gerar XML"><i class="bx bx-code"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $r->NFC_STATUS == 2) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/autorizar/' . $r->NFC_ID . '" style="margin-right: 1%" class="btn-nwe3" title="Autorizar"><i class="bx bx-check"></i></a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-nfecom" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form id="formNfecom" action="<?php echo base_url() ?>index.php/nfecom/adicionar" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h3 id="myModalLabel">Nova NFECom</h3>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Obrigatório o preenchimento dos campos com asterisco.</div>
            <div class="span12" style="margin-left: 0">
                <label for="cliente">Cliente *</label>
                <select class="span12" name="clientes_id" id="cliente" required>
                    <option value="">Selecione um cliente</option>
                    <?php foreach ($clientes as $cliente) { ?>
                        <option value="<?php echo $cliente->idClientes; ?>"><?php echo $cliente->nomeCliente; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="span12" style="margin-left: 0">
                <label for="servico">Serviço *</label>
                <select class="span12" name="servicos_id" id="servico" required>
                    <option value="">Selecione um serviço</option>
                    <?php foreach ($servicos as $servico) { ?>
                        <option value="<?php echo $servico->idServicos; ?>"><?php echo $servico->nome; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="span12" style="margin-left: 0">
                <label for="observacoes">Observações *</label>
                <textarea class="span12" name="observacoes" id="observacoes" required></textarea>
            </div>
            <div class="span6" style="margin-left: 0">
                <label for="numeroContrato">Número do Contrato *</label>
                <input type="text" class="span12" name="numeroContrato" id="numeroContrato" required>
            </div>
            <div class="span6">
                <label for="dataContratoIni">Data Início Contrato *</label>
                <input type="date" class="span12" name="dataContratoIni" id="dataContratoIni" required>
            </div>
            <div class="span6" style="margin-left: 0">
                <label for="serie">Série</label>
                <input type="number" class="span12" name="serie" id="serie" value="1" min="1" max="999">
            </div>
            <div class="span6">
                <label for="dataEmissao">Data Emissão *</label>
                <input type="date" class="span12" name="dataEmissao" id="dataEmissao" required>
            </div>
            <div class="span6" style="margin-left: 0">
                <label for="valorBruto">Valor Bruto *</label>
                <input type="number" class="span12" name="valorBruto" id="valorBruto" step="0.01" required>
            </div>
            <div class="span6">
                <label for="comissaoAgencia">Comissão Agência</label>
                <input type="number" class="span12" name="comissaoAgencia" id="comissaoAgencia" step="0.01" value="0">
            </div>
            <div class="span6" style="margin-left: 0">
                <label for="dataVencimento">Data Vencimento *</label>
                <input type="date" class="span12" name="dataVencimento" id="dataVencimento" required>
            </div>
            <div class="span6">
                <label for="dataPeriodoIni">Período Uso - Início *</label>
                <input type="date" class="span12" name="dataPeriodoIni" id="dataPeriodoIni" required>
            </div>
            <div class="span6" style="margin-left: 0">
                <label for="dataPeriodoFim">Período Uso - Fim *</label>
                <input type="date" class="span12" name="dataPeriodoFim" id="dataPeriodoFim" required>
            </div>
            <div class="span6">
                <label for="dadosBancarios">Dados Bancários</label>
                <textarea class="span12" name="dadosBancarios" id="dadosBancarios"></textarea>
            </div>

            <!-- Dados do Cliente (preenchidos automaticamente) -->
            <div class="span12" style="margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                <h4>Dados do Cliente</h4>
            </div>

            <div class="span6" style="margin-left: 0">
                <label for="nomeCliente">Nome do Cliente</label>
                <input type="text" class="span12" name="nomeCliente" id="nomeCliente" readonly>
            </div>
            <div class="span6">
                <label for="cnpjCliente">CNPJ/CPF</label>
                <input type="text" class="span12" name="cnpjCliente" id="cnpjCliente" readonly>
            </div>

            <div class="span12" style="margin-left: 0">
                <label for="enderecoCliente">Endereço Completo</label>
                <input type="text" class="span12" name="enderecoCliente" id="enderecoCliente" readonly placeholder="Selecione um cliente para carregar o endereço">
            </div>

            <!-- Campos ocultos necessários -->
            <input type="hidden" name="logradouroCliente" id="logradouroCliente">
            <input type="hidden" name="numeroCliente" id="numeroCliente">
            <input type="hidden" name="bairroCliente" id="bairroCliente">
            <input type="hidden" name="municipioCliente" id="municipioCliente">
            <input type="hidden" name="codMunCliente" id="codMunCliente">
            <input type="hidden" name="cepCliente" id="cepCliente">
            <input type="hidden" name="ufCliente" id="ufCliente">
        </div>
        <div class="modal-footer">
            <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
            <button class="btn btn-success">Salvar</button>
        </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function(){
    $('#cliente').change(function(){
        var clienteId = $(this).val();
        if(clienteId) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/nfecom/getCliente/' + clienteId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    // Preencher os campos básicos
                    $('#nomeCliente').val(data.nomeCliente || '');
                    $('#cnpjCliente').val(data.cnpjCliente || '');

                    // Concatenar endereço completo
                    var enderecoCompleto = '';
                    if (data.logradouroCliente) {
                        enderecoCompleto += data.logradouroCliente;
                    }
                    if (data.numeroCliente) {
                        enderecoCompleto += ', ' + data.numeroCliente;
                    }
                    if (data.bairroCliente) {
                        enderecoCompleto += ' - ' + data.bairroCliente;
                    }
                    if (data.municipioCliente) {
                        enderecoCompleto += ' - ' + data.municipioCliente;
                    }
                    if (data.ufCliente) {
                        enderecoCompleto += '/' + data.ufCliente;
                    }
                    if (data.cepCliente) {
                        enderecoCompleto += ' - CEP: ' + data.cepCliente;
                    }

                    $('#enderecoCliente').val(enderecoCompleto);

                    // Preencher campos ocultos necessários para processamento
                    $('#logradouroCliente').val(data.logradouroCliente || '');
                    $('#numeroCliente').val(data.numeroCliente || '');
                    $('#bairroCliente').val(data.bairroCliente || '');
                    $('#municipioCliente').val(data.municipioCliente || '');
                    $('#codMunCliente').val(data.codMunCliente || '');
                    $('#cepCliente').val(data.cepCliente || '');
                    $('#ufCliente').val(data.ufCliente || '');
                },
                error: function() {
                    alert('Erro ao buscar dados do cliente');
                }
            });
        } else {
            // Limpar campos quando nenhum cliente selecionado
            $('#nomeCliente, #cnpjCliente, #enderecoCliente, #logradouroCliente, #numeroCliente, #bairroCliente, #municipioCliente, #codMunCliente, #cepCliente, #ufCliente').val('');
        }
    });

    $('#servico').change(function(){
        var servicoId = $(this).val();
        if(servicoId) {
            $.ajax({
                url: '<?php echo base_url(); ?>index.php/servicos/getServico/' + servicoId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Dados do serviço serão preenchidos automaticamente no controller
                }
            });
        }
    });
});
</script>