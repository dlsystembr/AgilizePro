<style>
    select {
        width: 70px;
    }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-file-contract"></i>
        </span>
        <h5>Contratos</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aContrato')) { ?>
            <div class="span3">
                <a href="<?= base_url() ?>index.php/contratos/adicionar" class="button btn btn-mini btn-success"
                    style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                        Novo Contrato
                    </span>
                </a>
            </div>
        <?php } ?>
        <form class="span9" method="get" action="<?= base_url() ?>index.php/contratos"
            style="display: flex; justify-content: flex-end;">
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa"
                    placeholder="Buscar por Número, Cliente ou CPF/CNPJ..." class="span12"
                    value="<?= $this->input->get('pesquisa') ?>">
            </div>
            <div class="span1">
                <button class="button btn btn-mini btn-warning" style="min-width: 30px">
                    <span class="button__icon"><i class='bx bx-search-alt'></i></span></button>
            </div>
        </form>
    </div>

    <div class="widget-box">
        <h5 style="padding: 3px 0"></h5>
        <div class="widget-content nopadding tab-content">
            <table id="tabela" class="table table-bordered ">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>CPF/CNPJ</th>
                        <th>Data Início</th>
                        <th>Data Fim</th>
                        <th>Tipo Assinante</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                    <td colspan="8">Nenhum Contrato Cadastrado</td>
                  </tr>';
                    }
                    
                    $tiposAssinante = [
                        '1' => 'Comercial',
                        '2' => 'Industrial',
                        '3' => 'Residencial/PF',
                        '4' => 'Produtor Rural',
                        '5' => 'Orgão Público Estadual',
                        '6' => 'Prestador de Telecom',
                        '7' => 'Missões Diplomáticas',
                        '8' => 'Igrejas e Templos',
                        '99' => 'Outros'
                    ];
                    
                    foreach ($results as $r) {
                        echo '<tr>';
                        echo '<td>' . $r->ctr_numero . '</td>';
                        echo '<td>' . ($r->pes_nome ?: '-') . '</td>';
                        echo '<td>' . ($r->pes_cpfcnpj ?: '-') . '</td>';
                        echo '<td>' . date('d/m/Y', strtotime($r->ctr_data_inicio)) . '</td>';
                        echo '<td>' . ($r->ctr_data_fim ? date('d/m/Y', strtotime($r->ctr_data_fim)) : '-') . '</td>';
                        echo '<td>' . ($tiposAssinante[$r->ctr_tipo_assinante] ?? '-') . '</td>';
                        echo '<td>' . ((int)$r->ctr_situacao === 1 ? '<span class="label label-success">Ativo</span>' : '<span class="label">Inativo</span>') . '</td>';
                        echo '<td>';
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vContrato')) {
                            echo '<a href="' . base_url() . 'index.php/contratos/visualizar/' . $r->ctr_id . '" style="margin-right: 1%" class="btn-nwe" title="Visualizar Contrato"><i class="bx bx-show bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eContrato')) {
                            echo '<a href="' . base_url() . 'index.php/contratos/editar/' . $r->ctr_id . '" style="margin-right: 1%" class="btn-nwe3" title="Editar Contrato"><i class="bx bx-edit bx-xs"></i></a>';
                        }
                        if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dContrato')) {
                            echo '<a href="#modal-excluir" role="button" data-toggle="modal" contrato="' . $r->ctr_id . '" style="margin-right: 1%" class="btn-nwe4" title="Excluir Contrato"><i class="bx bx-trash-alt bx-xs"></i></a>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>
<?php echo $this->pagination->create_links(); ?>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/contratos/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Contrato</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idContrato" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir este contrato?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i
                        class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span
                    class="button__text2">Excluir</span></button>
        </div>
    </form>
    
</div>

<script type="text/javascript">
    $(document).ready(function () {
        // Capturar clique no link de excluir que tem o atributo 'contrato'
        $(document).on('click', 'a[contrato]', function (event) {
            var contratoId = $(this).attr('contrato');
            if (contratoId) {
                $('#idContrato').val(contratoId);
                console.log('ID do contrato definido:', contratoId);
            }
        });
    });
</script>
