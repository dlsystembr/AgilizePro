<style>
    .usu-table input[type="checkbox"] { margin: 0 6px; vertical-align: middle; }
    .usu-table th, .usu-table td { padding: 8px 12px; vertical-align: middle; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -15px -10px 0">
        <span class="icon"><i class="bx bx-user"></i></span>
        <h5>Usuários do grupo: <?php echo htmlspecialchars($grupo->gpu_nome); ?></h5>
    </div>
    <a href="<?php echo site_url('gruposUsuario/gerenciar'); ?>" class="button btn btn-mini btn-warning" style="max-width: 120px">
        <span class="button__icon"><i class="bx bx-undo"></i></span>
        <span class="button__text2">Voltar</span>
    </a>

    <?php
    $usuarios_todos = isset($usuarios_todos) ? $usuarios_todos : [];
    $ids_no_grupo = isset($ids_no_grupo) ? $ids_no_grupo : [];
    ?>
    <?php if (empty($usuarios_todos)): ?>
        <div class="alert alert-warning" style="margin-top: 15px;">
            Nenhum usuário do mesmo grupo empresarial encontrado para vincular.
        </div>
    <?php else: ?>
        <form action="<?php echo site_url('gruposUsuario/salvarUsuariosGrupo'); ?>" method="post" id="formUsuariosGrupo">
            <input type="hidden" name="gpu_id" value="<?php echo (int) $grupo->gpu_id; ?>" />
            <div class="widget-box" style="margin-top: 15px;">
                <div class="widget-title" style="margin: 10px 0 0">
                    <span class="icon"><i class="bx bx-list-check"></i></span>
                    <h5>Marque os usuários que devem pertencer a este grupo (empresa atual)</h5>
                </div>
                <div class="widget-content">
                    <p style="margin-bottom: 10px;">
                        <button type="button" id="btnMarcarTodosUsu" class="button btn btn-mini btn-info">
                            <span class="button__icon"><i class="bx bx-check-square"></i></span>
                            <span class="button__text2">Marcar todos</span>
                        </button>
                        <button type="button" id="btnDesmarcarTodosUsu" class="button btn btn-mini btn-default" style="margin-left: 6px;">
                            <span class="button__icon"><i class="bx bx-square"></i></span>
                            <span class="button__text2">Desmarcar todos</span>
                        </button>
                    </p>
                    <table class="table table-bordered usu-table">
                        <thead>
                            <tr>
                                <th width="50"><label class="perm-label"><input type="checkbox" id="usuColToggle" /> Incluir</label></th>
                                <th>#</th>
                                <th>Nome</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios_todos as $u): ?>
                                <?php $no_grupo = isset($ids_no_grupo[(int) $u->usu_id]); ?>
                                <tr>
                                    <td>
                                        <label style="margin: 0;">
                                            <input type="checkbox" name="usu_ids[]" value="<?php echo (int) $u->usu_id; ?>" class="usu-check" <?php echo $no_grupo ? 'checked="checked"' : ''; ?> />
                                        </label>
                                    </td>
                                    <td><?php echo (int) $u->usu_id; ?></td>
                                    <td><?php echo htmlspecialchars($u->usu_nome); ?></td>
                                    <td><?php echo htmlspecialchars($u->usu_email); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="form-actions" style="margin-top: 15px;">
                        <button type="submit" class="button btn btn-success">
                            <span class="button__icon"><i class="bx bx-save"></i></span>
                            <span class="button__text2">Salvar vínculos</span>
                        </button>
                        <a href="<?php echo site_url('gruposUsuario/gerenciar'); ?>" class="button btn btn-warning">
                            <span class="button__icon"><i class="bx bx-undo"></i></span>
                            <span class="button__text2">Voltar</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script type="text/javascript">
$(document).ready(function() {
    var form = document.getElementById('formUsuariosGrupo');
    if (!form) return;

    $('#btnMarcarTodosUsu').on('click', function() {
        form.querySelectorAll('input.usu-check').forEach(function(c) { c.checked = true; });
        $('#usuColToggle').prop('checked', true);
    });
    $('#btnDesmarcarTodosUsu').on('click', function() {
        form.querySelectorAll('input.usu-check').forEach(function(c) { c.checked = false; });
        $('#usuColToggle').prop('checked', false);
    });

    $('#usuColToggle').on('change', function() {
        var checked = this.checked;
        form.querySelectorAll('input.usu-check').forEach(function(c) { c.checked = checked; });
    });

    form.querySelectorAll('input.usu-check').forEach(function(box) {
        box.addEventListener('change', function() {
            var all = form.querySelectorAll('input.usu-check');
            var allChecked = true;
            for (var i = 0; i < all.length; i++) { if (!all[i].checked) { allChecked = false; break; } }
            document.getElementById('usuColToggle').checked = allChecked;
        });
    });
});
</script>
