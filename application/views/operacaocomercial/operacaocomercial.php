<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title" style="margin: -20px 0 0">
                <span class="icon">
                    <i class="fas fa-exchange-alt"></i>
                </span>
                <h5>Operações Comerciais</h5>
            </div>
            <div class="widget-content nopadding tab-content">
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th>Nome da Operação</th>
                            <th>Mensagem Nota Fiscal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$results) { ?>
                            <tr>
                                <td colspan="3">Nenhuma Operação Comercial Cadastrada</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($results as $r) { ?>
                                <tr>
                                    <td><?= $r->nome_operacao ?></td>
                                    <td><?= $r->mensagem_nota ?></td>
                                    <td>
                                        <a href="<?= base_url('index.php/operacaocomercial/editar/' . $r->id) ?>" class="btn btn-info" title="Editar"><i class="fas fa-edit"></i></a>
                                        <a href="<?= base_url('index.php/operacaocomercial/excluir/' . $r->id) ?>" class="btn btn-danger" title="Excluir" onclick="return confirm('Deseja realmente excluir esta operação?');"><i class="fas fa-trash-alt"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <a href="<?= base_url('index.php/operacaocomercial/adicionar') ?>" class="btn btn-success"><i class="fas fa-plus"></i> Adicionar Operação Comercial</a>
    </div>
</div> 