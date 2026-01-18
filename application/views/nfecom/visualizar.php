<div class="row-fluid" style="margin-top: 0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>NFECom - Visualizar</h5>
            </div>
            <div class="widget-content">
                <div class="span12" id="divNfecom">
                    <div class="span12 well" style="margin-left: 0">
                        <div class="span6">
                            <strong>Número NF:</strong> <?php echo $result->NFC_NNF; ?><br>
                            <strong>Série:</strong> <?php echo $result->NFC_SERIE; ?><br>
                            <strong>Chave:</strong> <?php echo $result->NFC_CH_NFCOM; ?><br>
                            <strong>Data Emissão:</strong> <?php echo date('d/m/Y H:i', strtotime($result->NFC_DHEMI)); ?><br>
                            <strong>Status:</strong>
                            <span class="badge" style="background-color:
                                <?php
                                echo match($result->NFC_STATUS) {
                                    0 => '#CDB380',
                                    1 => '#436eee',
                                    2 => '#00cd00',
                                    3 => '#4d9c79',
                                    4 => '#f24c6f',
                                    default => '#999'
                                };
                                ?>">
                                <?php
                                echo match($result->NFC_STATUS) {
                                    0 => 'Rascunho',
                                    1 => 'Salvo',
                                    2 => 'Enviado',
                                    3 => 'Autorizado',
                                    4 => 'Rejeitado',
                                    default => 'Desconhecido'
                                };
                                ?>
                            </span>
                        </div>
                        <div class="span6">
                            <strong>Cliente:</strong> <?php echo $result->NFC_X_NOME_DEST; ?><br>
                            <strong>CNPJ:</strong> <?php echo $result->NFC_CNPJ_DEST; ?><br>
                            <strong>Número Contrato:</strong> <?php echo $result->NFC_N_CONTRATO; ?><br>
                            <strong>Data Início Contrato:</strong> <?php echo date('d/m/Y', strtotime($result->NFC_D_CONTRATO_INI)); ?><br>
                            <strong>Competência:</strong> <?php echo $result->NFC_COMPET_FAT; ?><br>
                        </div>
                    </div>

                    <div class="span12" style="margin-left: 0">
                        <div class="span6">
                            <strong>Emitente:</strong> <?php echo $result->NFC_X_NOME_EMIT; ?><br>
                            <strong>CNPJ Emitente:</strong> <?php echo $result->NFC_CNPJ_EMIT; ?><br>
                            <strong>IE Emitente:</strong> <?php echo $result->NFC_IE_EMIT; ?><br>
                            <strong>Endereço:</strong> <?php echo $result->NFC_X_LGR_EMIT . ', ' . $result->NFC_NRO_EMIT . ' - ' . $result->NFC_X_BAIRRO_EMIT . ', ' . $result->NFC_X_MUN_EMIT . '/' . $result->NFC_UF_EMIT; ?>
                        </div>
                        <div class="span6">
                            <strong>Destinatário:</strong> <?php echo $result->NFC_X_NOME_DEST; ?><br>
                            <strong>CNPJ Destinatário:</strong> <?php echo $result->NFC_CNPJ_DEST; ?><br>
                            <strong>Endereço:</strong> <?php echo $result->NFC_X_LGR_DEST . ', ' . $result->NFC_NRO_DEST . ' - ' . $result->NFC_X_BAIRRO_DEST . ', ' . $result->NFC_X_MUN_DEST . '/' . $result->NFC_UF_DEST; ?>
                        </div>
                    </div>

                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <h4>Itens da NFCom</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Código</th>
                                    <th>Descrição</th>
                                    <th>CFOP</th>
                                    <th>Quantidade</th>
                                    <th>Valor Unitário</th>
                                    <th>Valor Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($itens as $item): ?>
                                <tr>
                                    <td><?php echo $item->NFI_N_ITEM; ?></td>
                                    <td><?php echo $item->NFI_C_PROD; ?></td>
                                    <td><?php echo $item->NFI_X_PROD; ?></td>
                                    <td><?php echo $item->NFI_CFOP; ?></td>
                                    <td><?php echo number_format($item->NFI_Q_FATURADA, 4, ',', '.'); ?></td>
                                    <td>R$ <?php echo number_format($item->NFI_V_ITEM, 2, ',', '.'); ?></td>
                                    <td>R$ <?php echo number_format($item->NFI_V_PROD, 2, ',', '.'); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <div class="span6">
                            <h4>Totais</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Valor Produtos:</strong></td>
                                    <td>R$ <?php echo number_format($result->NFC_V_PROD, 2, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>PIS:</strong></td>
                                    <td>R$ <?php echo number_format($result->NFC_V_PIS, 2, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>COFINS:</strong></td>
                                    <td>R$ <?php echo number_format($result->NFC_V_COFINS, 2, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>IRRF:</strong></td>
                                    <td>R$ <?php echo number_format($result->NFC_V_IRRF, 2, ',', '.'); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Valor NF:</strong></td>
                                    <td><strong>R$ <?php echo number_format($result->NFC_V_NF, 2, ',', '.'); ?></strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="span6">
                            <h4>Datas e Períodos</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <td><strong>Data Vencimento:</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($result->NFC_D_VENC_FAT)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Período Uso Início:</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($result->NFC_D_PER_USO_INI)); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Período Uso Fim:</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($result->NFC_D_PER_USO_FIM)); ?></td>
                                </tr>
                                <?php if ($result->NFC_N_PROT): ?>
                                <tr>
                                    <td><strong>Número Protocolo:</strong></td>
                                    <td><?php echo $result->NFC_N_PROT; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Data Recebimento:</strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($result->NFC_DH_RECBTO)); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <?php if ($result->NFC_INF_CPL): ?>
                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <h4>Informações Complementares</h4>
                        <div class="well">
                            <?php echo nl2br($result->NFC_INF_CPL); ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="span12" style="margin-left: 0; margin-top: 20px;">
                        <div class="span6">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNfecom')) { ?>
                                <a href="<?php echo base_url() ?>index.php/nfecom/danfe/<?php echo $result->NFC_ID; ?>" class="btn btn-inverse"><i class="fas fa-file-pdf"></i> Visualizar DANFE</a>
                            <?php } ?>
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $result->NFC_STATUS < 2) { ?>
                                <a href="<?php echo base_url() ?>index.php/nfecom/gerarXml/<?php echo $result->NFC_ID; ?>" class="btn btn-info"><i class="fas fa-code"></i> Gerar XML</a>
                            <?php } ?>
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eNfecom') && $result->NFC_STATUS == 2) { ?>
                                <a href="<?php echo base_url() ?>index.php/nfecom/autorizar/<?php echo $result->NFC_ID; ?>" class="btn btn-success"><i class="fas fa-check"></i> Autorizar na SEFAZ</a>
                            <?php } ?>
                        </div>
                        <div class="span6" style="text-align: right;">
                            <a href="<?php echo base_url() ?>index.php/nfecom" class="btn"><i class="fas fa-backward"></i> Voltar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>