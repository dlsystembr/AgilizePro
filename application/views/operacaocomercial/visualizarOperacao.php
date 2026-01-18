<?php if (isset($custom_error) && $custom_error != '') { echo $custom_error; } ?>
<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-exchange-alt"></i>
        </span>
        <h5>Visualizar Operação Comercial</h5>
    </div>
    <div class="widget-content nopadding tab-content form-horizontal">
        <div class="control-group">
            <label for="OPC_SIGLA" class="control-label">Sigla</label>
            <div class="controls">
                <input type="text" class="span2" value="<?php echo $result->OPC_SIGLA; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_NOME" class="control-label">Nome</label>
            <div class="controls">
                <input type="text" class="span6" value="<?php echo $result->OPC_NOME; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_NATUREZA_OPERACAO" class="control-label">Natureza da Operação</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_NATUREZA_OPERACAO; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_TIPO_MOVIMENTO" class="control-label">Tipo de Movimento</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_TIPO_MOVIMENTO; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_AFETA_CUSTO" class="control-label">Afeta Custo</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_AFETA_CUSTO ? 'Sim' : 'Não'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_FATO_FISCAL" class="control-label">Fato Fiscal</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_FATO_FISCAL ? 'Sim' : 'Não'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_GERA_FINANCEIRO" class="control-label">Gera Financeiro</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_GERA_FINANCEIRO ? 'Sim' : 'Não'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_MOVIMENTA_ESTOQUE" class="control-label">Movimenta Estoque</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_MOVIMENTA_ESTOQUE ? 'Sim' : 'Não'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_SITUACAO" class="control-label">Situação</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->OPC_SITUACAO ? 'Ativo' : 'Inativo'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="OPC_FINALIDADE_NFE" class="control-label">Finalidade NFe</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php 
                    switch($result->OPC_FINALIDADE_NFE) {
                        case 1: echo 'Normal'; break;
                        case 2: echo 'Complementar'; break;
                        case 3: echo 'Ajuste'; break;
                        case 4: echo 'Devolução'; break;
                        default: echo 'Não definido';
                    }
                ?>" readonly />
            </div>
        </div>

        <div class="form-actions">
            <div class="span12">
                <div class="span6 offset3" style="display:flex;justify-content: center">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eOperacaoComercial')) { ?>
                        <a href="<?php echo base_url() ?>index.php/operacaocomercial/editar/<?php echo $result->OPC_ID; ?>" class="button btn btn-success">
                            <span class="button__icon"><i class="fas fa-edit"></i></span>
                            <span class="button__text2">Editar</span>
                        </a>
                    <?php } ?>
                    <a href="<?php echo base_url() ?>index.php/operacaocomercial" class="button btn btn-mini btn-warning">
                        <span class="button__icon"><i class="fas fa-arrow-left"></i></span>
                        <span class="button__text2">Voltar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 