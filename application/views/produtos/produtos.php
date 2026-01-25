<style>
  select {
    width: 70px;
  }
  
  /* Estilos para badges de tipo de produto */
  .badge-tipo {
    display: inline-block;
    padding: 3px 7px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    white-space: nowrap;
  }
  
  .badge-produto {
    background: linear-gradient(135deg, #5cb85c 0%, #4cae4c 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(92, 184, 92, 0.3);
  }
  
  .badge-servico {
    background: linear-gradient(135deg, #5bc0de 0%, #46b8da 100%);
    color: white;
    box-shadow: 0 2px 4px rgba(91, 192, 222, 0.3);
  }
  
  .badge-tipo i {
    font-size: 10px;
    margin-right: 2px;
    vertical-align: middle;
  }
</style>
<div class="new122">
    <div class="widget-title" style="margin: -20px 0 0">
        <span class="icon">
            <i class="fas fa-shopping-bag"></i>
        </span>
        <h5>Produtos</h5>
    </div>
    <div class="span12" style="margin-left: 0">
        <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) : ?>
            <div class="span3 flexxn" style="display: flex;">
                <a href="<?= base_url() ?>index.php/produtos/adicionar" class="button btn btn-mini btn-success" style="max-width: 160px">
                    <span class="button__icon"><i class='bx bx-plus-circle'></i></span><span class="button__text2"> Produtos</span>
                </a>
                <a href="#modal-etiquetas" role="button" data-toggle="modal" class="button btn btn-mini btn-warning" style="max-width: 160px">
                    <span class="button__icon"><i class='bx bx-barcode-reader' ></i></span><span class="button__text2">Gerar Etiquetas</span>
                </a>
            </div>
        <?php endif; ?>
        <form class="span9" method="get" action="<?= base_url() ?>index.php/produtos" style="display: flex; justify-content: flex-end;">
            <div class="span3">
                <input type="text" name="pesquisa" id="pesquisa" placeholder="Buscar por Nome ou Cod. barra..." class="span12" value="<?=$this->input->get('pesquisa')?>">
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
                    <th>Cod.</th>
                    <th>Cod. Barra</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Finalidade</th>
                    <th>Estoque</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
                </thead>
                <tbody>
                <?php

                if (!$results) {
                    echo '<tr>
                                    <td colspan="7">Nenhum Produto Cadastrado</td>
                                    </tr>';
                }
                foreach ($results as $r) {
                    // Determinar o tipo de item
                    $tipoBadge = '';
                    if (isset($r->PRO_TIPO) && $r->PRO_TIPO == '2') {
                        $tipoBadge = '<span class="badge-tipo badge-servico"><i class="bx bx-briefcase"></i> Serviço</span>';
                    } else {
                        $tipoBadge = '<span class="badge-tipo badge-produto"><i class="bx bx-package"></i> Produto</span>';
                    }
                    $finalidadeLabel = $finalidadesProduto[$r->PRO_FINALIDADE] ?? ($r->PRO_FINALIDADE ?: 'Não informado');
                    
                    echo '<tr>';
                    echo '<td>' . $r->PRO_ID . '</td>';
                    echo '<td>' . $r->PRO_COD_BARRA . '</td>';
                    echo '<td>' . $r->PRO_DESCRICAO . '</td>';
                    echo '<td style="text-align: center;">' . $tipoBadge . '</td>';
                    echo '<td>' . $finalidadeLabel . '</td>';
                    echo '<td>' . $r->PRO_ESTOQUE . '</td>';
                    echo '<td>R$ ' . number_format($r->PRO_PRECO_VENDA, 2, ',', '.') . '</td>';
                    echo '<td>';
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/produtos/visualizar/' . $r->PRO_ID . '" class="btn-nwe" title="Visualizar Produto"><i class="bx bx-show bx-xs"></i></a>  ';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
                        echo '<a style="margin-right: 1%" href="' . base_url() . 'index.php/produtos/editar/' . $r->PRO_ID . '" class="btn-nwe3" title="Editar Produto"><i class="bx bx-edit bx-xs"></i></a>';
                    }
                    if ($this->permission->checkPermission($this->session->userdata('permissao'), 'dProduto')) {
                        echo '<a style="margin-right: 1%" href="#modal-excluir" role="button" data-toggle="modal" produto="' . $r->PRO_ID . '" class="btn-nwe4" title="Excluir Produto"><i class="bx bx-trash-alt bx-xs"></i></a>';
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
<div id="modal-excluir" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/produtos/excluir" method="post">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel"><i class="fas fa-trash-alt"></i> Excluir Produto</h5>
        </div>
        <div class="modal-body">
            <input type="hidden" id="idProduto" class="idProduto" name="id" value=""/>
            <h5 style="text-align: center">Deseja realmente excluir este produto?</h5>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
            <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true">
              <span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
            <button class="button btn btn-danger"><span class="button__icon"><i class='bx bx-trash'></i></span> <span class="button__text2">Excluir</span></button>
        </div>
    </form>
</div>

<!-- Modal Etiquetas -->
<div id="modal-etiquetas" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <form action="<?php echo base_url() ?>index.php/relatorios/produtosEtiquetas" method="get">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h5 id="myModalLabel">Gerar etiquetas com Código de Barras</h5>
        </div>
        <div class="modal-body">
            <div class="span12 alert alert-info" style="margin-left: 0"> Escolha o intervalo de produtos para gerar as etiquetas.</div>

            <div class="span12" style="margin-left: 0;">
                <div class="span6" style="margin-left: 0;">
                    <label for="valor">De</label>
                    <input class="span9" style="margin-left: 0" type="text" id="de_id" name="de_id" placeholder="ID do primeiro produto" value=""/>
                </div>


                <div class="span6">
                    <label for="valor">Até</label>
                    <input class="span9" type="text" id="ate_id" name="ate_id" placeholder="ID do último produto" value=""/>
                </div>

                <div class="span4">
                    <label for="valor">Qtd. do Estoque</label>
                    <input class="span12" type="checkbox" name="qtdEtiqueta" value="true"/>
                </div>

                <div class="span6">
                    <label class="span12" for="valor">Formato Etiqueta</label>
                    <select class="span5" name="etiquetaCode">
                        <option value="EAN13">EAN-13</option>
                        <option value="UPCA">UPCA</option>
                        <option value="C93">CODE 93</option>
                        <option value="C128A">CODE 128</option>
                        <option value="CODABAR">CODABAR</option>
                        <option value="QR">QR-CODE</option>
                    </select>
                </div>

            </div>
        </div>
        <div class="modal-footer" style="display:flex;justify-content: center">
          <button class="button btn btn-warning" data-dismiss="modal" aria-hidden="true"><span class="button__icon"><i class="bx bx-x"></i></span><span class="button__text2">Cancelar</span></button>
          <button class="button btn btn-success"><span class="button__icon"><i class='bx bx-barcode'></i></span><span class="button__text2">Gerar</span></button>
        </div>
    </form>
</div>
<script src="<?php echo base_url() ?>assets/js/jquery.validate.js"></script>
<!-- Modal Etiquetas e Estoque-->
<script type="text/javascript">
    $(document).ready(function () {
        $(document).on('click', 'a', function (event) {
            var produto = $(this).attr('produto');
            $('.idProduto').val(produto);
        });
    });
</script>
