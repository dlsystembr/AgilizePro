<?php
// Get theme settings
$theme = $this->config->item('theme');
$primary_color = isset($theme['primary_color']) ? $theme['primary_color'] : '#1a1a1a';
$secondary_color = isset($theme['secondary_color']) ? $theme['secondary_color'] : '#2d2d2d';
$accent_color = isset($theme['accent_color']) ? $theme['accent_color'] : '#3498db';
$text_color = isset($theme['text_color']) ? $theme['text_color'] : '#ffffff';
$background_color = isset($theme['background_color']) ? $theme['background_color'] : '#121212';
$card_background = '#1e1e1e';
$border_color = '#333333';
?>

<style>
.nfe-container {
    background-color: <?php echo $card_background; ?>;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    padding: 20px;
    margin-top: 20px;
    border: 1px solid <?php echo $border_color; ?>;
}

.nfe-header {
    color: <?php echo $text_color; ?>;
    border-bottom: 2px solid <?php echo $accent_color; ?>;
    padding-bottom: 15px;
    margin-bottom: 20px;
}

.nfe-header h5 {
    font-size: 1.5em;
    margin: 0;
    font-weight: 600;
}

.nfe-section {
    background: <?php echo $secondary_color; ?>;
    border-radius: 6px;
    margin-bottom: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
    border: 1px solid <?php echo $border_color; ?>;
}

.nfe-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
}

.nfe-section-header {
    background-color: <?php echo $primary_color; ?>;
    color: <?php echo $text_color; ?>;
    padding: 12px 15px;
    border-radius: 6px 6px 0 0;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid <?php echo $border_color; ?>;
}

.nfe-section-header i {
    margin-right: 10px;
    transition: transform 0.3s ease;
}

.nfe-section-header.collapsed i {
    transform: rotate(-90deg);
}

.nfe-section-content {
    padding: 15px;
    background: <?php echo $secondary_color; ?>;
    border-radius: 0 0 6px 6px;
}

