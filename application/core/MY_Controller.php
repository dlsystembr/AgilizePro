<?php

class MY_Controller extends CI_Controller
{
    public $data = [
        'configuration' => [
            'per_page' => 10,
            'next_link' => 'Próxima',
            'prev_link' => 'Anterior',
            'full_tag_open' => '<div class="pagination alternate"><ul>',
            'full_tag_close' => '</ul></div>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'cur_tag_open' => '<li><a style="color: #2D335B"><b>',
            'cur_tag_close' => '</b></a></li>',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'first_link' => 'Primeira',
            'last_link' => 'Última',
            'first_tag_open' => '<li>',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li>',
            'last_tag_close' => '</li>',
            'app_name' => 'Map-OS',
            'app_theme' => 'white',
            'os_notification' => 'cliente',
            'control_estoque' => '1',
            'notifica_whats' => '',
            'control_baixa' => '0',
            'control_editos' => '1',
            'control_datatable' => '1',
            'pix_key' => '',
            'tipo_documento' => 'NFe',
            'ambiente' => '2',
            'versao_nfe' => '4.00',
            'tipo_impressao_danfe' => '1',
            'orientacao_danfe' => 'P',
            'csc' => null,
            'csc_id' => null
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        if ((! session_id()) || (! $this->session->userdata('logado'))) {
            redirect('login');
        }
        $this->load_configuration();
    }

    private function load_configuration()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        
        // Inicializa o array de configurações com valores padrão
        $defaultConfigs = [
            'app_name' => 'Map-OS',
            'app_theme' => 'white',
            'control_datatable' => '1',
            'tipo_documento' => 'NFe',
            'ambiente' => 2,
            'versao_nfe' => '4.00',
            'tipo_impressao_danfe' => 1,
            'orientacao_danfe' => 'P',
            'csc' => null,
            'csc_id' => null,
            'per_page' => 10,
            'next_link' => 'Próxima',
            'prev_link' => 'Anterior',
            'full_tag_open' => '<div class="pagination alternate"><ul>',
            'full_tag_close' => '</ul></div>',
            'num_tag_open' => '<li>',
            'num_tag_close' => '</li>',
            'cur_tag_open' => '<li><a style="color: #2D335B"><b>',
            'cur_tag_close' => '</b></a></li>',
            'prev_tag_open' => '<li>',
            'prev_tag_close' => '</li>',
            'next_tag_open' => '<li>',
            'next_tag_close' => '</li>',
            'first_link' => 'Primeira',
            'last_link' => 'Última',
            'first_tag_open' => '<li>',
            'first_tag_close' => '</li>',
            'last_tag_open' => '<li>',
            'last_tag_close' => '</li>',
            'os_notification' => 'cliente',
            'control_estoque' => '1',
            'notifica_whats' => '',
            'control_baixa' => '0',
            'control_editos' => '1',
            'pix_key' => '',
            'app_version' => $this->config->item('app_version')
        ];
        
        try {
            $query = $this->CI->db->get('configuracoes');
            
            if ($query && $query->num_rows() > 0) {
                $configs = [];
                foreach ($query->result() as $row) {
                    $configs[$row->config] = $row->valor;
                }
                // Mescla com as configurações padrão
                $this->data['configuration'] = array_merge($defaultConfigs, $configs);
            } else {
                $this->data['configuration'] = $defaultConfigs;
            }
        } catch (Exception $e) {
            log_message('error', 'Error loading configurations: ' . $e->getMessage());
            $this->data['configuration'] = $defaultConfigs;
        }
    }

    public function layout()
    {
        // Garante que as configurações sejam um array
        if (is_object($this->data['configuration'])) {
            $this->data['configuration'] = json_decode(json_encode($this->data['configuration']), true);
        }
        
        // load views
        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu');
        $this->load->view('tema/conteudo');
        $this->load->view('tema/rodape');
    }
}
