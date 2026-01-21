<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-edit"></i></span>
    <h5>Editar Usuário do Tenant: <?= $tenant->ten_nome ?></h5>
  </div>
  <div class="widget-content nopadding">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url("index.php/super/editarUsuarioTenant/{$tenant->ten_id}/{$result->idUsuarios}") ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="nome" name="nome" value="<?= set_value('nome', $result->nome) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="cpf" class="control-label">CPF<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="cpf" name="cpf" value="<?= set_value('cpf', $result->cpf) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="rg" class="control-label">RG</label>
        <div class="controls">
          <input type="text" id="rg" name="rg" value="<?= set_value('rg', $result->rg) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="email" class="control-label">E-mail<span class="required">*</span></label>
        <div class="controls">
          <input type="email" id="email" name="email" value="<?= set_value('email', $result->email) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="senha" class="control-label">Nova Senha (deixe em branco para manter)</label>
        <div class="controls">
          <input type="password" id="senha" name="senha" value="" minlength="6" />
        </div>
      </div>

      <div class="control-group">
        <label for="telefone" class="control-label">Telefone<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="telefone" name="telefone" value="<?= set_value('telefone', $result->telefone) ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="celular" class="control-label">Celular</label>
        <div class="controls">
          <input type="text" id="celular" name="celular" value="<?= set_value('celular', $result->celular) ?>" />
        </div>
      </div>

      <div class="control-group">
        <label for="permissoes_id" class="control-label">Permissões<span class="required">*</span></label>
        <div class="controls">
          <select id="permissoes_id" name="permissoes_id" required>
            <option value="">Selecione...</option>
            <?php if (isset($permissoes)): ?>
              <?php foreach ($permissoes as $permissao): ?>
                <option value="<?= $permissao->idPermissao ?>" <?= set_select('permissoes_id', $permissao->idPermissao, $result->permissoes_id == $permissao->idPermissao) ?>>
                  <?= $permissao->nome ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label for="situacao" class="control-label">Situação</label>
        <div class="controls">
          <select id="situacao" name="situacao">
            <option value="1" <?= set_select('situacao', '1', $result->situacao == 1) ?>>Ativo</option>
            <option value="0" <?= set_select('situacao', '0', $result->situacao == 0) ?>>Inativo</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label for="dataExpiracao" class="control-label">Data de Expiração</label>
        <div class="controls">
          <input type="date" id="dataExpiracao" name="dataExpiracao" value="<?= set_value('dataExpiracao', $result->dataExpiracao) ?>" />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Salvar</button>
        <a href="<?= base_url("index.php/super/usuariosTenant/{$tenant->ten_id}") ?>" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
</div>

