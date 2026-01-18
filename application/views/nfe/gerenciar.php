<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-list"></i>
                </span>
                <h5>Gerenciamento de NFe's</h5>
            </div>
            <div class="widget-content nopadding">
                <?php 
                    $CI = &get_instance();
                    $configNFe = $CI->db->get('configuracoes_nfe')->row();
                    $preview_nfe_ativo = ($configNFe && isset($configNFe->preview_nfe) && $configNFe->preview_nfe) ? true : false;
                    $is_consulta = $this->session->flashdata('is_consulta');
                ?>

                <!-- Filtros -->
                <div class="row-fluid" style="margin-bottom: 20px;">
                    <form action="<?php echo base_url() ?>index.php/nfe/gerenciar" method="get" class="form-horizontal">
                        <div class="span12">
                            <div class="span3">
                                <label for="data_inicio">Data Início</label>
                                <input type="date" class="span12" name="data_inicio" id="data_inicio" value="<?php echo isset($_GET['data_inicio']) ? $_GET['data_inicio'] : date('Y-m-d', strtotime('-7 days')); ?>">
                            </div>
                            <div class="span3">
                                <label for="data_fim">Data Fim</label>
                                <input type="date" class="span12" name="data_fim" id="data_fim" value="<?php echo isset($_GET['data_fim']) ? $_GET['data_fim'] : date('Y-m-d'); ?>">
                            </div>
                            <div class="span3">
                                <label for="chave">Chave de Acesso</label>
                                <input type="text" class="span12" name="chave" id="chave" value="<?php echo isset($_GET['chave']) ? $_GET['chave'] : ''; ?>" placeholder="Digite a chave">
                            </div>
                            <div class="span3">
                                <label for="cliente">Nome do Cliente</label>
                                <input type="text" class="span12" name="cliente" id="cliente" value="<?php echo isset($_GET['cliente']) ? $_GET['cliente'] : ''; ?>" placeholder="Digite o nome">
                            </div>
                        </div>
                        <div class="span12" style="margin-top: 10px;">
                            <div class="span3">
                                <label for="numero">Número NF</label>
                                <input type="text" class="span12" name="numero" id="numero" value="<?php echo isset($_GET['numero']) ? $_GET['numero'] : ''; ?>" placeholder="Digite o número">
                            </div>
                            <div class="span3">
                                <label for="modelo">Modelo</label>
                                <select class="span12" name="modelo" id="modelo">
                                    <option value="">Todos</option>
                                    <option value="55" <?php echo (isset($_GET['modelo']) && $_GET['modelo'] == '55') ? 'selected' : ''; ?>>NFe</option>
                                    <option value="65" <?php echo (isset($_GET['modelo']) && $_GET['modelo'] == '65') ? 'selected' : ''; ?>>NFCe</option>
                                </select>
                            </div>
                            <div class="span3">
                                <label for="status">Status</label>
                                <select class="span12" name="status" id="status">
                                    <option value="">Todos</option>
                                    <option value="1" <?php echo (isset($_GET['status']) && $_GET['status'] == '1') ? 'selected' : ''; ?>>Autorizada</option>
                                    <option value="2" <?php echo (isset($_GET['status']) && $_GET['status'] == '2') ? 'selected' : ''; ?>>Cancelada</option>
                                    <option value="0" <?php echo (isset($_GET['status']) && $_GET['status'] == '0') ? 'selected' : ''; ?>>Rejeitada</option>
                                </select>
                            </div>
                            <div class="span3" style="margin-top: 20px;">
                                <button type="submit" class="button btn btn-info">
                                    <span class="button__icon"><i class='bx bx-search'></i></span>
                                    <span class="button__text2">Filtrar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal de Resposta da SEFAZ -->
                <?php if ($this->session->flashdata('nfe_modal') && (!$preview_nfe_ativo || $is_consulta)): ?>
                <div class="modal fade" id="nfeModal" tabindex="-1" role="dialog" aria-labelledby="nfeModalLabel" style="padding-right: 0 !important;">
                    <div class="modal-dialog" style="width: 100%; margin: 0; padding: 0;">
                        <div class="modal-content" style="border-radius: 0;">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title" id="nfeModalLabel">Resposta da SEFAZ</h4>
                            </div>
                            <div class="modal-body" style="padding: 15px;">
                                <?php 
                                    $nfe_modal = $this->session->flashdata('nfe_modal');
                                ?>
                                <table class="table table-bordered" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 33%;">
                                            <label style="font-size: 14px; display: block;"><strong>Número NFe:</strong></label>
                                            <span class="text-break"><?php echo $nfe_modal['numero_nfe']; ?></span>
                                        </td>
                                        <td style="width: 33%;">
                                            <label style="font-size: 14px; display: block;"><strong>Chave NFe:</strong></label>
                                            <span class="text-break"><?php echo $nfe_modal['chave_nfe']; ?></span>
                                        </td>
                                        <td style="width: 33%;">
                                            <label style="font-size: 14px; display: block;"><strong>Status:</strong></label>
                                            <span class="label label-<?php echo ($nfe_modal['status'] == 'Autorizada') ? 'success' : ($nfe_modal['status'] == 'Cancelada' ? 'danger' : 'danger'); ?>" style="<?php echo ($nfe_modal['status'] == 'Cancelada') ? 'background-color: #dc3545 !important;' : ''; ?>">
                                                <?php echo $nfe_modal['status']; ?>
                                            </span>
                                        </td>
                                    </tr>
                                </table>

                                <div style="margin-bottom: 10px;">
                                    <label style="font-size: 14px; display: block;"><strong>Motivo:</strong></label>
                                    <span class="text-break"><?php echo $nfe_modal['motivo']; ?></span>
                                </div>

                                <div>
                                    <label style="font-size: 14px; display: block;"><strong>Protocolo:</strong></label>
                                    <div class="well well-sm" style="margin-bottom: 0;">
                                        <pre style="white-space: pre-wrap; word-wrap: break-word; margin: 0; font-size: 12px; line-height: 1.4; font-family: monospace; background: transparent; border: none; max-height: calc(100vh - 350px); overflow-y: auto;"><?php echo htmlspecialchars($nfe_modal['protocolo']); ?></pre>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Data Emissão</th>
                            <th>Número NFe</th>
                            <th>Chave NFe</th>
                            <th>Cliente</th>
                            <th>Valor Total</th>
                            <th>Modelo</th>
                            <th>Status</th>
                            <th>Retorno</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($nfe) { ?>
                            <?php foreach ($nfe as $n) { ?>
                                <tr>
                                    <td><?php echo $n->id ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($n->created_at)) ?></td>
                                    <td><?php echo $n->numero_nfe ?></td>
                                    <td><?php echo $n->chave_nfe ?></td>
                                    <td><?php echo $n->nomeCliente ?></td>
                                    <td>R$ <?php
                                        if (isset($n->valor_total)) {
                                            echo number_format($n->valor_total, 2, ',', '.');
                                        } else {
                                            echo '-';
                                        }
                                    ?></td>
                                    <td><?php echo $n->modelo == 65 ? 'NFC-e' : 'NFe' ?></td>
                                    <td>
                                        <?php if ($n->status == 1) { ?>
                                            <span class="badge badge-success">Autorizada</span>
                                        <?php } elseif ($n->status == 2) { ?>
                                            <span class="badge badge-danger" style="background-color: #dc3545 !important;">Cancelada</span>
                                        <?php } else { ?>
                                            <span class="badge badge-danger">Rejeitada</span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($n->chave_retorno_evento) { ?>
                                            <span class="badge badge-info" title="<?php echo $n->chave_retorno_evento ?>">
                                                <?php echo substr($n->chave_retorno_evento, 0, 30) . '...' ?>
                                            </span>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if ($n->status == 0 && empty($n->entrada_id)) { ?>
                                            <a href="#modal-reemitir" data-toggle="modal" data-id="<?php echo $n->id; ?>" class="btn btn-warning tip-top reemitir-nfe" title="Reemitir NFe">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        <?php } ?>
                                        <?php if ($n->status == 0 && !empty($n->entrada_id)) { ?>
                                            <a href="#modal-reemitir-devolucao" data-toggle="modal" data-id="<?php echo $n->id; ?>" class="btn btn-warning tip-top reemitir-devolucao" title="Reemitir Devolução">
                                                <i class="fas fa-redo"></i>
                                            </a>
                                        <?php } ?>
                                        <a href="<?php echo base_url() ?>index.php/nfe/consultar/<?php echo $n->id; ?>" class="btn btn-primary tip-top" title="Consultar NFe">
                                            <i class="fas fa-search"></i>
                                        </a>
                                        <?php if ($n->status == 1) { ?>
                                            <?php 
                                            // Verifica se a NFe tem mais de 24 horas e não é uma devolução autorizada
                                            $data_emissao = new DateTime($n->created_at);
                                            $agora = new DateTime();
                                            $diferenca = $agora->diff($data_emissao);
                                            $horas = ($diferenca->days * 24) + $diferenca->h;
                                            
                                            if ($horas >= 24 && empty($n->entrada_id) && empty($n->chave_retorno_evento)) { ?>
                                                <a href="#modal-devolucao" data-toggle="modal" data-id="<?php echo $n->id; ?>" class="btn btn-info tip-top devolucao-nfe" title="Devolução">
                                                    <i class="fas fa-undo"></i>
                                                </a>
                                            <?php } ?>
                                            <?php if ($n->status == 1 && !empty($n->entrada_id) && empty($n->chave_retorno_evento)) { ?>
                                                <a href="#modal-reemitir-devolucao" data-toggle="modal" data-id="<?php echo $n->id; ?>" class="btn btn-warning tip-top reemitir-devolucao" title="Reemitir Devolução">
                                                    <i class="fas fa-redo"></i>
                                                </a>
                                            <?php } ?>
                                            <a href="<?php echo base_url() ?>index.php/nfe/imprimir/<?php echo $n->id; ?>" class="btn btn-inverse tip-top" title="Imprimir DANFE" target="_blank" onclick="imprimirDanfe(event, this.href)"><i class="fas fa-print"></i></a>
                                            <a href="#modal-cancelar" class="btn btn-danger tip-top cancelar-nfe" data-toggle="modal" data-id="<?php echo $n->id; ?>" title="Cancelar NFe"><i class="fas fa-times"></i></a>
                                        <?php } elseif ($n->status == 2) { ?>
                                            <a href="<?php echo base_url(); ?>index.php/nfe/imprimirCancelamento/<?php echo $n->id; ?>" class="btn btn-info tip-top" title="Imprimir Cancelamento" target="_blank" onclick="imprimirDanfe(event, this.href)">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="9">Nenhuma NFe encontrada</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reemitir NFe -->
<div id="modal-reemitir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Reemitir NFe</h3>
    </div>
    <div class="modal-body">
        <form action="<?php echo base_url() ?>index.php/nfe/reemitir" method="post" id="formReemitir">
            <input type="hidden" name="nfe_id" id="nfe_id" value="" />
            <p>Deseja realmente reemitir esta NFe?</p>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-warning" id="btnReemitir">Reemitir</button>
    </div>
</div>

<!-- Modal Cancelar NFe -->
<div id="modal-cancelar" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Cancelar NFe</h3>
    </div>
    <div class="modal-body">
        <form action="<?php echo base_url() ?>index.php/nfe/cancelar" method="post" id="formCancelar">
            <input type="hidden" name="nfe_id" id="nfe_id_cancelar" value="" />
            <div class="control-group">
                <label for="justificativa" class="control-label">Justificativa do Cancelamento*</label>
                <div class="controls">
                    <textarea class="span12" name="justificativa" id="justificativa" rows="3" required style="background-color: var(--modal-bg); color: var(--text-color);"></textarea>
                    <span class="help-block" style="color: var(--text-color);">Informe a justificativa para o cancelamento da NFe (mínimo 15 caracteres)</span>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-danger" id="btnCancelar">Confirmar Cancelamento</button>
    </div>
</div>

<!-- Modal Status Venda -->
<div id="modal-status-venda" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="statusVendaModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="statusVendaModalLabel">Status da Venda</h3>
    </div>
    <div class="modal-body">
        <p>Deseja cancelar a venda associada a esta NFe?</p>
        <div class="alert alert-info">
            <strong>Sim:</strong> A venda será marcada como "Cancelada"<br>
            <strong>Não:</strong> A venda será marcada como "Orçamento"
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true" id="btnNaoCancelar">Não</button>
        <button class="btn btn-danger" id="btnSimCancelar">Sim</button>
    </div>
</div>

<!-- Modal Devolução NFe -->
<div id="modal-devolucao" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Devolução de NFe</h3>
    </div>
    <div class="modal-body">
        <form action="<?php echo base_url() ?>index.php/nfe/devolver" method="post" id="formDevolucao">
            <input type="hidden" name="nfe_id" id="nfe_id_devolucao" value="" />
            
            <div class="control-group">
                <label class="control-label">Deseja devolver todos os itens?</label>
                <div class="controls">
                    <label class="radio inline">
                        <input type="radio" name="devolver_todos" value="true" checked> Sim
                    </label>
                    <label class="radio inline">
                        <input type="radio" name="devolver_todos" value="false"> Não
                    </label>
                </div>
            </div>

            <div id="selecao-itens" style="display: none;">
                <div class="alert alert-info">
                    <strong>Observação:</strong> Selecione os itens que deseja devolver e informe a quantidade para cada um.
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Selecionar</th>
                            <th>Produto</th>
                            <th>Quantidade Original</th>
                            <th>Quantidade a Devolver</th>
                        </tr>
                    </thead>
                    <tbody id="itens-devolucao">
                        <!-- Os itens serão preenchidos via JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info">
                <strong>Observação:</strong> A nota de devolução será emitida com a Natureza da Operação "Devolução de Venda" e o CFOP será ajustado de acordo com o estado do cliente.
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-info" id="btnDevolucao">Confirmar Devolução</button>
    </div>
</div>

<!-- Modal Reemitir Devolução -->
<div id="modal-reemitir-devolucao" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Reemitir Devolução</h3>
    </div>
    <div class="modal-body">
        <form action="<?php echo base_url() ?>index.php/devolucaoCompra/reemitirDevolucao" method="post" id="formReemitirDevolucao">
            <input type="hidden" name="nfe_id" id="nfe_id_reemitir_devolucao" value="" />
            <p>Deseja realmente reemitir esta devolução?</p>
        </form>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
        <button class="btn btn-warning" id="btnReemitirDevolucao">Reemitir</button>
    </div>
</div>

<style>
    /* Estilos para tema escuro */
    [data-theme="dark"] #modal-cancelar .modal-content {
        background-color: #1a1a1a;
        color: #ffffff;
    }

    [data-theme="dark"] #modal-cancelar .modal-header {
        background-color: #2d2d2d;
        border-bottom: 1px solid #3d3d3d;
    }

    [data-theme="dark"] #modal-cancelar .modal-body {
        background-color: #1a1a1a;
    }

    [data-theme="dark"] #modal-cancelar .modal-footer {
        background-color: #2d2d2d;
        border-top: 1px solid #3d3d3d;
    }

    [data-theme="dark"] #modal-cancelar .control-label {
        color: #ffffff;
    }

    [data-theme="dark"] #modal-cancelar .help-block {
        color: #cccccc;
    }

    [data-theme="dark"] #modal-cancelar textarea {
        background-color: #2d2d2d;
        color: #ffffff;
        border: 1px solid #3d3d3d;
    }

    [data-theme="dark"] #modal-cancelar textarea:focus {
        border-color: #4d4d4d;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.1);
    }

    /* Estilos para loading */
    .modal.loading .modal-content {
        position: relative;
    }

    .modal.loading .modal-content:after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    .modal.loading .modal-content:before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 50px;
        height: 50px;
        margin: -25px 0 0 -25px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        z-index: 1001;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>

