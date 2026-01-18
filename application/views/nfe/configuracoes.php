<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-cogs"></i>
                </span>
                <h5>Configurações do Sistema de Notas Fiscais</h5>
            </div>
            <div class="widget-content">
                <div class="span12">
                    <div class="row-fluid">
                        <div class="span4">
                            <a href="<?php echo base_url(); ?>index.php/nfe/certificado" class="button btn btn-success tip-top" style="max-width: 100%; margin-bottom: 10px;" title="Configurar Certificado Digital">
                                <span class="button__icon"><i class='fas fa-certificate'></i></span>
                                <span class="button__text2">Certificado Digital</span>
                            </a>
                        </div>
                        <div class="span4">
                            <a href="<?php echo base_url(); ?>index.php/nfe/configuracoesNFe" class="button btn btn-primary tip-top" style="max-width: 100%; margin-bottom: 10px;" title="Configurar NFe">
                                <span class="button__icon"><i class='fas fa-file-invoice'></i></span>
                                <span class="button__text2">Configurações NFe</span>
                            </a>
                        </div>
                        <div class="span4">
                            <a href="<?php echo base_url(); ?>index.php/nfe/configuracoesNFCe" class="button btn btn-warning tip-top" style="max-width: 100%; margin-bottom: 10px;" title="Configurar NFC-e">
                                <span class="button__icon"><i class='fas fa-receipt'></i></span>
                                <span class="button__text2">Configurações NFC-e</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.tip-top').tooltip({ placement: 'top' });
    });
</script> 