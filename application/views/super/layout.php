<?php
// Usar o mesmo layout padrão do sistema (topo + menu lateral + conteúdo + rodapé)
$this->load->config('permission');

if (!isset($this->data['configuration'])) {
    $this->data['configuration'] = [];
}
$this->data['configuration'] = array_merge([
    'app_name' => 'AgilizePro - Super Admin',
    'app_theme' => 'white',
    'per_page' => 20,
], $this->data['configuration']);

// Topo (navbar superior) - mesmo do restante do sistema
$this->load->view('tema/topo', $this->data);

// Menu lateral do Super - mesma estrutura do menu padrão (sidebar)
$this->load->view('tema/menu_super');

// Conteúdo (breadcrumb + view da tela)
$this->data['view'] = isset($this->data['view']) ? $this->data['view'] : null;
$this->load->view('tema/conteudo', $this->data);

// Rodapé
$this->load->view('tema/rodape');
