<?php
require_once FCPATH . 'application/vendor/autoload.php';
?>
<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-certificate"></i>
                </span>
                <h5>Configuração do Certificado Digital</h5>
            </div>
            <div class="widget-content">
                <?php 
                if (isset($certificado) && $certificado) { 
                ?>
                <div class="alert alert-info">
                    <h4>Certificado Atual</h4>
                    <p><strong>Nome do Certificado:</strong> <?php echo htmlspecialchars($certificado->nome_certificado); ?></p>
                    <p><strong>Data de Validade:</strong> <?php echo date('d/m/Y', strtotime($certificado->data_validade)); ?></p>
                    <p><strong>Data de Cadastro:</strong> <?php echo date('d/m/Y H:i:s', strtotime($certificado->created_at)); ?></p>
                    <?php 
                    $hoje = new DateTime();
                    $validade = new DateTime($certificado->data_validade);
                    if ($hoje > $validade) {
                        echo '<div class="alert alert-danger"><strong>ATENÇÃO:</strong> Este certificado está vencido!</div>';
                    }
                    ?>
                </div>
                <?php } ?>

                <form action="<?php echo base_url() ?>index.php/nfe/saveCertificate" method="post" enctype="multipart/form-data" class="form-horizontal">
                    <div class="control-group">
                        <label for="certificado" class="control-label">Certificado Digital (.pfx)</label>
                        <div class="controls">
                            <input type="file" name="certificado" id="certificado" accept=".pfx" required>
                            <p class="help-block">Selecione o arquivo do certificado digital no formato PFX.</p>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="senha" class="control-label">Senha do Certificado</label>
                        <div class="controls">
                            <input type="password" name="senha" id="senha" required>
                            <p class="help-block">Digite a senha do certificado digital.</p>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?php echo isset($certificado) && $certificado ? 'Atualizar' : 'Salvar'; ?> Certificado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.widget-box {
    border: 1px solid #ddd;
    margin-bottom: 20px;
}

.widget-title {
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    padding: 10px 15px;
}

.widget-content {
    padding: 15px;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}

.alert-info {
    color: #0c5460;
    background-color: #d1ecf1;
    border-color: #bee5eb;
}

.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
    margin-top: 10px;
}

.control-group {
    margin-bottom: 15px;
}

.control-label {
    font-weight: bold;
    margin-bottom: 5px;
}

.controls {
    margin-bottom: 10px;
}

.help-block {
    color: #666;
    font-size: 12px;
    margin-top: 5px;
}
</style>