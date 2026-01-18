<div class="row-fluid" style="margin-top:0">
    <div class="span12">
        <div class="widget-box">
            <div class="widget-title">
                <span class="icon">
                    <i class="fas fa-file-invoice"></i>
                </span>
                <h5>Emitir Nota Fiscal</h5>
            </div>
            <div class="widget-content">
                <div class="row-fluid">
                    <div class="span12">
                        <form action="<?php echo base_url() ?>index.php/nfe/emitirNota" method="post" id="formNFe">
                            <input type="hidden" name="venda_id" value="<?php echo isset($venda) ? $venda->idVendas : '' ?>">
                            
                            <div class="accordion" id="accordion2">
                                <!-- Dados do Emitente -->
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                                            <i class="fas fa-building"></i> Dados do Emitente
                                        </a>
                                    </div>
                                    <div id="collapseOne" class="accordion-body collapse in">
                                        <div class="accordion-inner">
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="emitente_nome">Nome</label>
                                                    <input type="text" class="span12" name="emitente_nome" value="<?php echo $emitente->nome ?>" readonly>
                                                </div>
                                                <div class="span6">
                                                    <label for="emitente_cnpj">CNPJ</label>
                                                    <input type="text" class="span12" name="emitente_cnpj" value="<?php echo $emitente->cnpj ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="emitente_ie">Inscrição Estadual</label>
                                                    <input type="text" class="span12" name="emitente_ie" value="<?php echo $emitente->ie ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dados do Destinatário -->
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                                            <i class="fas fa-user"></i> Dados do Destinatário
                                        </a>
                                    </div>
                                    <div id="collapseTwo" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_nome">Nome</label>
                                                    <input type="text" class="span12" name="destinatario_nome" value="<?php echo isset($cliente) ? $cliente->nomeCliente : '' ?>" required>
                                                </div>
                                                <div class="span6">
                                                    <label for="destinatario_cnpj">CNPJ/CPF</label>
                                                    <input type="text" class="span12" name="destinatario_cnpj" value="<?php echo isset($cliente) ? $cliente->documento : '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_ie">Inscrição Estadual</label>
                                                    <input type="text" class="span12" name="destinatario_ie" value="<?php echo isset($cliente) ? $cliente->inscricao : '' ?>">
                                                </div>
                                                <div class="span6">
                                                    <label for="destinatario_email">Email</label>
                                                    <input type="email" class="span12" name="destinatario_email" value="<?php echo isset($cliente) ? $cliente->email : '' ?>">
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_endereco">Endereço</label>
                                                    <input type="text" class="span12" name="destinatario_endereco" value="<?php echo isset($cliente) ? $cliente->rua : '' ?>" required>
                                                </div>
                                                <div class="span6">
                                                    <label for="destinatario_numero">Número</label>
                                                    <input type="text" class="span12" name="destinatario_numero" value="<?php echo isset($cliente) ? $cliente->numero : '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_complemento">Complemento</label>
                                                    <input type="text" class="span12" name="destinatario_complemento" value="<?php echo isset($cliente) ? $cliente->complemento : '' ?>">
                                                </div>
                                                <div class="span6">
                                                    <label for="destinatario_bairro">Bairro</label>
                                                    <input type="text" class="span12" name="destinatario_bairro" value="<?php echo isset($cliente) ? $cliente->bairro : '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_cidade">Cidade</label>
                                                    <input type="text" class="span12" name="destinatario_cidade" value="<?php echo isset($cliente) ? $cliente->cidade : '' ?>" required>
                                                </div>
                                                <div class="span6">
                                                    <label for="destinatario_estado">UF</label>
                                                    <input type="text" class="span12" name="destinatario_estado" value="<?php echo isset($cliente) ? $cliente->estado : '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_ibge">Código IBGE</label>
                                                    <input type="text" class="span12" name="destinatario_ibge" value="<?php echo isset($cliente) && property_exists($cliente, 'ibge') ? $cliente->ibge : '' ?>" required>
                                                </div>
                                                <div class="span6">
                                                    <label for="destinatario_cep">CEP</label>
                                                    <input type="text" class="span12" name="destinatario_cep" value="<?php echo isset($cliente) ? $cliente->cep : '' ?>" required>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="destinatario_telefone">Telefone</label>
                                                    <input type="text" class="span12" name="destinatario_telefone" value="<?php echo isset($cliente) ? $cliente->telefone : '' ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dados da Nota -->
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseThree">
                                            <i class="fas fa-file-alt"></i> Dados da Nota
                                        </a>
                                    </div>
                                    <div id="collapseThree" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="natureza_operacao">Natureza da Operação</label>
                                                    <input type="text" class="span12" name="natureza_operacao" value="<?php echo $operacao->nome_operacao; ?>" readonly>
                                                </div>
                                                <div class="span6">
                                                    <label for="forma_pagamento">Forma de Pagamento</label>
                                                    <select class="span12" name="forma_pagamento" required>
                                                        <option value="0">À Vista</option>
                                                        <option value="1">À Prazo</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="span12">
                                                <div class="span6">
                                                    <label for="tipo_nota">Tipo de Nota</label>
                                                    <select class="span12" name="tipo_nota" required>
                                                        <option value="0">Saída</option>
                                                        <option value="1">Entrada</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Produtos -->
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">
                                            <i class="fas fa-box"></i> Produtos
                                        </a>
                                    </div>
                                    <div id="collapseFour" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Código</th>
                                                        <th>Descrição</th>
                                                        <th>NCM</th>
                                                        <th>CFOP</th>
                                                        <th>Unidade</th>
                                                        <th>Quantidade</th>
                                                        <th>Valor Unitário</th>
                                                        <th>Valor Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (isset($produtos)) { ?>
                                                        <?php foreach ($produtos as $p) { ?>
                                                            <tr>
                                                                <td><?php echo $p->idProdutos ?></td>
                                                                <td><?php echo $p->descricao ?></td>
                                                                <td><?php echo $p->NCMs ?></td>
                                                                <td><?php echo $p->cfop ?></td>
                                                                <td><?php echo $p->unidade ?></td>
                                                                <td><?php echo $p->quantidade ?></td>
                                                                <td>R$ <?php echo number_format($p->preco, 2, ',', '.') ?></td>
                                                                <td>R$ <?php echo number_format($p->preco * $p->quantidade, 2, ',', '.') ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8">
                                                                    <div class="span12">
                                                                        <div class="span6">
                                                                            <label for="cst">CST/CSOSN</label>
                                                                            <input type="text" class="span12" name="cst" value="<?php echo $p->cst ?? $p->csosn; ?>" readonly>
                                                                        </div>
                                                                        <div class="span6">
                                                                            <label for="cfop">CFOP</label>
                                                                            <input type="text" class="span12" name="cfop" value="<?php echo $p->cfop; ?>" readonly>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8">
                                                                    <div class="span12">
                                                                        <div class="span3">
                                                                            <label for="cst_ipi">CST IPI</label>
                                                                            <input type="text" class="span12" name="cst_ipi" value="<?php echo isset($p->cst_ipi) ? $p->cst_ipi : ''; ?>" readonly>
                                                                        </div>
                                                                        <div class="span3">
                                                                            <label for="aliq_ipi">Alíquota IPI (%)</label>
                                                                            <input type="text" class="span12" name="aliq_ipi" value="<?php echo isset($p->aliq_ipi) ? $p->aliq_ipi : ''; ?>" readonly>
                                                                        </div>
                                                                        <div class="span3">
                                                                            <label for="valor_ipi">Valor IPI</label>
                                                                            <input type="text" class="span12" name="valor_ipi" value="<?php echo isset($p->valor_ipi) ? number_format($p->valor_ipi, 2, ',', '.') : ''; ?>" readonly>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8">
                                                                    <div class="span12">
                                                                        <div class="span3">
                                                                            <label for="cst_pis">CST PIS</label>
                                                                            <input type="text" class="span12" name="cst_pis" value="<?php echo isset($p->cst_pis) ? $p->cst_pis : ''; ?>" readonly>
                                                                        </div>
                                                                        <div class="span3">
                                                                            <label for="aliq_pis">Alíquota PIS (%)</label>
                                                                            <input type="text" class="span12" name="aliq_pis" value="<?php echo isset($p->aliq_pis) ? $p->aliq_pis : ''; ?>" readonly>
                                                                        </div>
                                                                        <div class="span3">
                                                                            <label for="valor_pis">Valor PIS</label>
                                                                            <input type="text" class="span12" name="valor_pis" value="<?php echo isset($p->valor_pis) ? number_format($p->valor_pis, 2, ',', '.') : ''; ?>" readonly>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8">
                                                                    <div class="span12">
                                                                        <div class="span3">
                                                                            <label for="cst_cofins">CST COFINS</label>
                                                                            <input type="text" class="span12" name="cst_cofins" value="<?php echo isset($p->cst_cofins) ? $p->cst_cofins : ''; ?>" readonly>
                                                                        </div>
                                                                        <div class="span3">
                                                                            <label for="aliq_cofins">Alíquota COFINS (%)</label>
                                                                            <input type="text" class="span12" name="aliq_cofins" value="<?php echo isset($p->aliq_cofins) ? $p->aliq_cofins : ''; ?>" readonly>
                                                                        </div>
                                                                        <div class="span3">
                                                                            <label for="valor_cofins">Valor COFINS</label>
                                                                            <input type="text" class="span12" name="valor_cofins" value="<?php echo isset($p->valor_cofins) ? number_format($p->valor_cofins, 2, ',', '.') : ''; ?>" readonly>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr>
                                                            <td colspan="8">Nenhum produto encontrado</td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Impostos -->
                                <div class="accordion-group">
                                    <div class="accordion-heading">
                                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFive">
                                            <i class="fas fa-percentage"></i> Impostos
                                        </a>
                                    </div>
                                    <div id="collapseFive" class="accordion-body collapse">
                                        <div class="accordion-inner">
                                            <div class="span12">
                                                <div class="span3">
                                                    <label for="crt">CRT</label>
                                                    <input type="text" class="span12" name="crt" value="<?php 
                                                        $this->db->select('valor');
                                                        $this->db->from('configuracoes');
                                                        $this->db->where('config', 'regime_tributario');
                                                        $this->db->limit(1);
                                                        $regime = $this->db->get()->row();
                                                        $crt = ($regime && strtolower($regime->valor) === 'simples nacional') ? '1' : '3';
                                                        echo $crt;
                                                    ?>" readonly>
                                                </div>
                                                <div class="span3">
                                                    <label for="aliq_icms">Alíquota ICMS (%)</label>
                                                    <input type="text" class="span12" name="aliq_icms" value="<?php 
                                                        if ($crt == '3') {
                                                            echo ($cliente->estado == 'GO') ? '19,00' : '12,00';
                                                        } else {
                                                            echo '0,00';
                                                        }
                                                    ?>" readonly>
                                                </div>
                                                <div class="span3">
                                                    <label for="base_icms">Base ICMS</label>
                                                    <input type="text" class="span12" name="base_icms" value="<?php 
                                                        if ($crt == '3') {
                                                            echo number_format($venda->valorTotal, 2, ',', '.');
                                                        } else {
                                                            echo '0,00';
                                                        }
                                                    ?>" readonly>
                                                </div>
                                                <div class="span3">
                                                    <label for="valor_icms">Valor ICMS</label>
                                                    <input type="text" class="span12" name="valor_icms" value="<?php 
                                                        if ($crt == '3') {
                                                            $aliq = ($cliente->estado == 'GO') ? 19 : 12;
                                                            $valor = ($venda->valorTotal * $aliq) / 100;
                                                            echo number_format($valor, 2, ',', '.');
                                                        } else {
                                                            echo '0,00';
                                                        }
                                                    ?>" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-actions">
                                <div class="span12">
                                    <div class="span6 offset3" style="display: flex;justify-content: center">
                                        <button type="submit" class="button btn btn-primary">
                                            <span class="button__icon"><i class='bx bx-file'></i></span><span class="button__text2">Emitir NFe</span></button>
                                        <a href="<?php echo base_url() ?>index.php/nfe/emitirNFCe/<?php echo $venda->idVendas; ?>" class="button btn btn-success">
                                            <span class="button__icon"><i class='bx bx-file'></i></span><span class="button__text2">Emitir NFC-e</span></a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $('#formNFe').submit(function() {
        var $btn = $(this).find('button[type="submit"]');
        $btn.prop('disabled', true);
        $btn.find('.button__text2').text('Emitindo...');
    });
});
</script> 