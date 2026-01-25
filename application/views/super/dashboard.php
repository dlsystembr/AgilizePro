<div class="new122">
  <div class="widget-title" style="margin:-15px -10px 0">
    <h5>Dashboard Super Administrador</h5>
  </div>

  <div class="row-fluid" style="margin-top: 20px;">
    <div class="span4">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="icon-building"></i></span>
          <h5>Total de Tenants</h5>
        </div>
        <div class="widget-content" style="text-align: center; padding: 30px;">
          <h1 style="font-size: 48px; margin: 0; color: #2D335B;"><?= $total_tenants ?></h1>
          <p style="margin-top: 10px; color: #666;">Tenants cadastrados</p>
        </div>
      </div>
    </div>
    <div class="span4">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="icon-user"></i></span>
          <h5>Total de Usuários</h5>
        </div>
        <div class="widget-content" style="text-align: center; padding: 30px;">
          <h1 style="font-size: 48px; margin: 0; color: #2D335B;"><?= $total_usuarios ?></h1>
          <p style="margin-top: 10px; color: #666;">Usuários no sistema</p>
        </div>
      </div>
    </div>
    <div class="span4">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="icon-star"></i></span>
          <h5>Super Usuários</h5>
        </div>
        <div class="widget-content" style="text-align: center; padding: 30px;">
          <h1 style="font-size: 48px; margin: 0; color: #2D335B;"><?= $total_super_usuarios ?></h1>
          <p style="margin-top: 10px; color: #666;">Super administradores</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row-fluid" style="margin-top: 20px;">
    <div class="span12">
      <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
          <span class="icon">
            <i class="icon-list"></i>
          </span>
          <h5 style="padding: 3px 0"></h5>
        </div>
        <div class="widget-content nopadding tab-content">
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
              <?php if ($ultimos_tenants): ?>
                <?php foreach ($ultimos_tenants as $tenant): ?>
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
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7">Nenhum tenant cadastrado.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

