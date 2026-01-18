<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?= site_url('classificacaofiscal/excluir') ?>" id="formClassificacao" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Classificação Fiscal</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idClassificacao" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta classificação fiscal?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">
                <i class="fas fa-times"></i> Cancelar
            </button>
            <button class="btn btn-success">
                <i class="fas fa-check"></i> Confirmar
            </button>
        </div>
    </form>
</div> 