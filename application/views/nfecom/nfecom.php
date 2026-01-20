<style>
    select {
        width: 70px;
    }
    
    /* Modal fixes for Bootstrap 2.3.2 */
    #nfecomModal {
        position: fixed !important;
        width: 90% !important;
        max-width: 900px !important;
        left: 75% !important;
        top: 50% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 99999 !important;
        background-color: white !important;
        border: 2px solid #333 !important;
        border-radius: 6px !important;
        box-shadow: 0 5px 25px rgba(0,0,0,0.7) !important;
        display: none;
    }

    @media (max-width: 1200px) {
        #nfecomModal {
            left: 60% !important;
        }
    }

    @media (max-width: 899px) {
        #nfecomModal {
            width: 95% !important;
            max-width: none !important;
            left: 50% !important;
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
        white-space: pre-wrap;
        word-wrap: break-word;
        background: #f5f5f5;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 4px;
        font-size: 0.8em;
    }

    @media (max-width: 768px) {
        /* Force table to not be like tables anymore */
        #tabela table, 
        #tabela thead, 
        #tabela tbody, 
        #tabela th, 
        #tabela td, 
        #tabela tr { 
            display: block; 
        }
        
        /* Hide table headers (but not display: none;, for accessibility) */
        /* Hide table headers completely */
        #tabela thead { 
            display: none;
        }
        
        #tabela tr { 
            border: 1px solid #ccc; 
            margin-bottom: 10px; 
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        #tabela td { 
            /* Behave like a "row" */
            border: none;
            border-bottom: 1px solid #eee; 
            position: relative;
            padding-left: 10px !important; 
            padding-right: 10px !important; 
            padding-top: 8px !important; /* Reduced padding */
            padding-bottom: 8px !important; /* Reduced padding */
            white-space: normal;
            text-align: left;
            min-height: 20px; /* Reduced min-height */
            display: flex;       /* Use flexbox for inline layout */
            flex-direction: row; /* Horizontal alignment */
            align-items: center; /* Vertically center */
            justify-content: flex-start; /* Start from left */
        }
        
        /* Hide the first 'line' (Nº NF column) of the card as requested */
        #tabela td:nth-of-type(1) {
            display: none;
        }
        
        #tabela td:before { 
            /* Label styling */
            position: static; /* No longer absolute */
            width: auto; 
            padding-right: 5px; 
            white-space: nowrap;
            text-align: left;
            font-weight: bold;
            color: #444; 
            content: attr(data-label) ": "; /* Add colon */
            font-size: 0.9em;
            text-transform: none; /* Normalize case if desired, or keep uppercase */
            letter-spacing: normal;
            opacity: 1;
            flex-shrink: 0; /* Prevent label from shrinking */
        }

        #tabela td:last-child {
            border-bottom: 0;
            text-align: center;
            padding-left: 0 !important; 
            padding-right: 0 !important;
            padding-top: 10px !important;
            padding-bottom: 10px !important;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 5px; /* Reduced gap between icons */
            background: #f9f9f9; 
        }
        
        #tabela td:last-child:before {
            display: none;
        }
        
        /* Adjust layout */
        .widget-content {
            border: none !important;
        }
        
        /* Adjust actions button container */
        #tabela td:last-child a {
            display: inline-flex !important;
            align-items: center;
            justify-content: center;
            width: 45px !important; /* Slightly smaller width */
            height: 40px !important; /* Slightly smaller height */
            margin: 0 !important;
            font-size: 1.3em; /* Adjusted icon size */
            border-radius: 6px; 
        }
        
        #tabela td:last-child a i {
            display: block; 
            font-size: 20px; 
        }
        
        /* Adjust search form fields */
        div.span12 > div.span3:first-child { /* "Nova NFECom" button container */
            width: 100% !important;
            margin-bottom: 15px !important;
            text-align: center;
        }

        form.span9 {
            width: 100% !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            margin-left: 0 !important;
        }

        .search-input-container, 
        .status-select-container, 
        .date-inputs-container, 
        .search-button-container {
            width: 100% !important;
            margin-left: 0 !important;
            margin-bottom: 10px !important;
            display: block !important;
        }

        /* Special handling for date inputs to be side-by-side */
        .date-inputs-container {
             display: flex !important;
             gap: 10px;
        }
        
        .date-inputs-container input {
            width: 50% !important;
            margin: 0 !important;
            min-height: 30px; /* Better touch target */
        }

        /* Search Button */
        .search-button-container button {
            width: 100% !important;
            height: 40px !important; /* Bigger target */
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        form.form-horizontal .control-group {
            margin-bottom: 10px;
        }
    }

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
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aNfecom')) { ?>
            <div class="span3">
                <a href="<?php echo base_url(); ?>index.php/nfecom/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
                    <span class="button__text2">Nova NFECom</span>
                </a>
            </div>
        <?php } ?>
        <form class="span9" method="get" action="<?php echo base_url(); ?>index.php/nfecom/gerenciar" style="display: flex; justify-content: flex-end;">
            <div class="span3 search-input-container">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Nº NF, Cliente, Chave ou Status..." class="span12" value="<?php echo $this->input->get('pesquisa'); ?>">
            </div>
            <div class="span2 status-select-container">
                <select name="status" class="span12">
                    <option value="">Todos os status</option>
                    <option value="0" <?php echo $this->input->get('status') == '0' ? 'selected' : ''; ?>>Rascunho</option>
                    <option value="1" <?php echo $this->input->get('status') == '1' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="2" <?php echo $this->input->get('status') == '2' ? 'selected' : ''; ?>>Enviado</option>
                    <option value="3" <?php echo $this->input->get('status') == '3' ? 'selected' : ''; ?>>Autorizado</option>
                    <option value="4" <?php echo $this->input->get('status') == '4' ? 'selected' : ''; ?>>Rejeitada</option>
                    <option value="5" <?php echo $this->input->get('status') == '5' ? 'selected' : ''; ?>>Autorizada</option>
                    <option value="7" <?php echo $this->input->get('status') == '7' ? 'selected' : ''; ?>>Cancelada</option>
                </select>
            </div>
            <div class="span3 date-inputs-container">
                <input type="date" name="data" id="data" placeholder="De" class="span6 datepicker" autocomplete="off" value="<?php echo $this->input->get('data'); ?>">
                <input type="date" name="data2" id="data2" placeholder="Até" class="span6 datepicker" autocomplete="off" value="<?php echo $this->input->get('data2'); ?>">
            </div>
            <div class="span1 search-button-container">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span>
                </button>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Nº NF</th>
                        <th>Cliente</th>
                        <th class="col-estado">Estado</th>
                        <th class="col-municipio">Município</th>
                        <th class="col-data-emissao">Data Emissão</th>
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
                            echo '<td data-label="Nº NF">' . $r->NFC_NNF . '</td>';
                            echo '<td data-label="Cliente">' . $r->NFC_X_NOME_DEST . '</td>';
                            echo '<td data-label="Estado" class="col-estado">' . $r->NFC_UF_DEST . '</td>';
                            echo '<td data-label="Município" class="col-municipio">' . $r->NFC_X_MUN_DEST . '</td>';
                            echo '<td data-label="Data Emissão" class="col-data-emissao">' . $dataEmissao . '</td>';
                            echo '<td data-label="Valor">R$ ' . $valorTotal . '</td>';
                            $motivo = !empty($r->NFC_X_MOTIVO) ? htmlspecialchars($r->NFC_X_MOTIVO) : 'Sem retorno da SEFAZ';
                            echo '<td data-label="Status"><span class="badge" style="background-color: ' . $corStatus . '; border-color: ' . $corStatus . '; cursor: help;" data-toggle="tooltip" title="' . $motivo . '">' . $statusDesc . '</span></td>';
                            echo '<td data-label="Ações" style="text-align:center; white-space: nowrap;">';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/visualizar/' . $r->NFC_ID . '" class="btn-nwe" title="Ver dados da nota" style="margin-right: 1%"><i class="bx bx-show"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) {
                                if ($r->NFC_STATUS < 2) {
                                    // NFCom nova ou salva - permite gerar
                                    echo '<a href="#" onclick="gerarNFCom(' . $r->NFC_ID . ')" class="btn-nwe3" title="Gerar NFCom" style="margin-right: 1%"><i class="bx bx-paper-plane"></i></a>';
                                    echo '<a href="' . base_url() . 'index.php/nfecom/gerarXmlPreEmissao/' . $r->NFC_ID . '" class="btn-nwe" target="_blank" title="Gerar XML (Pré-Emissão)" style="margin-right: 1%"><i class="bx bx-code-alt"></i></a>';
                                } elseif ($r->NFC_STATUS == 4) {
                                    // NFCom rejeitada - permite reemitir
                                    echo '<a href="#" onclick="gerarNFCom(' . $r->NFC_ID . ')" class="btn-nwe3" title="Reemitir Nota" style="margin-right: 1%"><i class="bx bx-revision"></i></a>';
                                    echo '<a href="' . base_url() . 'index.php/nfecom/gerarXmlPreEmissao/' . $r->NFC_ID . '" class="btn-nwe" target="_blank" title="Gerar XML (Pré-Emissão)" style="margin-right: 1%"><i class="bx bx-code-alt"></i></a>';
                                }
                                // NFCom autorizada (status 3 ou 5) não mostra botão
                                // NFCom cancelada (status 7) não mostra botão
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/danfe/' . $r->NFC_ID . '" class="btn-nwe" target="_blank" title="Imprimir NFCom" style="margin-right: 1%"><i class="bx bx-printer"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom') && ($r->NFC_STATUS == 3 || $r->NFC_STATUS == 5)) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/gerarXml/' . $r->NFC_ID . '" class="btn-nwe" title="Baixar XML Autorizado" style="margin-right: 1%"><i class="bx bx-download"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($r->NFC_STATUS == 3 || $r->NFC_STATUS == 5)) {
                                echo '<a href="#" onclick="abrirModalCancelamento(' . $r->NFC_ID . ')" class="btn-nwe4" title="Cancelar NFCom" style="margin-right: 1%"><i class="bx bx-x-circle"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $r->NFC_STATUS >= 2) {
                                echo '<a href="#" onclick="consultarNFCom(' . $r->NFC_ID . ')" class="btn-nwe2" title="Consultar Status na SEFAZ" style="margin-right: 1%"><i class="bx bx-search"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $r->NFC_STATUS != 3 && $r->NFC_STATUS != 7) {
                                echo '<a href="' . base_url() . 'index.php/nfecom/excluir/' . $r->NFC_ID . '" class="btn-nwe4" title="Excluir NFCom" style="margin-right: 1%" onclick="return confirm(\'Tem certeza que deseja excluir esta NFCom?\')"><i class="bx bx-trash-alt"></i></a>';
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

<!-- Modal de Cancelamento -->
<div id="modalCancelamento" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modalCancelamentoLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalCancelamentoLabel">
            <i class="fas fa-ban"></i> Cancelar NFCom
        </h3>
    </div>
    <div class="modal-body">
        <form id="formCancelamento">
            <input type="hidden" id="nfecom_id_cancelamento" name="nfecom_id" value="">
            <div class="control-group">
                <label class="control-label" for="justificativa_cancelamento">
                    Justificativa do Cancelamento <span class="required">*</span>
                </label>
                <div class="controls">
                    <textarea id="justificativa_cancelamento" name="justificativa" class="span12" rows="5" 
                              placeholder="Informe a justificativa para o cancelamento (mínimo 15 caracteres)" 
                              required></textarea>
                    <span class="help-inline" style="display: none; color: red;" id="erro_justificativa"></span>
                    <small class="help-block">A justificativa deve ter no mínimo 15 caracteres.</small>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="btnConfirmarCancelamento">
            <i class="fas fa-ban"></i> Confirmar Cancelamento
        </button>
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
    var isCancelado = data.status.toLowerCase().includes('cancelada') || data.cstat == '101';
    var statusClass = isSuccess ? 'success' : (isCancelado ? 'danger' : (data.status.toLowerCase().includes('rejeitada') ? 'danger' : 'warning'));
    var statusIcon = isSuccess ? 'fa-check-circle text-success' : (isCancelado ? 'fa-ban text-danger' : 'fa-exclamation-circle text-danger');

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
    if (data.id) {
        // Sempre mostrar botão XML, mesmo quando cancelado
        footer += `
            <a href="<?php echo base_url(); ?>index.php/nfecom/gerarXml/${data.id}" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i> XML</a>
        `;
        // Mostrar DANFE apenas se autorizado
        if (isSuccess) {
            footer += `
                <a href="<?php echo base_url(); ?>index.php/nfecom/danfe/${data.id}" class="btn btn-info" target="_blank"><i class="fas fa-eye"></i> DANFE</a>
            `;
        }
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
            'padding-right': '0'
        });

        // Garantir que os modais estão completamente fechados
        $('#nfecomModal').removeClass('in show').hide();
        $('#modal-nfecom').removeClass('in show').hide();

        // Resetar estilos dos modais de forma segura
        $('#nfecomModal, #modal-nfecom').css({
            'display': 'none'
        });
        
        // Apenas remover classes de visibilidade ativa
        $('#nfecomModal, #modal-nfecom').removeClass('in show');
        
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

