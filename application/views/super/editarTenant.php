<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-edit"></i></span>
    <h5>Editar Tenant</h5>
  </div>
  <div class="widget-content nopadding">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url("index.php/super/editarTenant/{$result->ten_id}") ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="ten_nome" class="control-label">Nome do Tenant<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="ten_nome" name="ten_nome" value="<?= set_value('ten_nome', $result->ten_nome) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="ten_cnpj" class="control-label">CNPJ</label>
        <div class="controls">
          <input type="text" id="ten_cnpj" name="ten_cnpj" value="<?= set_value('ten_cnpj', $result->ten_cnpj) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="ten_email" class="control-label">E-mail</label>
        <div class="controls">
          <input type="email" id="ten_email" name="ten_email" value="<?= set_value('ten_email', $result->ten_email) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="ten_telefone" class="control-label">Telefone</label>
        <div class="controls">
          <input type="text" id="ten_telefone" name="ten_telefone" value="<?= set_value('ten_telefone', $result->ten_telefone) ?>" />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?= base_url('index.php/super/tenants') ?>" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
</div>