<script>
$(document).ready(function() {
    <?php if ($this->session->flashdata('nfe_modal')): ?>
        $('#nfeModal').modal('show');
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        swal({
            title: "Erro!",
            text: "<?php echo $this->session->flashdata('error'); ?>",
            type: "error",
            confirmButtonText: "Fechar"
        });
    <?php endif; ?>

    $('.reemitir-nfe').click(function() {
        var id = $(this).data('id');
        $('#nfe_id').val(id);
    });

    $('.cancelar-nfe').click(function() {
        var nfeId = $(this).data('id');
        $('#nfe_id_cancelar').val(nfeId);
    });

    $('#btnReemitir').click(function() {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Reemitindo...');
        $('#formReemitir').submit();
    });

    $('#btnCancelar').click(function() {
        var $btn = $(this);
        var nfe_id = $('#nfe_id_cancelar').val();
        var justificativa = $('#justificativa').val();

        // Desabilita o botão e mostra loading
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Cancelando...');
        
        // Adiciona classe de loading ao modal
        $('#modal-cancelar').addClass('loading');

        $.ajax({
            url: '<?php echo base_url(); ?>index.php/nfe/cancelar',
            type: 'POST',
            data: {
                nfe_id: nfe_id,
                justificativa: justificativa
            },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    // Fecha o modal de cancelamento
                    $('#modal-cancelar').modal('hide');
                    
                    // Se for uma NFe de devolução, não mostra o modal de cancelamento de venda
                    if (!data.data.is_devolucao) {
                        $('#modal-status-venda').modal('show');
                    } else {
                        swal({
                            title: "Sucesso!",
                            text: data.message,
                            type: "success",
                            confirmButtonText: "OK"
                        }).then(function() {
                            window.location.reload();
                        });
                    }
                } else {
                    swal({
                        title: "Erro!",
                        text: data.message,
                        type: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function() {
                swal({
                    title: "Erro!",
                    text: "Erro ao processar a requisição.",
                    type: "error",
                    confirmButtonText: "OK"
                });
            },
            complete: function() {
                // Reabilita o botão e restaura o texto original
                $btn.prop('disabled', false).html('Confirmar Cancelamento');
                // Remove classe de loading do modal
                $('#modal-cancelar').removeClass('loading');
            }
        });
    });

    // Quando clicar em "Sim" no modal de status da venda
    $('#btnSimCancelar').click(function() {
        var $btn = $(this);
        // Desabilita o botão e mostra loading
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        // Adiciona classe de loading ao modal
        $('#modal-status-venda').addClass('loading');
        
        $.ajax({
            url: '<?php echo base_url() ?>index.php/nfe/atualizarStatusVenda',
            type: 'POST',
            data: {
                nfe_id: $('#nfe_id_cancelar').val(),
                cancelar_venda: true
            },
            success: function(response) {
                $('#modal-status-venda').modal('hide');
                swal({
                    title: "Sucesso!",
                    text: "Venda marcada como 'Cancelada' com sucesso!",
                    type: "success",
                    confirmButtonText: "OK"
                }).then(function() {
                    window.location.reload();
                });
            },
            error: function() {
                swal({
                    title: "Erro!",
                    text: "Ocorreu um erro ao atualizar o status da venda.",
                    type: "error",
                    confirmButtonText: "OK"
                });
            },
            complete: function() {
                // Reabilita o botão e restaura o texto original
                $btn.prop('disabled', false).html('Sim');
                // Remove classe de loading do modal
                $('#modal-status-venda').removeClass('loading');
            }
        });
    });

    // Quando clicar em "Não" no modal de status da venda
    $('#btnNaoCancelar').click(function() {
        var $btn = $(this);
        // Desabilita o botão e mostra loading
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        // Adiciona classe de loading ao modal
        $('#modal-status-venda').addClass('loading');
        
        $.ajax({
            url: '<?php echo base_url() ?>index.php/nfe/atualizarStatusVenda',
            type: 'POST',
            data: {
                nfe_id: $('#nfe_id_cancelar').val(),
                cancelar_venda: false
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Exibe log no console
                    console.log('Alteração de Status da Venda:');
                    console.log('Cliente:', response.log_data.cliente);
                    console.log('Usuário:', response.log_data.usuario);
                    console.log('Data:', response.log_data.data_alteracao);
                    console.log('Status Anterior:', response.log_data.status_anterior);
                    console.log('Status Novo:', response.log_data.status_novo);
                    console.log('Valor Total:', 'R$ ' + parseFloat(response.log_data.valor_total).toFixed(2));
                    
                    $('#modal-status-venda').modal('hide');
                    swal({
                        title: "Sucesso!",
                        text: "Venda marcada como 'Orçamento' com sucesso!",
                        type: "success",
                        confirmButtonText: "OK"
                    }).then(function() {
                        window.location.reload();
                    });
                } else {
                    swal({
                        title: "Erro!",
                        text: response.message || "Ocorreu um erro ao atualizar o status da venda.",
                        type: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Erro na requisição:', xhr.responseText);
                var errorMessage = "Ocorreu um erro ao atualizar o status da venda.";
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Erro ao parsear resposta:', e);
                }
                
                swal({
                    title: "Erro!",
                    text: errorMessage,
                    type: "error",
                    confirmButtonText: "OK"
                });
            },
            complete: function() {
                // Reabilita o botão e restaura o texto original
                $btn.prop('disabled', false).html('Não');
                // Remove classe de loading do modal
                $('#modal-status-venda').removeClass('loading');
            }
        });
    });

    // Adiciona loading nos botões de consulta
    $('a[href*="consultar"]').click(function(e) {
        var $link = $(this);
        $link.html('<i class="fas fa-spinner fa-spin"></i> Consultando...');
    });

    // Adiciona loading nos botões de emitir
    $('a[href*="emitir"]').click(function(e) {
        var $link = $(this);
        $link.html('<i class="fas fa-spinner fa-spin"></i> Emitindo...');
    });

    // Devolução NFe
    $('.devolucao-nfe').click(function() {
        var id = $(this).data('id');
        $('#nfe_id_devolucao').val(id);
        
        // Limpa a tabela de itens
        $('#itens-devolucao').empty();
        
        // Busca os itens da venda
        $.get('<?php echo base_url() ?>index.php/nfe/getItensVenda/' + id, function(data) {
            if (data.success) {
                data.itens.forEach(function(item) {
                    var row = '<tr>' +
                        '<td>' +
                        '<input type="checkbox" name="itens_selecionados[]" value="' + item.idProdutos + '" ' +
                        'onchange="toggleQuantidade(this)" checked>' +
                        '</td>' +
                        '<td>' + item.descricao + '</td>' +
                        '<td>' + item.quantidade + '</td>' +
                        '<td>' +
                        '<input type="number" name="quantidades[' + item.idProdutos + ']" ' +
                        'min="1" max="' + item.quantidade + '" value="' + item.quantidade + '" ' +
                        'class="input-mini quantidade-input" required>' +
                        '</td>' +
                        '</tr>';
                    $('#itens-devolucao').append(row);
                });
            } else {
                swal({
                    title: "Erro!",
                    text: data.message || "Erro ao carregar os itens da venda.",
                    type: "error",
                    confirmButtonText: "OK"
                });
            }
        }, 'json');
    });

    // Função para habilitar/desabilitar o input de quantidade
    window.toggleQuantidade = function(checkbox) {
        var quantidadeInput = $(checkbox).closest('tr').find('.quantidade-input');
        if (checkbox.checked) {
            quantidadeInput.prop('disabled', false);
        } else {
            quantidadeInput.prop('disabled', true);
            quantidadeInput.val('');
        }
    };

    $('#btnDevolucao').click(function() {
        var $btn = $(this);
        var form = $('#formDevolucao');
        var devolverTodos = $('input[name="devolver_todos"]:checked').val();
        
        if (devolverTodos === 'false') {
            // Verifica se pelo menos um item foi selecionado
            var itensSelecionados = $('input[name="itens_selecionados[]"]:checked').length;
            if (itensSelecionados === 0) {
                swal({
                    title: "Atenção!",
                    text: "Selecione pelo menos um item para devolução.",
                    type: "warning",
                    confirmButtonText: "OK"
                });
                return false;
            }
            
            // Verifica se as quantidades são válidas para os itens selecionados
            var quantidadesValidas = true;
            $('input[name="itens_selecionados[]"]:checked').each(function() {
                var quantidadeInput = $(this).closest('tr').find('.quantidade-input');
                var qtd = parseInt(quantidadeInput.val());
                var max = parseInt(quantidadeInput.attr('max'));
                if (qtd <= 0 || qtd > max) {
                    quantidadesValidas = false;
                    return false;
                }
            });
            
            if (!quantidadesValidas) {
                swal({
                    title: "Atenção!",
                    text: "As quantidades informadas são inválidas.",
                    type: "warning",
                    confirmButtonText: "OK"
                });
                return false;
            }
        }
        
        // Desabilita o botão e mostra loading
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        // Adiciona classe de loading ao modal
        $('#modal-devolucao').addClass('loading');
        
        form.submit();
    });

    // Mostra/esconde a seleção de itens baseado na opção selecionada
    $('input[name="devolver_todos"]').change(function() {
        if ($(this).val() === 'false') {
            $('#selecao-itens').show();
        } else {
            $('#selecao-itens').hide();
        }
    });

    // Reemitir Devolução
    $('.reemitir-devolucao').click(function() {
        var id = $(this).data('id');
        $('#nfe_id_reemitir_devolucao').val(id);
    });

    $('#btnReemitirDevolucao').click(function() {
        $('#formReemitirDevolucao').submit();
    });
});

function imprimirDanfe(event, url) {
    event.preventDefault();
    var printWindow = window.open(url, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}
</script>

<?php if ($this->session->flashdata('preview_nfe_id')): ?>
<script>
    window.onload = function() {
        var previewWindow = window.open('<?php echo base_url('index.php/nfe/imprimir/' . $this->session->flashdata('preview_nfe_id')); ?>', '_blank');
        // Aguarda o carregamento da janela e abre o diálogo de impressão
        previewWindow.onload = function() {
            previewWindow.print();
        };
    };
</script>
<?php endif; ?> 