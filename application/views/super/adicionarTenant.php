<div class="new122">
  <div class="widget-title" style="margin:-15px -10px 0">
    <h5>Adicionar Tenant</h5>
  </div>
  <div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
      <span class="icon">
        <i class="icon-plus"></i>
      </span>
      <h5 style="padding: 3px 0"></h5>
    </div>
    <div class="widget-content nopadding tab-content">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url('index.php/super/adicionarTenant') ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="ten_nome" class="control-label">Nome do Tenant<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="ten_nome" name="ten_nome" value="<?= set_value('ten_nome') ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="ten_cnpj" class="control-label">CNPJ</label>
        <div class="controls">
          <input type="text" id="ten_cnpj" name="ten_cnpj" value="<?= set_value('ten_cnpj') ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="ten_email" class="control-label">E-mail</label>
        <div class="controls">
          <input type="email" id="ten_email" name="ten_email" value="<?= set_value('ten_email') ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="ten_telefone" class="control-label">Telefone</label>
        <div class="controls">
          <input type="text" id="ten_telefone" name="ten_telefone" value="<?= set_value('ten_telefone') ?>" />
        </div>
      </div>

      <hr style="margin: 20px 0; border-top: 1px solid #ddd;">
      <h5 style="margin-bottom: 15px;">Usuário Administrador do Tenant</h5>
      <p style="color: #666; font-size: 12px; margin-bottom: 15px;">Criar um usuário administrador para este tenant (opcional)</p>

      <div class="control-group">
        <label for="usuario_nome" class="control-label">Nome do Usuário</label>
        <div class="controls">
          <input type="text" id="usuario_nome" name="usuario_nome" value="<?= set_value('usuario_nome') ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="usuario_email" class="control-label">E-mail do Usuário</label>
        <div class="controls">
          <input type="email" id="usuario_email" name="usuario_email" value="<?= set_value('usuario_email') ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="usuario_senha" class="control-label">Senha do Usuário</label>
        <div class="controls">
          <input type="password" id="usuario_senha" name="usuario_senha" value="<?= set_value('usuario_senha') ?>" />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Adicionar</button>
        <a href="<?= base_url('index.php/super/tenants') ?>" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
</div>

