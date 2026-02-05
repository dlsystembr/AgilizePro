<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-building"></i></span>
    <h5>Empresas do Grupo: <?= htmlspecialchars($grupo->gre_nome) ?></h5>
  </div>
  <div class="span12" style="margin-left: 0; margin-bottom: 15px;">
    <a href="<?= base_url("index.php/super/adicionarEmpresa/{$grupo->gre_id}") ?>" class="button btn btn-mini btn-success" style="max-width: 165px">
      <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
      <span class="button__text2">Adicionar Empresa</span>
    </a>
    <a href="<?= base_url('index.php/super/gruposEmpresariais') ?>" class="button btn btn-mini" style="max-width: 120px">
      <span class="button__icon"><i class='bx bx-arrow-back'></i></span>
      <span class="button__text2">Voltar</span>
    </a>
  </div>

  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <form method="get" action="<?= base_url("index.php/super/empresas/{$grupo->gre_id}") ?>" style="margin: 15px 0;">
      <div class="input-append">
        <input type="text" name="pesquisa" placeholder="Pesquisar..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" />
        <button type="submit" class="btn"><i class="bx bx-search"></i></button>
      </div>
    </form>

      <table id="tabela" class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Razão Social</th>
            <th>Nome Fantasia</th>
            <th>CNPJ</th>
            <th>E-mail</th>
            <th>Situação</th>
            <th>Data Cadastro</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($results)): ?>
            <tr>
              <td colspan="8">Nenhuma empresa vinculada a este grupo.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($results as $empresa): ?>
              <tr>
                <td><?= $empresa->emp_id ?></td>
                <td><?= htmlspecialchars($empresa->emp_razao_social ?? '') ?></td>
                <td><?= htmlspecialchars($empresa->emp_nome_fantasia ?? '-') ?></td>
                <td><?= $empresa->emp_cnpj ?: '-' ?></td>
                <td><?= $empresa->emp_email ?: '-' ?></td>
                <td><?= (isset($empresa->emp_ativo) && $empresa->emp_ativo == 1) ? '<span class="label label-success">Ativo</span>' : '<span class="label label-important">Inativo</span>' ?></td>
                <td><?= !empty($empresa->emp_data_cadastro) && $empresa->emp_data_cadastro != '0000-00-00 00:00:00' ? date('d/m/Y H:i', strtotime($empresa->emp_data_cadastro)) : '-' ?></td>
                <td>
                  <a href="<?= base_url("index.php/super/usuariosEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" class="btn-nwe3" title="Usuários da empresa"><i class="bx bx-user"></i></a>
                  <a href="<?= base_url("index.php/super/menusEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" class="btn-nwe3" title="Menus permitidos"><i class="bx bx-menu"></i></a>
                  <a href="<?= base_url("index.php/super/editarEmpresa/{$grupo->gre_id}/{$empresa->emp_id}") ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                  <a href="#modal-remover-<?= $empresa->emp_id ?>" data-toggle="modal" class="btn-nwe3" title="Remover do grupo" style="color: #d32f2f;"><i class="bx bx-trash"></i></a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if (!empty($results)): foreach ($results as $empresa): ?>
<div id="modal-remover-<?= $empresa->emp_id ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Remover do Grupo</h3>
  </div>
  <div class="modal-body">
    <p>Remover a empresa <strong><?= htmlspecialchars($empresa->emp_razao_social ?? $empresa->emp_nome_fantasia ?? '') ?></strong> deste grupo?</p>
    <p class="text-info">A empresa não será excluída do sistema, apenas desvinculada do grupo.</p>
  </div>
  <div class="modal-footer">
    <form method="post" action="<?= base_url('index.php/super/excluirEmpresa') ?>">
      <input type="hidden" name="id" value="<?= $empresa->emp_id ?>" />
      <input type="hidden" name="grupo_id" value="<?= $grupo->gre_id ?>" />
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
      <button class="btn btn-danger">Remover do grupo</button>
    </form>
  </div>
</div>
<?php endforeach; endif; ?>

<?= $this->pagination->create_links(); ?>
