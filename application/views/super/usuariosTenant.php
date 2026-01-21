<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-user"></i></span>
    <h5>Usuários do Tenant: <?= $tenant->ten_nome ?></h5>
  </div>
  <div class="widget-content">
    <a href="<?= base_url("index.php/super/adicionarUsuarioTenant/{$tenant->ten_id}") ?>" class="button btn btn-success">
      <span class="button__icon"><i class='icon-plus'></i></span>
      <span class="button__text">Adicionar Usuário</span>
    </a>
    <a href="<?= base_url('index.php/super/tenants') ?>" class="button btn">
      <span class="button__icon"><i class='icon-arrow-left'></i></span>
      <span class="button__text">Voltar</span>
    </a>

    <form method="get" action="<?= base_url("index.php/super/usuariosTenant/{$tenant->ten_id}") ?>" style="margin: 20px 0;">
      <div class="input-append">
        <input type="text" name="pesquisa" placeholder="Pesquisar..." value="<?= isset($search) ? $search : '' ?>" />
        <button type="submit" class="btn"><i class="icon-search"></i></button>
      </div>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>CPF</th>
          <th>Telefone</th>
          <th>Permissão</th>
          <th>Situação</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($results)): ?>
          <tr>
            <td colspan="8">Nenhum usuário cadastrado para este tenant.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($results as $usuario): ?>
            <tr>
              <td><?= $usuario->idUsuarios ?></td>
              <td><?= $usuario->nome ?></td>
              <td><?= $usuario->email ?></td>
              <td><?= $usuario->cpf ?></td>
              <td><?= $usuario->telefone ?></td>
              <td><?= $usuario->permissao ?></td>
              <td><?= $usuario->situacao == 1 ? 'Ativo' : 'Inativo' ?></td>
              <td>
                <a href="<?= base_url("index.php/super/editarUsuarioTenant/{$tenant->ten_id}/{$usuario->idUsuarios}") ?>" class="btn btn-info btn-mini"><i class="icon-edit"></i> Editar</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <?= $this->pagination->create_links(); ?>
  </div>
</div>