.nfe-button {
    background-color: <?php echo $accent_color; ?>;
    color: <?php echo $text_color; ?>;
    border: none;
    padding: 10px 20px;
    border-radius: 4px;
    margin: 5px;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.nfe-button:hover {
    background-color: #2980b9;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.nfe-button i {
    font-size: 1.1em;
}

/* Modal Styles */
.nfe-modal {
    background-color: rgba(0, 0, 0, 0.8);
}

.nfe-modal .modal-content {
    background-color: <?php echo $card_background; ?>;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
    border: 1px solid <?php echo $border_color; ?>;
}

.nfe-modal .modal-header {
    background-color: <?php echo $primary_color; ?>;
    color: <?php echo $text_color; ?>;
    border-radius: 6px 6px 0 0;
    border-bottom: 1px solid <?php echo $border_color; ?>;
}

.nfe-modal .modal-body {
    color: <?php echo $text_color; ?>;
}

.nfe-modal .table {
    background: <?php echo $secondary_color; ?>;
    border-radius: 6px;
    overflow: hidden;
    color: <?php echo $text_color; ?>;
}

.nfe-modal .table td {
    padding: 12px;
    vertical-align: middle;
    border-color: <?php echo $border_color; ?>;
}

.nfe-modal .well {
    background-color: <?php echo $primary_color; ?>;
    border: 1px solid <?php echo $border_color; ?>;
    border-radius: 4px;
}

.nfe-modal pre {
    color: <?php echo $text_color; ?>;
    font-family: 'Consolas', 'Monaco', monospace;
    background-color: <?php echo $primary_color; ?>;
    border: 1px solid <?php echo $border_color; ?>;
    padding: 10px;
    border-radius: 4px;
}

.nfe-status {
    padding: 6px 12px;
    border-radius: 4px;
    font-weight: 500;
}

.nfe-status.success {
    background-color: #27ae60;
    color: white;
}

.nfe-status.error {
    background-color: #c0392b;
    color: white;
}

.nfe-status.warning {
    background-color: #f39c12;
    color: white;
}

.alert-info {
    background-color: <?php echo $primary_color; ?>;
    border-color: <?php echo $border_color; ?>;
    color: <?php echo $text_color; ?>;
}

.close {
    color: <?php echo $text_color; ?>;
    opacity: 0.8;
}

.close:hover {
    color: <?php echo $text_color; ?>;
    opacity: 1;
}

.text-break {
    color: <?php echo $text_color; ?>;
    word-break: break-all;
}

label {
    color: <?php echo $text_color; ?>;
    opacity: 0.9;
}
</style>

<div class="nfe-container">
    <div class="nfe-header">
        <h5><i class="fas fa-receipt"></i> Emissor de Notas Fiscais</h5>
            </div>
    
                <div class="row-fluid">
                    <div class="span12">
                        <div class="accordion" id="accordion2">
                            <!-- Seção de Emissão de NFe -->
                <div class="nfe-section">
                    <div class="nfe-section-header" data-toggle="collapse" data-target="#collapseOne">
                        <div>
                                        <i class="fas fa-plus"></i> Emissão de NFe
                        </div>
                        <i class="fas fa-chevron-down"></i>
                                </div>
                    <div id="collapseOne" class="collapse show">
                        <div class="nfe-section-content">
                            <a href="<?php echo base_url() ?>index.php/nfe/buscarVendas" class="nfe-button">
                                            <i class="fas fa-search"></i> Buscar Vendas
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção de Gerenciamento -->
                <div class="nfe-section">
                    <div class="nfe-section-header" data-toggle="collapse" data-target="#collapseTwo">
                        <div>
                                        <i class="fas fa-list"></i> Gerenciamento
                        </div>
                        <i class="fas fa-chevron-down"></i>
                                </div>
                    <div id="collapseTwo" class="collapse">
                        <div class="nfe-section-content">
                            <a href="<?php echo base_url() ?>index.php/nfe/gerenciar" class="nfe-button">
                                            <i class="fas fa-list"></i> Listar NFe's
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção de Configurações -->
                <div class="nfe-section">
                    <div class="nfe-section-header" data-toggle="collapse" data-target="#collapseThree">
                        <div>
                                        <i class="fas fa-cog"></i> Configurações
                        </div>
                        <i class="fas fa-chevron-down"></i>
                                </div>
                    <div id="collapseThree" class="collapse">
                        <div class="nfe-section-content">
                            <a href="<?php echo base_url() ?>index.php/nfe/configuracoes" class="nfe-button">
                                            <i class="fas fa-cog"></i> Configurações Gerais
                                        </a>
                            <a href="<?php echo base_url() ?>index.php/nfe/certificado" class="nfe-button">
                                            <i class="fas fa-certificate"></i> Certificado Digital
                                        </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resposta da SEFAZ -->
<div class="modal fade nfe-modal" id="nfeModal" tabindex="-1" role="dialog" aria-labelledby="nfeModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="nfeModalLabel">
                    <i class="fas fa-file-invoice"></i> Resposta da SEFAZ
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php if ($this->session->flashdata('nfe_modal')): 
                    $nfe_modal = $this->session->flashdata('nfe_modal');
                ?>
                    <div class="table-responsive">
                        <table class="table">
                        <tr>
                                <td>
                                    <label><strong>Número NFe:</strong></label>
                                    <div class="text-break"><?php echo $nfe_modal['numero_nfe']; ?></div>
                            </td>
                                <td>
                                    <label><strong>Chave NFe:</strong></label>
                                    <div class="text-break"><?php echo $nfe_modal['chave_nfe']; ?></div>
                            </td>
                                <td>
                                    <label><strong>Modelo:</strong></label>
                                    <div class="text-break"><?php echo isset($nfe_modal['modelo']) && $nfe_modal['modelo'] == 65 ? 'NFC-e (65)' : 'NFe (55)'; ?></div>
                            </td>
                                <td>
                                    <label><strong>Status:</strong></label>
                                    <div class="nfe-status <?php echo ($nfe_modal['status'] == 'Autorizada') ? 'success' : 'error'; ?>">
                                    <?php echo $nfe_modal['status']; ?>
                                    </div>
                            </td>
                        </tr>
                    </table>
                    </div>

                    <div class="mt-3">
                        <label><strong>Motivo:</strong></label>
                        <div class="text-break"><?php echo $nfe_modal['motivo']; ?></div>
                    </div>

                    <div class="mt-3">
                        <label><strong>Protocolo:</strong></label>
                        <div class="well">
                            <pre><?php echo htmlspecialchars($nfe_modal['protocolo']); ?></pre>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label><strong>XML:</strong></label>
                        <div class="well">
                            <pre><?php echo htmlspecialchars($nfe_modal['xml']); ?></pre>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Nenhuma resposta da SEFAZ disponível.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="nfe-button" data-dismiss="modal">
                    <i class="fas fa-times"></i> Fechar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle chevron icon on section header click
    $('.nfe-section-header').click(function() {
        $(this).find('.fa-chevron-down').toggleClass('fa-rotate-180');
    });

    // Show modal if there's flash data
    <?php if ($this->session->flashdata('nfe_modal')): ?>
        $('#nfeModal').modal('show');
    <?php endif; ?>
});
</script> 