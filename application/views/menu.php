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
            <li><a href="<?php echo base_url() ?>index.php/relatorios/clientes"><i class="fa fa-circle-o"></i> Relatório de Clientes</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/produtos"><i class="fa fa-circle-o"></i> Relatório de Produtos</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/servicos"><i class="fa fa-circle-o"></i> Relatório de Serviços</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/os"><i class="fa fa-circle-o"></i> Relatório de OS</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/vendas"><i class="fa fa-circle-o"></i> Relatório de Vendas</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/financeiro"><i class="fa fa-circle-o"></i> Relatório Financeiro</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/receitas"><i class="fa fa-circle-o"></i> Relatório de Receitas</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/despesas"><i class="fa fa-circle-o"></i> Relatório de Despesas</a></li>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/estatisticas"><i class="fa fa-circle-o"></i> Estatísticas de Faturamento</a></li>
            <?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'rNfe')) { ?>
            <li><a href="<?php echo base_url() ?>index.php/relatorios/nfe_emitidas"><i class="fa fa-circle-o"></i> Relatório de NF-e Emitidas</a></li>
            <?php } ?>
        </ul>
    </li>
<?php } ?> 