<div class="span12" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-users"></i>
            </span>
            <h5>Visualizar Tipo de Pessoa</h5>
        </div>
        <div class="widget-content">
            <div class="span6">
                <label for="nome">Nome</label>
                <input type="text" class="span12" value="<?php echo $result->nome; ?>" readonly />
            </div>
            <div class="span6">
                <label for="descricao">Descrição</label>
                <input type="text" class="span12" value="<?php echo $result->descricao; ?>" readonly />
            </div>
            <div class="span6">
                <label for="situacao">Situação</label>
                <input type="text" class="span12" value="<?php echo $result->situacao == 1 ? 'Ativo' : 'Inativo'; ?>" readonly />
            </div>
            <div class="span12" style="padding: 1%;">
                <div class="span6 offset3" style="display:flex;justify-content: center">
                    <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eTipoPessoa')) { ?>
                        <a href="<?php echo base_url() ?>index.php/tiposPessoa/editar/<?php echo $result->idTipoPessoa; ?>" class="button btn btn-info">
                            <span class="button__icon"><i class='bx bx-edit'></i></span> <span class="button__text2">Editar</span>
                        </a>
                    <?php } ?>
                    <a href="<?php echo base_url() ?>index.php/tiposPessoa" class="button btn btn-mini btn-warning">
                        <span class="button__icon"><i class="bx bx-undo"></i></span> <span class="button__text2">Voltar</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div> 