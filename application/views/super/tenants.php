<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-building"></i></span>
    <h5>Gerenciar Tenants</h5>
  </div>
  <div class="span12" style="margin-left: 0; margin-bottom: 15px;">
    <a href="<?= base_url('index.php/super/adicionarTenant') ?>" class="button btn btn-mini btn-success" style="max-width: 165px">
      <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
      <span class="button__text2">Adicionar Tenant</span>
    </a>
  </div>

  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <form method="get" action="<?= base_url('index.php/super/tenants') ?>" style="margin: 15px 0;">
      <div class="input-append">
        <input type="text" name="pesquisa" placeholder="Pesquisar..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" />
        <button type="submit" class="btn"><i class="bx bx-search"></i></button>
      </div>
    </form>

      <table id="tabela" class="table table-bordered">
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
                <td><?= $tenant->ten_cnpj ?: '-' ?></td>
                <td><?= $tenant->ten_email ?: '-' ?></td>
                <td><?= $tenant->ten_telefone ?: '-' ?></td>
                <td><?= !empty($tenant->ten_data_cadastro) && $tenant->ten_data_cadastro != '0000-00-00 00:00:00' && $tenant->ten_data_cadastro != '1969-12-31' ? date('d/m/Y H:i', strtotime($tenant->ten_data_cadastro)) : '-' ?></td>
                <td>
                  <a href="<?= base_url("index.php/super/editarTenant/{$tenant->ten_id}") ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                  <a href="<?= base_url("index.php/super/usuariosTenant/{$tenant->ten_id}") ?>" class="btn-nwe3" title="Usuários"><i class="bx bx-user"></i></a>
                  <a href="<?= base_url("index.php/super/permissoesMenu/{$tenant->ten_id}") ?>" class="btn-nwe3" title="Permissões"><i class="bx bx-lock"></i></a>
                  <a href="#modal-excluir-<?= $tenant->ten_id ?>" data-toggle="modal" class="btn-nwe3" title="Excluir" style="color: #d32f2f;"><i class="bx bx-trash"></i></a>
                
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
    </div>
  </div>
</div>

<?= $this->pagination->create_links(); ?>

