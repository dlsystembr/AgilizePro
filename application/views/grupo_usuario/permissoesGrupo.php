<style>
    .perm-table { margin-bottom: 1.5rem; }
    .perm-table th, .perm-table td { padding: 8px 12px; vertical-align: middle; }
    .perm-table th { background: #f5f5f5; }
    .perm-table input[type="checkbox"] { margin: 0 4px; }
    .perm-label { white-space: nowrap; font-weight: normal; margin-right: 10px; }
    .perm-section { margin-bottom: 2rem; }
    .perm-section h6 { margin: 0 0 10px 0; color: #333; }
</style>

<div class="new122">
    <div class="widget-title" style="margin: -15px -10px 0">
        <span class="icon"><i class="fas fa-shield-alt"></i></span>
        <h5>Permissões do grupo: <?php echo htmlspecialchars($grupo->gpu_nome); ?></h5>
    </div>
    <a href="<?php echo site_url('gruposUsuario/gerenciar'); ?>" class="button btn btn-mini btn-warning" style="max-width: 120px">
        <span class="button__icon"><i class="bx bx-undo"></i></span>
        <span class="button__text2">Voltar</span>
    </a>

    <?php
    $menus_cadastro = isset($menus_cadastro) ? $menus_cadastro : [];
    $menus_relatorio = isset($menus_relatorio) ? $menus_relatorio : [];
    $tem_menus = !empty($menus_cadastro) || !empty($menus_relatorio);
    ?>
    <?php if (!$tem_menus): ?>
        <div class="alert alert-warning" style="margin-top: 15px;">
            Nenhum menu liberado para esta empresa. Configure os menus da empresa em <strong>Super &gt; Empresas &gt; Menus da empresa</strong>.
        </div>
    <?php else: ?>
        <form action="<?php echo site_url('gruposUsuario/salvarPermissoes'); ?>" method="post" id="formPermissoes">
            <input type="hidden" name="gpu_id" value="<?php echo (int) $grupo->gpu_id; ?>" />

            <?php /* ========== MENUS DE CADASTRO (CRUD) ========== */ ?>
            <?php if (!empty($menus_cadastro)): ?>
            <div class="perm-section widget-box">
                <div class="widget-title" style="margin: 10px 0 0">
                    <span class="icon"><i class="bx bx-folder"></i></span>
                    <h5>Menus de cadastro (Visualizar, Editar, Deletar, Alterar)</h5>
                </div>
                <div class="widget-content">
                    <p style="margin-bottom: 10px;">
                        <button type="button" class="button btn btn-mini btn-info btn-marcar-cadastro">
                            <span class="button__icon"><i class="bx bx-check-square"></i></span>
                            <span class="button__text2">Marcar todos</span>
                        </button>
                        <button type="button" class="button btn btn-mini btn-default btn-desmarcar-cadastro" style="margin-left: 6px;">
                            <span class="button__icon"><i class="bx bx-square"></i></span>
                            <span class="button__text2">Desmarcar todos</span>
                        </button>
                    </p>
                    <table class="table table-bordered perm-table perm-table-cadastro">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th><label class="perm-label"><input type="checkbox" class="perm-col-toggle perm-cadastro" data-col="visualizar" /> Visualizar</label></th>
                                <th><label class="perm-label"><input type="checkbox" class="perm-col-toggle perm-cadastro" data-col="editar" /> Editar</label></th>
                                <th><label class="perm-label"><input type="checkbox" class="perm-col-toggle perm-cadastro" data-col="deletar" /> Deletar</label></th>
                                <th><label class="perm-label"><input type="checkbox" class="perm-col-toggle perm-cadastro" data-col="alterar" /> Alterar</label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menus_cadastro as $me): ?>
                                <?php
                                $mep_id = (int) $me->mep_id;
                                $pa = isset($permissoes_atuais[$mep_id]) ? $permissoes_atuais[$mep_id] : [
                                    'gup_visualizar' => 0, 'gup_editar' => 0, 'gup_deletar' => 0, 'gup_alterar' => 0, 'gup_relatorio' => 0
                                ];
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($me->men_nome); ?></strong></td>
                                    <td><label class="perm-label"><input type="checkbox" name="perm[<?php echo $mep_id; ?>][visualizar]" value="1" class="perm-checkbox perm-cadastro perm-visualizar" <?php echo $pa['gup_visualizar'] ? 'checked="checked"' : ''; ?> /></label></td>
                                    <td><label class="perm-label"><input type="checkbox" name="perm[<?php echo $mep_id; ?>][editar]" value="1" class="perm-checkbox perm-cadastro perm-editar" <?php echo $pa['gup_editar'] ? 'checked="checked"' : ''; ?> /></label></td>
                                    <td><label class="perm-label"><input type="checkbox" name="perm[<?php echo $mep_id; ?>][deletar]" value="1" class="perm-checkbox perm-cadastro perm-deletar" <?php echo $pa['gup_deletar'] ? 'checked="checked"' : ''; ?> /></label></td>
                                    <td><label class="perm-label"><input type="checkbox" name="perm[<?php echo $mep_id; ?>][alterar]" value="1" class="perm-checkbox perm-cadastro perm-alterar" <?php echo $pa['gup_alterar'] ? 'checked="checked"' : ''; ?> /></label></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <?php /* ========== RELATÓRIOS (apenas Relatório) ========== */ ?>
            <?php if (!empty($menus_relatorio)): ?>
            <div class="perm-section widget-box">
                <div class="widget-title" style="margin: 10px 0 0">
                    <span class="icon"><i class="bx bx-pie-chart-alt-2"></i></span>
                    <h5>Relatórios (acesso ao relatório)</h5>
                </div>
                <div class="widget-content">
                    <p style="margin-bottom: 10px;">
                        <button type="button" class="button btn btn-mini btn-info btn-marcar-relatorio">
                            <span class="button__icon"><i class="bx bx-check-square"></i></span>
                            <span class="button__text2">Marcar todos</span>
                        </button>
                        <button type="button" class="button btn btn-mini btn-default btn-desmarcar-relatorio" style="margin-left: 6px;">
                            <span class="button__icon"><i class="bx bx-square"></i></span>
                            <span class="button__text2">Desmarcar todos</span>
                        </button>
                    </p>
                    <table class="table table-bordered perm-table perm-table-relatorio">
                        <thead>
                            <tr>
                                <th>Relatório</th>
                                <th><label class="perm-label"><input type="checkbox" id="permColRelatorioAll" class="perm-col-toggle perm-relatorio-col" data-col="relatorio" /> Acesso</label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($menus_relatorio as $me): ?>
                                <?php
                                $mep_id = (int) $me->mep_id;
                                $pa = isset($permissoes_atuais[$mep_id]) ? $permissoes_atuais[$mep_id] : ['gup_relatorio' => 0];
                                ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($me->men_nome); ?></strong></td>
                                    <td><label class="perm-label"><input type="checkbox" name="perm[<?php echo $mep_id; ?>][relatorio]" value="1" class="perm-checkbox perm-relatorio-only perm-relatorio" <?php echo !empty($pa['gup_relatorio']) ? 'checked="checked"' : ''; ?> /></label></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="button btn btn-success">
                    <span class="button__icon"><i class="bx bx-save"></i></span>
                    <span class="button__text2">Salvar permissões</span>
                </button>
                <a href="<?php echo site_url('gruposUsuario/gerenciar'); ?>" class="button btn btn-warning">
                    <span class="button__icon"><i class="bx bx-undo"></i></span>
                    <span class="button__text2">Voltar</span>
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script type="text/javascript">
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('formPermissoes');
        if (!form) return;

        // Marcar/Desmarcar todos - Cadastro
        form.querySelectorAll('.btn-marcar-cadastro').forEach(function(btn) {
            btn.addEventListener('click', function() {
                form.querySelectorAll('.perm-table-cadastro input.perm-checkbox').forEach(function(c) { c.checked = true; });
                form.querySelectorAll('.perm-table-cadastro .perm-col-toggle').forEach(function(c) { c.checked = true; });
            });
        });
        form.querySelectorAll('.btn-desmarcar-cadastro').forEach(function(btn) {
            btn.addEventListener('click', function() {
                form.querySelectorAll('.perm-table-cadastro input.perm-checkbox').forEach(function(c) { c.checked = false; });
                form.querySelectorAll('.perm-table-cadastro .perm-col-toggle').forEach(function(c) { c.checked = false; });
            });
        });

        // Marcar/Desmarcar todos - Relatório
        form.querySelectorAll('.btn-marcar-relatorio').forEach(function(btn) {
            btn.addEventListener('click', function() {
                form.querySelectorAll('.perm-table-relatorio input.perm-checkbox').forEach(function(c) { c.checked = true; });
                var h = form.querySelector('#permColRelatorioAll'); if (h) h.checked = true;
            });
        });
        form.querySelectorAll('.btn-desmarcar-relatorio').forEach(function(btn) {
            btn.addEventListener('click', function() {
                form.querySelectorAll('.perm-table-relatorio input.perm-checkbox').forEach(function(c) { c.checked = false; });
                var h = form.querySelector('#permColRelatorioAll'); if (h) h.checked = false;
            });
        });

        // Cabeçalho: marcar/desmarcar coluna - Cadastro
        form.querySelectorAll('.perm-table-cadastro .perm-col-toggle').forEach(function(headerCheck) {
            headerCheck.addEventListener('change', function() {
                var col = this.getAttribute('data-col');
                var box = this.closest('.perm-table-cadastro');
                if (!box) return;
                box.querySelectorAll('input.perm-' + col).forEach(function(c) { c.checked = headerCheck.checked; });
            });
        });

        // Cabeçalho: marcar/desmarcar coluna - Relatório
        var permColRelatorioAll = form.querySelector('#permColRelatorioAll');
        if (permColRelatorioAll) {
            permColRelatorioAll.addEventListener('change', function() {
                form.querySelectorAll('.perm-table-relatorio input.perm-relatorio').forEach(function(c) { c.checked = this.checked; }.bind(this));
            });
        }

        // Atualizar checkbox do cabeçalho quando mudar os da coluna - Cadastro
        form.querySelectorAll('.perm-table-cadastro input.perm-checkbox').forEach(function(box) {
            box.addEventListener('change', function() {
                var name = this.name;
                var col = name.indexOf('[visualizar]') !== -1 ? 'visualizar' : name.indexOf('[editar]') !== -1 ? 'editar' : name.indexOf('[deletar]') !== -1 ? 'deletar' : 'alterar';
                var table = this.closest('.perm-table-cadastro');
                if (!table) return;
                var colBoxes = table.querySelectorAll('input.perm-' + col);
                var allChecked = true;
                for (var i = 0; i < colBoxes.length; i++) { if (!colBoxes[i].checked) { allChecked = false; break; } }
                var header = table.querySelector('.perm-col-toggle[data-col="' + col + '"]');
                if (header) header.checked = allChecked;
            });
        });

        // Atualizar checkbox do cabeçalho - Relatório
        form.querySelectorAll('.perm-table-relatorio input.perm-relatorio').forEach(function(box) {
            box.addEventListener('change', function() {
                var table = this.closest('.perm-table-relatorio');
                if (!table) return;
                var colBoxes = table.querySelectorAll('input.perm-relatorio');
                var allChecked = true;
                for (var i = 0; i < colBoxes.length; i++) { if (!colBoxes[i].checked) { allChecked = false; break; } }
                var header = form.querySelector('#permColRelatorioAll');
                if (header) header.checked = allChecked;
            });
        });
    });
})();
</script>
