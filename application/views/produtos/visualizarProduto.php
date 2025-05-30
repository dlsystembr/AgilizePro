<div class="accordion" id="collapse-group">
    <div class="accordion-group widget-box">
        <div class="accordion-heading">
            <div class="widget-title" style="margin: -20px 0 0">
                <a data-parent="#collapse-group" href="#collapseGOne" data-toggle="collapse">
                    <span class="icon"><i class="fas fa-shopping-bag"></i></span>
                    <h5>Dados do Produto</h5>
                </a>
            </div>
        </div>
        <div class="collapse in accordion-body">
            <div class="widget-content">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td style="text-align: center; width: 30%"><strong>Código de Barra</strong></td>
                            <td>
                                <?php echo $result->codDeBarra ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right; width: 30%"><strong>Descrição</strong></td>
                            <td>
                                <?php echo $result->descricao ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Unidade</strong></td>
                            <td>
                                <?php echo $result->unidade ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Preço de Compra</strong></td>
                            <td>R$
                                <?php echo $result->precoCompra; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Preço de Venda</strong></td>
                            <td>R$
                                <?php echo $result->precoVenda; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Estoque</strong></td>
                            <td>
                                <?php echo $result->estoque; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Estoque Mínimo</strong></td>
                            <td>
                                <?php echo $result->estoqueMinimo; ?>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right"><strong>Origem do Produto</strong></td>
                            <td>
                                <?php
                                if (isset($result->origem)) {
                                    $origens = array(
                                        0 => '0 - Nacional (exceto as indicadas nos códigos 3, 4, 5 e 8)',
                                        1 => '1 - Estrangeira – Importação direta',
                                        2 => '2 - Estrangeira – Adquirida no mercado interno',
                                        3 => '3 - Nacional – Conteúdo de importação superior a 40% e inferior ou igual a 70%',
                                        4 => '4 - Nacional – Produzido conforme os processos produtivos básicos (PPB)',
                                        5 => '5 - Nacional – Conteúdo de importação inferior ou igual a 40%',
                                        6 => '6 - Estrangeira – Importação direta sem similar nacional, constante da CAMEX',
                                        7 => '7 - Estrangeira – Adquirida no mercado interno, sem similar nacional',
                                        8 => '8 - Nacional – Conteúdo de importação superior a 70%'
                                    );
                                    echo $origens[$result->origem];
                                } else {
                                    echo '0 - Nacional (exceto as indicadas nos códigos 3, 4, 5 e 8)';
                                }
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
