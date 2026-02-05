<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-grid-alt"></i></span>
    <h5>Dashboard Super Administrador</h5>
  </div>

  <div class="row-fluid" style="margin-top: 20px;">
    <div class="span3">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="bx bx-buildings"></i></span>
          <h5>Grupos Empresariais</h5>
        </div>
        <div class="widget-content" style="text-align: center; padding: 30px;">
          <h1 style="font-size: 48px; margin: 0; color: #2D335B;"><?= isset($total_grupos_empresariais) ? $total_grupos_empresariais : 0 ?></h1>
          <p style="margin-top: 10px; color: #666;">Grupos cadastrados</p>
        </div>
      </div>
    </div>
    <div class="span3">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="bx bx-building"></i></span>
          <h5>Total de Empresas</h5>
        </div>
        <div class="widget-content" style="text-align: center; padding: 30px;">
          <h1 style="font-size: 48px; margin: 0; color: #2D335B;"><?= isset($total_empresas) ? $total_empresas : 0 ?></h1>
          <p style="margin-top: 10px; color: #666;">Empresas nos grupos</p>
        </div>
      </div>
    </div>
    <div class="span3">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="bx bx-user"></i></span>
          <h5>Total de Usuários</h5>
        </div>
        <div class="widget-content" style="text-align: center; padding: 30px;">
          <h1 style="font-size: 48px; margin: 0; color: #2D335B;"><?= $total_usuarios ?></h1>
          <p style="margin-top: 10px; color: #666;">Usuários no sistema</p>
        </div>
      </div>
    </div>
    <div class="span3">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon"><i class="bx bx-user-check"></i></span>
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
          <span class="icon"><i class="bx bx-list-ul"></i></span>
          <h5 style="padding: 3px 0">Últimos Grupos Empresariais</h5>
        </div>
        <div class="widget-content nopadding tab-content">
          <table id="tabela" class="table table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Nome do Grupo</th>
                <th>Data Cadastro</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($ultimos_grupos_empresariais)): ?>
                <?php foreach ($ultimos_grupos_empresariais as $grupo): ?>
                  <tr>
                    <td><?= $grupo->gre_id ?></td>
                    <td><?= htmlspecialchars($grupo->gre_nome) ?></td>
                    <td><?= !empty($grupo->gre_data_cadastro) && $grupo->gre_data_cadastro != '0000-00-00 00:00:00' ? date('d/m/Y H:i', strtotime($grupo->gre_data_cadastro)) : '-' ?></td>
                    <td>
                      <a href="<?= base_url("index.php/super/editarGrupoEmpresarial/{$grupo->gre_id}") ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                      <a href="<?= base_url("index.php/super/empresas/{$grupo->gre_id}") ?>" class="btn-nwe3" title="Empresas"><i class="bx bx-building"></i></a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="4">Nenhum grupo empresarial cadastrado.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

