<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-exchange-alt"></i>
        </span>
        <h5>Editar Operação Comercial</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <?php echo form_open('operacaocomercial/editar/'.$result->OPC_ID, 'id="formOperacaoComercial" class="form-horizontal"'); ?>
        
        <div class="control-group">
            <label for="OPC_SIGLA" class="control-label">Sigla<span class="required">*</span></label>
            <div class="controls">
                <input id="OPC_SIGLA" class="span2" name="OPC_SIGLA" type="text" value="<?php echo $result->OPC_SIGLA; ?>" />
                <span class="help-inline">Ex: VENDA, COMPRA, DEV</span>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_NOME" class="control-label">Nome<span class="required">*</span></label>
            <div class="controls">
                <input id="OPC_NOME" class="span6" name="OPC_NOME" type="text" value="<?php echo $result->OPC_NOME; ?>" />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_NATUREZA_OPERACAO" class="control-label">Natureza da Operação<span class="required">*</span></label>
            <div class="controls">
                <select name="OPC_NATUREZA_OPERACAO" id="OPC_NATUREZA_OPERACAO" class="span4">
                    <option value="Venda" <?php echo $result->OPC_NATUREZA_OPERACAO == 'Venda' ? 'selected' : ''; ?>>Venda</option>
                    <option value="Compra" <?php echo $result->OPC_NATUREZA_OPERACAO == 'Compra' ? 'selected' : ''; ?>>Compra</option>
                    <option value="Transferencia" <?php echo $result->OPC_NATUREZA_OPERACAO == 'Transferencia' ? 'selected' : ''; ?>>Transferência</option>
                    <option value="Outras" <?php echo $result->OPC_NATUREZA_OPERACAO == 'Outras' ? 'selected' : ''; ?>>Outras</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_TIPO_MOVIMENTO" class="control-label">Tipo de Movimento<span class="required">*</span></label>
            <div class="controls">
                <select name="OPC_TIPO_MOVIMENTO" id="OPC_TIPO_MOVIMENTO" class="span4">
                    <option value="Entrada" <?php echo $result->OPC_TIPO_MOVIMENTO == 'Entrada' ? 'selected' : ''; ?>>Entrada</option>
                    <option value="Saida" <?php echo $result->OPC_TIPO_MOVIMENTO == 'Saida' ? 'selected' : ''; ?>>Saída</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_AFETA_CUSTO" class="control-label">Afeta Custo</label>
            <div class="controls">
                <select name="OPC_AFETA_CUSTO" id="OPC_AFETA_CUSTO" class="span4">
                    <option value="1" <?php echo $result->OPC_AFETA_CUSTO == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->OPC_AFETA_CUSTO == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_FATO_FISCAL" class="control-label">Fato Fiscal</label>
            <div class="controls">
                <select name="OPC_FATO_FISCAL" id="OPC_FATO_FISCAL" class="span4">
                    <option value="1" <?php echo $result->OPC_FATO_FISCAL == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->OPC_FATO_FISCAL == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_GERA_FINANCEIRO" class="control-label">Gera Financeiro</label>
            <div class="controls">
                <select name="OPC_GERA_FINANCEIRO" id="OPC_GERA_FINANCEIRO" class="span4">
                    <option value="1" <?php echo $result->OPC_GERA_FINANCEIRO == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->OPC_GERA_FINANCEIRO == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_MOVIMENTA_ESTOQUE" class="control-label">Movimenta Estoque</label>
            <div class="controls">
                <select name="OPC_MOVIMENTA_ESTOQUE" id="OPC_MOVIMENTA_ESTOQUE" class="span4">
                    <option value="1" <?php echo $result->OPC_MOVIMENTA_ESTOQUE == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->OPC_MOVIMENTA_ESTOQUE == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_SITUACAO" class="control-label">Situação</label>
            <div class="controls">
                <select name="OPC_SITUACAO" id="OPC_SITUACAO" class="span4">
                    <option value="1" <?php echo $result->OPC_SITUACAO == 1 ? 'selected' : ''; ?>>Ativo</option>
                    <option value="0" <?php echo $result->OPC_SITUACAO == 0 ? 'selected' : ''; ?>>Inativo</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_FINALIDADE_NFE" class="control-label">Finalidade NFe<span class="required">*</span></label>
            <div class="controls">
                <select name="OPC_FINALIDADE_NFE" id="OPC_FINALIDADE_NFE" class="span4">
                    <option value="1" <?php echo $result->OPC_FINALIDADE_NFE == 1 ? 'selected' : ''; ?>>Normal</option>
                    <option value="2" <?php echo $result->OPC_FINALIDADE_NFE == 2 ? 'selected' : ''; ?>>Complementar</option>
                    <option value="3" <?php echo $result->OPC_FINALIDADE_NFE == 3 ? 'selected' : ''; ?>>Ajuste</option>
                    <option value="4" <?php echo $result->OPC_FINALIDADE_NFE == 4 ? 'selected' : ''; ?>>Devolução</option>
                </select>
            </div>
        </div>

        <div class="form-actions">
            <div class="span12">
                <div class="span6 offset3" style="display:flex;justify-content: center">
                    <button type="submit" class="button btn btn-success">
                        <span class="button__icon"><i class='bx bx-save'></i></span> <span class="button__text2">Salvar</span>
                    </button>
                    <a href="<?php echo base_url() ?>index.php/operacaocomercial" id="btnAdicionar" class="button btn btn-mini btn-warning">
                        <span class="button__icon"><i class='bx bx-undo'></i></span> <span class="button__text2">Voltar</span>
                    </a>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#formOperacaoComercial').validate({
            rules: {
                OPC_SIGLA: {
                    required: true,
                    minlength: 2
                },
                OPC_NOME: {
                    required: true
                },
                OPC_NATUREZA_OPERACAO: {
                    required: true
                },
                OPC_TIPO_MOVIMENTO: {
                    required: true
                },
                OPC_FINALIDADE_NFE: {
                    required: true
                }
            },
            messages: {
                OPC_SIGLA: {
                    required: "Campo obrigatório",
                    minlength: "Mínimo 2 caracteres"
                },
                OPC_NOME: {
                    required: "Campo obrigatório"
                },
                OPC_NATUREZA_OPERACAO: {
                    required: "Campo obrigatório"
                },
                OPC_TIPO_MOVIMENTO: {
                    required: "Campo obrigatório"
                },
                OPC_FINALIDADE_NFE: {
                    required: "Campo obrigatório"
                }
            },
            errorClass: "help-inline",
            errorElement: "span",
            highlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').addClass('error');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).parents('.control-group').removeClass('error');
                $(element).parents('.control-group').addClass('success');
            }
        });
    });
</script> 