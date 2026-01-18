<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aTipoPessoa')) { ?>
    <a href="<?php echo base_url(); ?>index.php/tipos_pessoa/adicionar" class="btn btn-success">
        <i class="fas fa-plus"></i> Adicionar Tipo de Pessoa
    </a>
<?php } ?>

<div class="span12" style="margin-left: 0">
    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-users"></i>
            </span>
            <h5>Tipos de Pessoa</h5>
            <div class="buttons">
                <a title="Adicionar Novo Tipo de Pessoa" class="btn btn-mini btn-inverse" href="<?php echo base_url() ?>index.php/tiposPessoa/adicionar">
                    <i class="fas fa-plus"></i> Adicionar Novo Tipo de Pessoa
                </a>
            </div>
        </div>
        <div class="widget-content">
            <table id="tabela" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$results) { ?>
                        <tr>
                            <td colspan="4">Nenhum tipo de pessoa encontrado</td>
                        </tr>
                    <?php } ?>
                    <?php foreach ($results as $r) {
                        echo '<tr>';
                        echo '<td>' . $r->nome . '</td>';
                        echo '<td>' . $r->descricao . '</td>';
                        echo '<td>' . ($r->situacao == 1 ? 'Ativo' : 'Inativo') . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTipoPessoa')) {
                            echo '<a href="' . base_url() . 'index.php/tiposPessoa/visualizar/' . $r->idTipoPessoa . '" class="btn btn-info tip-top" title="Ver mais detalhes"><i class="fas fa-eye"></i></a>  ';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eTipoPessoa')) {
                            echo '<a href="' . base_url() . 'index.php/tiposPessoa/editar/' . $r->idTipoPessoa . '" class="btn btn-info tip-top" title="Editar"><i class="fas fa-edit"></i></a>  ';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dTipoPessoa')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" tipo="' . $r->idTipoPessoa . '" class="btn btn-danger tip-top" title="Excluir"><i class="fas fa-trash-alt"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/tiposPessoa/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Tipo de Pessoa</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idTipoPessoa" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este tipo de pessoa?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
                <span class="button__icon"><i class="bx bx-x"></i></span> <span class="button__text2">Cancelar</span>
            </button>
            <button class="button btn btn-danger">
                <span class="button__icon"><i class="bx bx-trash"></i></span> <span class="button__text2">Excluir</span>
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