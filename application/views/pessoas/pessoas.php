<style>
    select {
        width: 70px;
    }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-users"></i>
        </span>
        <h5>Pessoas</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aPessoa')) { ?>
            <div class="span3">
                <a href="<?= base_url() ?>index.php/pessoas/adicionar" class="button btn btn-mini btn-success"
                    style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                        Nova Pessoa
                    </span>
                </a>
            </div>
        <?php } ?>
        <form class="span9" method="get" action="<?= base_url() ?>index.php/pessoas"
            style="display: flex; justify-content: flex-end;">
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa"
                    placeholder="Buscar por Nome, Razão, CPF/CNPJ ou Código..." class="span12"
                    value="<?= $this->input->get('pesquisa') ?>">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span></button>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>CPF/CNPJ</th>
                        <th>Razão Social</th>
                        <th>Tipo</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                    <td colspan="7">Nenhuma Pessoa Cadastrada</td>
                  </tr>';
                    }
                    foreach ($results as $r) {
                        echo '<tr>';
                        echo '<td>' . ($r->pes_codigo ?: '-') . '</td>';
                        echo '<td>' . $r->pes_nome . '</td>';
                        echo '<td>' . $r->pes_cpfcnpj . '</td>';
                        echo '<td>' . ($r->pes_razao_social ?: '-') . '</td>';
                        echo '<td>' . ($r->pes_fisico_juridico === 'F' ? 'Física' : 'Jurídica') . '</td>';
                        echo '<td>' . ((int)$r->pes_situacao === 1 ? '<span class="label label-success">Ativo</span>' : '<span class="label">Inativo</span>') . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPessoa')) {
                            echo '<a href="' . base_url() . 'index.php/pessoas/visualizar/' . $r->pes_id . '" style="margin-right: 1%" class="btn-nwe" title="Visualizar Pessoa"><i class="bx bx-show bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'ePessoa')) {
                            echo '<a href="' . base_url() . 'index.php/pessoas/editar/' . $r->pes_id . '" style="margin-right: 1%" class="btn-nwe3" title="Editar Pessoa"><i class="bx bx-edit bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dPessoa')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" pessoa="' . $r->pes_id . '" style="margin-right: 1%" class="btn-nwe4" title="Excluir Pessoa"><i class="bx bx-trash-alt bx-xs"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/pessoas/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Pessoa</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idPessoa" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta pessoa?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i
                        class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span
                    class="button__text2">Excluir</span></button>
        </div>
    </form>
    
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', 'a', function (event) {
            var pessoa = $(this).attr('pessoa');
            $('#idPessoa').val(pessoa);
        });
    });
</script>