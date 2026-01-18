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