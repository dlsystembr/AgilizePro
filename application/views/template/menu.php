  <li <?php if(isset($menuPermissoes)) echo 'class="active"'; ?>>
    <a href="<?php echo base_url('permissoes'); ?>">
      <i class="fas fa-lock"></i> Permissões
    </a>
  </li>
  <li <?php if(isset($menuTributacaoProduto)) echo 'class="active"'; ?>>
    <a href="<?php echo base_url('tributacaoproduto'); ?>">
      <i class="fas fa-balance-scale"></i> Tributação Produto
    </a>
  </li> 