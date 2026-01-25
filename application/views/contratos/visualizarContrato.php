<style>
    .info-box {
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        margin-bottom: 20px;
        background: #fff;
    }

    .info-box-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e0e0e0;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        color: #333;
    }

    .info-box-content {
        padding: 15px;
    }

    .info-row {
        display: flex;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        width: 200px;
        flex-shrink: 0;
    }

    .info-value {
        color: #333;
        flex: 1;
    }
</style>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-file-contract"></i>
                </span>
                <h5>Visualizar Contrato</h5>
            </div>
            <div class="widget-content nopadding tab-content">

                <!-- Informações do Contrato -->
                <div class="info-box" style="margin-top: 20px;">
                    <div class="info-box-header">
                        <i class="fas fa-file-contract"></i>
                        <span>Dados do Contrato</span>
                    </div>
                    <div class="info-box-content">
                        <div class="info-row">
                            <div class="info-label">Número do Contrato:</div>
                            <div class="info-value"><strong><?= $result->ctr_numero ?></strong></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Cliente:</div>
                            <div class="info-value">
                                <?= $result->pes_nome ?>
                                <?php if ($result->pes_razao_social): ?>
                                    <br><small>(<?= $result->pes_razao_social ?>)</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">CPF/CNPJ:</div>
                            <div class="info-value"><?= $result->pes_cpfcnpj ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Data de Início:</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($result->ctr_data_inicio)) ?></div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Data de Fim:</div>
                            <div class="info-value">
                                <?= $result->ctr_data_fim ? date('d/m/Y', strtotime($result->ctr_data_fim)) : '<em>Não definida</em>' ?>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Tipo de Assinante:</div>
                            <div class="info-value">
                                <span class="label label-info"><?= $tiposAssinante[$result->ctr_tipo_assinante] ?? 'N/A' ?></span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-label">Situação:</div>
                            <div class="info-value">
                                <?= (int)$result->ctr_situacao === 1 ? '<span class="label label-success">Ativo</span>' : '<span class="label">Inativo</span>' ?>
                            </div>
                        </div>

                        <?php if ($result->ctr_anexo): ?>
                        <div class="info-row">
                            <div class="info-label">Anexo:</div>
                            <div class="info-value">
                                <a href="<?= base_url() ?>index.php/contratos/download_anexo/<?= $result->ctr_id ?>" class="btn btn-mini btn-info" target="_blank">
                                    <i class="fas fa-download"></i> Download do Anexo
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($result->ctr_observacao): ?>
                        <div class="info-row">
                            <div class="info-label">Observação:</div>
                            <div class="info-value"><?= nl2br(htmlspecialchars($result->ctr_observacao)) ?></div>
                        </div>
                        <?php endif; ?>

                        <div class="info-row">
                            <div class="info-label">Data de Cadastro:</div>
                            <div class="info-value"><?= date('d/m/Y H:i:s', strtotime($result->ctr_data_cadastro)) ?></div>
                        </div>

                        <?php if ($result->ctr_data_alteracao): ?>
                        <div class="info-row">
                            <div class="info-label">Última Alteração:</div>
                            <div class="info-value"><?= date('d/m/Y H:i:s', strtotime($result->ctr_data_alteracao)) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Itens do Contrato -->
                <?php if (!empty($itens)): ?>
                <div class="info-box" style="margin-top: 20px;">
                    <div class="info-box-header">
                        <i class="fas fa-list"></i>
                        <span>Itens do Contrato (Serviços)</span>
                    </div>
                    <div class="info-box-content">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Serviço</th>
                                    <th style="width: 120px; text-align: right;">Preço</th>
                                    <th style="width: 120px; text-align: right;">Quantidade</th>
                                    <th style="width: 120px; text-align: right;">Subtotal</th>
                                    <th>Observação</th>
                                    <th style="width: 80px; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $totalGeral = 0;
                                foreach ($itens as $item): 
                                    $subtotal = floatval($item->cti_preco) * floatval($item->cti_quantidade);
                                    $totalGeral += $subtotal;
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($item->pro_descricao) ?></td>
                                    <td style="text-align: right;">R$ <?= number_format($item->cti_preco, 2, ',', '.') ?></td>
                                    <td style="text-align: right;"><?= number_format($item->cti_quantidade, 4, ',', '.') ?> <?= htmlspecialchars($item->pro_unid_medida) ?></td>
                                    <td style="text-align: right;"><strong>R$ <?= number_format($subtotal, 2, ',', '.') ?></strong></td>
                                    <td><?= $item->cti_observacao ? htmlspecialchars($item->cti_observacao) : '<em>Sem observação</em>' ?></td>
                                    <td style="text-align: center;">
                                        <?= (int)$item->cti_ativo === 1 ? '<span class="label label-success">Ativo</span>' : '<span class="label">Inativo</span>' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align: right;">Total Geral:</th>
                                    <th style="text-align: right;">R$ <?= number_format($totalGeral, 2, ',', '.') ?></th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Botões de ação -->
                <div class="form-actions">
                    <div class="span12">
                        <div class="span6 offset3" style="display: flex;justify-content: center; gap: 10px;">
                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eContrato')): ?>
                            <a href="<?= base_url() ?>index.php/contratos/editar/<?= $result->ctr_id ?>" class="button btn btn-mini btn-info">
                                <span class="button__icon"><i class='bx bx-edit'></i></span>
                                <span class="button__text2">Editar</span>
                            </a>
                            <?php endif; ?>

                            <a href="<?= base_url() ?>index.php/contratos" class="button btn btn-mini btn-warning">
                                <span class="button__icon"><i class="bx bx-undo"></i></span>
                                <span class="button__text2">Voltar</span>
                            </a>

                            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dContrato')): ?>
                            <a href="#modal-excluir" role="button" data-toggle="modal" class="button btn btn-mini btn-danger">
                                <span class="button__icon"><i class='bx bx-trash'></i></span>
                                <span class="button__text2">Excluir</span>
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Excluir -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?= base_url() ?>index.php/contratos/excluir/<?= $result->ctr_id ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Contrato</h5>
        </div>
        <div class="modal-body">
            <h5 style="text-align: center">Deseja realmente excluir este contrato?</h5>
            <p style="text-align: center"><strong>Número: <?= $result->ctr_numero ?></strong></p>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span>
                <span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger">
                <span class="button__icon"><i class='bx bx-trash'></i></span>
                <span class="button__text2">Excluir</span>
            </button>
        </div>
    </form>
</div>
