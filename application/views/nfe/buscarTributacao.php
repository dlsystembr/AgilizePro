<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} ?>

<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-search"></i>
                </span>
                <h5>Buscar Tributação</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <div class="span12" style="margin-left: 0">
                    <form action="<?php echo base_url() ?>index.php/nfe/buscarTributacao" method="post" id="formTributacao">
                        <div class="span12" style="padding: 1%; margin-left: 0">
                            <div class="span6">
                                <label for="venda_id">Venda<span class="required">*</span></label>
                                <input type="text" class="span12" name="venda_id" id="venda_id" required />
                            </div>
                        </div>
                        <div class="span12" style="padding: 1%; margin-left: 0">
                            <div class="span12" style="display:flex; justify-content: center;">
                                <button class="button btn btn-primary" id="btnBuscar">
                                    <span class="button__icon"><i class="bx bx-search"></i></span>
                                    <span class="button__text2">Buscar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if (isset($tributacao)) { ?>
                    <div class="span12" style="margin-left: 0">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>CFOP</th>
                                    <th>CST/CSOSN</th>
                                    <th>Natureza da Operação</th>
                                    <th>Destinação</th>
                                    <th>Objetivo Comercial</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $tributacao->cfop; ?></td>
                                    <td>
                                        <?php 
                                        if ($tributacao->csosn !== null) {
                                            echo $tributacao->csosn;
                                        } else {
                                            echo $tributacao->cst;
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $tributacao->operacao_comercial; ?></td>
                                    <td><?php echo $tributacao->destinacao; ?></td>
                                    <td><?php echo $tributacao->objetivo_comercial; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $("#venda_id").autocomplete({
            source: "<?php echo base_url(); ?>index.php/nfe/autoCompleteVenda",
            minLength: 1,
            select: function(event, ui) {
                $("#venda_id").val(ui.item.id);
            }
        });

        $("#formTributacao").validate({
            rules: {
                venda_id: {
                    required: true
                }
            },
            messages: {
                venda_id: {
                    required: 'Campo obrigatório'
                }
            },
            submitHandler: function(form) {
                var dados = $(form).serialize();
                $.ajax({
                    type: "POST",
                    url: "<?php echo base_url(); ?>index.php/nfe/buscarTributacao",
                    data: dados,
                    dataType: 'json',
                    success: function(data) {
                        if (data.result == true) {
                            window.location.reload();
                        } else {
                            Swal.fire({
                                type: "error",
                                title: "Atenção",
                                text: data.message
                            });
                        }
                    }
                });
                return false;
            }
        });
    });
</script> 