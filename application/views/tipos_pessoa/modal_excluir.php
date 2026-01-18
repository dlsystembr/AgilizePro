<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/tipos_pessoa/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Tipo de Pessoa</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idTipoPessoa" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este tipo de pessoa?</h5>
            <p style="text-align: center; color: #d9534f;">
                <strong>Atenção!</strong> Esta ação não pode ser desfeita.
            </p>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class='bx bx-x'></i></span> <span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger">
                <span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span>
            </button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a', function(event) {
            var tipo = $(this).attr('tipo');
            $('#idTipoPessoa').val(tipo);
        });
    });
</script> 