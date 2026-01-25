<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Produtos extends MY_Controller
{
    /**
     * Catálogo de finalidades aplicáveis aos produtos.
     *
     * @var array
     */
    private $finalidadesProduto = [];
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

        $this->finalidadesProduto = [
            'Comercialização' => 'Comercialização / Revenda',
            'Consumo' => 'Consumo / Uso próprio',
            'Ativo Imobilizado' => 'Ativo imobilizado',
            'Serviço' => 'Serviço',
            'Outros' => 'Outros'
        ];
        $this->data['finalidadesProduto'] = $this->finalidadesProduto;
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
        $tipo = $this->input->post('pro_tipo');

        if ($tipo == '2') { // Serviço
            $_POST['pro_preco_compra'] = $_POST['pro_preco_compra'] ?: '0';
            $_POST['pro_preco_venda'] = $_POST['pro_preco_venda'] ?: '0';
            $_POST['pro_estoque_minimo'] = $_POST['pro_estoque_minimo'] ?: '0';
            $_POST['pro_origem'] = $_POST['pro_origem'] ?: '0';

            // Regras de validação para serviços
            $this->form_validation->set_rules('pro_descricao', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('pro_unid_medida', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('pro_cclass_serv', 'cClass', 'required|trim');
        } else { // Produto
            // Regras de validação para produtos (usando a configuração padrão)
            $this->form_validation->set_rules('pro_descricao', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('pro_unid_medida', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('pro_preco_compra', 'Preço de Compra', 'required|trim');
            $this->form_validation->set_rules('pro_preco_venda', 'Preço de Venda', 'required|trim');
            $this->form_validation->set_rules('pro_origem', 'Origem do Produto', 'required|trim|integer|greater_than_equal_to[0]|less_than_equal_to[8]');
        }

        $finalidadesLista = implode(',', array_keys($this->finalidadesProduto));
        $this->form_validation->set_rules('pro_finalidade', 'Finalidade', 'required|trim|in_list[' . $finalidadesLista . ']');

        if ($this->form_validation->run() == false) {
            $this->data['custom_error'] = (validation_errors() ? '<div class="form_error">' . validation_errors() . '</div>' : false);
        } else {
            $precoCompra = $this->input->post('pro_preco_compra');
            $precoCompra = str_replace(',', '.', $precoCompra);
            $precoVenda = $this->input->post('pro_preco_venda');
            $precoVenda = str_replace(',', '.', $precoVenda);
            $data = [
                'ten_id' => $this->session->userdata('ten_id'),
                'pro_cod_barra' => $this->input->post('pro_cod_barra'),
                'pro_descricao' => $this->input->post('pro_descricao'),
                'pro_unid_medida' => $this->input->post('pro_unid_medida'),
                'pro_preco_compra' => $precoCompra,
                'pro_preco_venda' => $precoVenda,
                'pro_estoque_minimo' => $this->input->post('pro_estoque_minimo'),
                'pro_ncm' => $this->input->post('pro_ncm'),
                'ncm_id' => $this->input->post('ncm_id'),
                'pro_origem' => $this->input->post('pro_origem'),
                'pro_tipo' => $this->input->post('pro_tipo') ?: 1,
                'pro_cclass_serv' => $this->input->post('pro_cclass_serv'),
                'pro_finalidade' => $tipo == '2'
                    ? 'Serviço'
                    : ($this->input->post('pro_finalidade') ?: 'Comercialização'),
                'pro_peso_bruto' => $this->input->post('pro_peso_bruto') ? str_replace(',', '.', $this->input->post('pro_peso_bruto')) : null,
                'pro_peso_liquido' => $this->input->post('pro_peso_liquido') ? str_replace(',', '.', $this->input->post('pro_peso_liquido')) : null,
                'pro_largura' => $this->input->post('pro_largura') ? str_replace(',', '.', $this->input->post('pro_largura')) : null,
                'pro_altura' => $this->input->post('pro_altura') ? str_replace(',', '.', $this->input->post('pro_altura')) : null,
                'pro_comprimento' => $this->input->post('pro_comprimento') ? str_replace(',', '.', $this->input->post('pro_comprimento')) : null,
                'pro_entrada' => $this->input->post('pro_entrada') ?: 0,
                'pro_saida' => $this->input->post('pro_saida') ?: 0
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
        $tipo = $this->input->post('pro_tipo');

        if ($tipo == '2') { // Serviço
            $_POST['precoCompra'] = $_POST['precoCompra'] ?: '0';
            // Não sobrescrever precoVenda - deixar o valor que o usuário digitou
            // Se estiver vazio, definir como '0' apenas para validação
            if (empty($_POST['precoVenda']) || trim($_POST['precoVenda']) == '') {
                $_POST['precoVenda'] = '0';
            }
            $_POST['estoqueMinimo'] = $_POST['estoqueMinimo'] ?: '0';
            $_POST['pro_origem'] = $_POST['pro_origem'] ?: '0';
            $_POST['peso_bruto'] = '0.000';
            $_POST['peso_liquido'] = '0.000';
            $_POST['largura'] = '0.000';
            $_POST['altura'] = '0.000';
            $_POST['comprimento'] = '0.000';

            // Regras de validação para serviços
            $this->form_validation->set_rules('descricao', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('pro_unid_medida', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('pro_cclass_serv', 'cClass', 'required|trim');
            $this->form_validation->set_rules('precoVenda', 'Preço Serviço', 'required|trim');
        } else { // Produto
            // Regras de validação para produtos (usando a configuração padrão)
            $this->form_validation->set_rules('descricao', 'Descrição', 'required|trim');
            $this->form_validation->set_rules('unidade', 'Unidade', 'required|trim');
            $this->form_validation->set_rules('precoCompra', 'Preço de Compra', 'required|trim');
            $this->form_validation->set_rules('precoVenda', 'Preço de Venda', 'required|trim');
            $this->form_validation->set_rules('pro_origem', 'Origem do Produto', 'required|trim|integer|greater_than_equal_to[0]|less_than_equal_to[8]');
        }

        $finalidadesLista = implode(',', array_keys($this->finalidadesProduto));
        $this->form_validation->set_rules('pro_finalidade', 'Finalidade', 'required|trim|in_list[' . $finalidadesLista . ']');

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
            $tipo = $this->input->post('pro_tipo') ?: 1;
            
            // Processar precoVenda - importante para serviços
            $precoVenda = $this->input->post('precoVenda');
            // Se for serviço e o campo estiver vazio, pode ser que o campo não foi enviado (estava oculto)
            // Nesse caso, buscar o valor atual do banco
            if ($tipo == '2' && (empty($precoVenda) || trim($precoVenda) == '')) {
                $produtoAtual = $this->produtos_model->getById($this->input->post('pro_id'));
                if ($produtoAtual && !empty($produtoAtual->pro_preco_venda)) {
                    $precoVenda = $produtoAtual->pro_preco_venda;
                } else {
                    $precoVenda = '0';
                }
            }
            $precoVenda = str_replace(',', '.', $precoVenda);
            
            $data = [
                'ten_id' => $this->session->userdata('ten_id'),
                'pro_cod_barra' => $this->input->post('codDeBarra'),
                'pro_descricao' => $this->input->post('descricao'),
                'pro_unid_medida' => $this->input->post('unidade') ?: $this->input->post('pro_unid_medida'),
                'pro_preco_compra' => $precoCompra,
                'pro_preco_venda' => $precoVenda,
                'pro_estoque_minimo' => $this->input->post('estoqueMinimo'),
                'pro_ncm' => $this->input->post('pro_ncm'),
                'ncm_id' => $this->input->post('ncm_id'),
                'pro_origem' => $this->input->post('pro_origem'),
                'pro_tipo' => $tipo,
                'pro_cclass_serv' => $this->input->post('pro_cclass_serv'),
                'pro_finalidade' => $tipo == '2'
                    ? 'Serviço'
                    : ($this->input->post('pro_finalidade') ?: 'Comercialização'),
                'pro_peso_bruto' => $this->input->post('peso_bruto') ? str_replace(',', '.', $this->input->post('peso_bruto')) : null,
                'pro_peso_liquido' => $this->input->post('peso_liquido') ? str_replace(',', '.', $this->input->post('peso_liquido')) : null,
                'pro_largura' => $this->input->post('largura') ? str_replace(',', '.', $this->input->post('largura')) : null,
                'pro_altura' => $this->input->post('altura') ? str_replace(',', '.', $this->input->post('altura')) : null,
                'pro_comprimento' => $this->input->post('comprimento') ? str_replace(',', '.', $this->input->post('comprimento')) : null,
                'pro_entrada' => $this->input->post('entrada') ?: 0,
                'pro_saida' => $this->input->post('saida') ?: 0
            ];
            
            // Se for serviço, garantir que o preço de venda seja salvo corretamente
            if ($tipo == '2') {
                // Para serviços, sempre salvar o precoVenda que foi informado
                // O valor já foi processado acima (linha 217), então apenas garantir que está no array
                // Não precisa fazer nada especial, o valor já está em $data['pro_preco_venda']
                log_message('debug', 'Editando serviço - pro_id: ' . $this->input->post('pro_id') . ', pro_preco_venda: ' . $precoVenda);
            }

            if ($this->produtos_model->edit('produtos', $data, 'pro_id', $this->input->post('pro_id')) == true) {
                $this->session->set_flashdata('success', 'Produto editado com sucesso!');
                log_info('Alterou um produto. ID: ' . $this->input->post('pro_id'));
                redirect(site_url('produtos/visualizar/') . $this->input->post('pro_id'));
            } else {
                $this->data['custom_error'] = '<div class="form_error"><p>An Error Occured</p></div>';
            }
        }

        $this->data['result'] = $this->produtos_model->getById($this->uri->segment(3));
        if ($this->data['result'] && empty($this->data['result']->pro_finalidade)) {
            $this->data['result']->pro_finalidade = $this->data['result']->pro_tipo == '2'
                ? 'Serviço'
                : 'Comercialização';
        }
        
        // Normalizar NCM para exibição (alguns registros antigos podem ter apenas ncm_id ou apenas pro_ncm)
        if ($this->data['result']) {
            // Debug: ver o que vem do banco
            log_message('debug', 'pro_ncm do banco: ' . ($this->data['result']->pro_ncm ?? 'NULL'));
            log_message('debug', 'ncm_id do banco: ' . ($this->data['result']->ncm_id ?? 'NULL'));
            log_message('debug', 'pro_tipo: ' . ($this->data['result']->pro_tipo ?? 'NULL'));
            
            // Se for produto e tiver NCM de serviço (00000000), buscar o NCM correto pelo ncm_id
            if ($this->data['result']->pro_tipo == '1' && $this->data['result']->pro_ncm == '00000000' && !empty($this->data['result']->ncm_id)) {
                $ncmRow = $this->db->select('ncm_codigo')->from('ncms')->where('ncm_id', $this->data['result']->ncm_id)->get()->row();
                if ($ncmRow && !empty($ncmRow->ncm_codigo) && $ncmRow->ncm_codigo != '00000000') {
                    $this->data['result']->pro_ncm = $ncmRow->ncm_codigo;
                    log_message('debug', 'pro_ncm corrigido de ncm_id para produto: ' . $ncmRow->ncm_codigo);
                } else {
                    // Se não encontrou NCM válido, limpar
                    $this->data['result']->pro_ncm = '';
                    $this->data['result']->ncm_id = '';
                    log_message('debug', 'NCM inválido limpo para produto');
                }
            }
            
            // Se tiver ncm_id mas pro_ncm vazio, busca o código do NCM
            if (!empty($this->data['result']->ncm_id) && empty($this->data['result']->pro_ncm)) {
                $ncmRow = $this->db->select('ncm_codigo')->from('ncms')->where('ncm_id', $this->data['result']->ncm_id)->get()->row();
                if ($ncmRow && !empty($ncmRow->ncm_codigo)) {
                    $this->data['result']->pro_ncm = $ncmRow->ncm_codigo;
                    log_message('debug', 'pro_ncm atualizado de ncm_id: ' . $ncmRow->ncm_codigo);
                }
            }
            // Se tiver pro_ncm mas ncm_id vazio, tenta buscar o ID do NCM
            if (empty($this->data['result']->ncm_id) && !empty($this->data['result']->pro_ncm)) {
                $ncmRow = $this->db->select('ncm_id')->from('ncms')->where('ncm_codigo', $this->data['result']->pro_ncm)->get()->row();
                if ($ncmRow && !empty($ncmRow->ncm_id)) {
                    $this->data['result']->ncm_id = $ncmRow->ncm_id;
                    log_message('debug', 'ncm_id atualizado de pro_ncm: ' . $ncmRow->ncm_id);
                }
            }
            
            // Debug final
            log_message('debug', 'pro_ncm final: ' . ($this->data['result']->pro_ncm ?? 'NULL'));
            log_message('debug', 'ncm_id final: ' . ($this->data['result']->ncm_id ?? 'NULL'));
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
            redirect(site_url('produtos/editar/') . $this->input->post('pro_id'));
        }

        if ($this->data['result'] && empty($this->data['result']->pro_finalidade)) {
            $this->data['result']->pro_finalidade = 'COMERCIALIZACAO';
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
        $this->db->where('pro_id', $id);
        $movimentacao = $this->db->get('itens_de_vendas')->num_rows();

        if ($movimentacao > 0) {
            $this->session->set_flashdata('error', 'Este produto não pode ser excluído pois possui movimentação.');
            redirect(base_url() . 'index.php/produtos/gerenciar/');
        }

        $this->produtos_model->delete('produtos_os', 'pro_id', $id);
        $this->produtos_model->delete('itens_de_vendas', 'pro_id', $id);
        $this->produtos_model->delete('produtos', 'pro_id', $id);

        log_info('Removeu um produto. ID: ' . $id);

        $this->session->set_flashdata('success', 'Produto excluido com sucesso!');
        redirect(site_url('produtos/gerenciar/'));
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
