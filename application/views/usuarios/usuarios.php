<style>
    select {
        width: 70px;
    }
    .situacao-ativo {
        background-color: #00cd00;
        color: white;
    }
    .situacao-inativo {
        background-color: #ff0000;
        color: white;
    }
</style>

<div class="new122">
    <div class="widget-title" style="margin:-15px -10px 0">
        <h5>Usuários</h5>
    </div>
    <a href="<?= base_url('index.php/usuarios/adicionar') ?>" class="button btn btn-success" style="max-width: 160px">
        <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">Adicionar Usuário</span>
    </a>

    <div class="widget-box">
        <div class="widget-title" style="margin: -20px 0 0">
            <span class="icon">
                <i class="fas fa-cash-register"></i>
            </span>
            <h5 style="padding: 3px 0"></h5>
        </div>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Nível</th>
                        <th>Situação</th>
                        <th>Validade</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($results)): ?>
                        <tr>
                            <td colspan="7">Nenhum Usuário Cadastrado</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($results as $r): ?>
                            <tr>
                                <td><?= $r->usu_id ?></td>
                                <td><?= $r->usu_nome ?></td>
                                <td><?= $r->usu_email ?></td>
                                <td><?= $r->permissao ?></td>
                                <?php
                                $situacao = (isset($r->usu_situacao) ? $r->usu_situacao : $r->situacao) == 1 ? 'Ativo' : 'Inativo';
                                $situacaoClasse = (isset($r->usu_situacao) ? $r->usu_situacao : $r->situacao) == 1 ? 'situacao-ativo' : 'situacao-inativo';
                                ?>
                                <td><span class="badge <?= $situacaoClasse ?>"><?= ucfirst($situacao) ?></span></td>
                                <td><?= isset($r->usu_data_expiracao) ? $r->usu_data_expiracao : ($r->dataExpiracao ?? '-') ?></td>
                                <td>
                                    <a href="<?= base_url('index.php/usuarios/editar/' . $r->usu_id) ?>" class="btn-nwe3" title="Editar"><i class="bx bx-edit"></i></a>
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
