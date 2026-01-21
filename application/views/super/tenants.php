<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-building"></i></span>
    <h5>Gerenciar Tenants</h5>
  </div>
  <div class="widget-content">
    <a href="<?= base_url('index.php/super/adicionarTenant') ?>" class="button btn btn-success">
      <span class="button__icon"><i class='icon-plus'></i></span>
      <span class="button__text">Adicionar Tenant</span>
    </a>

    <form method="get" action="<?= base_url('index.php/super/tenants') ?>" style="margin: 20px 0;">
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
          <th>CNPJ</th>
          <th>E-mail</th>
          <th>Telefone</th>
          <th>Data Cadastro</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($results)): ?>
          <tr>
            <td colspan="7">Nenhum tenant cadastrado.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($results as $tenant): ?>
            <tr>
              <td><?= $tenant->ten_id ?></td>
              <td><?= $tenant->ten_nome ?></td>
              <td><?= $tenant->ten_cnpj ?></td>
              <td><?= $tenant->ten_email ?></td>
              <td><?= $tenant->ten_telefone ?></td>
              <td><?= date('d/m/Y H:i', strtotime($tenant->ten_data_cadastro)) ?></td>
              <td>
                <a href="<?= base_url("index.php/super/editarTenant/{$tenant->ten_id}") ?>" class="btn btn-info btn-mini"><i class="icon-edit"></i> Editar</a>
                <a href="<?= base_url("index.php/super/usuariosTenant/{$tenant->ten_id}") ?>" class="btn btn-success btn-mini"><i class="icon-user"></i> Usuários</a>
                <a href="<?= base_url("index.php/super/permissoesMenu/{$tenant->ten_id}") ?>" class="btn btn-warning btn-mini"><i class="icon-lock"></i> Permissões</a>
                <a href="#modal-excluir-<?= $tenant->ten_id ?>" data-toggle="modal" class="btn btn-danger btn-mini"><i class="icon-remove"></i> Excluir</a>
                
                <div id="modal-excluir-<?= $tenant->ten_id ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h3 id="myModalLabel">Excluir Tenant</h3>
                  </div>
                  <div class="modal-body">
                    <p>Deseja realmente excluir o tenant <strong><?= $tenant->ten_nome ?></strong>?</p>
                    <p class="text-error"><strong>Atenção:</strong> Esta ação não pode ser desfeita!</p>
                  </div>
                  <div class="modal-footer">
                    <form method="post" action="<?= base_url('index.php/super/excluirTenant') ?>">
                      <input type="hidden" name="id" value="<?= $tenant->ten_id ?>" />
                      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                      <button class="btn btn-danger">Excluir</button>
                    </form>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <?= $this->pagination->create_links(); ?>
  </div>
</div>

