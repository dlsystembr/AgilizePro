<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-user-plus"></i></span>
    <h5>Adicionar usuário à empresa: <?= htmlspecialchars($empresa->emp_razao_social ?? $empresa->emp_nome_fantasia ?? 'Empresa') ?></h5>
  </div>
  <p style="margin: 8px 0 16px; color: #666;">Grupo: <?= htmlspecialchars($grupo->gre_nome) ?></p>
  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <?php if (isset($custom_error) && $custom_error): ?>
      <?= $custom_error ?>
    <?php endif; ?>

    <form action="<?= base_url("index.php/super/adicionarUsuarioEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" method="post" class="form-horizontal">
      <div class="control-group">
        <label for="nome" class="control-label">Nome<span class="required">*</span></label>
        <div class="controls">
          <input type="text" id="nome" name="nome" value="<?= set_value('nome') ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="email" class="control-label">E-mail<span class="required">*</span></label>
        <div class="controls">
          <input type="email" id="email" name="email" value="<?= set_value('email') ?>" required />
        </div>
      </div>

      <div class="control-group">
        <label for="senha" class="control-label">Senha<span class="required">*</span></label>
        <div class="controls">
          <input type="password" id="senha" name="senha" value="<?= set_value('senha') ?>" required minlength="6" />
        </div>
      </div>

      <div class="control-group">
        <label for="gpu_id" class="control-label">Grupo de usuário<span class="required">*</span></label>
        <div class="controls">
          <select id="gpu_id" name="gpu_id" required>
            <option value="">Selecione...</option>
            <?php if (isset($grupos)): ?>
              <?php foreach ($grupos as $g): ?>
                <option value="<?= (int) $g->gpu_id ?>" <?= set_select('gpu_id', $g->gpu_id) ?>>
                  <?= htmlspecialchars($g->gpu_nome, ENT_QUOTES, 'UTF-8') ?>
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
            <option value="1" <?= set_select('situacao', '1', true) ?>>Ativo</option>
            <option value="0" <?= set_select('situacao', '0') ?>>Inativo</option>
          </select>
        </div>
      </div>

      <div class="control-group">
        <label for="dataExpiracao" class="control-label">Data de Expiração</label>
        <div class="controls">
          <input type="date" id="dataExpiracao" name="dataExpiracao" value="<?= set_value('dataExpiracao') ?>" />
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-success">Adicionar</button>
        <a href="<?= base_url("index.php/super/usuariosEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" class="btn">Cancelar</a>
      </div>
    </form>
  </div>
  </div>
</div>
