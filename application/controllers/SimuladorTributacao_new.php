<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SimuladorTributacao extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para acessar o Simulador de Tributação.');
            redirect(base_url());
        }

        $this->load->model('ClassificacaoFiscal_model');
        $this->load->model('TributacaoProduto_model');
        $this->load->model('Clientes_model');
        $this->load->model('Produtos_model');
        $this->load->model('OperacaoComercial_model');
        $this->load->model('Mapos_model');

        $this->data['menuConfiguracoes'] = 'Configurações';
        $this->data['menuSimulador'] = 'Simulador de Tributação';
    }

    public function index()
    {
        $this->data['view'] = 'simuladortributacao/simulador';

        // Buscar operações comerciais para o select
        $this->data['operacoes'] = $this->OperacaoComercial_model->get();

        return $this->layout();
    }

    public function simular()
    {
        // Recebe os dados do formulário
        $operacao_id = $this->input->post('operacao_comercial_id');
        $cliente_id = $this->input->post('cliente_id');
        $produto_id = $this->input->post('produto_id');
        $valor_produto = $this->input->post('valor_produto');
        $quantidade = $this->input->post('quantidade') ?: 1;

        // Validações básicas
        if (empty($operacao_id) || empty($cliente_id) || empty($produto_id) || empty($valor_produto)) {
            $resultado = [
                'sucesso' => false,
                'mensagem' => 'Todos os campos são obrigatórios!'
            ];
            header('Content-Type: application/json');
            echo json_encode($resultado);
            return;
        }

        try {
            // Buscar dados do cliente
            $cliente = $this->Clientes_model->getById($cliente_id);
            if (!$cliente) {
                throw new Exception('Cliente não encontrado.');
            }

            // Buscar dados do produto
            $produto = $this->Produtos_model->getById($produto_id);
            if (!$produto) {
                throw new Exception('Produto não encontrado.');
            }

            // Buscar dados do emitente
            $emitente = $this->Mapos_model->getEmitente();
            if (!$emitente) {
                throw new Exception('Emitente não configurado.');
            }

            // Determinar destinação (estadual ou interestadual)
            $destinacao = ($cliente->estado === $emitente->uf) ? 'estadual' : 'interestadual';

            // Buscar tributação (CST, CFOP)
            $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                $operacao_id,
                $cliente->natureza_contribuinte,
                $destinacao,
                $cliente->objetivo_comercial
            );

            if (!$tributacao) {
                throw new Exception('Não foi encontrada classificação fiscal para os parâmetros informados. Verifique se existe uma classificação cadastrada.');
            }

            // Buscar configuração de tributação do produto (se existir)
            $tributacao_produto = null;
            if (isset($produto->tributacao_produto_id) && $produto->tributacao_produto_id) {
                $tributacao_produto = $this->TributacaoProduto_model->getById($produto->tributacao_produto_id);
            }

            // Calcular valores
            $valor_base = floatval($valor_produto) * intval($quantidade);

            // Inicializar valores de impostos
            $valor_ipi = 0;
            $valor_pis = 0;
            $valor_cofins = 0;
            $aliq_ipi = 0;
            $aliq_pis = 0;
            $aliq_cofins = 0;

            // Se tiver configuração de tributação do produto, calcular impostos
            if ($tributacao_produto) {
                $aliq_ipi = floatval($tributacao_produto->aliq_ipi_saida);
                $aliq_pis = floatval($tributacao_produto->aliq_pis_saida);
                $aliq_cofins = floatval($tributacao_produto->aliq_cofins_saida);

                $valor_ipi = ($valor_base * $aliq_ipi) / 100;
                $valor_pis = ($valor_base * $aliq_pis) / 100;
                $valor_cofins = ($valor_base * $aliq_cofins) / 100;
            }

            $valor_total = $valor_base + $valor_ipi;
            $total_impostos = $valor_ipi + $valor_pis + $valor_cofins;

            // Montar resultado
            $resultado = [
                'sucesso' => true,
                'dados' => [
                    // Dados da Classificação Fiscal
                    'classificacao_fiscal_id' => isset($tributacao->id) ? $tributacao->id : (isset($tributacao->clf_id) ? $tributacao->clf_id : 'N/A'),
                    'cst' => isset($tributacao->cst) ? $tributacao->cst : (isset($tributacao->clf_cst) ? $tributacao->clf_cst : 'N/A'),
                    'csosn' => isset($tributacao->csosn) ? $tributacao->csosn : (isset($tributacao->clf_csosn) ? $tributacao->clf_csosn : 'N/A'),
                    'cfop' => isset($tributacao->cfop) ? $tributacao->cfop : (isset($tributacao->clf_cfop) ? $tributacao->clf_cfop : 'N/A'),
                    'tipo_icms' => isset($tributacao->tipo_icms) ? $tributacao->tipo_icms : (isset($tributacao->CLF_TIPO_ICMS) ? $tributacao->CLF_TIPO_ICMS : 'N/A'),

                    // Dados do Cliente
                    'cliente_nome' => $cliente->nomeCliente,
                    'cliente_uf' => $cliente->estado,
                    'cliente_natureza' => $cliente->natureza_contribuinte,
                    'cliente_objetivo' => $cliente->objetivo_comercial,

                    // Dados do Produto
                    'produto_descricao' => $produto->descricao,

                    // Destinação
                    'destinacao' => $destinacao,

                    // Valores Calculados
                    'quantidade' => $quantidade,
                    'valor_unitario' => number_format($valor_produto, 2, ',', '.'),
                    'valor_base' => number_format($valor_base, 2, ',', '.'),
                    'aliq_ipi' => number_format($aliq_ipi, 2, ',', '.'),
                    'valor_ipi' => number_format($valor_ipi, 2, ',', '.'),
                    'aliq_pis' => number_format($aliq_pis, 2, ',', '.'),
                    'valor_pis' => number_format($valor_pis, 2, ',', '.'),
                    'aliq_cofins' => number_format($aliq_cofins, 2, ',', '.'),
                    'valor_cofins' => number_format($valor_cofins, 2, ',', '.'),
                    'valor_total' => number_format($valor_total, 2, ',', '.'),
                    'total_impostos' => number_format($total_impostos, 2, ',', '.'),

                    // CST do Produto (se houver)
                    'cst_ipi' => $tributacao_produto ? $tributacao_produto->cst_ipi_saida : 'N/A',
                    'cst_pis' => $tributacao_produto ? $tributacao_produto->cst_pis_saida : 'N/A',
                    'cst_cofins' => $tributacao_produto ? $tributacao_produto->cst_cofins_saida : 'N/A',
                ]
            ];

            header('Content-Type: application/json');
            echo json_encode($resultado);

        } catch (Exception $e) {
            $resultado = [
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ];
            header('Content-Type: application/json');
            echo json_encode($resultado);
        }
    }

    // Autocomplete para clientes
    public function autoCompleteCliente()
    {
        header('Content-Type: application/json');

        try {
            $termo = isset($_GET['term']) ? $_GET['term'] : '';

            if (empty($termo)) {
                echo json_encode([]);
                return;
            }

            // Pegar tenant_id da sess�o$ten_id = $this->session->userdata('ten_id');// Query direta com tenant$termo_like = "%{$termo}%";$sql = "SELECT idClientes as id, nomeCliente as label, nomeCliente as value, estado, natureza_contribuinte, objetivo_comercial FROM clientes WHERE ten_id = ? AND (nomeCliente LIKE ? OR documento LIKE ?)) LIMIT 10";$query = $this->db->query($sql, [$ten_id, $termo_like, $termo_like]);
            $clientes = $query->result();

            echo json_encode($clientes);
        } catch (Exception $e) {
            log_message('error', 'Erro em autoCompleteCliente: ' . $e->getMessage());
            echo json_encode([]);
        }
    }

    // Autocomplete para produtos
    public function autoCompleteProduto()
    {
        header('Content-Type: application/json');

        try {
            $termo = isset($_GET['term']) ? $_GET['term'] : '';

            if (empty($termo)) {
                echo json_encode([]);
                return;
            }

            // Query direta
            $termo_like = "%{$termo}%";
            $sql = "SELECT idProdutos as id, descricao as label, descricao as value, precoVenda
                    FROM produtos 
                    WHERE descricao LIKE ? OR codDeBarra LIKE ?
                    LIMIT 10";

            $query = $this->db->query($sql, [$termo_like, $termo_like]);
            $produtos = $query->result();

            echo json_encode($produtos);
        } catch (Exception $e) {
            log_message('error', 'Erro em autoCompleteProduto: ' . $e->getMessage());
            echo json_encode([]);
        }
    }
}

