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
                    // VOLTAR
                    echo '<a href="' . base_url() . 'index.php/nfecom" class="btn-nwe" title="Voltar" style="margin-right: 1%"><i class="bx bx-arrow-back"></i></a>';

                    // EDITAR: Status < 2 (Rascunho/Salvo) ou 4 (Rejeitada)
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($result->nfc_status < 2 || $result->nfc_status == 4)) {
                        echo '<a href="' . base_url() . 'index.php/nfecom/editar/' . $result->nfc_id . '" class="btn-nwe" title="Editar NFCom" style="margin-right: 1%"><i class="bx bx-edit"></i></a>';
                    }

                    // TRANSMITIR / REEMITIR: Status < 2 (Rascunho/Salvo) ou 4 (Rejeitada)
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($result->nfc_status < 2 || $result->nfc_status == 4)) {
                        $label = ($result->nfc_status == 4) ? 'Reemitir' : 'Transmitir';
                        echo '<a href="#" onclick="gerarNFCom(' . $result->nfc_id . '); return false;" class="btn-nwe3" title="' . $label . ' NFCom" style="margin-right: 1%"><i class="bx bx-paper-plane"></i></a>';
                        echo '<a href="' . base_url() . 'index.php/nfecom/gerarXmlPreEmissao/' . $result->nfc_id . '" target="_blank" class="btn-nwe" title="Gerar XML (Pré-Emissão)" style="margin-right: 1%"><i class="bx bx-code-alt"></i></a>';
                    }

                    // CONSULTAR STATUS: Status >= 2 (Enviado+) AND Permissão Editar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $result->nfc_status >= 2) {
                        echo '<a href="#" onclick="consultarNFCom(' . $result->nfc_id . '); return false;" class="btn-nwe2" title="Consultar Status na SEFAZ" style="margin-right: 1%"><i class="bx bx-search"></i></a>';
                    }

                    // IMPRIMIR / DANFE: Permissão de visualizar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) {
                        echo '<a target="_blank" href="' . base_url() . 'index.php/nfecom/danfe/' . $result->nfc_id . '" class="btn-nwe" title="Imprimir NFCom" style="margin-right: 1%"><i class="bx bx-printer"></i></a>';
                    }

                    // XML AUTORIZADO: Status 3 (Autorizada) ou 5 (Autorizada)
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom') && ($result->nfc_status == 3 || $result->nfc_status == 5)) {
                        echo '<a target="_blank" href="' . base_url() . 'index.php/nfecom/gerarXml/' . $result->nfc_id . '" class="btn-nwe" title="Baixar XML Autorizado" style="margin-right: 1%"><i class="bx bx-download"></i></a>';
                    }

                    // CANCELAR: Status 3 ou 5 AND Permissão Editar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && ($result->nfc_status == 3 || $result->nfc_status == 5)) {
                        echo '<a href="#" onclick="abrirModalCancelamento(' . $result->nfc_id . '); return false;" class="btn-nwe4" title="Cancelar NFCom" style="margin-right: 1%"><i class="bx bx-x-circle"></i></a>';
                    }

                    // EXCLUIR: Status != 3 (Autorizada) AND Status != 7 (Cancelada) AND Permissão Editar
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $result->nfc_status != 3 && $result->nfc_status != 7) {
                        echo '<a href="' . base_url() . 'index.php/nfecom/excluir/' . $result->nfc_id . '" class="btn-nwe4" title="Excluir NFCom" style="margin-right: 1%" onclick="return confirm(\'Tem certeza que deseja excluir esta NFCom?\')"><i class="bx bx-trash-alt"></i></a>';
                    }
                    ?>
                </div>
            </div>
            <div class="widget-content" id="printOs">
                <div class="invoice-content">
                    <div class="invoice-head" style="margin-bottom: 0">
                        <table class="table table-condensed">
                            <tbody>
                                <tr>
                                    <td style="width: 50%; padding-left: 0">
                                        <h3 style="margin-top: 0;">NFCom #<?php echo $result->nfc_nnf; ?></h3>
                                        <span><b>Chave:</b> <?php echo $result->nfc_ch_nfcom; ?></span><br />
                                        <span><b>Série:</b> <?php echo $result->nfc_serie; ?></span><br />
                                        <span><b>Emissão:</b>
                                            <?php echo date('d/m/Y H:i', strtotime($result->nfc_dhemi)); ?></span>
                                    </td>
                                    <td style="width: 50%; padding-left: 0; text-align: right;">
                                        <h3>
                                            <?php
                                            switch ((int) $result->nfc_status) {
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
                                        <?php if ($result->nfc_x_motivo): ?>
                                            <span class="text-error"><b>Status SEFAZ:</b>
                                                <?php echo $result->nfc_c_stat . ' - ' . $result->nfc_x_motivo; ?></span>
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
                                            <b><?php echo $result->nfc_x_nome_emit; ?></b></span><br />
                                        <span>CNPJ: <?php echo $result->nfc_cnpj_emit; ?></span><br />
                                        <span>IE: <?php echo $result->nfc_ie_emit; ?></span>
                                    </td>
                                    <td style="width: 50%; padding-left: 0">
                                        <h5><b>DESTINATÁRIO</b></h5>
                                        <span><i class="fas fa-user"></i>
                                            <b><?php echo $result->nfc_x_nome_dest; ?></b></span><br />
                                        <span>CNPJ/CPF: <?php echo $result->nfc_cnpj_dest; ?></span><br />
                                        <span><?php echo $result->nfc_x_lgr_dest . ', ' . $result->nfc_nro_dest . ' - ' . $result->nfc_x_bairro_dest; ?></span><br />
                                        <span><?php echo $result->nfc_x_mun_dest . '/' . $result->nfc_uf_dest . ' - CEP: ' . $result->nfc_cep_dest; ?></span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="margin-top: 20px;">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab1" data-toggle="tab">Itens/Serviços</a></li>
                            <li><a href="#tab1b" data-toggle="tab">Tributos dos Itens</a></li>
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
                                                <td><?php echo $item->nfi_c_prod; ?></td>
                                                <td><?php echo $item->nfi_x_prod; ?></td>
                                                <td><?php echo $item->nfi_u_med; ?></td>
                                                <td><?php echo number_format($item->nfi_q_faturada, 4, ',', '.'); ?></td>
                                                <td>R$ <?php echo number_format($item->nfi_v_item, 2, ',', '.'); ?></td>
                                                <td>R$ <?php echo number_format($item->nfi_v_desc, 2, ',', '.'); ?></td>
                                                <td>R$ <?php echo number_format($item->nfi_v_prod, 2, ',', '.'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="tab1b">
                                <h4 style="margin-top: 0;">Tributos e Classificações Fiscais dos Itens</h4>
                                <?php foreach ($itens as $index => $item): ?>
                                    <div class="well" style="margin-bottom: 15px;">
                                        <h5 style="margin-top: 0; color: #2D335B;">
                                            <strong>Item <?php echo $index + 1; ?>:</strong> <?php echo $item->nfi_x_prod; ?>
                                        </h5>
                                        <table class="table table-bordered table-condensed" style="margin-bottom: 0;">
                                            <tbody>
                                                <tr style="background-color: #e8f4f8;">
                                                    <td colspan="4"><b>Informações do Item</b></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%;"><b>Código:</b></td>
                                                    <td><?php echo $item->nfi_c_prod; ?></td>
                                                    <td style="width: 20%;"><b>Unidade:</b></td>
                                                    <td><?php echo $item->nfi_u_med ?? '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Quantidade:</b></td>
                                                    <td><?php echo number_format($item->nfi_q_faturada ?? 0, 4, ',', '.'); ?></td>
                                                    <td><b>Valor Unitário:</b></td>
                                                    <td>R$ <?php echo number_format(($item->nfi_v_item ?? 0) / ($item->nfi_q_faturada ?? 1), 2, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor Total:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_prod ?? 0, 2, ',', '.'); ?></td>
                                                    <td><b>Desconto:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_desc ?? 0, 2, ',', '.'); ?></td>
                                                </tr>
                                                <tr style="background-color: #e8f4f8;">
                                                    <td colspan="4"><b>Classificação Fiscal</b></td>
                                                </tr>
                                                <tr>
                                                    <td style="width: 20%;"><b>CFOP:</b></td>
                                                    <td><?php echo $item->nfi_cfop ?? '-'; ?></td>
                                                    <td style="width: 20%;"><b>Cód. Classificação:</b></td>
                                                    <td><?php echo $item->nfi_c_class ?? '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>CST ICMS:</b></td>
                                                    <td><?php echo $item->nfi_cst_icms ?? '-'; ?></td>
                                                    <td><b>CST PIS:</b></td>
                                                    <td><?php echo $item->nfi_cst_pis ?? '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>CST COFINS:</b></td>
                                                    <td colspan="3"><?php echo $item->nfi_cst_cofins ?? '-'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td><b>CST PIS:</b></td>
                                                    <td><?php echo $item->nfi_cst_pis ?? '-'; ?></td>
                                                    <td><b>CST COFINS:</b></td>
                                                    <td><?php echo $item->nfi_cst_cofins ?? '-'; ?></td>
                                                </tr>
                                                <tr style="background-color: #f9f9f9;">
                                                    <td colspan="4"><b>PIS</b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Base de Cálculo PIS:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_bc_pis ?? 0, 2, ',', '.'); ?></td>
                                                    <td><b>Alíquota PIS (%):</b></td>
                                                    <td><?php echo number_format($item->nfi_p_pis ?? 0, 2, ',', '.'); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor PIS:</b></td>
                                                    <td colspan="3">R$ <?php echo number_format($item->nfi_v_pis ?? 0, 2, ',', '.'); ?></td>
                                                </tr>
                                                <tr style="background-color: #f9f9f9;">
                                                    <td colspan="4"><b>COFINS</b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Base de Cálculo COFINS:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_bc_cofins ?? 0, 2, ',', '.'); ?></td>
                                                    <td><b>Alíquota COFINS (%):</b></td>
                                                    <td><?php echo number_format($item->nfi_p_cofins ?? 0, 2, ',', '.'); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor COFINS:</b></td>
                                                    <td colspan="3">R$ <?php echo number_format($item->nfi_v_cofins ?? 0, 2, ',', '.'); ?></td>
                                                </tr>
                                                <?php if (isset($item->nfi_v_bc_fust) && $item->nfi_v_bc_fust > 0): ?>
                                                <tr style="background-color: #f9f9f9;">
                                                    <td colspan="4"><b>FUST</b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Base de Cálculo FUST:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_bc_fust ?? 0, 2, ',', '.'); ?></td>
                                                    <td><b>Alíquota FUST (%):</b></td>
                                                    <td><?php echo number_format($item->nfi_p_fust ?? 0, 2, ',', '.'); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor FUST:</b></td>
                                                    <td colspan="3">R$ <?php echo number_format($item->nfi_v_fust ?? 0, 2, ',', '.'); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (isset($item->nfi_v_bc_funtel) && $item->nfi_v_bc_funtel > 0): ?>
                                                <tr style="background-color: #f9f9f9;">
                                                    <td colspan="4"><b>FUNTTEL</b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Base de Cálculo FUNTTEL:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_bc_funtel ?? 0, 2, ',', '.'); ?></td>
                                                    <td><b>Alíquota FUNTTEL (%):</b></td>
                                                    <td><?php echo number_format($item->nfi_p_funtel ?? 0, 2, ',', '.'); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td><b>Valor FUNTTEL:</b></td>
                                                    <td colspan="3">R$ <?php echo number_format($item->nfi_v_funtel ?? 0, 2, ',', '.'); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (isset($item->nfi_v_bc_irrf) && $item->nfi_v_bc_irrf > 0): ?>
                                                <tr style="background-color: #f9f9f9;">
                                                    <td colspan="4"><b>IRRF</b></td>
                                                </tr>
                                                <tr>
                                                    <td><b>Base de Cálculo IRRF:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_bc_irrf ?? 0, 2, ',', '.'); ?></td>
                                                    <td><b>Valor IRRF:</b></td>
                                                    <td>R$ <?php echo number_format($item->nfi_v_irrf ?? 0, 2, ',', '.'); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="tab-pane" id="tab2">
                                <table class="table table-bordered table-condensed">
                                    <tbody>
                                        <tr>
                                            <td><b>Contrato:</b> <?php echo $result->nfc_n_contrato; ?></td>
                                            <td><b>Início Vigência:</b>
                                                <?php echo date('d/m/Y', strtotime($result->nfc_d_contrato_ini)); ?>
                                            </td>
                                            <td><b>Fim Vigência:</b>
                                                <?php echo $result->nfc_d_contrato_fim ? date('d/m/Y', strtotime($result->nfc_d_contrato_fim)) : '-'; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Cód. Assinante:</b> <?php echo $result->nfc_i_cod_assinante; ?></td>
                                            <td><b>Tipo Assinante:</b> <?php echo $result->nfc_tp_assinante; ?></td>
                                            <td><b>Tipo Serviço:</b> <?php echo $result->nfc_tp_serv_util; ?></td>
                                        </tr>
                                        <tr>
                                            <td><b>Competência:</b> <?php echo $result->nfc_compet_fat; ?></td>
                                            <td><b>Vencimento:</b>
                                                <?php echo date('d/m/Y', strtotime($result->nfc_d_venc_fat)); ?></td>
                                            <td><b>Protocolo:</b> <?php echo $result->nfc_n_prot; ?></td>
                                        </tr>
                                        <?php if ($result->nfc_inf_cpl): ?>
                                            <tr>
                                                <td colspan="3"><b>Informações
                                                        Complementares:</b><br><?php echo nl2br($result->nfc_inf_cpl); ?>
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
                                                <?php echo number_format($result->nfc_v_prod, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total Descontos:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->nfc_v_desc, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total Outras Despesas:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->nfc_v_outro, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total PIS:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->nfc_v_pis, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right;"><b>Total COFINS:</b></td>
                                            <td style="text-align: right;">R$
                                                <?php echo number_format($result->nfc_v_cofins, 2, ',', '.'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: right; font-size: 16px;"><b>TOTAL NF:</b></td>
                                            <td style="text-align: right; font-size: 16px;"><b>R$
                                                    <?php echo number_format($result->nfc_v_nf, 2, ',', '.'); ?></b>
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

        // Create data object safely for CSRF
        var dataObj = { id: id, ajax: 'true' };
        dataObj[csrfTokenName] = csrfTokenHash;

        $.ajax({
            url: baseUrl + 'index.php/nfecom/consultar',
            type: 'POST',
            data: dataObj,
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

        // Create data object safely for CSRF
        var dataObj = { id: id, ajax: 'true' };
        dataObj[csrfTokenName] = csrfTokenHash;

        $.ajax({
            url: baseUrl + 'index.php/nfecom/gerarXml',
            type: 'POST',
            data: dataObj,
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