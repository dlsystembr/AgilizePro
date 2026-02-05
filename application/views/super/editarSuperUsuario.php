<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-edit"></i></span>
    <h5>Editar Super Usuário</h5>
  </div>
  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url("index.php/super/editarSuperUsuario/{$result->uss_id}") ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="uss_nome" class="control-label">Nome<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="uss_nome" name="uss_nome" value="<?= set_value('uss_nome', $result->uss_nome) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_cpf" class="control-label">CPF<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="uss_cpf" name="uss_cpf" value="<?= set_value('uss_cpf', $result->uss_cpf) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_rg" class="control-label">RG</label>
        <div class="controls">
          <input type="text" id="uss_rg" name="uss_rg" value="<?= set_value('uss_rg', $result->uss_rg) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_email" class="control-label">E-mail<span class="required">*</span></label>
        <div class="controls">
          <input type="email" id="uss_email" name="uss_email" value="<?= set_value('uss_email', $result->uss_email) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_senha" class="control-label">Nova Senha (deixe em branco para manter)</label>
        <div class="controls">
          <input type="password" id="uss_senha" name="uss_senha" value="" minlength="6" />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_telefone" class="control-label">Telefone<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="uss_telefone" name="uss_telefone" value="<?= set_value('uss_telefone', $result->uss_telefone) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_celular" class="control-label">Celular</label>
        <div class="controls">
          <input type="text" id="uss_celular" name="uss_celular" value="<?= set_value('uss_celular', $result->uss_celular) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="uss_situacao" class="control-label">Situação</label>
        <div class="controls">
          <select id="uss_situacao" name="uss_situacao">
            <option value="1" <?= set_select('uss_situacao', '1', $result->uss_situacao == 1) ?>>Ativo</option>
            <option value="0" <?= set_select('uss_situacao', '0', $result->uss_situacao == 0) ?>>Inativo</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label for="uss_data_expiracao" class="control-label">Data de Expiração</label>
        <div class="controls">
          <input type="date" id="uss_data_expiracao" name="uss_data_expiracao" value="<?= set_value('uss_data_expiracao', $result->uss_data_expiracao) ?>" />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?= base_url('index.php/super/superUsuarios') ?>" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
</div>

