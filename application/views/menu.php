<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vClassificacaoFiscal')) { ?>
    <li>
        <a href="<?php echo base_url(); ?>index.php/classificacaoFiscal">
            <i class="fa fa-tags"></i> <span>Classificação Fiscal</span>
        </a>
    </li>
<?php } ?> 
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vNcm')) { ?>
    <li>
        <a href="<?php echo base_url(); ?>index.php/ncms">
            <i class="fa fa-list"></i> <span>NCMs</span>
        </a>
    </li>
<?php } ?>
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vAliquota')) { ?>
    <li>
        <a href="<?php echo base_url() ?>index.php/aliquotas">
            <i class="bx bx-percentage"></i>
            <span class="link_name">Alíquotas</span>
        </a>
    </li>
<?php } ?>
<li class="<?php echo $this->uri->segment(1) == 'faturamentoEntrada' ? 'active' : ''; ?>">
    <a href="<?php echo base_url() ?>index.php/faturamentoEntrada">
        <i class="fa fa-file-text"></i> <span>Faturamento de Entrada</span>
    </a>
</li>
<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vRelatorio')) { ?>
    <li class="treeview">
        <a href="#"><i class="fa fa-bar-chart"></i> <span>Relatórios</span>
            <span class="pull-right-container">
                <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/clientes"><i class="fa fa-circle-o"></i> Relatório de Clientes</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/produtos"><i class="fa fa-circle-o"></i> Relatório de Produtos</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/servicos"><i class="fa fa-circle-o"></i> Relatório de Serviços</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/os"><i class="fa fa-circle-o"></i> Relatório de OS</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/vendas"><i class="fa fa-circle-o"></i> Relatório de Vendas</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rContrato')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/contratos"><i class="fa fa-circle-o"></i> Relatório de Contratos</a></li>
            <?php } ?>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/financeiro"><i class="fa fa-circle-o"></i> Relatório Financeiro</a></li>
            <?php } ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/receitas"><i class="fa fa-circle-o"></i> Relatório de Receitas</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/despesas"><i class="fa fa-circle-o"></i> Relatório de Despesas</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/estatisticas"><i class="fa fa-circle-o"></i> Estatísticas de Faturamento</a></li>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rNfe')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/nfe_emitidas"><i class="fa fa-circle-o"></i> Relatório de NF-e Emitidas</a></li>
            <?php } ?>
        </ul>
    </li>
<?php } ?> 