<div class="new122">
  <div class="widget-title" style="margin:-15px -10px 0">
    <h5>Editar Super Usuário</h5>
  </div>
  <div class="widget-box">
    <div class="widget-title" style="margin: -20px 0 0">
      <span class="icon">
        <i class="icon-edit"></i>
      </span>
      <h5 style="padding: 3px 0"></h5>
    </div>
    <div class="widget-content nopadding tab-content">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url("index.php/super/editarSuperUsuario/{$result->USS_ID}") ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="USS_NOME" class="control-label">Nome<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="USS_NOME" name="USS_NOME" value="<?= set_value('USS_NOME', $result->USS_NOME) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_CPF" class="control-label">CPF<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="USS_CPF" name="USS_CPF" value="<?= set_value('USS_CPF', $result->USS_CPF) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_RG" class="control-label">RG</label>
        <div class="controls">
          <input type="text" id="USS_RG" name="USS_RG" value="<?= set_value('USS_RG', $result->USS_RG) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_EMAIL" class="control-label">E-mail<span class="required">*</span></label>
        <div class="controls">
          <input type="email" id="USS_EMAIL" name="USS_EMAIL" value="<?= set_value('USS_EMAIL', $result->USS_EMAIL) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_SENHA" class="control-label">Nova Senha (deixe em branco para manter)</label>
        <div class="controls">
          <input type="password" id="USS_SENHA" name="USS_SENHA" value="" minlength="6" />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_TELEFONE" class="control-label">Telefone<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="USS_TELEFONE" name="USS_TELEFONE" value="<?= set_value('USS_TELEFONE', $result->USS_TELEFONE) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_CELULAR" class="control-label">Celular</label>
        <div class="controls">
          <input type="text" id="USS_CELULAR" name="USS_CELULAR" value="<?= set_value('USS_CELULAR', $result->USS_CELULAR) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="USS_SITUACAO" class="control-label">Situação</label>
        <div class="controls">
          <select id="USS_SITUACAO" name="USS_SITUACAO">
            <option value="1" <?= set_select('USS_SITUACAO', '1', $result->USS_SITUACAO == 1) ?>>Ativo</option>
            <option value="0" <?= set_select('USS_SITUACAO', '0', $result->USS_SITUACAO == 0) ?>>Inativo</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label for="USS_DATA_EXPIRACAO" class="control-label">Data de Expiração</label>
        <div class="controls">
          <input type="date" id="USS_DATA_EXPIRACAO" name="USS_DATA_EXPIRACAO" value="<?= set_value('USS_DATA_EXPIRACAO', $result->USS_DATA_EXPIRACAO) ?>" />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?= base_url('index.php/super/superUsuarios') ?>" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
</div>

