<?php if (isset($custom_error) && $custom_error != '') {
    echo $custom_error;
} ?>
<div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-invoice-dollar"></i>
        </span>
        <h5>Visualizar Classificação Fiscal</h5>
    </div>
    <div class="widget-content nopadding tab-content form-horizontal">
        <div class="control-group">
            <label for="operacao_comercial" class="control-label">Operação Comercial</label>
            <div class="controls">
                <input type="text" class="span6" value="<?php echo $result->nome_operacao; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="natureza_contribuinte" class="control-label">Natureza do Contribuinte</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->natureza_contribuinte; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="cfop" class="control-label">CFOP</label>
            <div class="controls">
                <input type="text" class="span2" value="<?php echo $result->cfop; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="destinacao" class="control-label">Destinação</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->destinacao; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="objetivo_comercial" class="control-label">Objetivo Comercial</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->objetivo_comercial; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="tipo_icms" class="control-label">Tipo ICMS</label>
            <div class="controls">
                <input type="text" class="span4" value="<?php echo $result->tipo_icms; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="mensagem_fiscal" class="control-label">Mensagem Fiscal</label>
            <div class="controls">
                <textarea class="span12" rows="3" readonly><?php echo $result->mensagem_fiscal; ?></textarea>
            </div>
        </div>

        <?php if ($regime_tributario === 'Simples Nacional') { ?>
            <div class="control-group">
                <label for="csosn" class="control-label">CSOSN</label>
                <div class="controls">
                    <input type="text" class="span2" value="<?php echo $result->csosn; ?>" readonly />
                </div>
            </div>
        <?php } else { ?>
            <div class="control-group">
                <label for="cst" class="control-label">CST</label>
                <div class="controls">
                    <input type="text" class="span2" value="<?php echo $result->cst; ?>" readonly />
                </div>
            </div>
        <?php } ?>

        <div class="control-group">
            <label for="cClassTrib" class="control-label">Classe Tributária</label>
            <div class="controls">
                <input type="text" class="span3" value="<?php echo $result->cClassTrib ?: 'Não informado'; ?>"
                    readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="cst_ibs" class="control-label">CST IBS</label>
            <div class="controls">
                <input type="text" class="span2" value="<?php echo $result->cst_ibs ?: 'Não informado'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="aliq_ibs" class="control-label">Alíquota IBS (%)</label>
            <div class="controls">
                <input type="text" class="span2"
                    value="<?php echo $result->aliq_ibs ? $result->aliq_ibs . '%' : 'Não informado'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="cst_cbs" class="control-label">CST CBS</label>
            <div class="controls">
                <input type="text" class="span2" value="<?php echo $result->cst_cbs ?: 'Não informado'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="aliq_cbs" class="control-label">Alíquota CBS (%)</label>
            <div class="controls">
                <input type="text" class="span2"
                    value="<?php echo $result->aliq_cbs ? $result->aliq_cbs . '%' : 'Não informado'; ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="created_at" class="control-label">Data de Criação</label>
            <div class="controls">
                <input type="text" class="span4"
                    value="<?php echo date('d/m/Y H:i:s', strtotime($result->created_at)); ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="updated_at" class="control-label">Última Atualização</label>
            <div class="controls">
                <input type="text" class="span4"
                    value="<?php echo date('d/m/Y H:i:s', strtotime($result->updated_at)); ?>" readonly />
            </div>
        </div>

        <div class="control-group">
            <label for="situacao" class="control-label">Situação</label>
            <div class="controls">
                <span class="badge <?php echo $result->situacao == 1 ? 'badge-success' : 'badge-important'; ?>">
                    <?php echo $result->situacao == 1 ? 'Ativa' : 'Inativa'; ?>
                </span>
            </div>
        </div>

        <div class="form-actions">
            <div class="span12">
                <div class="span6 offset3" style="display:flex;justify-content: center">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eClassificacaoFiscal')) { ?>
                        <a href="<?php echo base_url() ?>index.php/classificacaoFiscal/editar/<?php echo $result->id; ?>"
                            class="button btn btn-success">
                            <span class="button__icon"><i class="bx bx-edit-alt"></i></span>
                            <span class="button__text2">Editar</span>
                        </a>
                    <?php } ?>
                    <a href="<?php echo base_url() ?>index.php/classificacaoFiscal"
                        class="button btn btn-mini btn-warning">
                        <span class="button__icon"><i class="bx bx-arrow-back"></i></span>
                        <span class="button__text2">Voltar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>