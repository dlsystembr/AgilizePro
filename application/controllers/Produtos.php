<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Produtos extends MY_Controller
{
    /**
     * @property CI_Input $input
     * @property CI_Session $session
     * @property CI_Loader $load
     * @property CI_URI $uri
     * @property Produtos_model $produtos_model
     * @property Usuarios_model $usuarios_model
     * @property CI_Form_validation $form_validation
     * @property CI_Pagination $pagination
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
        $this->load->model('produtos_model');
        $this->load->model('TributacaoProduto_model');
        $this->load->model('Ncm_model');
        $this->data['menuProdutos'] = 'Produtos';
    }

    public function index()
    {
        $this->gerenciar();
    }

    public function gerenciar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar produtos.');
            redirect(base_url());
        }

        $pesquisa = $this->input->get('pesquisa');

        $this->load->library('pagination');

        $this->data['configuration']['base_url'] = site_url('produtos/gerenciar/');
        $this->data['configuration']['total_rows'] = $this->produtos_model->count('produtos');
        if ($pesquisa) {
            $this->data['configuration']['suffix'] = "?pesquisa={$pesquisa}";
            $this->data['configuration']['first_url'] = base_url("index.php/produtos") . "\?pesquisa={$pesquisa}";
        }

        $this->pagination->initialize($this->data['configuration']);

        $this->data['results'] = $this->produtos_model->get('produtos', '*', $pesquisa, $this->data['configuration']['per_page'], $this->uri->segment(3));

        $this->data['view'] = 'produtos/produtos';

        return $this->layout();
    }

    public function adicionar()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar produtos.');
            redirect(base_url());
        }

        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Define regras de validação baseadas no tipo
        $tipo = $this->input->post('PRO_TIPO');

        if ($tipo == '2') { // Serviço
            $_POST['PRO_PRECO_COMPRA'] = $_POST['PRO_PRECO_COMPRA'] ?: '0';
            $_POST['PRO_PRECO_VENDA'] = $_POST['PRO_PRECO_VENDA'] ?: '0';
            $_POST['PRO_ESTOQUE'] = $_POST['PRO_ESTOQUE'] ?: '0';
            $_POST['PRO_ESTOQUE_MINIMO'] = $_POST['PRO_ESTOQUE_MINIMO'] ?: '0';
            $_POST['PRO_ORIGEM'] = $_POST['PRO_ORIGEM'] ?: '0';

            // Regras de validação para serviços
            $this->form_validation->set_rules('PRO_DESCRICAO', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('PRO_UNID_MEDIDA', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('PRO_CCLASS_SERV', 'cClass', 'required|trim');
        } else { // Produto
            // Regras de validação para produtos (usando a configuração padrão)
            $this->form_validation->set_rules('PRO_DESCRICAO', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('PRO_UNID_MEDIDA', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('PRO_PRECO_COMPRA', 'Preço de Compra', 'required|trim');
            $this->form_validation->set_rules('PRO_PRECO_VENDA', 'Preço de Venda', 'required|trim');
            $this->form_validation->set_rules('PRO_ESTOQUE', 'Estoque', 'required|trim');
            $this->form_validation->set_rules('PRO_ORIGEM', 'Origem do Produto', 'required|trim|integer|greater_than_equal_to[0]|less_than_equal_to[8]');
        }

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $precoCompra = $this->input->post('PRO_PRECO_COMPRA');
            $precoCompra = str_replace(',', '.', $precoCompra);
            $precoVenda = $this->input->post('PRO_PRECO_VENDA');
            $precoVenda = str_replace(',', '.', $precoVenda);
            $data = [
                'PRO_COD_BARRA' => $this->input->post('PRO_COD_BARRA'),
                'PRO_DESCRICAO' => $this->input->post('PRO_DESCRICAO'),
                'PRO_UNID_MEDIDA' => $this->input->post('PRO_UNID_MEDIDA'),
                'PRO_PRECO_COMPRA' => $precoCompra,
                'PRO_PRECO_VENDA' => $precoVenda,
                'PRO_ESTOQUE' => $this->input->post('PRO_ESTOQUE'),
                'PRO_ESTOQUE_MINIMO' => $this->input->post('PRO_ESTOQUE_MINIMO'),
                'PRO_NCM' => $this->input->post('PRO_NCM'),
                'NCM_ID' => $this->input->post('NCM_ID'),
                'PRO_ORIGEM' => $this->input->post('PRO_ORIGEM'),
                'PRO_TIPO' => $this->input->post('PRO_TIPO') ?: 1,
                'PRO_CCLASS_SERV' => $this->input->post('PRO_CCLASS_SERV'),
                'PRO_PESO_BRUTO' => $this->input->post('PRO_PESO_BRUTO') ? str_replace(',', '.', $this->input->post('PRO_PESO_BRUTO')) : null,
                'PRO_PESO_LIQUIDO' => $this->input->post('PRO_PESO_LIQUIDO') ? str_replace(',', '.', $this->input->post('PRO_PESO_LIQUIDO')) : null,
                'PRO_LARGURA' => $this->input->post('PRO_LARGURA') ? str_replace(',', '.', $this->input->post('PRO_LARGURA')) : null,
                'PRO_ALTURA' => $this->input->post('PRO_ALTURA') ? str_replace(',', '.', $this->input->post('PRO_ALTURA')) : null,
                'PRO_COMPRIMENTO' => $this->input->post('PRO_COMPRIMENTO') ? str_replace(',', '.', $this->input->post('PRO_COMPRIMENTO')) : null
            ];

            if ($this->produtos_model->add('produtos', $data) == true) {
                $id = $this->db->insert_id();
                $this->session->set_flashdata('success', 'Produto adicionado com sucesso!');
                log_info('Adicionou um produto');
                redirect(site_url('produtos/visualizar/' . $id));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>Ocorreu um erro ao tentar adicionar o produto.</p></div>';
            }
        }
        $this->data['tributacoes'] = $this->TributacaoProduto_model->get();
        $this->data['view'] = 'produtos/adicionarProduto';

        return $this->layout();
    }

    public function editar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para editar produtos.');
            redirect(base_url());
        }
        $this->load->library('form_validation');
        $this->data['custom_error'] = '';

        // Define regras de validação baseadas no tipo
        $tipo = $this->input->post('PRO_TIPO');

        if ($tipo == '2') { // Serviço
            $_POST['PRO_PRECO_COMPRA'] = $_POST['PRO_PRECO_COMPRA'] ?: '0';
            $_POST['PRO_PRECO_VENDA'] = $_POST['PRO_PRECO_VENDA'] ?: '0';
            $_POST['PRO_ESTOQUE'] = $_POST['PRO_ESTOQUE'] ?: '0';
            $_POST['PRO_ESTOQUE_MINIMO'] = $_POST['PRO_ESTOQUE_MINIMO'] ?: '0';
            $_POST['PRO_ORIGEM'] = $_POST['PRO_ORIGEM'] ?: '0';
            $_POST['PRO_PESO_BRUTO'] = '0.000';
            $_POST['PRO_PESO_LIQUIDO'] = '0.000';
            $_POST['PRO_LARGURA'] = '0.000';
            $_POST['PRO_ALTURA'] = '0.000';
            $_POST['PRO_COMPRIMENTO'] = '0.000';

            // Regras de validação para serviços
            $this->form_validation->set_rules('PRO_DESCRICAO', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('PRO_UNID_MEDIDA', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('PRO_CCLASS_SERV', 'cClass', 'required|trim');
        } else { // Produto
            // Regras de validação para produtos (usando a configuração padrão)
            $this->form_validation->set_rules('PRO_DESCRICAO', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('PRO_UNID_MEDIDA', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('PRO_PRECO_COMPRA', 'Preço de Compra', 'required|trim');
            $this->form_validation->set_rules('PRO_PRECO_VENDA', 'Preço de Venda', 'required|trim');
            $this->form_validation->set_rules('PRO_ESTOQUE', 'Estoque', 'required|trim');
            $this->form_validation->set_rules('PRO_ORIGEM', 'Origem do Produto', 'required|trim|integer|greater_than_equal_to[0]|less_than_equal_to[8]');
        }

        // Sanitiza campos decimais para validação (substitui vírgula por ponto)
        $decimalFields = ['PRO_PESO_BRUTO', 'PRO_PESO_LIQUIDO', 'PRO_LARGURA', 'PRO_ALTURA', 'PRO_COMPRIMENTO'];
        foreach ($decimalFields as $field) {
            if (isset($_POST[$field])) {
                $_POST[$field] = str_replace(',', '.', $_POST[$field]);
            }
        }

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $precoCompra = $this->input->post('PRO_PRECO_COMPRA');
            $precoCompra = str_replace(',', '.', $precoCompra);
            $precoVenda = $this->input->post('PRO_PRECO_VENDA');
            $precoVenda = str_replace(',', '.', $precoVenda);
            $data = [
                'PRO_COD_BARRA' => $this->input->post('PRO_COD_BARRA'),
                'PRO_DESCRICAO' => $this->input->post('PRO_DESCRICAO'),
                'PRO_UNID_MEDIDA' => $this->input->post('PRO_UNID_MEDIDA'),
                'PRO_PRECO_COMPRA' => $precoCompra,
                'PRO_PRECO_VENDA' => $precoVenda,
                'PRO_ESTOQUE' => $this->input->post('PRO_ESTOQUE'),
                'PRO_ESTOQUE_MINIMO' => $this->input->post('PRO_ESTOQUE_MINIMO'),
                'PRO_NCM' => $this->input->post('PRO_NCM'),
                'NCM_ID' => $this->input->post('NCM_ID'),
                'PRO_ORIGEM' => $this->input->post('PRO_ORIGEM'),
                'PRO_TIPO' => $this->input->post('PRO_TIPO') ?: 1,
                'PRO_CCLASS_SERV' => $this->input->post('PRO_CCLASS_SERV'),
                'PRO_PESO_BRUTO' => $this->input->post('PRO_PESO_BRUTO') ? str_replace(',', '.', $this->input->post('PRO_PESO_BRUTO')) : null,
                'PRO_PESO_LIQUIDO' => $this->input->post('PRO_PESO_LIQUIDO') ? str_replace(',', '.', $this->input->post('PRO_PESO_LIQUIDO')) : null,
                'PRO_LARGURA' => $this->input->post('PRO_LARGURA') ? str_replace(',', '.', $this->input->post('PRO_LARGURA')) : null,
                'PRO_ALTURA' => $this->input->post('PRO_ALTURA') ? str_replace(',', '.', $this->input->post('PRO_ALTURA')) : null,
                'PRO_COMPRIMENTO' => $this->input->post('PRO_COMPRIMENTO') ? str_replace(',', '.', $this->input->post('PRO_COMPRIMENTO')) : null
            ];

            if ($this->produtos_model->edit('produtos', $data, 'PRO_ID', $this->input->post('PRO_ID')) == true) {
                $this->session->set_flashdata('success', 'Produto editado com sucesso!');
                log_info('Alterou um produto. ID: ' . $this->input->post('PRO_ID'));
                redirect(site_url('produtos/visualizar/') . $this->input->post('PRO_ID'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured</p></div>';
            }
        }

        $this->data['result'] = $this->produtos_model->getById($this->uri->segment(3));
        $this->data['tributacoes'] = $this->TributacaoProduto_model->get();
        $this->data['view'] = 'produtos/editarProduto';

        return $this->layout();
    }

    public function visualizar()
    {
        if (!$this->uri->segment(3) || !is_numeric($this->uri->segment(3))) {
            $this->session->set_flashdata('error', 'Item não pode ser encontrado, parâmetro não foi passado corretamente.');
            redirect('mapos');
        }

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar produtos.');
            redirect(base_url());
        }

        $this->data['result'] = $this->produtos_model->getById($this->uri->segment(3));

        if ($this->data['result'] == null) {
            $this->session->set_flashdata('error', 'Produto não encontrado.');
            redirect(site_url('produtos/editar/') . $this->input->post('PRO_ID'));
        }

        $this->data['tributacoes'] = $this->TributacaoProduto_model->get();
        $this->data['view'] = 'produtos/visualizarProduto';

        return $this->layout();
    }

    public function excluir()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'dProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para excluir produtos.');
            redirect(base_url());
        }

        $id = $this->input->post('id');
        if ($id == null) {
            $this->session->set_flashdata('error', 'Erro ao tentar excluir produto.');
            redirect(base_url() . 'index.php/produtos/gerenciar/');
        }

        // Verificar se o produto tem movimentação
        $this->db->where('PRO_ID', $id);
        $movimentacao = $this->db->get('itens_de_vendas')->num_rows();

        if ($movimentacao > 0) {
            $this->session->set_flashdata('error', 'Este produto não pode ser excluído pois possui movimentação.');
            redirect(base_url() . 'index.php/produtos/gerenciar/');
        }

        $this->produtos_model->delete('produtos_os', 'PRO_ID', $id);
        $this->produtos_model->delete('itens_de_vendas', 'PRO_ID', $id);
        $this->produtos_model->delete('produtos', 'PRO_ID', $id);

        log_info('Removeu um produto. ID: ' . $id);

        $this->session->set_flashdata('success', 'Produto excluido com sucesso!');
        redirect(site_url('produtos/gerenciar/'));
    }

    public function atualizar_estoque()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'eProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para atualizar estoque de produtos.');
            redirect(base_url());
        }

        $idProduto = $this->input->post('id');
        $novoEstoque = $this->input->post('estoque');
        $estoqueAtual = $this->input->post('estoqueAtual');

        $estoque = $estoqueAtual + $novoEstoque;

        $data = [
            'PRO_ESTOQUE' => $estoque,
        ];

        if ($this->produtos_model->edit('produtos', $data, 'PRO_ID', $idProduto) == true) {
            $this->session->set_flashdata('success', 'Estoque de Produto atualizado com sucesso!');
            log_info('Atualizou estoque de um produto. ID: ' . $idProduto);
            redirect(site_url('produtos/visualizar/') . $idProduto);
        } else {
            $this->data['custom_error'] = '<div class="alert">Ocorreu um erro.</div>';
        }
    }

    private function validarCodigoBarra($codigo)
    {
        // Se o código estiver vazio, retorna true
        if (empty($codigo)) {
            return true;
        }

        // Remove caracteres não numéricos
        $codigo = preg_replace('/[^0-9]/', '', $codigo);

        // Verifica se tem 13 dígitos
        if (strlen($codigo) != 13) {
            return false;
        }

        // Calcula o dígito verificador
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += ($i % 2 == 0) ? intval($codigo[$i]) : intval($codigo[$i]) * 3;
        }

        $digito = (10 - ($soma % 10)) % 10;

        // Verifica se o último dígito é igual ao calculado
        return $digito == intval($codigo[12]);
    }

    private function gerarCodigoBarra($id)
    {
        // Formata o ID com zeros à esquerda para ter 12 dígitos
        $codigo = str_pad($id, 12, '0', STR_PAD_LEFT);

        // Calcula o dígito verificador
        $soma = 0;
        for ($i = 0; $i < 12; $i++) {
            $soma += ($i % 2 == 0) ? intval($codigo[$i]) : intval($codigo[$i]) * 3;
        }

        $digito = (10 - ($soma % 10)) % 10;

        // Retorna o código completo
        return $codigo . $digito;
    }

    public function gerarCodigoBarraAjax()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar produtos.');
            redirect(base_url());
        }

        $id = $this->input->get('id');
        if ($id) {
            $codigo = $this->gerarCodigoBarra($id);
            echo json_encode(['codigo' => $codigo]);
        } else {
            echo json_encode(['error' => 'ID não fornecido']);
        }
    }

    public function validarCodigoBarraAjax()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'aProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para adicionar produtos.');
            redirect(base_url());
        }

        $codigo = $this->input->post('codigo');
        $valido = $this->validarCodigoBarra($codigo);
        echo json_encode(['valido' => $valido]);
    }

    public function pesquisarNcm()
    {
        $termo = $this->input->post('termo');

        $result = $this->Ncm_model->pesquisar($termo);

        echo json_encode($result);
    }

    public function pesquisarCClass()
    {
        $termo = $this->input->post('termo');

        // Carregar arquivo JSON de cClass
        $cclass_file = FCPATH . 'assets/json/cclass_servico.json';
        $result = [];

        if (file_exists($cclass_file)) {
            $json_content = file_get_contents($cclass_file);
            $data = json_decode($json_content, true);

            if ($data && isset($data['cclass_servico'])) {
                foreach ($data['cclass_servico'] as $cclass) {
                    // Filtrar apenas códigos com 7 dígitos (como solicitado)
                    if (strlen($cclass['codigo']) === 7) {
                        // Verificar se o termo de pesquisa corresponde
                        if (empty($termo) ||
                            stripos($cclass['codigo'], $termo) !== false ||
                            stripos($cclass['descricao'], $termo) !== false) {
                            $result[] = $cclass;
                        }
                    }
                }
            }
        }

        echo json_encode($result);
    }
}
