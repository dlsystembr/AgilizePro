<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-lock"></i></span>
    <h5>Permissões de Menu - Tenant: <?= $tenant->ten_nome ?></h5>
  </div>
  <div class="widget-content nopadding">
    <form action="<?= base_url("index.php/super/permissoesMenu/{$tenant->ten_id}") ?>" method="post" class="form-horizontal">
      <div style="padding: 20px;">
        <p>Selecione quais permissões de menu este tenant terá acesso:</p>
        
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Permissão</th>
              <th>Ativo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($menus as $codigo => $nome): ?>
              <?php
              $checked = isset($permissoes_configuradas[$codigo]) && $permissoes_configuradas[$codigo] == 1;
              ?>
              <tr>
                <td><?= $nome ?></td>
                <td>
                  <input type="checkbox" name="permissoes[<?= $codigo ?>][<?= $codigo ?>]" value="1" <?= $checked ? 'checked' : '' ?> />
                  <input type="hidden" name="permissoes[<?= $codigo ?>][<?= $codigo ?>]" value="0" />
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

        <div class="form-actions" style="margin-top: 20px;">
          <button type="submit" class="btn btn-success">Salvar Permissões</button>
          <a href="<?= base_url("index.php/super/tenants") ?>" class="btn">Cancelar</a>
        </div>
      </div>
    </form>
  </div>
</div>

