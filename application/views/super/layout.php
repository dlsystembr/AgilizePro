<?php
// Usar o layout padrão do sistema
$this->load->config('permission');

// Configurações padrão para o super
if (!isset($this->data['configuration'])) {
    $this->data['configuration'] = [];
}
$this->data['configuration'] = array_merge([
    'app_name' => 'AgilizePro - Super Admin',
    'app_theme' => 'white',
    'per_page' => 20,
], $this->data['configuration']);

// Carregar topo
$this->load->view('tema/topo', $this->data);
?>

<style>
  .super-menu-custom {
    background: #f8f9fa;
    padding: 10px 0;
    margin-bottom: 20px;
    border-bottom: 2px solid #e0e0e0;
  }
  .super-menu-custom a {
    margin: 0 15px;
    color: #333;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 15px;
    border-radius: 4px;
    transition: all 0.3s;
    display: inline-block;
  }
  .super-menu-custom a:hover {
    background: #667eea;
    color: white;
  }
  .super-menu-custom a.active {
    background: #667eea;
    color: white;
  }
</style>

<div class="super-menu-custom">
  <div class="container-fluid">
    <div class="row-fluid">
      <div class="span12">
        <a href="<?= base_url('index.php/super') ?>" class="<?= $this->uri->segment(2) == '' || $this->uri->segment(2) == 'index' ? 'active' : '' ?>">
          <i class="icon-home"></i> Dashboard
        </a>
        <a href="<?= base_url('index.php/super/tenants') ?>" class="<?= $this->uri->segment(2) == 'tenants' ? 'active' : '' ?>">
          <i class="icon-building"></i> Tenants
        </a>
        <a href="<?= base_url('index.php/super/superUsuarios') ?>" class="<?= $this->uri->segment(2) == 'superUsuarios' ? 'active' : '' ?>">
          <i class="icon-user"></i> Super Usuários
        </a>
        <a href="<?= base_url('index.php/login/sair') ?>" style="float: right; color: #d32f2f;">
          <i class="icon-off"></i> Sair
        </a>
      </div>
    </div>
  </div>
</div>

<?php
// Carregar conteúdo usando o tema padrão
$this->data['view'] = isset($view) ? $view : null;
$this->load->view('tema/conteudo', $this->data);

// Carregar rodapé
$this->load->view('tema/rodape');
?>

