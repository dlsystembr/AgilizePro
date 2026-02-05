<style>
    .situacao-ativo { background-color: #00cd00; color: white; }
    .situacao-inativo { background-color: #ff0000; color: white; }
    .acoes-grupos { white-space: nowrap; }
    .acoes-grupos a { display: inline-block; margin-right: 6px; vertical-align: middle; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -15px -10px 0">
        <span class="icon"><i class="fas fa-users-cog"></i></span>
        <h5>Grupos de Usuário</h5>
    </div>
    <a href="<?php echo base_url('index.php/gruposUsuario/adicionar'); ?>" class="button btn btn-success" style="max-width: 180px">
        <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
        <span class="button__text2">Adicionar Grupo</span>
    </a>

    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon"><i class="bx bx-list-ul"></i></span>
            <h5 style="padding: 3px 0"></h5>
        </div>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Situação</th>
                        <th>Data cadastro</th>
                        <th width="160">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="6">Nenhum grupo de usuário cadastrado para esta empresa.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td><?php echo (int) $r->gpu_id; ?></td>
                                <td><?php echo htmlspecialchars($r->gpu_nome); ?></td>
                                <td><?php echo $r->gpu_descricao ? htmlspecialchars($r->gpu_descricao) : '—'; ?></td>
                                <?php
                                $situacao = (int) $r->gpu_situacao === 1 ? 'Ativo' : 'Inativo';
                                $situacaoClasse = (int) $r->gpu_situacao === 1 ? 'situacao-ativo' : 'situacao-inativo';
                                ?>
                                <td><span class="badge <?php echo $situacaoClasse; ?>"><?php echo $situacao; ?></span></td>
                                <td><?php echo $r->gpu_data_cadastro ? date('d/m/Y H:i', strtotime($r->gpu_data_cadastro)) : '—'; ?></td>
                                <td class="acoes-grupos">
                                    <a href="<?php echo base_url('index.php/gruposUsuario/permissoes/' . $r->gpu_id); ?>" class="btn-nwe3" title="Permissões"><i class="bx bx-shield"></i></a>
                                    <a href="<?php echo base_url('index.php/gruposUsuario/usuarios/' . $r->gpu_id); ?>" class="btn-nwe3" title="Usuários do grupo"><i class="bx bx-user"></i></a>
                                    <a href="<?php echo base_url('index.php/gruposUsuario/editar/' . $r->gpu_id); ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                                    <a href="#modal-excluir" role="button" data-toggle="modal" data-gpu-id="<?php echo $r->gpu_id; ?>" class="btn-nwe4" title="Excluir"><i class="bx bx-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php echo $this->pagination->create_links(); ?>

<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url('index.php/gruposUsuario/excluir'); ?>" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Grupo de Usuário</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idExcluir" name="id" value="" />
            <p style="text-align: center">Deseja realmente excluir este grupo? Usuários vinculados a ele na empresa deixarão de ter esse grupo.</p>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button type="button" class="button btn btn-warning" data-dismiss="modal"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button type="submit" class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span><span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $(document).on('click', 'a[data-gpu-id]', function() {
            $('#idExcluir').val($(this).data('gpu-id'));
        });
    });
</script>
