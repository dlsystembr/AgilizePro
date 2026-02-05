<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-user"></i></span>
    <h5>Usuários da empresa: <?= htmlspecialchars($empresa->emp_razao_social ?? $empresa->emp_nome_fantasia ?? 'Empresa') ?></h5>
  </div>
  <p style="margin: 8px 0 16px; color: #666;">Grupo: <?= htmlspecialchars($grupo->gre_nome) ?></p>
  <div class="span12" style="margin-left: 0; margin-bottom: 15px;">
    <a href="<?= base_url("index.php/super/adicionarUsuarioEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" class="button btn btn-mini btn-success" style="max-width: 165px">
      <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
      <span class="button__text2">Adicionar Usuário</span>
    </a>
    <a href="<?= base_url("index.php/super/empresas/{$grupo->gre_id}") ?>" class="button btn btn-mini" style="max-width: 120px">
      <span class="button__icon"><i class='bx bx-arrow-back'></i></span>
      <span class="button__text2">Voltar</span>
    </a>
  </div>

  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <form method="get" action="<?= base_url("index.php/super/usuariosEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" style="margin: 15px 0;">
      <div class="input-append">
        <input type="text" name="pesquisa" placeholder="Pesquisar por nome ou e-mail..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" />
        <button type="submit" class="btn"><i class="bx bx-search"></i></button>
      </div>
    </form>
      <table id="tabela" class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Grupo</th>
          <th>Situação</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($results)): ?>
          <tr>
            <td colspan="6">Nenhum usuário vinculado a esta empresa.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($results as $usuario): ?>
            <?php
            $u_id = isset($usuario->usu_id) ? $usuario->usu_id : $usuario->idUsuarios;
            $u_nome = isset($usuario->usu_nome) ? $usuario->usu_nome : $usuario->nome;
            $u_email = isset($usuario->usu_email) ? $usuario->usu_email : $usuario->email;
            $u_sit = isset($usuario->usu_situacao) ? $usuario->usu_situacao : $usuario->situacao;
            ?>
            <tr>
              <td><?= $u_id ?></td>
              <td><?= htmlspecialchars($u_nome) ?></td>
              <td><?= htmlspecialchars($u_email) ?></td>
              <td><?= isset($usuario->permissao) ? htmlspecialchars($usuario->permissao) : '-' ?></td>
              <td><?= $u_sit == 1 ? '<span class="label label-success">Ativo</span>' : '<span class="label label-important">Inativo</span>' ?></td>
              <td>
                <?php $gpu_id = isset($usuario->gpu_id) ? (int) $usuario->gpu_id : 0; ?>
                <?php if ($gpu_id): ?>
                <a href="<?= base_url("index.php/super/permissoesGrupoEmpresa/{$grupo->gre_id}/{$empresa->emp_id}/{$gpu_id}") ?>" class="btn-nwe3" title="Permissões do grupo"><i class="bx bx-lock-open-alt"></i></a>
                <?php endif; ?>
                <a href="<?= base_url("index.php/super/editarUsuarioEmpresa/{$grupo->gre_id}/{$empresa->emp_id}/{$u_id}") ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
    </div>
  </div>
</div>

<?= $this->pagination->create_links(); ?>
