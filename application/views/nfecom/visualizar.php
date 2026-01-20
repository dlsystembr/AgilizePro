<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: 10px 0 0">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Dados da NFCom</h5>
                <div class="buttons">
                    <?php
                    // EDITAR: Status < 2 (Rascunho/Salvo) ou 4 (Rejeitada)
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($result->NFC_STATUS < 2 || $result->NFC_STATUS == 4)) { ?>
                        <a title="Editar NFCom" class="button btn btn-mini btn-success"
                            href="<?php echo base_url(); ?>index.php/nfecom/editar/<?php echo $result->NFC_ID; ?>">
                            <span class="button__icon"><i class="bx bx-edit"></i></span>
                            <span class="button__text">Editar</span>
                        </a>
                    <?php } ?>

                    <?php
                    // TRANSMITIR / REEMITIR: Status < 2 (Rascunho/Salvo) ou 4 (Rejeitada)
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($result->NFC_STATUS < 2 || $result->NFC_STATUS == 4)) {
                        $label = ($result->NFC_STATUS == 4) ? 'Reemitir' : 'Transmitir';
                        ?>
                        <a href="#" class="button btn btn-mini btn-success btn-gerar-nfcom"
                            data-id="<?php echo $result->NFC_ID; ?>" 
                            onclick="gerarNFCom(<?php echo $result->NFC_ID; ?>); return false;"
                            title="<?php echo $label; ?> NFCom">
                            <span class="button__icon"><i class="bx bx-paper-plane"></i></span>
                            <span class="button__text"><?php echo $label; ?></span>
                        </a>
                
                        <!-- XML Pré-emissão -->
                        <a target="_blank" title="Gerar XML (Pré-Emissão)" class="button btn btn-mini btn-warning"
                            href="<?php echo base_url(); ?>index.php/nfecom/gerarXmlPreEmissao/<?php echo $result->NFC_ID; ?>">
                            <span class="button__icon"><i class="bx bx-code-alt"></i></span>
                            <span class="button__text">XML Prévia</span>
                        </a>
                    <?php } ?>

                    <?php
                    // IMPRIMIR / DANFE: Permissão de visualizar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) { ?>
                        <a target="_blank" title="Imprimir NFCom" class="button btn btn-mini btn-inverse"
                            href="<?php echo base_url(); ?>index.php/nfecom/danfe/<?php echo $result->NFC_ID; ?>">
                            <span class="button__icon"><i class="bx bx-printer"></i></span>
                            <span class="button__text">Imprimir</span>
                        </a>
                    <?php } ?>

                    <?php
                    // XML AUTORIZADO: Status 3 (Autorizada) ou 5 (Autorizada)
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom') && ($result->NFC_STATUS == 3 || $result->NFC_STATUS == 5)) { ?>
                        <a target="_blank" title="Baixar XML Autorizado" class="button btn btn-mini btn-warning"
                            href="<?php echo base_url(); ?>index.php/nfecom/gerarXml/<?php echo $result->NFC_ID; ?>">
                            <span class="button__icon"><i class="bx bx-download"></i></span>
                            <span class="button__text">XML</span>
                        </a>
                    <?php } ?>

                    <?php
                    // CONSULTAR STATUS: Status >= 2 (Enviado+) AND Permissão Editar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $result->NFC_STATUS >= 2) { ?>
                        <a href="#" class="button btn btn-mini btn-info btn-consultar-nfcom"
                            data-id="<?php echo $result->NFC_ID; ?>" 
                            onclick="consultarNFCom(<?php echo $result->NFC_ID; ?>); return false;"
                            title="Consultar Status na SEFAZ">
                            <span class="button__icon"><i class="bx bx-search"></i></span>
                            <span class="button__text">Consultar</span>
                        </a>
                    <?php } ?>

                    <?php
                    // CANCELAR: Status 3 ou 5 AND Permissão Editar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($result->NFC_STATUS == 3 || $result->NFC_STATUS == 5)) { ?>
                        <a href="#" class="button btn btn-mini btn-danger btn-cancelar-nfcom"
                            data-id="<?php echo $result->NFC_ID; ?>"
                            onclick="abrirModalCancelamento(<?php echo $result->NFC_ID; ?>); return false;"
                            title="Cancelar NFCom">
                            <span class="button__icon"><i class="bx bx-x-circle"></i></span>
                            <span class="button__text">Cancelar</span>
                        </a>
                    <?php } ?>

                    <?php
                    // EXCLUIR: Status != 3 (Autorizada) AND Status != 7 (Cancelada) AND Permissão Editar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $result->NFC_STATUS != 3 && $result->NFC_STATUS != 7) { ?>
                        <a href="<?php echo base_url(); ?>index.php/nfecom/excluir/<?php echo $result->NFC_ID; ?>"
                            class="button btn btn-mini btn-danger" title="Excluir NFCom"
                            onclick="return confirm('Tem certeza que deseja excluir esta NFCom?')">
                            <span class="button__icon"><i class="bx bx-trash-alt"></i></span>
                            <span class="button__text">Excluir</span>
                        </a>
                    <?php } ?>

                    <a href="<?php echo base_url(); ?>index.php/nfecom" class="button btn btn-mini btn-inverse">
                        <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                        <span class="button__text">Voltar</span>
                    </a>
                </div>
            </div>
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <div class="invoice-head" style="margin-bottom: 0">
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; padding-left: 0">
                                        <h3 style="margin-top: 0;">NFCom #<?php echo $result->NFC_NNF; ?></h3>
                                        <span><b>Chave:</b> <?php echo $result->NFC_CH_NFCOM; ?></span><br />
                                        <span><b>Série:</b> <?php echo $result->NFC_SERIE; ?></span><br />
                                        <span><b>Emissão:</b>
                                            <?php echo date('d/m/Y H:i', strtotime($result->NFC_DHEMI)); ?></span>
                                    </td>
                                    <td style="width: 50%; padding-left: 0; text-align: right;">
                                        <h3>
                                            <?php
                                            switch ((int) $result->NFC_STATUS) {
                                                case 0:
                                                    echo '<span class="badge" style="background-color: #CDB380">Rascunho</span>';
                                                    break;
                                                case 1:
                                                    echo '<span class="badge badge-info">Salvo</span>';
                                                    break;
                                                case 2:
                                                    echo '<span class="badge badge-success">Enviado</span>';
                                                    break;
                                                case 3:
                                                    echo '<span class="badge badge-success">Autorizado</span>';
                                                    break;
                                                case 4:
                                                    echo '<span class="badge badge-important">Rejeitada</span>';
                                                    break;
                                                case 5:
                                                    echo '<span class="badge badge-success">Autorizada</span>';
                                                    break;
                                                case 7:
                                                    echo '<span class="badge">Cancelada</span>';
                                                    break;
                                                default:
                                                    echo '<span class="badge">Desconhecido</span>';
                                                    break;
                                            }
                                            ?>
                                        </h3>
                                        <?php if ($result->NFC_X_MOTIVO): ?>
                                            <span class="text-error"><b>Status SEFAZ:</b>
                                                <?php echo $result->NFC_C_STAT . ' - ' . $result->NFC_X_MOTIVO; ?></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; padding-left: 0">
                                        <h5><b>EMITENTE</b></h5>
                                        <span><i class="fas fa-building"></i>
                                            <b><?php echo $result->NFC_X_NOME_EMIT; ?></b></span><br />
                                        <span>CNPJ: <?php echo $result->NFC_CNPJ_EMIT; ?></span><br />
                                        <span>IE: <?php echo $result->NFC_IE_EMIT; ?></span>
                                    </td>
                                    <td style="width: 50%; padding-left: 0">
                                        <h5><b>DESTINATÁRIO</b></h5>
                                        <span><i class="fas fa-user"></i>
                                            <b><?php echo $result->NFC_X_NOME_DEST; ?></b></span><br />
                                        <span>CNPJ/CPF: <?php echo $result->NFC_CNPJ_DEST; ?></span><br />
                                        <span><?php echo $result->NFC_X_LGR_DEST . ', ' . $result->NFC_NRO_DEST . ' - ' . $result->NFC_X_BAIRRO_DEST; ?></span><br />
                                        <span><?php echo $result->NFC_X_MUN_DEST . '/' . $result->NFC_UF_DEST . ' - CEP: ' . $result->NFC_CEP_DEST; ?></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 20px;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">Itens/Serviços</a></li>
                            <li><a href="#tab2" data-toggle="tab">Informações Gerais</a></li>
                            <li><a href="#tab3" data-toggle="tab">Totais e Impostos</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <table class="table table-bordered table-condensed" id="tblProdutos">
                                    <thead>
                                        <tr>
                                            <th>Cód.</th>
                                            <th>Descrição</th>
                                            <th>Unid.</th>
                                            <th>Qtd.</th>
                                            <th>Vlr. Unit.</th>
                                            <th>Vlr. Desc.</th>
                                            <th>Vlr. Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($itens as $item): ?>
                                            <tr>
                                                <td><?php echo $item->NFI_C_PROD; ?></td>
                                                <td><?php echo $item->NFI_X_PROD; ?></td>
                                                <td><?php echo $item->NFI_U_MED; ?></td>
                                                <td><?php echo number_format($item->NFI_Q_FATURADA, 4, ',', '.'); ?></td>
                                                <td>R$ <?php echo number_format($item->NFI_V_ITEM, 2, ',', '.'); ?></td>
                                                <td>R$ <?php echo number_format($item->NFI_V_DESC, 2, ',', '.'); ?></td>
                                                <td>R$ <?php echo number_format($item->NFI_V_PROD, 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="tab2">
                                <table class="table table-bordered table-condensed">
                                    <tbody>
                                        <tr>
                                            <td><b>Contrato:</b> <?php echo $result->NFC_N_CONTRATO; ?></td>
                                            <td><b>Início Vigência:</b>
                                                <?php echo date('d/m/Y', strtotime($result->NFC_D_CONTRATO_INI)); ?>
                                            </td>
                                            <td><b>Fim Vigência:</b>
                                                <?php echo $result->NFC_D_CONTRATO_FIM ? date('d/m/Y', strtotime($result->NFC_D_CONTRATO_FIM)) : '-'; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Cód. Assinante:</b> <?php echo $result->NFC_I_COD_ASSINANTE; ?></td>
                                            <td><b>Tipo Assinante:</b> <?php echo $result->NFC_TP_ASSINANTE; ?></td>
                                            <td><b>Tipo Serviço:</b> <?php echo $result->NFC_TP_SERV_UTIL; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Competência:</b> <?php echo $result->NFC_COMPET_FAT; ?></td>
                                            <td><b>Vencimento:</b>
                                                <?php echo date('d/m/Y', strtotime($result->NFC_D_VENC_FAT)); ?></td>
                                            <td><b>Protocolo:</b> <?php echo $result->NFC_N_PROT; ?></td>
                                        </tr>
                                        <?php if ($result->NFC_INF_CPL): ?>
                                            <tr>
                                                <td colspan="3"><b>Informações
                                                        Complementares:</b><br><?php echo nl2br($result->NFC_INF_CPL); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="tab3">
                                <table class="table table-bordered table-striped" style="width: 50%;">
                                    <tbody>
                                        <tr>
                                            <td style="text-align: right; width: 60%;"><b>Total Produtos/Serviços:</b>
                                            </td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->NFC_V_PROD, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total Descontos:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->NFC_V_DESC, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total Outras Despesas:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->NFC_V_OUTRO, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total PIS:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->NFC_V_PIS, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total COFINS:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->NFC_V_COFINS, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; font-size: 16px;"><b>TOTAL NF:</b></td>
                                            <td style="text-align: right; font-size: 16px;"><b>R$
                                                    <?php echo number_format($result->NFC_V_NF, 2, ',', '.'); ?></b>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resultado/Processamento -->
<div id="nfecomModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="modalTitle">Processando...</h3>
    </div>
    <div class="modal-body" id="modalContent">
        <div class="text-center py-5">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <p>Aguarde...</p>
        </div>
    </div>
    <div class="modal-footer" id="modalFooter">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
    </div>
</div>

<!-- Modal de Cancelamento -->
<div id="modalCancelamento" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel">Cancelar NFCom</h3>
    </div>
    <div class="modal-body">
        <input type="hidden" id="nfecom_id_cancelamento" value="">
        <div class="control-group">
            <label for="justificativa_cancelamento" class="control-label">Justificativa do Cancelamento</label>
            <div class="controls">
                <textarea id="justificativa_cancelamento" rows="3" class="span12"
                    placeholder="Descreva o motivo do cancelamento (mínimo 15 caracteres)"></textarea>
                <span class="help-block text-error" id="erro_justificativa"
                    style="display:none; color: #b94a48;"></span>
            </div>
        </div>
        <div class="alert alert-warning">
            <strong>Atenção:</strong> O cancelamento é irreversível e só pode ser feito dentro do prazo legal.
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Fechar</button>
        <button class="btn btn-danger" id="btnConfirmarCancelamento">Confirmar Cancelamento</button>
    </div>
</div>

<script>
    // Base URL para uso em JavaScript
    var baseUrl = '<?php echo base_url(); ?>';
    
    // CSRF Tokens
    var csrfTokenName = '<?php echo $this->security->get_csrf_token_name(); ?>';
    var csrfTokenHash = '<?php echo $this->security->get_csrf_hash(); ?>';
    
    // Expose functions to global scope
    window.renderModalNfecom = function (data) {
        var isSuccess = data.status.toLowerCase().includes('autorizado') || data.cstat == '100';
        var isCancelado = data.status.toLowerCase().includes('cancelada') || data.cstat == '101';
        var statusClass = isSuccess ? 'success' : (isCancelado ? 'danger' : (data.status.toLowerCase().includes('rejeitada') ? 'danger' : 'warning'));
        var statusIcon = isSuccess ? 'fa-check-circle text-success' : (isCancelado ? 'fa-ban text-danger' : 'fa-exclamation-circle text-danger');

        var html = `
            <div class="text-center mb-4" style="text-align: center;">
                <i class="fas ${statusIcon} fa-4x mb-2" style="font-size: 4em;"></i>
                <h3 class="text-${statusClass}">${data.status}</h3>
            </div>

            <div class="alert alert-${statusClass}">
                <strong>Mensagem:</strong> ${data.motivo}
            </div>

            <table class="table table-condensed table-bordered">
                <tr>
                    <td><strong>Número:</strong> ${data.numero_nfcom}</td>
                    <td><strong>Cód. SEFAZ:</strong> ${data.cstat || '-'}</td>
                </tr>
                <tr>
                    <td colspan="2"><strong>Chave:</strong> ${data.chave_nfcom}</td>
                </tr>
                ${data.protocolo ? `
                <tr>
                    <td colspan="2"><strong>Protocolo:</strong> ${data.protocolo}</td>
                </tr>` : ''}
            </table>
            
            ${data.retorno ? `
            <div class="accordion" id="accordion2">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseRetorno">
                            Ver Retorno Técnico Completo
                        </a>
                    </div>
                    <div id="collapseRetorno" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <pre style="max-height: 200px; overflow-y: auto;">${data.retorno}</pre>
                        </div>
                    </div>
                </div>
            </div>` : ''}
        `;

        var footer = '';
        if (data.id) {
            footer += `<a href="${baseUrl}index.php/nfecom/gerarXml/${data.id}" class="btn btn-primary" target="_blank"><i class="fas fa-download"></i> XML</a>`;
            if (isSuccess) {
                footer += ` <a href="${baseUrl}index.php/nfecom/danfe/${data.id}" class="btn btn-info" target="_blank"><i class="fas fa-eye"></i> DANFE</a>`;
            }
        }
        footer += '<button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload();">Fechar e Atualizar</button>';

        $('#modalTitle').text('Resultado SEFAZ');
        $('#modalContent').html(html);
        $('#modalFooter').html(footer);

        $('#nfecomModal').modal('show');
    };

    window.consultarNFCom = function (id) {
        $('#modalTitle').text('Consultando SEFAZ...');
        $('#modalContent').html('<div class="text-center py-5" style="text-align: center;"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3" style="font-size: 4em;"></i><p>Consultando status da NFCom na SEFAZ...</p></div>');
        $('#modalFooter').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>');

        $('#nfecomModal').modal('show');

        $.ajax({
            url: baseUrl + 'index.php/nfecom/consultar',
            type: 'POST',
            data: { id: id, ajax: 'true', [csrfTokenName]: csrfTokenHash },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    renderModalNfecom(res.modal);
                } else {
                    $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> ' + res.message + '</div>');
                }
            },
            error: function () {
                $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> Não foi possível comunicar com o servidor.</div>');
            }
        });
    };

    window.gerarNFCom = function (id) {
        $('#modalTitle').text('Transmitindo NFCom...');
        $('#modalContent').html('<div class="text-center py-5" style="text-align: center;"><i class="fas fa-spinner fa-spin fa-3x text-primary mb-3" style="font-size: 4em;"></i><p>Conectando à SEFAZ...</p></div>');
        $('#modalFooter').html('<button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>');

        $('#nfecomModal').modal('show');

        $.ajax({
            url: baseUrl + 'index.php/nfecom/gerarXml',
            type: 'POST',
            data: { id: id, ajax: 'true', [csrfTokenName]: csrfTokenHash },
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    renderModalNfecom(res.modal);
                } else {
                    $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> ' + res.message + '</div>');
                }
            },
            error: function () {
                $('#modalContent').html('<div class="alert alert-danger"><strong>Erro:</strong> Não foi possível comunicar com o servidor.</div>');
            }
        });
    };

    window.abrirModalCancelamento = function (nfecomId) {
        $('#nfecom_id_cancelamento').val(nfecomId);
        $('#justificativa_cancelamento').val('');
        $('#erro_justificativa').hide();
        $('#modalCancelamento').modal('show');
    };

    $(document).ready(function () {
        // Evento de Cancelamento
        $('#btnConfirmarCancelamento').on('click', function () {
            var nfecomId = $('#nfecom_id_cancelamento').val();
            var justificativa = $('#justificativa_cancelamento').val().trim();

            if (!justificativa) {
                $('#erro_justificativa').text('A justificativa é obrigatória.').show();
                return;
            }

            if (justificativa.length < 15) {
                $('#erro_justificativa').text('A justificativa deve ter no mínimo 15 caracteres.').show();
                return;
            }

            $('#erro_justificativa').hide();

            var $btn = $(this);
            var originalText = $btn.html();
            $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processando...');

            $.ajax({
                url: baseUrl + 'index.php/nfecom/cancelar',
                type: 'POST',
                data: {
                    nfecom_id: nfecomId,
                    justificativa: justificativa,
                    [csrfTokenName]: csrfTokenHash
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#modalCancelamento').modal('hide');
                        alert('NFCom cancelada com sucesso!');
                        location.reload();
                    } else {
                        $('#erro_justificativa').text(response.message || 'Erro ao cancelar NFCom.').show();
                        $btn.prop('disabled', false).html(originalText);
                    }
                },
                error: function (xhr, status, error) {
                    var errorMsg = 'Erro ao processar cancelamento.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    $('#erro_justificativa').text(errorMsg).show();
                    $btn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Event Delegation para Botões (garante funcionamento mesmo se elementos forem recriados)
        $(document).on('click', '.btn-gerar-nfcom', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            gerarNFCom(id);
        });

        $(document).on('click', '.btn-consultar-nfcom', function (e) {
            e.preventDefault();
            console.log('Botão Consultar clicado!');
            var id = $(this).data('id');
            console.log('ID da NFCom:', id);
            if (typeof consultarNFCom === 'function') {
                consultarNFCom(id);
            } else {
                console.error('Função consultarNFCom não está definida!');
                alert('Erro: Função consultarNFCom não encontrada. Verifique o console.');
            }
        });

        $(document).on('click', '.btn-cancelar-nfcom', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            abrirModalCancelamento(id);
        });
    });
</script>