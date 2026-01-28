<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vPermissao')) { ?>
    <li <?php if (isset($menuPermissoes))
        echo 'class="active"'; ?>>
        <a href="<?php echo base_url('permissoes'); ?>">
            <i class="fas fa-lock"></i> Permissões
        </a>
    </li>
<?php } ?>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vEmpresa')) { ?>
    <li <?php if (isset($menuEmpresas))
        echo 'class="active"'; ?>>
        <a href="<?php echo base_url('empresas'); ?>">
            <i class="fas fa-building"></i> Empresas
        </a>
    </li>
<?php } ?>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vTributacaoProduto')) { ?>
    <li <?php if (isset($menuTributacaoProduto))
        echo 'class="active"'; ?>>
        <a href="<?php echo base_url('tributacaoproduto'); ?>">
            <i class="fas fa-balance-scale"></i> Tributação Produto
        </a>
    </li>
<?php } ?>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vOperacaoComercial')) { ?>
    <li <?php if (isset($menuOperacaoComercial))
        echo 'class="active"'; ?>>
        <a href="<?php echo base_url('operacaocomercial'); ?>">
            <i class="fas fa-exchange-alt"></i> Operação Comercial
        </a>
    </li>
<?php } ?>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vCertificado')) { ?>
    <li <?php if (isset($menuCertificados))
        echo 'class="active"'; ?>>
        <a href="<?php echo base_url('certificados'); ?>">
            <i class="fas fa-certificate"></i> Certificados
        </a>
    </li>
<?php } ?>

<?php if ($this->permission->checkPermission($this->session->userdata('permissao'), 'vConfigFiscal')) { ?>
    <li <?php if (isset($menuConfigFiscais))
        echo 'class="active"'; ?>>
        <a href="<?php echo site_url('configuracoesfiscais'); ?>">
            <i class="fas fa-file-invoice-dollar"></i> Configurações Fiscais
        </a>
    </li>
<?php } ?>