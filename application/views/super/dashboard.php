<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-dashboard"></i></span>
    <h5>Dashboard Super Administrador</h5>
  </div>
  <div class="widget-content">
    <div class="row-fluid">
      <div class="span4">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon"><i class="icon-building"></i></span>
            <h5>Total de Tenants</h5>
          </div>
          <div class="widget-content">
            <h2><?= $total_tenants ?></h2>
          </div>
        </div>
      </div>
      <div class="span4">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon"><i class="icon-user"></i></span>
            <h5>Total de Usuários</h5>
          </div>
          <div class="widget-content">
            <h2><?= $total_usuarios ?></h2>
          </div>
        </div>
      </div>
      <div class="span4">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon"><i class="icon-star"></i></span>
            <h5>Super Usuários</h5>
          </div>
          <div class="widget-content">
            <h2><?= $total_super_usuarios ?></h2>
          </div>
        </div>
      </div>
    </div>

    <div class="row-fluid" style="margin-top: 20px;">
      <div class="span12">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon"><i class="icon-list"></i></span>
            <h5>Últimos Tenants Cadastrados</h5>
          </div>
          <div class="widget-content nopadding">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nome</th>
                  <th>CNPJ</th>
                  <th>E-mail</th>
                  <th>Data Cadastro</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($ultimos_tenants): ?>
                  <?php foreach ($ultimos_tenants as $tenant): ?>
                    <tr>
                      <td><?= $tenant->ten_id ?></td>
                      <td><?= $tenant->ten_nome ?></td>
                      <td><?= $tenant->ten_cnpj ?></td>
                      <td><?= $tenant->ten_email ?></td>
                      <td><?= date('d/m/Y H:i', strtotime($tenant->ten_data_cadastro)) ?></td>
                      <td>
                        <a href="<?= base_url("index.php/super/editarTenant/{$tenant->ten_id}") ?>" class="btn btn-info btn-mini"><i class="icon-edit"></i> Editar</a>
                        <a href="<?= base_url("index.php/super/usuariosTenant/{$tenant->ten_id}") ?>" class="btn btn-success btn-mini"><i class="icon-user"></i> Usuários</a>
                        <a href="<?= base_url("index.php/super/permissoesMenu/{$tenant->ten_id}") ?>" class="btn btn-warning btn-mini"><i class="icon-lock"></i> Permissões</a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="6">Nenhum tenant cadastrado.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

