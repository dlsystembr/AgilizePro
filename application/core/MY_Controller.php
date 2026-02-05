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
            'app_name' => 'AgilizePro',
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

        // Verificar se é super usuário (não precisa de ten_id)
        $is_super = $this->session->userdata('is_super');
        
        if ((! session_id()) || (! $this->session->userdata('logado'))) {
            redirect('login');
        }
        
        // Se não for super usuário, carregar configurações normalmente
        if (!$is_super) {
            $this->load_configuration();
        }
    }

    private function load_configuration()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
        
        // Inicializa o array de configurações com valores padrão
        $defaultConfigs = [
            'app_name' => 'AgilizePro',
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

        // Menus liberados para a empresa do usuário (null = sem restrição; array = só esses identificadores)
        $this->data['menus_liberados'] = null;
        $emp_id = $this->session->userdata('emp_id');
        if ($emp_id && $this->db->table_exists('menu_empresa') && $this->db->table_exists('menus')) {
            $rows = $this->db->select('m.men_identificador')
                ->from('menu_empresa me')
                ->join('menus m', 'm.men_id = me.men_id')
                ->where('me.emp_id', $emp_id)
                ->get()->result();
            if (!empty($rows)) {
                $this->data['menus_liberados'] = array_map(function ($r) {
                    return $r->men_identificador;
                }, $rows);
            }
        }

        // load views
        $this->load->view('tema/topo', $this->data);
        $this->load->view('tema/menu', $this->data);
        $this->load->view('tema/conteudo');
        $this->load->view('tema/rodape');
    }
}
