<style>
    .nfecom-header {
        background: #f8f9fa;
        border: 1px solid #e1e8ed;
        color: #333;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .nfecom-status-badge {
        font-size: 14px;
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: bold;
    }

    .nfecom-card {
        border: 1px solid #e1e8ed;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .nfecom-card-header {
        background: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #e1e8ed;
        border-radius: 8px 8px 0 0;
    }

    .nfecom-card-body {
        padding: 20px;
    }

    .edit-mode {
        background-color: #fff3cd;
        border-color: #ffeaa7;
    }

    /* Botão Moderno de Editar */
    .btn-edit-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-edit-modern:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .btn-edit-modern:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-edit-modern i {
        font-size: 16px;
    }

    .btn-edit-modern span {
        font-weight: 600;
    }

    /* Botões de edição na área de ações */
    .btn-edit {
        margin-left: 10px;
        border-radius: 6px;
        font-weight: 600;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
</style>

<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <!-- Header com informações principais -->
        <div class="nfecom-header">
            <div class="row-fluid">
                <div class="span8">
                    <h3 style="margin: 0; color: #333;">
                        <i class="fas fa-file-invoice"></i> NFCom
                        <?php echo $result->NFC_NNF; ?>
                    </h3>
                    <p style="margin: 5px 0 0 0; color: #666;">
                        Série:
                        <?php echo $result->NFC_SERIE; ?> | Chave:
                        <?php echo $result->NFC_CH_NFCOM; ?>
                    </p>
                </div>
                <div class="span4" style="text-align: right;">
                    <div class="nfecom-status-badge" style="background-color:
                        <?php
                        switch ((int) $result->NFC_STATUS) {
                            case 0:
                                echo '#CDB380';
                                break;
                            case 1:
                                echo '#436eee';
                                break;
                            case 2:
                                echo '#00cd00';
                                break;
                            case 3:
                                echo '#4d9c79';
                                break;
                            case 4:
                                echo '#f24c6f';
                                break;
                            case 5:
                                echo '#28a745';
                                break;
                            case 7:
                                echo '#999';
                                break;
                            default:
                                echo '#999';
                                break;
                        }
                        ?>
                        ?>; color: white; display: inline-block; margin-bottom: 10px;">
                        <?php
                        switch ((int) $result->NFC_STATUS) {
                            case 0:
                                echo 'Rascunho';
                                break;
                            case 1:
                                echo 'Salvo';
                                break;
                            case 2:
                                echo 'Enviado';
                                break;
                            case 3:
                                echo 'Autorizado';
                                break;
                            case 4:
                                echo 'Rejeitada';
                                break;
                            case 5:
                                echo 'Autorizada';
                                break;
                            case 7:
                                echo 'Cancelada';
                                break;
                            default:
                                echo 'Desconhecido (Status: ' . $result->NFC_STATUS . ')';
                                break;
                        }
                        ?>
                    </div>
                    <?php if ($result->NFC_X_MOTIVO): ?>
                        <div style="font-size: 12px; opacity: 0.9;">
                            <strong>Último Status:</strong><br>
                            <?php echo htmlspecialchars(substr($result->NFC_X_MOTIVO, 0, 60)); ?>
                            <?php if (strlen($result->NFC_X_MOTIVO) > 60)
                                echo '...'; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Abas para organizar as informações -->
        <div class="widget-box">
            <div class="widget-title">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#dados-gerais" data-toggle="tab">Dados Gerais</a></li>
                    <li><a href="#emitente-destinatario" data-toggle="tab">Emitente & Destinatário</a></li>
                    <li><a href="#itens-servicos" data-toggle="tab">Itens/Serviços</a></li>
                    <li><a href="#totais-protocolo" data-toggle="tab">Totais & Protocolo</a></li>
                    <?php if ($result->NFC_INF_CPL): ?>
                        <li><a href="#informacoes" data-toggle="tab">Informações</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="widget-content tab-content">

                <!-- Aba Dados Gerais -->
                <div class="tab-pane active" id="dados-gerais">
                    <div
                        class="nfecom-card <?php echo ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) ? 'edit-mode' : ''; ?>">
                        <div class="nfecom-card-header">
                            <h4><i class="fas fa-info-circle"></i> Dados Gerais da NFCom</h4>
                        </div>
                        <div class="nfecom-card-body">
                            <form id="editForm" method="post"
                                action="<?php echo base_url(); ?>index.php/nfecom/editar/<?php echo $result->NFC_ID; ?>">
                                <div class="row-fluid">
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label">Número NF:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_NNF; ?>
                                                </span>
                                                <input type="text" name="nfc_nnf" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_NNF; ?>" style="display: none;"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Série:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_SERIE; ?>
                                                </span>
                                                <input type="text" name="nfc_serie" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_SERIE; ?>" style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Data Emissão:</label>
                                            <div class="controls">
                                                <span>
                                                    <?php echo date('d/m/Y H:i', strtotime($result->NFC_DHEMI)); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <div class="control-group">
                                            <label class="control-label">Número Contrato:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_N_CONTRATO; ?>
                                                </span>
                                                <input type="text" name="nfc_n_contrato" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_N_CONTRATO; ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Operação Comercial:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo !empty($result->operacao_nome) ? $result->operacao_nome : 'Não vinculada'; ?>
                                                </span>
                                                <select name="opc_id" class="span12 edit-field" style="display: none;">
                                                    <option value="">Selecione uma operação...</option>
                                                    <?php
                                                    // Carregar as operações direto aqui para o select
                                                    $operacoes_vis = $this->db->get('operacao_comercial')->result();
                                                    foreach ($operacoes_vis as $op): ?>
                                                        <option value="<?php echo $op->OPC_ID; ?>" <?php echo (isset($result->OPC_ID) && $result->OPC_ID == $op->OPC_ID) ? 'selected' : ''; ?>>
                                                            <?php echo $op->OPC_NOME; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Data Início Contrato:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo date('d/m/Y', strtotime($result->NFC_D_CONTRATO_INI)); ?>
                                                </span>
                                                <input type="date" name="nfc_d_contrato_ini" class="span12 edit-field"
                                                    value="<?php echo date('Y-m-d', strtotime($result->NFC_D_CONTRATO_INI)); ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Cód. Assinante:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_I_COD_ASSINANTE; ?>
                                                </span>
                                                <input type="text" name="nfc_i_cod_assinante" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_I_COD_ASSINANTE; ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Data Fim Contrato:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_D_CONTRATO_FIM ? date('d/m/Y', strtotime($result->NFC_D_CONTRATO_FIM)) : 'Não informado'; ?>
                                                </span>
                                                <input type="date" name="nfc_d_contrato_fim" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_D_CONTRATO_FIM; ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Tipo Assinante:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_TP_ASSINANTE; ?>
                                                </span>
                                                <select name="nfc_tp_assinante" class="span12 edit-field"
                                                    style="display: none;">
                                                    <option value="1" <?php echo ($result->NFC_TP_ASSINANTE == 1) ? 'selected' : ''; ?>>1 - Comercial</option>
                                                    <option value="2" <?php echo ($result->NFC_TP_ASSINANTE == 2) ? 'selected' : ''; ?>>2 - Industrial</option>
                                                    <option value="3" <?php echo ($result->NFC_TP_ASSINANTE == 3) ? 'selected' : ''; ?>>3 - Residencial/PF</option>
                                                    <option value="4" <?php echo ($result->NFC_TP_ASSINANTE == 4) ? 'selected' : ''; ?>>4 - Produtor Rural</option>
                                                    <option value="99" <?php echo ($result->NFC_TP_ASSINANTE == 99) ? 'selected' : ''; ?>>99 - Outros</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Tipo Serviço:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_TP_SERV_UTIL; ?>
                                                </span>
                                                <select name="nfc_tp_serv_util" class="span12 edit-field"
                                                    style="display: none;">
                                                    <option value="1" <?php echo ($result->NFC_TP_SERV_UTIL == 1) ? 'selected' : ''; ?>>1 - Telefonia</option>
                                                    <option value="2" <?php echo ($result->NFC_TP_SERV_UTIL == 2) ? 'selected' : ''; ?>>2 - Com. de Dados</option>
                                                    <option value="3" <?php echo ($result->NFC_TP_SERV_UTIL == 3) ? 'selected' : ''; ?>>3 - TV por Assinatura</option>
                                                    <option value="4" <?php echo ($result->NFC_TP_SERV_UTIL == 4) ? 'selected' : ''; ?>>4 - Internet</option>
                                                    <option value="5" <?php echo ($result->NFC_TP_SERV_UTIL == 5) ? 'selected' : ''; ?>>5 - Multimídia</option>
                                                    <option value="6" <?php echo ($result->NFC_TP_SERV_UTIL == 6) ? 'selected' : ''; ?>>6 - Outros</option>
                                                    <option value="7" <?php echo ($result->NFC_TP_SERV_UTIL == 7) ? 'selected' : ''; ?>>7 - Vários (Combo)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Competência:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_COMPET_FAT; ?>
                                                </span>
                                                <input type="text" name="nfc_compet_fat" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_COMPET_FAT; ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Nova linha para Informações de Pagamento -->
                                <div class="row-fluid"
                                    style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #eee;">
                                    <h5 style="margin-left: 10px; color: #555;"><i class="fas fa-credit-card"></i>
                                        Informações de Pagamento</h5>
                                    <div class="span3">
                                        <div class="control-group">
                                            <label class="control-label">Vencimento:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo date('d/m/Y', strtotime($result->NFC_D_VENC_FAT)); ?>
                                                </span>
                                                <input type="date" name="nfc_d_venc_fat" class="span12 edit-field"
                                                    value="<?php echo date('Y-m-d', strtotime($result->NFC_D_VENC_FAT)); ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label">Boleto (Linha Digitável):</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_LINHA_DIGITAVEL ?: '<span class="text-muted">Não informado</span>'; ?>
                                                </span>
                                                <input type="text" name="nfc_linha_digitavel" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_LINHA_DIGITAVEL; ?>"
                                                    placeholder="Linha digitável" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span4">
                                        <div class="control-group">
                                            <label class="control-label">Pix (Chave Pix):</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_CHAVE_PIX ?: '<span class="text-muted">Não informado</span>'; ?>
                                                </span>
                                                <input type="text" name="nfc_chave_pix" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_CHAVE_PIX; ?>"
                                                    placeholder="Chave Pix" style="display: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="edit-actions"
                                    style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e1e8ed;">
                                    <div class="span6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="cancelEdit()">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                    <div class="span6" style="text-align: right;">
                                        <button type="button" class="btn btn-primary" onclick="reenviarNota()">
                                            <i class="fas fa-paper-plane"></i> Salvar e Reenviar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aba Emitente & Destinatário -->
                <div class="tab-pane" id="emitente-destinatario">
                    <div
                        class="nfecom-card <?php echo ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) ? 'edit-mode' : ''; ?>">
                        <div class="nfecom-card-header">
                            <h4><i class="fas fa-building"></i> Emitente & Destinatário</h4>
                            <?php if ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')): ?>
                                <div style="float: right;">
                                    <button type="button" class="btn btn-edit-modern" onclick="toggleEditMode()"
                                        title="Editar NFCom">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="nfecom-card-body">
                            <form id="editForm" method="post"
                                action="<?php echo base_url(); ?>index.php/nfecom/editar/<?php echo $result->NFC_ID; ?>">
                                <div class="row-fluid">
                                    <div class="span6">
                                        <h5><i class="fas fa-building"></i> Emitente</h5>
                                        <div class="control-group">
                                            <label class="control-label">Nome:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_X_NOME_EMIT; ?>
                                                </span>
                                                <input type="text" name="nfc_x_nome_emit" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_X_NOME_EMIT; ?>"
                                                    style="display: none;" readonly>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">CNPJ:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_CNPJ_EMIT; ?>
                                                </span>
                                                <input type="text" name="nfc_cnpj_emit" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_CNPJ_EMIT; ?>" style="display: none;"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Inscrição Estadual:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_IE_EMIT; ?>
                                                </span>
                                                <input type="text" name="nfc_ie_emit" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_IE_EMIT; ?>" style="display: none;"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <h5><i class="fas fa-user"></i> Destinatário</h5>
                                        <div class="control-group">
                                            <label class="control-label">Nome:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_X_NOME_DEST; ?>
                                                </span>
                                                <input type="text" name="nfc_x_nome_dest" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_X_NOME_DEST; ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">CNPJ:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_CNPJ_DEST; ?>
                                                </span>
                                                <input type="text" name="nfc_cnpj_dest" class="span12 edit-field"
                                                    value="<?php echo $result->NFC_CNPJ_DEST; ?>"
                                                    style="display: none;">
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <label class="control-label">Endereço:</label>
                                            <div class="controls">
                                                <span class="view-mode">
                                                    <?php echo $result->NFC_X_LGR_DEST . ', ' . $result->NFC_NRO_DEST; ?><br>
                                                    <?php echo $result->NFC_X_BAIRRO_DEST . ', ' . $result->NFC_X_MUN_DEST . '/' . $result->NFC_UF_DEST; ?><br>
                                                    CEP:
                                                    <?php echo $result->NFC_CEP_DEST; ?>
                                                </span>
                                                <div class="edit-field" style="display: none;">
                                                    <input type="text" name="nfc_x_lgr_dest" class="span12"
                                                        value="<?php echo $result->NFC_X_LGR_DEST; ?>"
                                                        placeholder="Logradouro" style="margin-bottom: 5px;">
                                                    <input type="text" name="nfc_nro_dest" class="span4"
                                                        value="<?php echo $result->NFC_NRO_DEST; ?>" placeholder="Nº"
                                                        style="margin-bottom: 5px;">
                                                    <input type="text" name="nfc_x_bairro_dest" class="span8"
                                                        value="<?php echo $result->NFC_X_BAIRRO_DEST; ?>"
                                                        placeholder="Bairro" style="margin-bottom: 5px;">
                                                    <input type="text" name="nfc_x_mun_dest" class="span8"
                                                        value="<?php echo $result->NFC_X_MUN_DEST; ?>"
                                                        placeholder="Cidade" style="margin-bottom: 5px;">
                                                    <input type="text" name="nfc_uf_dest" class="span2"
                                                        value="<?php echo $result->NFC_UF_DEST; ?>" placeholder="UF"
                                                        maxlength="2" style="margin-bottom: 5px;">
                                                    <input type="text" name="nfc_cep_dest" class="span4"
                                                        value="<?php echo $result->NFC_CEP_DEST; ?>" placeholder="CEP">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="edit-actions"
                                    style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e1e8ed;">
                                    <div class="span6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="cancelEdit()">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                    <div class="span6" style="text-align: right;">
                                        <button type="button" class="btn btn-primary" onclick="reenviarNota()">
                                            <i class="fas fa-paper-plane"></i> Salvar e Reenviar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aba Itens/Serviços -->
                <div class="tab-pane" id="itens-servicos">
                    <div
                        class="nfecom-card <?php echo ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) ? 'edit-mode' : ''; ?>">
                        <div class="nfecom-card-header">
                            <h4><i class="fas fa-list"></i> Itens/Serviços da NFCom</h4>
                            <?php if ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')): ?>
                                <div style="float: right;">
                                    <button type="button" class="btn btn-edit-modern" onclick="toggleEditMode()"
                                        title="Editar NFCom">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="nfecom-card-body">
                            <form id="editForm" method="post"
                                action="<?php echo base_url(); ?>index.php/nfecom/editar/<?php echo $result->NFC_ID; ?>">
                                <!-- Modo Visualização -->
                                <div class="view-mode">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Descrição</th>
                                                    <th>cClass</th>
                                                    <th>Unid</th>
                                                    <th style="text-align: center;">Qtd</th>
                                                    <th style="text-align: right;">Vlr Unit.</th>
                                                    <th style="text-align: right;">Vlr Desc.</th>
                                                    <th style="text-align: right;">Vlr Outros</th>
                                                    <th style="text-align: right;">Vlr Final</th>
                                                    <th style="text-align: center;">CST ICMS</th>
                                                    <th style="text-align: center;">CFOP</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($itens as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <?php echo $item->NFI_C_PROD; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $item->NFI_X_PROD; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $item->NFI_C_CLASS; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $item->NFI_U_MED; ?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <?php echo number_format($item->NFI_Q_FATURADA, 4, ',', '.'); ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo number_format($item->NFI_V_ITEM, 2, ',', '.'); ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo number_format($item->NFI_V_DESC, 2, ',', '.'); ?>
                                                        </td>
                                                        <td style="text-align: right;">
                                                            <?php echo number_format($item->NFI_V_OUTRO, 2, ',', '.'); ?>
                                                        </td>
                                                        <td style="text-align: right;"><strong>
                                                                <?php echo number_format($item->NFI_V_PROD, 2, ',', '.'); ?>
                                                            </strong></td>
                                                        <td style="text-align: center;">
                                                            <?php echo $item->NFI_CST_ICMS; ?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <?php echo $item->NFI_CFOP; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Modo Edição -->
                                <div class="edit-field" style="display: none;">
                                    <div id="itens-container">
                                        <?php $itemCount = 0; ?>
                                        <?php foreach ($itens as $item):
                                            $itemCount++; ?>
                                            <div class="item-row"
                                                style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: #f9f9f9;">
                                                <div class="row-fluid">
                                                    <div class="span1">
                                                        <label>Item:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][n_item]"
                                                            value="<?php echo $item->NFI_N_ITEM; ?>" class="span12"
                                                            readonly>
                                                    </div>
                                                    <div class="span2">
                                                        <label>Código:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][c_prod]"
                                                            value="<?php echo $item->NFI_C_PROD; ?>" class="span12">
                                                    </div>
                                                    <div class="span4">
                                                        <label>Descrição:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][x_prod]"
                                                            value="<?php echo $item->NFI_X_PROD; ?>" class="span12">
                                                    </div>
                                                    <div class="span2">
                                                        <label>cClass:</label>
                                                        <span><?php echo $item->NFI_C_CLASS; ?></span>
                                                        <input type="hidden"
                                                            name="itens[<?php echo $itemCount; ?>][c_class]"
                                                            value="<?php echo $item->NFI_C_CLASS; ?>">
                                                    </div>
                                                    <div class="span2">
                                                        <label>CST ICMS:</label>
                                                        <select name="itens[<?php echo $itemCount; ?>][cst_icms]"
                                                            class="span12">
                                                            <option value="00" <?php echo ($item->NFI_CST_ICMS == '00') ? 'selected' : ''; ?>>00 - Tribut. Integral</option>
                                                            <option value="20" <?php echo ($item->NFI_CST_ICMS == '20') ? 'selected' : ''; ?>>20 - Red. Base Calc.</option>
                                                            <option value="40" <?php echo ($item->NFI_CST_ICMS == '40') ? 'selected' : ''; ?>>40 - Isenta</option>
                                                            <option value="41" <?php echo ($item->NFI_CST_ICMS == '41') ? 'selected' : ''; ?>>41 - Não Tributada</option>
                                                            <option value="50" <?php echo ($item->NFI_CST_ICMS == '50') ? 'selected' : ''; ?>>50 - Suspensão</option>
                                                            <option value="51" <?php echo ($item->NFI_CST_ICMS == '51') ? 'selected' : ''; ?>>51 - Diferimento</option>
                                                            <option value="90" <?php echo ($item->NFI_CST_ICMS == '90') ? 'selected' : ''; ?>>90 - Outras</option>
                                                        </select>
                                                    </div>
                                                    <div class="span1">
                                                        <label>CFOP:</label>
                                                        <select name="itens[<?php echo $itemCount; ?>][cfop]"
                                                            class="span12">
                                                            <option value="5303" <?php echo ($item->NFI_CFOP == '5303') ? 'selected' : ''; ?>>5303 - Com. Não Contribuinte</option>
                                                            <option value="5307" <?php echo ($item->NFI_CFOP == '5307') ? 'selected' : ''; ?>>5307 - Com. Isenta Não Contrib.</option>
                                                            <option value="6303" <?php echo ($item->NFI_CFOP == '6303') ? 'selected' : ''; ?>>6303 - Interstate Não Contrib.</option>
                                                            <option value="6307" <?php echo ($item->NFI_CFOP == '6307') ? 'selected' : ''; ?>>6307 - Interstate Isenta</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row-fluid">
                                                    <div class="span1">
                                                        <label>Unid:</label>
                                                        <span><?php echo $item->NFI_U_MED; ?></span>
                                                        <input type="hidden" name="itens[<?php echo $itemCount; ?>][u_med]"
                                                            value="<?php echo $item->NFI_U_MED; ?>">
                                                    </div>
                                                    <div class="span2">
                                                        <label>Qtd:</label>
                                                        <input type="text"
                                                            name="itens[<?php echo $itemCount; ?>][q_faturada]"
                                                            value="<?php echo number_format($item->NFI_Q_FATURADA, 4, ',', '.'); ?>"
                                                            class="span12">
                                                    </div>
                                                    <div class="span2">
                                                        <label>Vlr Unit:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][v_item]"
                                                            value="<?php echo number_format($item->NFI_V_ITEM, 2, ',', '.'); ?>"
                                                            class="span12">
                                                    </div>
                                                    <div class="span2">
                                                        <label>Vlr Desc:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][v_desc]"
                                                            value="<?php echo number_format($item->NFI_V_DESC, 2, ',', '.'); ?>"
                                                            class="span12">
                                                    </div>
                                                    <div class="span2">
                                                        <label>Vlr Outros:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][v_outro]"
                                                            value="<?php echo number_format($item->NFI_V_OUTRO, 2, ',', '.'); ?>"
                                                            class="span12">
                                                    </div>
                                                    <div class="span2">
                                                        <label>Valor Unitário:</label>
                                                        <input type="text" name="itens[<?php echo $itemCount; ?>][v_item]"
                                                            value="<?php echo number_format($item->NFI_V_ITEM, 2, ',', '.'); ?>"
                                                            class="span12">
                                                    </div>
                                                    <div class="span1">
                                                        <button type="button" class="btn btn-danger btn-mini"
                                                            onclick="removerItem(this)" style="margin-top: 20px;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <button type="button" class="btn btn-success btn-mini" onclick="adicionarItem()">
                                        <i class="fas fa-plus"></i> Adicionar Item
                                    </button>
                                </div>

                                <div class="edit-actions"
                                    style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e1e8ed;">
                                    <div class="span6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="cancelEdit()">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                    <div class="span6" style="text-align: right;">
                                        <button type="button" class="btn btn-primary" onclick="reenviarNota()">
                                            <i class="fas fa-paper-plane"></i> Salvar e Reenviar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aba Totais & Protocolo -->
                <div class="tab-pane" id="totais-protocolo">
                    <div
                        class="nfecom-card <?php echo ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) ? 'edit-mode' : ''; ?>">
                        <div class="nfecom-card-header">
                            <h4><i class="fas fa-calendar-alt"></i> Totais & Protocolo</h4>
                            <?php if ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')): ?>
                                <div style="float: right;">
                                    <button type="button" class="btn btn-edit-modern" onclick="toggleEditMode()"
                                        title="Editar NFCom">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="nfecom-card-body">
                            <form id="editForm" method="post"
                                action="<?php echo base_url(); ?>index.php/nfecom/editar/<?php echo $result->NFC_ID; ?>">
                                <div class="row-fluid">
                                    <div class="span6">
                                        <h5><i class="fas fa-calculator"></i> Totais da NFCom</h5>
                                        <div class="view-mode">
                                            <table class="table table-striped">
                                                <tr>
                                                    <td><strong>Valor Produtos/Serviços:</strong></td>
                                                    <td style="text-align: right;">R$
                                                        <?php echo number_format($result->NFC_V_PROD, 2, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>PIS:</strong></td>
                                                    <td style="text-align: right;">R$
                                                        <?php echo number_format($result->NFC_V_PIS, 2, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>COFINS:</strong></td>
                                                    <td style="text-align: right;">R$
                                                        <?php echo number_format($result->NFC_V_COFINS, 2, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>IRRF:</strong></td>
                                                    <td style="text-align: right;">R$
                                                        <?php echo number_format($result->NFC_V_IRRF, 2, ',', '.'); ?>
                                                    </td>
                                                </tr>
                                                <tr style="background-color: #f8f9fa;">
                                                    <td><strong style="font-size: 16px;">Valor Total NF:</strong></td>
                                                    <td style="text-align: right;"><strong
                                                            style="font-size: 16px; color: #28a745;">R$
                                                            <?php echo number_format($result->NFC_V_NF, 2, ',', '.'); ?>
                                                        </strong></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="span6">
                                        <h5><i class="fas fa-calendar-alt"></i> Datas e Protocolo</h5>
                                        <div class="view-mode">
                                            <table class="table table-striped">
                                                <tr>
                                                    <td><strong>Data Emissão:</strong></td>
                                                    <td>
                                                        <?php echo date('d/m/Y H:i', strtotime($result->NFC_DHEMI)); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Período Uso - Início:</strong></td>
                                                    <td>
                                                        <?php echo date('d/m/Y', strtotime($result->NFC_D_PER_USO_INI)); ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Período Uso - Fim:</strong></td>
                                                    <td>
                                                        <?php echo date('d/m/Y', strtotime($result->NFC_D_PER_USO_FIM)); ?>
                                                    </td>
                                                </tr>
                                                <?php if ($result->NFC_N_PROT): ?>
                                                    <tr style="background-color: #d4edda;">
                                                        <td><strong>Número Protocolo:</strong></td>
                                                        <td><span style="color: #155724; font-weight: bold;">
                                                                <?php echo $result->NFC_N_PROT; ?>
                                                            </span></td>
                                                    </tr>
                                                    <tr style="background-color: #d4edda;">
                                                        <td><strong>Data Recebimento SEFAZ:</strong></td>
                                                        <td>
                                                            <?php echo date('d/m/Y H:i', strtotime($result->NFC_DH_RECBTO)); ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                                <?php if ($result->NFC_C_STAT): ?>
                                                    <tr
                                                        style="<?php echo ($result->NFC_STATUS == 4) ? 'background-color: #f8d7da;' : 'background-color: #d4edda;'; ?>">
                                                        <td><strong>Código SEFAZ:</strong></td>
                                                        <td>
                                                            <?php echo $result->NFC_C_STAT; ?>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            </table>
                                        </div>

                                        <div class="edit-field" style="display: none;">
                                            <div class="control-group">
                                                <label class="control-label">Período Uso - Início:</label>
                                                <div class="controls">
                                                    <input type="date" name="nfc_d_per_uso_ini" class="span12"
                                                        value="<?php echo date('Y-m-d', strtotime($result->NFC_D_PER_USO_INI)); ?>">
                                                </div>
                                            </div>
                                            <div class="control-group">
                                                <label class="control-label">Período Uso - Fim:</label>
                                                <div class="controls">
                                                    <input type="date" name="nfc_d_per_uso_fim" class="span12"
                                                        value="<?php echo date('Y-m-d', strtotime($result->NFC_D_PER_USO_FIM)); ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="edit-actions"
                                    style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e1e8ed;">
                                    <div class="span6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="cancelEdit()">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                    <div class="span6" style="text-align: right;">
                                        <button type="button" class="btn btn-primary" onclick="reenviarNota()">
                                            <i class="fas fa-paper-plane"></i> Salvar e Reenviar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Aba Informações Complementares -->
                <div class="tab-pane" id="informacoes">
                    <div
                        class="nfecom-card <?php echo ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')) ? 'edit-mode' : ''; ?>">
                        <div class="nfecom-card-header">
                            <h4><i class="fas fa-info-circle"></i> Informações Complementares</h4>
                            <?php if ($result->NFC_STATUS == 4 && $this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')): ?>
                                <div style="float: right;">
                                    <button type="button" class="btn btn-edit-modern" onclick="toggleEditMode()"
                                        title="Editar NFCom">
                                        <i class="fas fa-edit"></i>
                                        <span>Editar</span>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="nfecom-card-body">
                            <form id="editForm" method="post"
                                action="<?php echo base_url(); ?>index.php/nfecom/editar/<?php echo $result->NFC_ID; ?>">
                                <!-- Modo Visualização -->
                                <div class="view-mode">
                                    <?php if ($result->NFC_INF_CPL): ?>
                                        <div class="well" style="background-color: #f8f9fa;">
                                            <?php echo nl2br($result->NFC_INF_CPL); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i> Nenhuma informação complementar cadastrada.
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Modo Edição -->
                                <div class="edit-field" style="display: none;">
                                    <div class="control-group">
                                        <label class="control-label">Informações Complementares:</label>
                                        <div class="controls">
                                            <textarea name="nfc_inf_cpl" class="span12" rows="6"
                                                placeholder="Digite aqui as informações complementares da NFCom..."><?php echo $result->NFC_INF_CPL; ?></textarea>
                                            <small class="help-block">Informações adicionais sobre a nota fiscal
                                                (observações, referências, etc.)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="edit-actions"
                                    style="display: none; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e1e8ed;">
                                    <div class="span6">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-save"></i> Salvar Alterações
                                        </button>
                                        <button type="button" class="btn btn-default" onclick="cancelEdit()">
                                            <i class="fas fa-times"></i> Cancelar
                                        </button>
                                    </div>
                                    <div class="span6" style="text-align: right;">
                                        <button type="button" class="btn btn-primary" onclick="reenviarNota()">
                                            <i class="fas fa-paper-plane"></i> Salvar e Reenviar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Área de Ações -->
        <div class="widget-box">
            <div class="widget-content" style="padding: 20px;">
                <div class="row-fluid">
                    <div class="span6">
                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) { ?>
                            <a href="<?php echo base_url() ?>index.php/nfecom/danfe/<?php echo $result->NFC_ID; ?>"
                                class="btn btn-inverse" target="_blank">
                                <i class="fas fa-file-pdf"></i> Visualizar DANFE
                            </a>
                        <?php } ?>

                        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom')): ?>
                            <?php if ($result->NFC_STATUS >= 3): ?>
                                <a href="<?php echo base_url() ?>index.php/nfecom/gerarXml/<?php echo $result->NFC_ID; ?>"
                                    class="btn btn-info" target="_blank">
                                    <i class="fas fa-download"></i> Baixar XML
                                </a>
                            <?php endif; ?>

                            <?php if ($result->NFC_STATUS == 4): ?>
                                <!-- Botões de Edição -->
                                <button type="submit" form="editForm" class="btn btn-success btn-edit"
                                    style="margin-left: 10px;">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                                <button type="button" onclick="reenviarNota()" class="btn btn-primary btn-edit"
                                    style="margin-left: 5px;">
                                    <i class="fas fa-paper-plane"></i> Salvar e Enviar
                                </button>
                            <?php elseif ($result->NFC_STATUS < 2): ?>
                                <a href="#" onclick="gerarNFCom(<?php echo $result->NFC_ID; ?>)" class="btn btn-success">
                                    <i class="fas fa-play"></i> Enviar para SEFAZ
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <div class="span6" style="text-align: right;">
                        <a href="<?php echo base_url() ?>index.php/nfecom" class="btn">
                            <i class="fas fa-arrow-left"></i> Voltar para Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var itemCount = <?php echo count($itens); ?>;

    function toggleEditMode() {
        $('.view-mode').toggle();
        $('.edit-field').toggle();
        $('.edit-actions').toggle();
        $('.nfecom-card').toggleClass('edit-mode');
    }

    function cancelEdit() {
        $('.view-mode').show();
        $('.edit-field').hide();
        $('.edit-actions').hide();
        $('.nfecom-card').removeClass('edit-mode');
    }

    function reenviarNota() {
        if (confirm('Tem certeza que deseja salvar as alterações e reenviar a NFCom?')) {
            // Adiciona um campo hidden para indicar reenvio
            $('<input>').attr({
                type: 'hidden',
                name: 'reenviar',
                value: '1'
            }).appendTo('#editForm');

            $('#editForm').submit();
        }
    }

    function adicionarItem() {
        itemCount++;
        const row = `
            <div class="item-row" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px; background: #f9f9f9;">
                <div class="row-fluid">
                   <div class="span1">
                       <label>Item:</label>
                       <input type="text" name="itens[${itemCount}][n_item]" value="${itemCount}" class="span12" readonly>
                   </div>
                   <div class="span2">
                       <label>Código:</label>
                       <input type="text" name="itens[${itemCount}][c_prod]" value="" class="span12">
                   </div>
                   <div class="span4">
                       <label>Descrição:</label>
                       <input type="text" name="itens[${itemCount}][x_prod]" value="" class="span12">
                   </div>
                   <div class="span2">
                       <label>cClass:</label>
                       <span></span>
                       <input type="hidden" name="itens[${itemCount}][c_class]" value="">
                   </div>
                   <div class="span2">
                        <label>CST ICMS:</label>
                        <select name="itens[${itemCount}][cst_icms]" class="span12">
                            <option value="00">00 - Tribut. Integral</option>
                            <option value="20">20 - Red. Base Calc.</option>
                            <option value="40">40 - Isenta</option>
                            <option value="41">41 - Não Tributada</option>
                            <option value="50">50 - Suspensão</option>
                            <option value="51">51 - Diferimento</option>
                            <option value="90">90 - Outras</option>
                        </select>
                   </div>
                   <div class="span1">
                       <label>CFOP:</label>
                       <select name="itens[${itemCount}][cfop]" class="span12">
                            <option value="5303">5303 - Com. Não Contribuinte</option>
                            <option value="5307">5307 - Com. Isenta Não Contrib.</option>
                            <option value="6303">6303 - Interstate Não Contrib.</option>
                            <option value="6307">6307 - Interstate Isenta</option>
                       </select>
                   </div>
                </div>
                <div class="row-fluid">
                    <div class="span1">
                       <label>Unid:</label>
                       <span>UN</span>
                       <input type="hidden" name="itens[${itemCount}][u_med]" value="UN">
                    </div>
                <div class="span2">
                    <label>Qtd:</label>
                    <input type="text" name="itens[${itemCount}][q_faturada]" value="1,0000" class="span12">
                </div>
                <div class="span2">
                    <label>Vlr Unit:</label>
                    <input type="text" name="itens[${itemCount}][v_item]" class="span12">
                </div>
                <div class="span2">
                    <label>Vlr Desc:</label>
                    <input type="text" name="itens[${itemCount}][v_desc]" value="0,00" class="span12">
                </div>
                <div class="span2">
                    <label>Vlr Outros:</label>
                    <input type="text" name="itens[${itemCount}][v_outro]" value="0,00" class="span12">
                </div>
                <div class="span1">
                    <button type="button" class="btn btn-danger btn-mini" onclick="removerItem(this)" style="margin-top: 20px;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
        $('#itens-container').append(row);
    }

    function removerItem(button) {
        if (confirm('Tem certeza que deseja remover este item?')) {
            $(button).closest('.item-row').remove();
        }
    }

    // Inicializar tooltips se houver
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
</div>
</div>
</div>
</div>
</div>