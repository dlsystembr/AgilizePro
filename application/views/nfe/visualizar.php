<div class="span12">
    <div class="widget-box">
        <div class="widget-title">
            <span class="icon">
                <i class="fas fa-eye"></i>
            </span>
            <h5>Visualizar NFe</h5>
        </div>
        <div class="widget-content">
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
                                    <input type="text" class="span12" value="<?php echo $emitente->nome ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="emitente_cnpj">CNPJ</label>
                                    <input type="text" class="span12" value="<?php echo $emitente->cnpj ?>" readonly>
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
                                    <input type="text" class="span12" value="<?php echo $nfe->destinatario_nome ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="destinatario_cnpj">CNPJ/CPF</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->destinatario_cnpj ?>" readonly>
                                </div>
                            </div>
                            <div class="span12">
                                <div class="span6">
                                    <label for="destinatario_ie">Inscrição Estadual</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->destinatario_ie ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="destinatario_email">Email</label>
                                    <input type="email" class="span12" value="<?php echo $nfe->destinatario_email ?>" readonly>
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
                                    <input type="text" class="span12" value="<?php echo $nfe->natureza_operacao ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="forma_pagamento">Forma de Pagamento</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->forma_pagamento == '0' ? 'À Vista' : 'À Prazo' ?>" readonly>
                                </div>
                            </div>
                            <div class="span12">
                                <div class="span6">
                                    <label for="tipo_nota">Tipo de Nota</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->tipo_nota == '0' ? 'Saída' : 'Entrada' ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Impostos -->
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseFour">
                            <i class="fas fa-percentage"></i> Impostos
                        </a>
                    </div>
                    <div id="collapseFour" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <div class="span12">
                                <div class="span6">
                                    <label for="icms_cst">ICMS - CST</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->icms_cst ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="icms_aliquota">ICMS - Alíquota (%)</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->icms_aliquota ?>" readonly>
                                </div>
                            </div>
                            <div class="span12">
                                <div class="span6">
                                    <label for="pis_cst">PIS - CST</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->pis_cst ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="cofins_cst">COFINS - CST</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->cofins_cst ?>" readonly>
                                </div>
                            </div>
                            <div class="span12">
                                <div class="span6">
                                    <label for="ipi_cst">IPI - CST</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->ipi_cst ?>" readonly>
                                </div>
                                <div class="span6">
                                    <label for="ipi_aliquota">IPI - Alíquota (%)</label>
                                    <input type="text" class="span12" value="<?php echo $nfe->ipi_aliquota ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo base_url() ?>index.php/nfe/gerenciar" class="btn">Voltar</a>
            </div>
        </div>
    </div>
</div> 