<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-edit"></i></span>
    <h5>Editar Grupo Empresarial</h5>
  </div>
  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url("index.php/super/editarGrupoEmpresarial/{$result->gre_id}") ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="gre_nome" class="control-label">Nome do Grupo Empresarial<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="gre_nome" name="gre_nome" value="<?= set_value('gre_nome', $result->gre_nome) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="gre_situacao" class="control-label">Situação</label>
        <div class="controls">
          <label class="checkbox inline">
            <input type="checkbox" id="gre_situacao" name="gre_situacao" value="1" <?= (isset($result->gre_situacao) && $result->gre_situacao == 1) ? 'checked' : '' ?> />
            Ativo
          </label>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?= base_url('index.php/super/gruposEmpresariais') ?>" class="btn">Cancelar</a>
      </div>
    </form>
    </div>
  </div>
</div>
