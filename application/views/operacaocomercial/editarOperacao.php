<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-exchange-alt"></i>
        </span>
        <h5>Editar Operação Comercial</h5>
    </div>
    <div class="widget-content nopadding tab-content">
        <?php echo form_open('operacaocomercial/editar/'.$result->opc_id, 'id="formOperacaoComercial" class="form-horizontal"'); ?>
        
        <div class="control-group">
            <label for="opc_sigla" class="control-label">Sigla<span class="required">*</span></label>
            <div class="controls">
                <input id="opc_sigla" class="span2" name="opc_sigla" type="text" value="<?php echo $result->opc_sigla; ?>" />
                <span class="help-inline">Ex: VENDA, COMPRA, DEV</span>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_nome" class="control-label">Nome<span class="required">*</span></label>
            <div class="controls">
                <input id="opc_nome" class="span6" name="opc_nome" type="text" value="<?php echo $result->opc_nome; ?>" />
            </div>
        </div>

        <div class="control-group">
            <label for="opc_natureza_operacao" class="control-label">Natureza da Operação<span class="required">*</span></label>
            <div class="controls">
                <select name="opc_natureza_operacao" id="opc_natureza_operacao" class="span4">
                    <option value="Venda" <?php echo $result->opc_natureza_operacao == 'Venda' ? 'selected' : ''; ?>>Venda</option>
                    <option value="Compra" <?php echo $result->opc_natureza_operacao == 'Compra' ? 'selected' : ''; ?>>Compra</option>
                    <option value="Transferencia" <?php echo $result->opc_natureza_operacao == 'Transferencia' ? 'selected' : ''; ?>>Transferência</option>
                    <option value="Outras" <?php echo $result->opc_natureza_operacao == 'Outras' ? 'selected' : ''; ?>>Outras</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_tipo_movimento" class="control-label">Tipo de Movimento<span class="required">*</span></label>
            <div class="controls">
                <select name="opc_tipo_movimento" id="opc_tipo_movimento" class="span4">
                    <option value="Entrada" <?php echo $result->opc_tipo_movimento == 'Entrada' ? 'selected' : ''; ?>>Entrada</option>
                    <option value="Saida" <?php echo $result->opc_tipo_movimento == 'Saida' ? 'selected' : ''; ?>>Saída</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_afeta_custo" class="control-label">Afeta Custo</label>
            <div class="controls">
                <select name="opc_afeta_custo" id="opc_afeta_custo" class="span4">
                    <option value="1" <?php echo $result->opc_afeta_custo == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->opc_afeta_custo == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_fato_fiscal" class="control-label">Fato Fiscal</label>
            <div class="controls">
                <select name="opc_fato_fiscal" id="opc_fato_fiscal" class="span4">
                    <option value="1" <?php echo $result->opc_fato_fiscal == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->opc_fato_fiscal == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_gera_financeiro" class="control-label">Gera Financeiro</label>
            <div class="controls">
                <select name="opc_gera_financeiro" id="opc_gera_financeiro" class="span4">
                    <option value="1" <?php echo $result->opc_gera_financeiro == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->opc_gera_financeiro == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_movimenta_estoque" class="control-label">Movimenta Estoque</label>
            <div class="controls">
                <select name="opc_movimenta_estoque" id="opc_movimenta_estoque" class="span4">
                    <option value="1" <?php echo $result->opc_movimenta_estoque == 1 ? 'selected' : ''; ?>>Sim</option>
                    <option value="0" <?php echo $result->opc_movimenta_estoque == 0 ? 'selected' : ''; ?>>Não</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_situacao" class="control-label">Situação</label>
            <div class="controls">
                <select name="opc_situacao" id="opc_situacao" class="span4">
                    <option value="1" <?php echo $result->opc_situacao == 1 ? 'selected' : ''; ?>>Ativo</option>
                    <option value="0" <?php echo $result->opc_situacao == 0 ? 'selected' : ''; ?>>Inativo</option>
                </select>
            </div>
        </div>

        <div class="control-group">
            <label for="opc_finalidade_nfe" class="control-label">Finalidade NFe<span class="required">*</span></label>
            <div class="controls">
                <select name="opc_finalidade_nfe" id="opc_finalidade_nfe" class="span4">
                    <option value="1" <?php echo $result->opc_finalidade_nfe == 1 ? 'selected' : ''; ?>>Normal</option>
                    <option value="2" <?php echo $result->opc_finalidade_nfe == 2 ? 'selected' : ''; ?>>Complementar</option>
                    <option value="3" <?php echo $result->opc_finalidade_nfe == 3 ? 'selected' : ''; ?>>Ajuste</option>
                    <option value="4" <?php echo $result->opc_finalidade_nfe == 4 ? 'selected' : ''; ?>>Devolução</option>
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
                opc_sigla: {
                    required: true,
                    minlength: 2
                },
                opc_nome: {
                    required: true
                },
                opc_natureza_operacao: {
                    required: true
                },
                opc_tipo_movimento: {
                    required: true
                },
                opc_finalidade_nfe: {
                    required: true
                }
            },
            messages: {
                opc_sigla: {
                    required: "Campo obrigatório",
                    minlength: "Mínimo 2 caracteres"
                },
                opc_nome: {
                    required: "Campo obrigatório"
                },
                opc_natureza_operacao: {
                    required: "Campo obrigatório"
                },
                opc_tipo_movimento: {
                    required: "Campo obrigatório"
                },
                opc_finalidade_nfe: {
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