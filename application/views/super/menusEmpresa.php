<style>
    .form-section { border: 1px solid #e0e0e0; border-radius: 4px; margin-bottom: 20px; background: #fff; }
    .form-section-header { background: #f8f9fa; border-bottom: 1px solid #e0e0e0; padding: 12px 15px; display: flex; align-items: center; gap: 8px; font-weight: 600; color: #333; }
    .form-section-content { padding: 15px; }
</style>

<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-menu"></i></span>
    <h5>Menus permitidos — <?= htmlspecialchars($empresa->emp_razao_social ?? $empresa->emp_nome_fantasia ?? 'Empresa') ?></h5>
  </div>
  <p style="margin: 8px 0 16px; color: #666;">Grupo: <?= htmlspecialchars($grupo->gre_nome) ?></p>

  <form action="<?= base_url("index.php/super/salvarMenusEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" method="post" class="form-horizontal">
    <?php /* Garante que men_id[] seja sempre enviado (array), para não limpar ao salvar quando nenhum checkbox está marcado */ ?>
    <input type="hidden" name="men_id[]" value="" />
    <div class="widget-box">
      <div class="widget-content nopadding tab-content">
        <?php if (!empty($menus)): ?>
        <div class="form-section" style="margin-top: 0;">
          <div class="form-section-header">
            <i class="bx bx-list-ul"></i>
            <span>Selecione os menus que esta empresa terá acesso</span>
          </div>
          <div class="form-section-content">
            <p style="margin-bottom: 12px; color: #666;">Marque os menus que a empresa <strong><?= htmlspecialchars($empresa->emp_razao_social ?? $empresa->emp_nome_fantasia ?? '') ?></strong> poderá acessar no sistema.</p>
            <div class="row-fluid" style="display: flex; flex-wrap: wrap; gap: 8px 20px;">
              <?php
              $menus_empresa = isset($menus_empresa) ? $menus_empresa : [];
              foreach ($menus as $m):
              $checked = in_array((int) $m->men_id, $menus_empresa, true) ? ' checked' : '';
              ?>
              <label class="checkbox inline" style="margin: 0; min-width: 200px;">
                <input type="checkbox" name="men_id[]" value="<?= (int) $m->men_id ?>"<?= $checked ?> />
                <i class="bx <?= htmlspecialchars($m->men_icone ?? 'bx-circle') ?>"></i>
                <?= htmlspecialchars($m->men_nome) ?>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php else: ?>
        <p class="alert alert-info">Nenhum menu cadastrado no sistema. Cadastre os menus primeiro.</p>
        <?php endif; ?>

        <div class="form-actions" style="margin-top: 20px;">
          <div class="span12">
            <div class="span6 offset3" style="display: flex; justify-content: center; gap: 10px;">
              <?php if (!empty($menus)): ?>
              <button type="submit" class="button btn btn-mini btn-success" style="max-width: 160px">
                <span class="button__icon"><i class="bx bx-save"></i></span>
                <span class="button__text2">Salvar menus</span>
              </button>
              <?php endif; ?>
              <a href="<?= base_url("index.php/super/empresas/{$grupo->gre_id}") ?>" class="button btn btn-mini btn-warning">
                <span class="button__icon"><i class="bx bx-undo"></i></span>
                <span class="button__text2">Voltar às empresas</span>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>
