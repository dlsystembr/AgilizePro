<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

class PedidosCompra extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('pedidoscompra_model');
        $this->load->model('mapos_model');
        $this->data['menuPedidosCompra'] = 'PedidosCompra';
        $this->data['configuration'] = $this->mapos_model->getConfig();
    }
    
    public function index()
    {
        $this->gerenciar();
    }
    
    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vPedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar pedidos de compra.');
            redirect(base_url());
        }
        
        $this->load->library('pagination');
        
        $where_array = [];
        
        $pesquisa = $this->input->get('pesquisa');
        $status = $this->input->get('status');
        $de = $this->input->get('data');
        $ate = $this->input->get('data2');
        
        if ($pesquisa) {
            $where_array['pesquisa'] = $pesquisa;
        }
        if ($status) {
            $where_array['status'] = $status;
        }
        if ($de) {
            $where_array['de'] = $de;
        }
        if ($ate) {
            $where_array['ate'] = $ate;
        }
        
        $this->data['configuration']['base_url'] = site_url('pedidoscompra/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->pedidoscompra_model->count('pedidos_compra');
        
        if (count($where_array) > 0) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
            $this->data['configuration']['first_url'] = base_url("index.php/pedidoscompra/gerenciar")."?pesquisa={$pesquisa}&status={$status}&data={$de}&data2={$ate}";
        }
        
        $this->pagination->initialize($this->data['configuration']);
        
        $this->data['results'] = $this->pedidoscompra_model->get('pedidos_compra', '*', $where_array, $this->data['configuration']['per_page'], $this->uri->segment(3));
        
        foreach ($this->data['results'] as $key => $pedido) {
            $this->data['results'][$key]->totalProdutos = $this->pedidoscompra_model->getTotalPedido($pedido->idPedido);
        }
        
        $this->data['view'] = 'pedidoscompra/pedidoscompra';
        
        return $this->layout();
    }
    
    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aPedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar pedidos de compra.');
            redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        
        if ($this->input->post()) {
            $this->form_validation->set_rules('fornecedor_id', 'Fornecedor', 'required|trim');
            $this->form_validation->set_rules('usuario_id', 'Responsável', 'required|trim');
            $this->form_validation->set_rules('data_pedido', 'Data do Pedido', 'required|trim');
            
            if ($this->form_validation->run() == false) {
                $this->data['custom_error'] = true;
            } else {
                $dataPedido = $this->input->post('data_pedido');

                try {
                    $dataPedido = explode('/', $dataPedido);
                    $dataPedido = $dataPedido[2] . '-' . $dataPedido[1] . '-' . $dataPedido[0];
                } catch (Exception $e) {
                    $dataPedido = date('Y-m-d');
                }

                $data = [
                    'data_pedido' => $dataPedido,
                    'observacoes' => $this->input->post('observacoes'),
                    'fornecedor_id' => $this->input->post('fornecedor_id'),
                    'usuario_id' => $this->input->post('usuario_id'),
                    'status' => 'Pendente'
                ];

                $id = $this->pedidoscompra_model->add('pedidos_compra', $data, true);

                if (is_numeric($id)) {
                    $this->session->set_flashdata('success', 'Pedido de compra iniciado com sucesso, adicione os produtos.');
                    log_info('Adicionou um pedido de compra. ID: ' . $id);
                    redirect(site_url('pedidoscompra/editar/') . $id);
                } else {
                    $this->data['custom_error'] = true;
                }
            }
        }
        
        $this->data['view'] = 'pedidoscompra/adicionarPedido';
        return $this->layout();
    }
    
    public function editar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }
        
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pedidos de compra');
            redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';
        
        $this->data['editavel'] = $this->pedidoscompra_model->isEditable($this->input->post('idPedido'));
        if (!$this->data['editavel']) {
            $this->session->set_flashdata('error', 'Este pedido de compra já está finalizado e não pode ser alterado.');
            redirect(site_url('pedidoscompra'));
        }
        
        if ($this->form_validation->run('pedidoscompra') == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        
            $dataPedido = $this->input->post('dataPedido');

            try {
                $dataPedido = explode('/', $dataPedido);
                $dataPedido = $dataPedido[2] . '-' . $dataPedido[1] . '-' . $dataPedido[0];
            } catch (Exception $e) {
                $dataPedido = date('Y/m/d');
            }

            $data = [
                'data_pedido' => $dataPedido,
                'observacoes' => $this->input->post('observacoes'),
                'fornecedor_id' => $this->input->post('fornecedor_id'),
                'usuario_id' => $this->input->post('usuario_id'),
                'status' => $this->input->post('status')
            ];

            if ($this->pedidoscompra_model->edit('pedidos_compra', $data, 'idPedido', $this->input->post('idPedido')) == true) {
                $this->session->set_flashdata('success', 'Pedido de compra editado com sucesso!');
                log_info('Alterou um pedido de compra. ID: ' . $this->input->post('idPedido'));
                redirect(site_url('pedidoscompra/editar/') . $this->input->post('idPedido'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro</p></div>';
            }
        }
        
        $this->data['result'] = $this->pedidoscompra_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->pedidoscompra_model->getProdutos($this->uri->segment(3));
        $this->data['view'] = 'pedidoscompra/editarPedido';
        
        return $this->layout();
    }
    
    public function visualizar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }
        
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vPedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar pedidos de compra.');
            redirect(base_url());
        }
        
        $this->data['custom_error'] = '';
        $this->data['result'] = $this->pedidoscompra_model->getById($this->uri->segment(3));
        $this->data['produtos'] = $this->pedidoscompra_model->getProdutos($this->uri->segment(3));
        $this->data['emitente'] = $this->mapos_model->getEmitente();
        
        $this->data['view'] = 'pedidoscompra/visualizarPedido';
        
        return $this->layout();
    }
    
    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dPedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir pedidos de compra');
            redirect(base_url());
        }
        
        $id = $this->input->post('idPedido');
        
        $editavel = $this->pedidoscompra_model->isEditable($id);
        if (!$editavel) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir. Pedido de compra já finalizado');
            redirect(site_url('pedidoscompra'));
        }
        
        if ($this->pedidoscompra_model->delete('itens_pedido', 'pedido_id', $id) == true) {
            $this->pedidoscompra_model->delete('pedidos_compra', 'idPedido', $id);
            log_info('Removeu um pedido de compra. ID: ' . $id);
            $this->session->set_flashdata('success', 'Pedido de compra excluído com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir pedido de compra.');
        }
        redirect(site_url('pedidoscompra/gerenciar/'));
    }
    
    public function autoCompleteProduto()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->pedidoscompra_model->autoCompleteProduto($q);
        }
    }
    
    public function autoCompleteFornecedor()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->pedidoscompra_model->autoCompleteFornecedor($q);
        }
    }
    
    public function autoCompleteUsuario()
    {
        if (isset($_GET['term'])) {
            $q = strtolower($_GET['term']);
            $this->pedidoscompra_model->autoCompleteUsuario($q);
        }
    }
    
    public function adicionarProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pedidos de compra.');
            redirect(base_url());
        }
        
        $this->load->library('form_validation');
        $this->form_validation->set_rules('quantidade', 'Quantidade', 'trim|required');
        $this->form_validation->set_rules('idProduto', 'Produto', 'trim|required');
        $this->form_validation->set_rules('idPedido', 'Pedido', 'trim|required');
        
        $idPedido = $this->input->post('idPedido');
        $editavel = $this->pedidoscompra_model->isEditable($idPedido);
        if (!$editavel) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(422)
                ->set_output(json_encode(['result' => false, 'messages' => '<br /><br /> <strong>Motivo:</strong> Pedido já finalizado']));
        }
        
        if ($this->form_validation->run() == false) {
            echo json_encode(['result' => false]);
        } else {
            $preco = $this->input->post('preco');
            $quantidade = $this->input->post('quantidade');
            $subtotal = $preco * $quantidade;
            $produto = $this->input->post('idProduto');
            $data = [
                'pedido_id' => $idPedido,
                'produto_id' => $produto,
                'quantidade' => $quantidade,
                'preco_unitario' => $preco,
                'subtotal' => $subtotal
            ];
            
            if ($this->pedidoscompra_model->add('itens_pedido', $data) == true) {
                log_info('Adicionou produto a um pedido de compra. ID (pedido): ' . $idPedido);
                echo json_encode(['result' => true]);
            } else {
                echo json_encode(['result' => false]);
            }
        }
    }
    
    public function excluirProduto()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pedidos de compra.');
            redirect(base_url());
        }
        
        $ID = $this->input->post('idProduto');
        $idPedido = $this->input->post('idPedido');
        
        if ($this->pedidoscompra_model->delete('itens_pedido', 'id', $ID) == true) {
            log_info('Removeu produto de um pedido de compra. ID (pedido): ' . $idPedido);
            echo json_encode(['result' => true]);
        } else {
            echo json_encode(['result' => false]);
        }
    }
    
    public function aprovar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'ePedidoCompra')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar pedidos de compra.');
            redirect(base_url());
        }
        
        $id = $this->input->post('id');
        $editavel = $this->pedidoscompra_model->isEditable($id);
        if (!$editavel) {
            $this->session->set_flashdata('error', 'Este pedido já está finalizado.');
            redirect(site_url('pedidoscompra'));
        }
        
        $data = [
            'status' => 'Aprovado',
            'data_aprovacao' => date('Y-m-d')
        ];
        
        if ($this->pedidoscompra_model->edit('pedidos_compra', $data, 'id', $id)) {
            log_info('Alterou status do pedido de compra para: Aprovado. ID: ' . $id);
            $this->session->set_flashdata('success', 'Pedido de compra aprovado com sucesso!');
        } else {
            $this->session->set_flashdata('error', 'Erro ao tentar aprovar pedido de compra!');
        }
        
        redirect(site_url('pedidoscompra/editar/') . $id);
    }
} 