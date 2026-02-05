<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-user"></i></span>
    <h5>Usuários do Tenant: <?= htmlspecialchars($tenant->ten_nome) ?></h5>
  </div>
  <div class="span12" style="margin-left: 0; margin-bottom: 15px;">
    <a href="<?= base_url("index.php/super/adicionarUsuarioTenant/{$tenant->ten_id}") ?>" class="button btn btn-mini btn-success" style="max-width: 165px">
      <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
      <span class="button__text2">Adicionar Usuário</span>
    </a>
    <a href="<?= base_url('index.php/super/tenants') ?>" class="button btn btn-mini" style="max-width: 120px">
      <span class="button__icon"><i class='bx bx-arrow-back'></i></span>
      <span class="button__text2">Voltar</span>
    </a>
  </div>

  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
      <table id="tabela" class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Permissão</th>
          <th>Situação</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($results)): ?>
          <tr>
            <td colspan="6">Nenhum usuário cadastrado para este tenant.</td>
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
              <td><?= $u_nome ?></td>
              <td><?= $u_email ?></td>
              <td><?= $usuario->permissao ?></td>
              <td><?= $u_sit == 1 ? 'Ativo' : 'Inativo' ?></td>
              <td>
                <a href="<?= base_url("index.php/super/editarUsuarioTenant/{$tenant->ten_id}/{$u_id}") ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
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

