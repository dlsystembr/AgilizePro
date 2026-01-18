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
                    <option value="1" <?php echo $this->input->get('status') == '1' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="2" <?php echo $this->input->get('status') == '2' ? 'selected' : ''; ?>>Enviado</option>
                    <option value="3" <?php echo $this->input->get('status') == '3' ? 'selected' : ''; ?>>Autorizado</option>
                    <option value="4" <?php echo $this->input->get('status') == '4' ? 'selected' : ''; ?>>Rejeitada</option>
                    <option value="5" <?php echo $this->input->get('status') == '5' ? 'selected' : ''; ?>>Autorizada</option>
                    <option value="7" <?php echo $this->input->get('status') == '7' ? 'selected' : ''; ?>>Cancelada</option>
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
                        <th>Motivo</th>
                        <th style="text-align:center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        if (!$results) {
                            echo '<tr>
                                    <td colspan="8">Nenhuma NFECom Cadastrada</td>
                                </tr>';
                        }
                        foreach ($results as $r) {
                            $dataEmissao = date('d/m/Y', strtotime($r->NFC_DHEMI));
                            $valorTotal = number_format($r->NFC_V_NF, 2, ',', '.');

                            $statusNum = (int)$r->NFC_STATUS;
                            if ($statusNum === 0) $statusDesc = 'Rascunho';
                            elseif ($statusNum === 1) $statusDesc = 'Pendente';
                            elseif ($statusNum === 2) $statusDesc = 'Enviado';
                            elseif ($statusNum === 3) $statusDesc = 'Autorizado';
                            elseif ($statusNum === 4) $statusDesc = 'Rejeitada';
                            elseif ($statusNum === 5) $statusDesc = 'Autorizada';
                            elseif ($statusNum === 7) $statusDesc = 'Cancelada';
                            else $statusDesc = 'Desconhecido (Status: ' . $r->NFC_STATUS . ' | Tipo: ' . gettype($r->NFC_STATUS) . ')';

                            $corStatus = match($statusNum) {
                                0 => '#CDB380', // Rascunho - bege
                                1 => '#f39c12', // Pendente - amarelo
                                2 => '#00cd00', // Enviado - verde claro
                                3 => '#4d9c79', // Autorizado - verde escuro
                                4 => '#f24c6f', // Rejeitada - vermelho
                                5 => '#28a745', // Autorizada - verde
                                7 => '#999',    // Cancelada - cinza
                                default => '#999' // Default - cinza
                            };

                            echo '<tr>';
                            echo '<td>' . $r->NFC_NNF . '</td>';
                            echo '<td>' . $r->NFC_CH_NFCOM . '</td>';
                            echo '<td>' . $r->NFC_X_NOME_DEST . '</td>';
                            echo '<td>' . $dataEmissao . '</td>';
                            echo '<td>R$ ' . $valorTotal . '</td>';
                            echo '<td><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '">' . $statusDesc . '</span></td>';
                            echo '<td>' . (!empty($r->NFC_X_MOTIVO) ? htmlspecialchars(substr($r->NFC_X_MOTIVO, 0, 50)) . (strlen($r->NFC_X_MOTIVO) > 50 ? '...' : '') : '-') . '</td>';
                            echo '<td style="text-align:center; white-space: nowrap;">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/visualizar/' . $r->NFC_ID . '" class="btn btn-mini btn-info" title="Ver dados da nota" style="margin-right: 2px">Ver dados</a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
                                if ($r->NFC_STATUS < 2) {
                                    // NFCom nova ou salva - permite gerar
                                    echo '<a href="#" onclick="gerarNFCom(' . $r->NFC_ID . ')" class="btn btn-mini btn-success" title="Gerar NFCom" style="margin-right: 2px">Gerar NFCom</a>';
                                } elseif ($r->NFC_STATUS == 4) {
                                    // NFCom rejeitada - permite reemitir
                                    echo '<a href="#" onclick="gerarNFCom(' . $r->NFC_ID . ')" class="btn btn-mini btn-success" title="Reemitir Nota" style="margin-right: 2px">Reemitir Nota</a>';
                                }
                                // NFCom autorizada (status 3 ou 5) não mostra botão
                                // NFCom cancelada (status 7) não mostra botão
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/danfe/' . $r->NFC_ID . '" class="btn btn-mini btn-inverse" target="_blank" title="Imprimir NFCom" style="margin-right: 2px"><i class="bx bx-printer"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom') && ($r->NFC_STATUS == 3 || $r->NFC_STATUS == 5)) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/gerarXml/' . $r->NFC_ID . '" class="btn btn-mini btn-warning" title="Baixar XML Autorizado" style="margin-right: 2px">XML</a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $r->NFC_STATUS >= 2) {
                                echo '<a href="#" onclick="consultarNFCom(' . $r->NFC_ID . ')" class="btn btn-mini" title="Consultar Status na SEFAZ" style="margin-right: 2px"><i class="bx bx-search"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $r->NFC_STATUS != 3) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/excluir/' . $r->NFC_ID . '" class="btn btn-mini btn-danger" title="Excluir NFCom" style="margin-right: 2px" onclick="return confirm(\'Tem certeza que deseja excluir esta NFCom?\')">Excluir</a>';
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

<!-- Modal de Resposta da SEFAZ -->
<div class="modal fade nfe-modal" id="nfecomModal" tabindex="-1" role="dialog" aria-labelledby="nfecomModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="nfecomModalLabel">
                    <i class="fas fa-file-invoice"></i> Resposta da SEFAZ
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if ($this->session->flashdata('nfecom_modal')): 
                    $nfecom_modal = $this->session->flashdata('nfecom_modal');
                ?>
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>
                                    <label><strong>Número NFeCOM:</strong></label>
                                    <div class="text-break"><?php echo $nfecom_modal['numero_nfcom']; ?></div>
                                </td>
                                <td>
                                    <label><strong>Chave NFeCOM:</strong></label>
                                    <div class="text-break"><?php echo $nfecom_modal['chave_nfcom']; ?></div>
                                </td>
                                <td>
                                    <label><strong>Status:</strong></label>
                                    <div class="nfe-status <?php echo ($nfecom_modal['status'] == 'Autorizado') ? 'success' : 'error'; ?>">
                                        <?php echo $nfecom_modal['status']; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label><strong>Motivo:</strong></label>
                        <div class="text-break"><?php echo $nfecom_modal['motivo']; ?></div>
                    </div>

                    <div class="mt-3">
                        <label><strong>Protocolo:</strong></label>
                        <div class="well">
                            <pre><?php echo htmlspecialchars($nfecom_modal['protocolo']); ?></pre>
                        </div>
                    </div>

                    <?php if (!empty($nfecom_modal['retorno'])): ?>
                        <div class="mt-3">
                            <label><strong>Retorno SEFAZ:</strong></label>
                            <div class="well">
                                <pre><?php echo htmlspecialchars($nfecom_modal['retorno']); ?></pre>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Nenhuma resposta da SEFAZ disponível.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <?php
                if ($this->session->flashdata('nfecom_modal')) {
                    $nfecom_modal = $this->session->flashdata('nfecom_modal');
                    if ($nfecom_modal['status'] == 'Autorizado') {
                ?>
                    <a href="<?php echo base_url() ?>index.php/nfecom/gerarXml/<?php echo $nfecom_modal['id']; ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-download"></i> Baixar XML
                    </a>
                    <a href="<?php echo base_url() ?>index.php/nfecom/baixarDanfe/<?php echo $nfecom_modal['id']; ?>" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-pdf"></i> Baixar DANFE
                    </a>
                    <a href="<?php echo base_url() ?>index.php/nfecom/danfe/<?php echo $nfecom_modal['id']; ?>" class="btn btn-info" target="_blank">
                        <i class="fas fa-eye"></i> Visualizar DANFE
                    </a>
                <?php
                    }
                }
                ?>
                <button type="button" class="nfe-button" data-dismiss="modal">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    /* Verificar se deve mostrar modal */
    <?php
    $showModal = false;
    if ($this->session->flashdata('nfecom_modal')) {
        $showModal = true;
    }
    ?>
    if (<?php echo $showModal ? 'true' : 'false'; ?>) {
        $('#nfecomModal').modal('show');
    }
});

