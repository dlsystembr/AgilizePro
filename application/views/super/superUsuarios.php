<div class="widget-box">
  <div class="widget-title">
    <span class="icon"><i class="icon-star"></i></span>
    <h5>Gerenciar Super Usuários</h5>
  </div>
  <div class="widget-content">
    <a href="<?= base_url('index.php/super/adicionarSuperUsuario') ?>" class="button btn btn-success">
      <span class="button__icon"><i class='icon-plus'></i></span>
      <span class="button__text">Adicionar Super Usuário</span>
    </a>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>CPF</th>
          <th>Telefone</th>
          <th>Situação</th>
          <th>Data Cadastro</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($results)): ?>
          <tr>
            <td colspan="8">Nenhum super usuário cadastrado.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($results as $super): ?>
            <tr>
              <td><?= $super->USS_ID ?></td>
              <td><?= $super->USS_NOME ?></td>
              <td><?= $super->USS_EMAIL ?></td>
              <td><?= $super->USS_CPF ?></td>
              <td><?= $super->USS_TELEFONE ?></td>
              <td><?= $super->USS_SITUACAO == 1 ? 'Ativo' : 'Inativo' ?></td>
              <td><?= date('d/m/Y', strtotime($super->USS_DATA_CADASTRO)) ?></td>
              <td>
                <a href="<?= base_url("index.php/super/editarSuperUsuario/{$super->USS_ID}") ?>" class="btn btn-info btn-mini"><i class="icon-edit"></i> Editar</a>
                <?php if ($super->USS_ID != $this->session->userdata('id_admin')): ?>
                  <a href="#modal-excluir-<?= $super->USS_ID ?>" data-toggle="modal" class="btn btn-danger btn-mini"><i class="icon-remove"></i> Excluir</a>
                  
                  <div id="modal-excluir-<?= $super->USS_ID ?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                      <h3 id="myModalLabel">Excluir Super Usuário</h3>
                    </div>
                    <div class="modal-body">
                      <p>Deseja realmente excluir o super usuário <strong><?= $super->USS_NOME ?></strong>?</p>
                      <p class="text-error"><strong>Atenção:</strong> Esta ação não pode ser desfeita!</p>
                    </div>
                    <div class="modal-footer">
                      <form method="post" action="<?= base_url('index.php/super/excluirSuperUsuario') ?>">
                        <input type="hidden" name="id" value="<?= $super->USS_ID ?>" />
                        <button class="btn" data-dismiss="modal" aria-hidden="true">Cancelar</button>
                        <button class="btn btn-danger">Excluir</button>
                      </form>
                    </div>
                  </div>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <?= $this->pagination->create_links(); ?>
  </div>
</div>

