<style>
    select {
        width: 70px;
    }
    
    /* Modal fixes for Bootstrap 2.3.2 */
    #nfecomModal {
        position: fixed !important;
        width: 90% !important;
        max-width: 900px !important;
        margin-left: 0 !important;
        left: 5% !important;
        top: 50px !important;
        z-index: 99999 !important;
        background-color: white !important;
        border: 2px solid #333 !important;
        border-radius: 6px !important;
        box-shadow: 0 5px 25px rgba(0,0,0,0.7) !important;
        display: none;
    }

    @media (min-width: 900px) {
        #nfecomModal {
            left: 50%;
            margin-left: -450px;
        }
    }

    #nfecomModal .modal-header {
        background: linear-gradient(135deg, #2c3e50 0%, #000000 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 6px 6px 0 0;
        border-bottom: 1px solid #ddd;
    }

    #nfecomModal .modal-header .modal-title {
        color: white;
        font-weight: 600;
        margin: 0;
        line-height: 30px;
    }

    #nfecomModal .modal-header .close {
        color: white;
        text-shadow: none;
        opacity: 0.8;
        padding: 5px;
    }


    /* Status Items Styling */
    .nfcom-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }

    .nfcom-info-item {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
    }

    .nfcom-info-label {
        font-weight: bold;
        color: #495057;
        font-size: 0.8rem;
        text-transform: uppercase;
        margin-bottom: 4px;
        display: block;
    }

    .nfcom-info-value {
        font-size: 0.95rem;
        word-break: break-all;
    }

    .nfcom-status-badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-weight: bold;
        font-size: 0.85rem;
    }

    .nfcom-status-success { background: #d4edda; color: #155724; }
    .nfcom-status-danger { background: #f8d7da; color: #721c24; }
    .nfcom-status-warning { background: #fff3cd; color: #856404; }

    /* Technical details */
    .nfcom-technical pre {
        background: #272822;
        color: #f8f8f2;
        padding: 15px;
        border-radius: 6px;
        font-size: 0.8rem;
        max-height: 250px;
        overflow-y: auto;
    }


    /* Estilos do modal melhorado de resposta SEFAZ */
    #nfecomModal .status-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 20px;
    }

    #nfecomModal .status-icon {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    #nfecomModal .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    #nfecomModal .info-row {
        display: contents;
    }

    #nfecomModal .info-item {
        background: #f8f9fa;
        padding: 12px;
        border-radius: 6px;
        border: 1px solid #dee2e6;
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
                        <th>Cliente</th>
                        <th>Estado</th>
                        <th>Município</th>
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
                            echo '<td>' . $r->NFC_X_NOME_DEST . '</td>';
                            echo '<td>' . $r->NFC_UF_DEST . '</td>';
                            echo '<td>' . $r->NFC_X_MUN_DEST . '</td>';
                            echo '<td>' . $dataEmissao . '</td>';
                            echo '<td>R$ ' . $valorTotal . '</td>';
                            $motivo = !empty($r->NFC_X_MOTIVO) ? htmlspecialchars($r->NFC_X_MOTIVO) : 'Sem retorno da SEFAZ';
                            echo '<td><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '; cursor: help;" data-toggle="tooltip" title="' . $motivo . '">' . $statusDesc . '</span></td>';
                            echo '<td style="text-align:center; white-space: nowrap;">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/visualizar/' . $r->NFC_ID . '" class="btn btn-mini btn-info" title="Ver dados da nota" style="margin-right: 2px">Ver dados</a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
                                if ($r->NFC_STATUS < 2) {
                                    // NFCom nova ou salva - permite gerar
                                    echo '<a href="#" onclick="gerarNFCom(' . $r->NFC_ID . ')" class="btn btn-mini btn-success" title="Gerar NFCom" style="margin-right: 2px">Gerar NFCom</a>';
                                    echo '<a href="' . base_url() . 'index.php/nfecom/gerarXmlPreEmissao/' . $r->NFC_ID . '" class="btn btn-mini btn-warning" target="_blank" title="Gerar XML (Pré-Emissão)" style="margin-right: 2px">Gerar XML</a>';
                                } elseif ($r->NFC_STATUS == 4) {
                                    // NFCom rejeitada - permite reemitir
                                    echo '<a href="#" onclick="gerarNFCom(' . $r->NFC_ID . ')" class="btn btn-mini btn-success" title="Reemitir Nota" style="margin-right: 2px">Reemitir Nota</a>';
                                    echo '<a href="' . base_url() . 'index.php/nfecom/gerarXmlPreEmissao/' . $r->NFC_ID . '" class="btn btn-mini btn-warning" target="_blank" title="Gerar XML (Pré-Emissão)" style="margin-right: 2px">Gerar XML</a>';
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
<div id="nfecomModal" class="modal" role="dialog" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title" id="nfecomModalLabel">
            <i class="fas fa-file-invoice"></i> <span id="modalTitle">Resposta da SEFAZ</span>
        </h3>
    </div>
    <div class="modal-body">
        <div id="modalContent">
            <?php if ($this->session->flashdata('nfecom_modal')):
                $m = $this->session->flashdata('nfecom_modal');
                $isSuccess = strpos(strtolower($m['status']), 'autorizado') !== false || ($m['cstat'] ?? '') == '100';
                $statusClass = $isSuccess ? 'success' : (strpos(strtolower($m['status']), 'rejeitada') !== false ? 'danger' : 'warning');
            ?>
            <div class="text-center mb-4">
                <i class="fas <?php echo $isSuccess ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger'; ?> fa-4x mb-2"></i>
                <h3 class="text-<?php echo $statusClass; ?>"><?php echo $m['status']; ?></h3>
            </div>

            <div class="nfcom-info-grid">
                <div class="nfcom-info-item">
                    <span class="nfcom-info-label">Número NFCom</span>
                    <span class="nfcom-info-value"><?php echo $m['numero_nfcom']; ?></span>
                </div>
                <div class="nfcom-info-item">
                    <span class="nfcom-info-label">Código SEFAZ</span>
                    <span class="nfcom-info-value"><?php echo $m['cstat'] ?? '-'; ?></span>
                </div>
                <div class="nfcom-info-item" style="grid-row: span 1; grid-column: span 2;">
                    <span class="nfcom-info-label">Chave de Acesso</span>
                    <span class="nfcom-info-value" style="font-size: 0.85rem;"><?php echo $m['chave_nfcom']; ?></span>
                </div>
            </div>

            <div class="alert alert-<?php echo $statusClass; ?>">
                <strong>Mensagem:</strong> <?php echo $m['motivo']; ?>
            </div>

            <?php if (!empty($m['protocolo'])): ?>
            <div class="nfcom-info-item mb-3">
                <span class="nfcom-info-label">Protocolo</span>
                <span class="nfcom-info-value"><?php echo $m['protocolo']; ?></span>
            </div>
            <?php endif; ?>

            <?php if (!empty($m['retorno'])): ?>
            <div class="nfcom-technical">
                <span class="nfcom-info-label">Retorno Detalhado</span>
                <pre><?php echo htmlspecialchars($m['retorno']); ?></pre>
            </div>
            <?php endif; ?>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
                <p>Aguarde o processamento...</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="modal-footer" id="modalFooter">
        <?php if ($this->session->flashdata('nfecom_modal')):
            $m = $this->session->flashdata('nfecom_modal');
            $id = $m['id'] ?? null;
            $isAutorizado = strpos(strtolower($m['status']), 'autorizado') !== false;
        ?>
            <?php if ($isAutorizado && $id): ?>
            <a href="<?php echo base_url(); ?>index.php/nfecom/gerarXml/<?php echo $id; ?>" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i> XML</a>
            <a href="<?php echo base_url(); ?>index.php/nfecom/danfe/<?php echo $id; ?>" class="btn btn-info" target="_blank"><i class="fas fa-eye"></i> DANFE</a>
            <?php endif; ?>
        <?php endif; ?>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
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

function renderModalNfecom(data) {
    var isSuccess = data.status.toLowerCase().includes('autorizado') || data.cstat == '100';
    var statusClass = isSuccess ? 'success' : (data.status.toLowerCase().includes('rejeitada') ? 'danger' : 'warning');
    var statusIcon = isSuccess ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger';

    var html = `
        <div class="text-center mb-4">
            <i class="fas ${statusIcon} fa-4x mb-2"></i>
            <h3 class="text-${statusClass}">${data.status}</h3>
        </div>

        <div class="nfcom-info-grid">
            <div class="nfcom-info-item">
                <span class="nfcom-info-label">Número NFCom</span>
                <span class="nfcom-info-value">${data.numero_nfcom}</span>
            </div>
            <div class="nfcom-info-item">
                <span class="nfcom-info-label">Código SEFAZ</span>
                <span class="nfcom-info-value">${data.cstat || '-'}</span>
            </div>
            <div class="nfcom-info-item" style="grid-column: span 2;">
                <span class="nfcom-info-label">Chave de Acesso</span>
                <span class="nfcom-info-value">${data.chave_nfcom}</span>
            </div>
        </div>

        <div class="alert alert-${statusClass}">
            <strong>Mensagem:</strong> ${data.motivo}
        </div>

        ${data.protocolo ? `
        <div class="nfcom-info-item mb-3">
            <span class="nfcom-info-label">Protocolo</span>
            <span class="nfcom-info-value">${data.protocolo}</span>
        </div>
        ` : ''}

        ${data.retorno ? `
        <div class="nfcom-technical">
            <span class="nfcom-info-label">Retorno Detalhado</span>
            <pre>${data.retorno}</pre>
        </div>
        ` : ''}
    `;

    var footer = '';
    if (isSuccess && data.id) {
        footer += `
            <a href="<?php echo base_url(); ?>index.php/nfecom/gerarXml/${data.id}" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i> XML</a>
            <a href="<?php echo base_url(); ?>index.php/nfecom/danfe/${data.id}" class="btn btn-info" target="_blank"><i class="fas fa-eye"></i> DANFE</a>
        `;
    }
    footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>';

    $('#modalTitle').text('Resultado SEFAZ');
    $('#modalContent').html(html);
    $('#modalFooter').html(footer);
    
    // Garantir visibilidade antes de mostrar
    $('#nfecomModal').css({
        'visibility': 'visible',
        'opacity': '1',
        'display': 'block',
        'pointer-events': 'auto'
    }).modal('show');
}

function consultarNFCom(id) {
    var btn = event.target.closest('a');
    var original = btn.innerHTML;
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.style.pointerEvents = 'none';

    // Mostrar loader no modal também
    $('#modalTitle').text('Consultando SEFAZ...');
    $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i><p>Consultando status da NFCom na SEFAZ...</p></div>');
    $('#modalFooter').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>');
    
    // Garantir visibilidade
    $('#nfecomModal').css({
        'visibility': 'visible',
        'opacity': '1',
        'display': 'block',
        'pointer-events': 'auto'
    }).modal('show');

    $.ajax({
        url: '<?php echo base_url(); ?>index.php/nfecom/consultar',
        type: 'POST',
        data: { id: id, ajax: 'true', '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
        dataType: 'json',
        success: function(res) {
            btn.innerHTML = original;
            btn.style.pointerEvents = 'auto';
            if (res.success) {
                renderModalNfecom(res.modal);
            } else {
                $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> ' + res.message + '</div>');
            }
        },
        error: function() {
            btn.innerHTML = original;
            btn.style.pointerEvents = 'auto';
            $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> Não foi possível comunicar com o servidor.</div>');
        }
    });
}

function gerarNFCom(id) {
    var btn = event.target.closest('a');
    var original = btn.innerHTML;
    var isReemitir = original.includes('Reemitir');
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    btn.style.pointerEvents = 'none';

    // Mostrar loader no modal
    $('#modalTitle').text(isReemitir ? 'Reemitindo NFCom...' : 'Transmitindo NFCom...');
    $('#modalContent').html('<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i><p>Aguarde a resposta da SEFAZ...</p></div>');
    $('#modalFooter').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>');
    
    // Garantir visibilidade
    $('#nfecomModal').css({
        'visibility': 'visible',
        'opacity': '1',
        'display': 'block',
        'pointer-events': 'auto'
    }).modal('show');

    $.ajax({
        url: '<?php echo base_url(); ?>index.php/nfecom/gerarXml',
        type: 'POST',
        data: { id: id, ajax: 'true', '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
        dataType: 'json',
        success: function(res) {
            btn.innerHTML = original;
            btn.style.pointerEvents = 'auto';
            if (res.success) {
                renderModalNfecom(res.modal);
                // Atualizar listagem ao fechar
                $('#nfecomModal').off('hidden.bs.modal').on('hidden.bs.modal', function() { location.reload(); });
            } else {
                $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> ' + res.message + '</div>');
            }
        },
        error: function() {
            btn.innerHTML = original;
            btn.style.pointerEvents = 'auto';
            $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> Não foi possível comunicar com o servidor.</div>');
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

// Limpeza completa dos modais NFCOM para evitar bloqueios na tela
$(document).ready(function() {
    console.log('NFCOM: Inicializando limpeza de modais');

    // Função para limpar completamente os modais
    function limparModaisNfcom() {
        console.log('NFCOM: Executando limpeza completa de modais');

        // Remover todos os backdrops residuais
        $('.modal-backdrop').remove();
        $('.modal-backdrop.show').remove();
        $('.modal-backdrop.in').remove();

        // Resetar classes do body
        $('body').removeClass('modal-open');
        $('body').css({
            'overflow': 'auto',
            'padding-right': '0',
            'pointer-events': 'auto'
        });

        // Garantir que os modais estão completamente fechados
        $('#nfecomModal').removeClass('in show').hide();
        $('#modal-nfecom').removeClass('in show').hide();

        // Resetar estilos dos modais de forma segura
        $('#nfecomModal, #modal-nfecom').css({
            'display': 'none',
            'pointer-events': 'none'
        });
        
        // Apenas remover classes de visibilidade ativa
        $('#nfecomModal, #modal-nfecom').removeClass('in show');

        // Liberar todos os elementos clicáveis
        $('input, select, textarea, button, a, .btn').each(function() {
            var $element = $(this);
            $element.prop('disabled', false).prop('readonly', false);
            $element.removeAttr('disabled').removeAttr('readonly');
            $element.css({
                'pointer-events': 'auto',
                'cursor': 'auto',
                'z-index': 'auto'
            });
        });

        // Resetar containers principais
        $('.widget-box, .widget-content, form, .row-fluid, .span6, .span12, .table, tbody, tr, td').css({
            'pointer-events': 'auto',
            'position': 'static',
            'z-index': 'auto'
        });

        // Verificar e corrigir qualquer elemento com pointer-events bloqueante
        $('*').each(function() {
            var $el = $(this);
            var pointerEvents = $el.css('pointer-events');
            if (pointerEvents === 'none') {
                $el.css('pointer-events', 'auto');
            }
        });

        console.log('NFCOM: Limpeza completa dos modais finalizada');
    }

    // Executar limpeza na carga da página
    setTimeout(limparModaisNfcom, 100);

    // Limpeza quando o modal principal é fechado
    $('#nfecomModal').on('hidden.bs.modal', function() {
        console.log('NFCOM: Modal principal fechado, executando limpeza');
        setTimeout(limparModaisNfcom, 200);
    });

    // Limpeza quando o modal de cadastro é fechado
    $('#modal-nfecom').on('hidden.bs.modal', function() {
        console.log('NFCOM: Modal de cadastro fechado, executando limpeza');
        setTimeout(limparModaisNfcom, 200);
    });

    // Limpeza periódica removida pois estava interferindo na exibição dos modais

    // Função global para emergência - pode ser chamada via console
    window.forcarLimpezaModaisNfcom = function() {
        console.log('NFCOM: Limpeza forçada executada manualmente');
        limparModaisNfcom();
        alert('Limpeza de modais NFCOM executada. A tela deve estar liberada agora.');
    };

    // Inicializar tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
</script>