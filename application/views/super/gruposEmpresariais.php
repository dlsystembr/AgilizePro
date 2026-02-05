<div class="new122">
  <div class="widget-title" style="margin: -20px 0 0">
    <span class="icon"><i class="bx bx-buildings"></i></span>
    <h5>Grupos Empresariais</h5>
  </div>
  <div class="span12" style="margin-left: 0; margin-bottom: 15px;">
    <a href="<?= base_url('index.php/super/adicionarGrupoEmpresarial') ?>" class="button btn btn-mini btn-success" style="max-width: 200px">
      <span class="button__icon"><i class='bx bx-plus-circle'></i></span>
      <span class="button__text2">Adicionar Grupo Empresarial</span>
    </a>
  </div>

  <div class="widget-box">
    <h5 style="padding: 3px 0"></h5>
    <div class="widget-content nopadding tab-content">
    <form method="get" action="<?= base_url('index.php/super/gruposEmpresariais') ?>" style="margin: 15px 0;">
      <div class="input-append">
        <input type="text" name="pesquisa" placeholder="Pesquisar por nome..." value="<?= isset($search) ? htmlspecialchars($search) : '' ?>" />
        <button type="submit" class="btn"><i class="bx bx-search"></i></button>
      </div>
    </form>

      <table id="tabela" class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome do Grupo</th>
            <th>Situação</th>
            <th>Data Cadastro</th>
            <th>Data Atualização</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($results)): ?>
            <tr>
              <td colspan="6">Nenhum grupo empresarial cadastrado.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($results as $grupo): ?>
              <tr>
                <td><?= $grupo->gre_id ?></td>
                <td><?= htmlspecialchars($grupo->gre_nome) ?></td>
                <td><?= (isset($grupo->gre_situacao) && $grupo->gre_situacao == 1) ? '<span class="label label-success">Ativo</span>' : '<span class="label label-important">Inativo</span>' ?></td>
                <td><?= !empty($grupo->gre_data_cadastro) && $grupo->gre_data_cadastro != '0000-00-00 00:00:00' ? date('d/m/Y H:i', strtotime($grupo->gre_data_cadastro)) : '-' ?></td>
                <td><?= !empty($grupo->gre_data_atualizacao) && $grupo->gre_data_atualizacao != '0000-00-00 00:00:00' ? date('d/m/Y H:i', strtotime($grupo->gre_data_atualizacao)) : '-' ?></td>
                <td>
                  <a href="<?= base_url("index.php/super/editarGrupoEmpresarial/{$grupo->gre_id}") ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
                  <a href="<?= base_url("index.php/super/empresas/{$grupo->gre_id}") ?>" class="btn-nwe3" title="Empresas"><i class="bx bx-building"></i></a>
                  <a href="#modal-excluir-<?= $grupo->gre_id ?>" data-toggle="modal" class="btn-nwe3" title="Excluir" style="color: #d32f2f;"><i class="bx bx-trash"></i></a>
                </td>
              </tr>
          <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php if (!empty($results)): foreach ($results as $grupo): ?>
<div id="modal-excluir-<?= $grupo->gre_id ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Excluir Grupo Empresarial</h3>
  </div>
  <div class="modal-body">
    <p>Deseja realmente excluir o grupo <strong><?= htmlspecialchars($grupo->gre_nome) ?></strong>?</p>
    <p class="text-error"><strong>Atenção:</strong> Só é possível excluir se não houver empresas vinculadas.</p>
  </div>
  <div class="modal-footer">
    <form method="post" action="<?= base_url('index.php/super/excluirGrupoEmpresarial') ?>">
      <input type="hidden" name="id" value="<?= $grupo->gre_id ?>" />
      <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
      <button class="btn btn-danger">Excluir</button>
    </form>
  </div>
</div>
<?php endforeach; endif; ?>

<?= $this->pagination->create_links(); ?>
