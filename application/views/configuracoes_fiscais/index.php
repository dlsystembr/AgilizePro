<style>
    .card-config {
        transition: transform .2s;
        cursor: pointer;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        background: #fff;
    }

    .card-config:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, .1);
        border-color: #0984e3;
    }

    .card-config .card-header {
        padding: 15px;
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-config .card-header i {
        font-size: 24px;
        color: #0984e3;
    }

    .card-config .card-body {
        padding: 15px;
        flex-grow: 1;
    }

    .card-config .card-footer {
        padding: 10px 15px;
        background: #fdfdfd;
        border-top: 1px solid #eee;
        text-align: right;
    }

    .status-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
    }
</style>

<div class="span12">
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon"><i class="fas fa-file-invoice-dollar"></i></span>
            <h5>Configurações Fiscais</h5>
        </div>
        <div class="widget-content">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <?php echo $this->session->flashdata('success'); ?>
                </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger">
                    <?php echo $this->session->flashdata('error'); ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Configure abaixo os parâmetros para cada tipo de documento fiscal que
                sua empresa emite.
            </div>

            <div class="row-fluid" style="margin-top: 20px;">
                <?php
                $icones = [
                    'NFE' => 'fa-file-invoice',
                    'NFCE' => 'fa-receipt',
                    'NFSE' => 'fa-hand-holding-usd',
                    'NFCOM' => 'fa-broadcast-tower',
                    'CTE' => 'fa-truck-moving',
                    'MDFE' => 'fa-boxes'
                ];

                foreach ($tiposDocumento as $tipo => $nome) {
                    $configAtiva = null;
                    foreach ($configuracoes as $cfg) {
                        if ($cfg->CFG_TIPO_DOCUMENTO === $tipo) {
                            $configAtiva = $cfg;
                            break;
                        }
                    }
                    ?>
                    <div class="span4" style="margin-left: 0; margin-right: 2%; margin-bottom: 20px;">
                        <div class="card-config"
                            onclick="location.href='<?php echo base_url(); ?>index.php/configuracoesfiscais/configurar/<?php echo $tipo; ?>'">
                            <div class="card-header">
                                <i class="fas <?php echo $icones[$tipo] ?? 'fa-file'; ?>"></i>
                                <?php if ($configAtiva && $configAtiva->CFG_ATIVO): ?>
                                    <span class="badge badge-success status-badge">ATIVO</span>
                                <?php else: ?>
                                    <span class="badge status-badge">INATIVO</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h5 style="margin-top: 0;">
                                    <?php echo $nome; ?>
                                </h5>
                                <?php if ($configAtiva): ?>
                                    <small class="text-muted">
                                        Ambiente:
                                        <?php echo $configAtiva->CFG_AMBIENTE == 1 ? 'Produção' : 'Homologação'; ?><br>
                                        Série:
                                        <?php echo $configAtiva->CFG_SERIE; ?> | Próximo Nº:
                                        <?php echo $configAtiva->CFG_NUMERO_ATUAL; ?><br>
                                        Certificado:
                                        <?php echo $configAtiva->CER_CNPJ ? preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $configAtiva->CER_CNPJ) : 'Não vinculado'; ?>
                                    </small>
                                <?php else: ?>
                                    <p class="text-muted"><small>Ainda não configurado.</small></p>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <span class="btn btn-mini btn-info">Configurar <i class="fas fa-chevron-right"></i></span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('tema/rodape'); ?>