// Função para abrir modal de cancelamento
function abrirModalCancelamento(nfecomId) {
    $('#nfecom_id_cancelamento').val(nfecomId);
    $('#justificativa_cancelamento').val('');
    $('#erro_justificativa').hide();
    $('#modalCancelamento').modal('show');
}

// Evento de confirmação de cancelamento
$(document).ready(function() {
    $('#btnConfirmarCancelamento').on('click', function() {
        var nfecomId = $('#nfecom_id_cancelamento').val();
        var justificativa = $('#justificativa_cancelamento').val().trim();
        
        // Validação
        if (!justificativa) {
            $('#erro_justificativa').text('A justificativa é obrigatória.').show();
            return;
        }
        
        if (justificativa.length < 15) {
            $('#erro_justificativa').text('A justificativa deve ter no mínimo 15 caracteres.').show();
            return;
        }
        
        $('#erro_justificativa').hide();
        
        // Desabilitar botão durante processamento
        var $btn = $(this);
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');
        
        // Enviar requisição AJAX
        $.ajax({
            url: '<?php echo base_url(); ?>index.php/nfecom/cancelar',
            type: 'POST',
            data: {
                nfecom_id: nfecomId,
                justificativa: justificativa,
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Fechar modal de cancelamento
                    $('#modalCancelamento').modal('hide');
                    
                    // Mostrar mensagem de sucesso
                    alert('NFCom cancelada com sucesso!');
                    
                    // Recarregar página
                    location.reload();
                } else {
                    // Mostrar erro
                    $('#erro_justificativa').text(response.message || 'Erro ao cancelar NFCom.').show();
                    $btn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Erro ao processar cancelamento.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                $('#erro_justificativa').text(errorMsg).show();
                $btn.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>