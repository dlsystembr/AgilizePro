<style>
    select {
        width: 70px;
    }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-receipt"></i>
        </span>
        <h5>Classificação Fiscal</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aClassificacaoFiscal')) { ?>
            <div class="span3">
                <a href="<?= base_url() ?>index.php/classificacaofiscal/adicionar" class="button btn btn-mini btn-success"
                    style="max-width: 165px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2">
                        Nova Classificação Fiscal
                    </span>
                </a>
            </div>
        <?php } ?>
        <form class="span9" method="get" action="<?= base_url() ?>index.php/classificacaofiscal"
            style="display: flex; justify-content: flex-end;">
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa"
                    placeholder="Buscar por Operação, CFOP ou Destinação..." class="span12"
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
                        <th>Operação Comercial</th>
                        <th>Tipo de Cliente</th>
                        <th><?= $regime_tributario === 'Simples Nacional' ? 'CSOSN' : 'CST' ?></th>
                        <th>Natureza do Contribuinte</th>
                        <th>Tipo ICMS</th>
                        <th>CFOP</th>
                        <th>Destinação</th>
                        <th>Objetivo Comercial</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!$results) {
                        echo '<tr>
                    <td colspan="9">Nenhuma Classificação Fiscal Cadastrada</td>
                  </tr>';
                    } else {
                        foreach ($results as $r) {
                            // Mapear valores para exibição
                            $natureza_display = $r->natureza_contribuinte;
                            if ($natureza_display == 'inscrito' || $natureza_display == 'Contribuinte') {
                                $natureza_display = 'Contribuinte';
                            } elseif ($natureza_display == 'nao_inscrito' || $natureza_display == 'Não Contribuinte') {
                                $natureza_display = 'Não Contribuinte';
                            }

                            $tipo_icms_display = $r->tipo_icms ?? 'ICMS Normal';
                            if ($tipo_icms_display == 'normal' || $tipo_icms_display == 'ICMS Normal') {
                                $tipo_icms_display = 'ICMS Normal';
                            } elseif ($tipo_icms_display == 'st' || $tipo_icms_display == 'Substituição Tributaria') {
                                $tipo_icms_display = 'Substituição Tributária';
                            } elseif ($tipo_icms_display == 'Serviço') {
                                $tipo_icms_display = 'Serviço';
                            }

                            $destinacao_display = $r->destinacao;
                            if ($destinacao_display == 'estadual' || $destinacao_display == 'Estadual') {
                                $destinacao_display = 'Estadual';
                            } elseif ($destinacao_display == 'interestadual' || $destinacao_display == 'Interestadual') {
                                $destinacao_display = 'Interestadual';
                            }

                            echo '<tr>';
                            echo '<td>' . ($r->nome_operacao ?: '-') . '</td>';
                            echo '<td>' . ($r->nome_tipo_cliente ?: '-') . '</td>';
                            echo '<td>' . ($regime_tributario === 'Simples Nacional' ? ($r->csosn ?: '-') : ($r->cst ?: '-')) . '</td>';
                            echo '<td>' . $natureza_display . '</td>';
                            echo '<td>' . $tipo_icms_display . '</td>';
                            echo '<td>' . ($r->cfop ?: '-') . '</td>';
                            echo '<td>' . $destinacao_display . '</td>';
                            echo '<td>' . ($r->objetivo_comercial ?: '-') . '</td>';
                            echo '<td>';
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vClassificacaoFiscal')) {
                                echo '<a href="' . base_url() . 'index.php/classificacaofiscal/visualizar/' . $r->id . '" style="margin-right: 1%" class="btn-nwe" title="Visualizar Classificação Fiscal"><i class="bx bx-show bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eClassificacaoFiscal')) {
                                echo '<a href="' . base_url() . 'index.php/classificacaofiscal/editar/' . $r->id . '" style="margin-right: 1%" class="btn-nwe3" title="Editar Classificação Fiscal"><i class="bx bx-edit bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aClassificacaoFiscal')) {
                                echo '<a href="' . base_url() . 'index.php/classificacaofiscal/clonar/' . $r->id . '" style="margin-right: 1%" class="btn-nwe2" title="Clonar Classificação Fiscal"><i class="bx bx-copy bx-xs"></i></a>';
                            }
                            if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dClassificacaoFiscal')) {
                                echo '<a href="#modal-excluir" role="button" data-toggle="modal" classificacao="' . $r->id . '" style="margin-right: 1%" class="btn-nwe4" title="Excluir Classificação Fiscal"><i class="bx bx-trash-alt bx-xs"></i></a>';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    } ?>
                </tbody>
            </table>

        </div>
    </div>
</div>

<!-- Modal -->
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/classificacaofiscal/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Excluir Classificação Fiscal</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idClassificacao" name="id" value="" />
            <h5 style="text-align: center">Deseja realmente excluir esta classificação fiscal?</h5>
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
        // Capturar clique no link de excluir que tem o atributo 'classificacao'
        $(document).on('click', 'a[classificacao]', function (event) {
            var classificacaoId = $(this).attr('classificacao');
            if (classificacaoId) {
                $('#idClassificacao').val(classificacaoId);
                console.log('ID da classificação definido:', classificacaoId);
            }
        });
    });
</script>