function consultarNFCom(id) {
    /* Verificar se a função está sendo chamada */
    console.log('consultarNFCom called with id:', id);

    /* Encontrar o botão clicado e alterar seu texto */
    var botao = event.target.closest('a');
    var textoOriginal = botao.innerHTML;

    /* Alterar texto do botão e desabilitar */
    botao.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Consultando...';
    botao.style.pointerEvents = 'none';
    botao.style.opacity = '0.6';

    /* Fazer chamada AJAX */
    $.ajax({
        url: '<?php echo base_url(); ?>index.php/nfecom/consultar',
        type: 'POST',
        data: {
            id: id,
            ajax: 'true',
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            /* Reverter alterações do botão */
            botao.innerHTML = textoOriginal;
            botao.style.pointerEvents = 'auto';
            botao.style.opacity = '1';

            if (response.success) {
                /* Preencher o modal com os dados retornados */
                $('#nfecomModal .modal-body').html(`
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>
                                    <label><strong>Número NFeCOM:</strong></label>
                                    <div class="text-break">${response.modal.numero_nfcom}</div>
                                </td>
                                <td>
                                    <label><strong>Chave NFeCOM:</strong></label>
                                    <div class="text-break">${response.modal.chave_nfcom}</div>
                                </td>
                                <td>
                                    <label><strong>Status:</strong></label>
                                    <div class="nfe-status ${response.modal.status.includes('Rejeitada') ? 'error' : response.modal.status == 'Autorizado' ? 'success' : 'warning'}">
                                        ${response.modal.status}
                                    </div>
                                    ${response.modal.cstat ? `
                                    <div class="mt-2">
                                        <small><strong>Código SEFAZ:</strong> ${response.modal.cstat}</small>
                                    </div>
                                    ` : ''}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label><strong>Motivo:</strong></label>
                        <div class="text-break">${response.modal.motivo}</div>
                    </div>

                    ${response.modal.protocolo ? `
                    <div class="mt-3">
                        <label><strong>Protocolo:</strong></label>
                        <div class="well">
                            <pre>${response.modal.protocolo}</pre>
                        </div>
                    </div>
                    ` : ''}

                    ${response.modal.retorno ? `
                    <div class="mt-3">
                        <label><strong>Retorno SEFAZ:</strong></label>
                        <div class="well">
                            <pre>${response.modal.retorno}</pre>
                        </div>
                    </div>
                    ` : ''}
                `);

                /* Adicionar botões se autorizado */
                let footerHtml = '';
                if (response.modal.status == 'Autorizado') {
                    footerHtml += `
                        <a href="<?php echo base_url(); ?>index.php/nfecom/gerarXml/${response.modal.id}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Baixar XML
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/nfecom/baixarDanfe/${response.modal.id}" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-pdf"></i> Baixar DANFE
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/nfecom/danfe/${response.modal.id}" class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> Visualizar DANFE
                        </a>
                    `;
                }
                footerHtml += '<button type="button" class="btn btn-default" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>';

                $('#nfecomModal .modal-footer').html(footerHtml);

                /* Mostrar modal */
                $('#nfecomModal').modal('show');

            } else {
                /* Mostrar erro no modal */
                $('#nfecomModal .modal-body').html(`
                    <div class="alert alert-danger">
                        <h4><i class="fas fa-exclamation-triangle"></i> Erro na Consulta</h4>
                        <p>${response.message}</p>
                    </div>
                `);
                $('#nfecomModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>');
                $('#nfecomModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            /* Reverter alterações do botão */
            botao.innerHTML = textoOriginal;
            botao.style.pointerEvents = 'auto';
            botao.style.opacity = '1';

            /* Mostrar erro no modal */
            $('#nfecomModal .modal-body').html(`
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Erro de Comunicação</h4>
                    <p>Não foi possível consultar a NFCom. Tente novamente.</p>
                    <small>Detalhes: ${error}</small>
                </div>
            `);
            $('#nfecomModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>');
            $('#nfecomModal').modal('show');
        }
    });
}

function gerarNFCom(id) {
    /* Verificar se a função está sendo chamada */
    console.log('gerarNFCom called with id:', id);

    /* Encontrar o botão clicado e alterar seu texto */
    var botao = event.target.closest('a');
    var textoOriginal = botao.innerHTML;
    var isReemitir = textoOriginal.includes('Reemitir');

    /* Alterar texto do botão e desabilitar */
    botao.innerHTML = isReemitir ? '<i class="fas fa-spinner fa-spin"></i> Reemitindo...' : '<i class="fas fa-spinner fa-spin"></i> Emitindo...';
    botao.style.pointerEvents = 'none';
    botao.style.opacity = '0.6';

    /* Fazer chamada AJAX */
    $.ajax({
        url: '<?php echo base_url(); ?>index.php/nfecom/gerarXml',
        type: 'POST',
        data: {
            id: id,
            ajax: 'true',
            '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
        },
        dataType: 'json',
        success: function(response) {
            /* Reverter alterações do botão */
            botao.innerHTML = textoOriginal;
            botao.style.pointerEvents = 'auto';
            botao.style.opacity = '1';

            if (response.success) {
                /* Preencher o modal com os dados retornados */
                $('#nfecomModal .modal-body').html(`
                    <div class="table-responsive">
                        <table class="table">
                            <tr>
                                <td>
                                    <label><strong>Número NFeCOM:</strong></label>
                                    <div class="text-break">${response.modal.numero_nfcom}</div>
                                </td>
                                <td>
                                    <label><strong>Chave NFeCOM:</strong></label>
                                    <div class="text-break">${response.modal.chave_nfcom}</div>
                                </td>
                                <td>
                                    <label><strong>Status:</strong></label>
                                    <div class="nfe-status ${response.modal.status.includes('Rejeitada') ? 'error' : response.modal.status == 'Autorizado' ? 'success' : 'warning'}">
                                        ${response.modal.status}
                                    </div>
                                    ${response.modal.cstat ? `
                                    <div class="mt-2">
                                        <small><strong>Código SEFAZ:</strong> ${response.modal.cstat}</small>
                                    </div>
                                    ` : ''}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="mt-3">
                        <label><strong>Motivo:</strong></label>
                        <div class="text-break">${response.modal.motivo}</div>
                    </div>

                    ${response.modal.protocolo ? `
                    <div class="mt-3">
                        <label><strong>Protocolo:</strong></label>
                        <div class="well">
                            <pre>${response.modal.protocolo}</pre>
                        </div>
                    </div>
                    ` : ''}

                    ${response.modal.retorno ? `
                    <div class="mt-3">
                        <label><strong>Retorno SEFAZ:</strong></label>
                        <div class="well">
                            <pre>${response.modal.retorno}</pre>
                        </div>
                    </div>
                    ` : ''}
                `);

                /* Adicionar botões se autorizado */
                let footerHtml = '';
                if (response.modal.status == 'Autorizado') {
                    footerHtml += `
                        <a href="<?php echo base_url(); ?>index.php/nfecom/gerarXml/${response.modal.id}" class="btn btn-primary" target="_blank">
                            <i class="fas fa-download"></i> Baixar XML
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/nfecom/baixarDanfe/${response.modal.id}" class="btn btn-success" target="_blank">
                            <i class="fas fa-file-pdf"></i> Baixar DANFE
                        </a>
                        <a href="<?php echo base_url(); ?>index.php/nfecom/danfe/${response.modal.id}" class="btn btn-info" target="_blank">
                            <i class="fas fa-eye"></i> Visualizar DANFE
                        </a>
                    `;
                }
                footerHtml += '<button type="button" class="nfe-button" data-dismiss="modal"><i class="fas fa-times"></i> Fechar</button>';

                $('#nfecomModal .modal-footer').html(footerHtml);

                /* Mostrar modal */
                $('#nfecomModal').modal('show');

                /* Recarregar a página após fechar o modal para atualizar a listagem */
                $('#nfecomModal').on('hidden.bs.modal', function() {
                    location.reload();
                });

            } else {
                /* Mostrar erro no modal */
                $('#nfecomModal .modal-body').html(`
                    <div class="alert alert-danger">
                        <h4><i class="fas fa-exclamation-triangle"></i> Erro na Emissão</h4>
                        <p>${response.message}</p>
                    </div>
                `);
                $('#nfecomModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>');
                $('#nfecomModal').modal('show');
            }
        },
        error: function(xhr, status, error) {
            /* Reverter alterações do botão */
            botao.innerHTML = textoOriginal;
            botao.style.pointerEvents = 'auto';
            botao.style.opacity = '1';

            /* Mostrar erro no modal */
            $('#nfecomModal .modal-body').html(`
                <div class="alert alert-danger">
                    <h4><i class="fas fa-exclamation-triangle"></i> Erro de Comunicação</h4>
                    <p>Não foi possível processar a NFCom. Tente novamente.</p>
                    <small>Detalhes: ${error}</small>
                </div>
            `);
            $('#nfecomModal .modal-footer').html('<button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>');
            $('#nfecomModal').modal('show');
        }
    });
}
</script>

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

                    /* Preencher os campos básicos */
                    $('#nomeCliente').val(data.nomeCliente || '');
                    $('#cnpjCliente').val(data.cnpjCliente || '');

                    /* Concatenar endereço completo */
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

                    /* Preencher campos ocultos necessários para processamento */
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
            /* Limpar campos quando nenhum cliente selecionado */
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
                    /* Dados do serviço serão preenchidos automaticamente no controller */
                }
            });
        }
    });
});
</script>