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
                'PRO_COMPRIMENTO' => $this->input->post('PRO_COMPRIMENTO') ? str_replace(',', '.', $this->input->post('PRO_COMPRIMENTO')) : null,
                'PRO_ENTRADA' => $this->input->post('PRO_ENTRADA') ?: 0,
                'PRO_SAIDA' => $this->input->post('PRO_SAIDA') ?: 0
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
            $_POST['precoCompra'] = $_POST['precoCompra'] ?: '0';
            // Não sobrescrever precoVenda - deixar o valor que o usuário digitou
            // Se estiver vazio, definir como '0' apenas para validação
            if (empty($_POST['precoVenda']) || trim($_POST['precoVenda']) == '') {
                $_POST['precoVenda'] = '0';
            }
            $_POST['estoque'] = $_POST['estoque'] ?: '0';
            $_POST['estoqueMinimo'] = $_POST['estoqueMinimo'] ?: '0';
            $_POST['PRO_ORIGEM'] = $_POST['PRO_ORIGEM'] ?: '0';
            $_POST['peso_bruto'] = '0.000';
            $_POST['peso_liquido'] = '0.000';
            $_POST['largura'] = '0.000';
            $_POST['altura'] = '0.000';
            $_POST['comprimento'] = '0.000';

            // Regras de validação para serviços
            $this->form_validation->set_rules('descricao', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('PRO_UNID_MEDIDA', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('PRO_CCLASS_SERV', 'cClass', 'required|trim');
            $this->form_validation->set_rules('precoVenda', 'Preço Serviço', 'required|trim');
        } else { // Produto
            // Regras de validação para produtos (usando a configuração padrão)
            $this->form_validation->set_rules('descricao', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('unidade', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('precoCompra', 'Preço de Compra', 'required|trim');
            $this->form_validation->set_rules('precoVenda', 'Preço de Venda', 'required|trim');
            $this->form_validation->set_rules('estoque', 'Estoque', 'required|trim');
            $this->form_validation->set_rules('PRO_ORIGEM', 'Origem do Produto', 'required|trim|integer|greater_than_equal_to[0]|less_than_equal_to[8]');
        }

        // Sanitiza campos decimais para validação (substitui vírgula por ponto)
        $decimalFields = ['peso_bruto', 'peso_liquido', 'largura', 'altura', 'comprimento'];
        foreach ($decimalFields as $field) {
            if (isset($_POST[$field])) {
                $_POST[$field] = str_replace(',', '.', $_POST[$field]);
            }
        }

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $precoCompra = $this->input->post('precoCompra');
            $precoCompra = str_replace(',', '.', $precoCompra);
            
            // Determinar o tipo do produto ANTES de processar precoVenda
            $tipo = $this->input->post('PRO_TIPO') ?: 1;
            
            // Processar precoVenda - importante para serviços
            $precoVenda = $this->input->post('precoVenda');
            // Se for serviço e o campo estiver vazio, pode ser que o campo não foi enviado (estava oculto)
            // Nesse caso, buscar o valor atual do banco
            if ($tipo == '2' && (empty($precoVenda) || trim($precoVenda) == '')) {
                $produtoAtual = $this->produtos_model->getById($this->input->post('PRO_ID'));
                if ($produtoAtual && !empty($produtoAtual->PRO_PRECO_VENDA)) {
                    $precoVenda = $produtoAtual->PRO_PRECO_VENDA;
                } else {
                    $precoVenda = '0';
                }
            }
            $precoVenda = str_replace(',', '.', $precoVenda);
            
            $data = [
                'PRO_COD_BARRA' => $this->input->post('codDeBarra'),
                'PRO_DESCRICAO' => $this->input->post('descricao'),
                'PRO_UNID_MEDIDA' => $this->input->post('unidade') ?: $this->input->post('PRO_UNID_MEDIDA'),
                'PRO_PRECO_COMPRA' => $precoCompra,
                'PRO_PRECO_VENDA' => $precoVenda,
                'PRO_ESTOQUE' => $this->input->post('estoque'),
                'PRO_ESTOQUE_MINIMO' => $this->input->post('estoqueMinimo'),
                'PRO_NCM' => $this->input->post('PRO_NCM'),
                'NCM_ID' => $this->input->post('NCM_ID'),
                'PRO_ORIGEM' => $this->input->post('PRO_ORIGEM'),
                'PRO_TIPO' => $tipo,
                'PRO_CCLASS_SERV' => $this->input->post('PRO_CCLASS_SERV'),
                'PRO_PESO_BRUTO' => $this->input->post('peso_bruto') ? str_replace(',', '.', $this->input->post('peso_bruto')) : null,
                'PRO_PESO_LIQUIDO' => $this->input->post('peso_liquido') ? str_replace(',', '.', $this->input->post('peso_liquido')) : null,
                'PRO_LARGURA' => $this->input->post('largura') ? str_replace(',', '.', $this->input->post('largura')) : null,
                'PRO_ALTURA' => $this->input->post('altura') ? str_replace(',', '.', $this->input->post('altura')) : null,
                'PRO_COMPRIMENTO' => $this->input->post('comprimento') ? str_replace(',', '.', $this->input->post('comprimento')) : null,
                'PRO_ENTRADA' => $this->input->post('entrada') ?: 0,
                'PRO_SAIDA' => $this->input->post('saida') ?: 0
            ];
            
            // Se for serviço, garantir que o preço de venda seja salvo corretamente
            if ($tipo == '2') {
                // Para serviços, sempre salvar o precoVenda que foi informado
                // O valor já foi processado acima (linha 217), então apenas garantir que está no array
                // Não precisa fazer nada especial, o valor já está em $data['PRO_PRECO_VENDA']
                log_message('debug', 'Editando serviço - PRO_ID: ' . $this->input->post('PRO_ID') . ', PRO_PRECO_VENDA: ' . $precoVenda);
            }

            if ($this->produtos_model->edit('produtos', $data, 'PRO_ID', $this->input->post('PRO_ID')) == true) {
                $this->session->set_flashdata('success', 'Produto editado com sucesso!');
                log_info('Alterou um produto. ID: ' . $this->input->post('PRO_ID'));
                redirect(site_url('produtos/visualizar/') . $this->input->post('PRO_ID'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured</p></div>';
            }
        }

        $this->data['result'] = $this->produtos_model->getById($this->uri->segment(3));
        
        // Normalizar NCM para exibição (alguns registros antigos podem ter apenas NCM_ID ou apenas PRO_NCM)
        if ($this->data['result']) {
            // Debug: ver o que vem do banco
            log_message('debug', 'PRO_NCM do banco: ' . ($this->data['result']->PRO_NCM ?? 'NULL'));
            log_message('debug', 'NCM_ID do banco: ' . ($this->data['result']->NCM_ID ?? 'NULL'));
            log_message('debug', 'PRO_TIPO: ' . ($this->data['result']->PRO_TIPO ?? 'NULL'));
            
            // Se for produto e tiver NCM de serviço (00000000), buscar o NCM correto pelo NCM_ID
            if ($this->data['result']->PRO_TIPO == '1' && $this->data['result']->PRO_NCM == '00000000' && !empty($this->data['result']->NCM_ID)) {
                $ncmRow = $this->db->select('NCM_CODIGO')->from('ncms')->where('NCM_ID', $this->data['result']->NCM_ID)->get()->row();
                if ($ncmRow && !empty($ncmRow->NCM_CODIGO) && $ncmRow->NCM_CODIGO != '00000000') {
                    $this->data['result']->PRO_NCM = $ncmRow->NCM_CODIGO;
                    log_message('debug', 'PRO_NCM corrigido de NCM_ID para produto: ' . $ncmRow->NCM_CODIGO);
                } else {
                    // Se não encontrou NCM válido, limpar
                    $this->data['result']->PRO_NCM = '';
                    $this->data['result']->NCM_ID = '';
                    log_message('debug', 'NCM inválido limpo para produto');
                }
            }
            
            // Se tiver NCM_ID mas PRO_NCM vazio, busca o código do NCM
            if (!empty($this->data['result']->NCM_ID) && empty($this->data['result']->PRO_NCM)) {
                $ncmRow = $this->db->select('NCM_CODIGO')->from('ncms')->where('NCM_ID', $this->data['result']->NCM_ID)->get()->row();
                if ($ncmRow && !empty($ncmRow->NCM_CODIGO)) {
                    $this->data['result']->PRO_NCM = $ncmRow->NCM_CODIGO;
                    log_message('debug', 'PRO_NCM atualizado de NCM_ID: ' . $ncmRow->NCM_CODIGO);
                }
            }
            // Se tiver PRO_NCM mas NCM_ID vazio, tenta buscar o ID do NCM
            if (empty($this->data['result']->NCM_ID) && !empty($this->data['result']->PRO_NCM)) {
                $ncmRow = $this->db->select('NCM_ID')->from('ncms')->where('NCM_CODIGO', $this->data['result']->PRO_NCM)->get()->row();
                if ($ncmRow && !empty($ncmRow->NCM_ID)) {
                    $this->data['result']->NCM_ID = $ncmRow->NCM_ID;
                    log_message('debug', 'NCM_ID atualizado de PRO_NCM: ' . $ncmRow->NCM_ID);
                }
            }
            
            // Debug final
            log_message('debug', 'PRO_NCM final: ' . ($this->data['result']->PRO_NCM ?? 'NULL'));
            log_message('debug', 'NCM_ID final: ' . ($this->data['result']->NCM_ID ?? 'NULL'));
        }
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

        $result = $this->ncm_model->pesquisar($termo);

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
