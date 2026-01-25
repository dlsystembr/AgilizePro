<?php if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SimuladorTributacao extends MY_Controller
{
    /**
     * Valida se houve erro de banco e lança exceção com contexto.
     */
    private function ensureNoDbError(string $contexto): void
    {
        $dbError = $this->db->error();
        if (!empty($dbError['code'])) {
            throw new Exception("Erro de banco ({$contexto}): " . ($dbError['message'] ?? 'desconhecido'));
        }
    }

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

        // Buscar opera??es comerciais para o select
        $this->data['operacoes'] = $this->OperacaoComercial_model->get();

        return $this->layout();
    }

    public function simular()
    {
        // Evitar que erros de SQL gerem fatal do CI: vamos capturar manualmente
        $this->db->db_debug = false;

        // Recebe os dados do formulário
        $operacao_id = $this->input->post('operacao_comercial_id');
        $cliente_id = $this->input->post('cliente_id');
        $produto_id = $this->input->post('produto_id');
        $valor_produto_str = $this->input->post('valor_produto');
        $quantidade = $this->input->post('quantidade') ?: 1;

        // Validações básicas
        if (empty($operacao_id) || empty($cliente_id) || empty($produto_id) || empty($valor_produto_str)) {
            $resultado = [
                'sucesso' => false,
                'mensagem' => 'Todos os campos são obrigatórios!'
            ];
            header('Content-Type: application/json');
            echo json_encode($resultado);
            return;
        }

        try {
            // Converter valor monetário (formato brasileiro: 1.234,56)
            $valor_produto_str = str_replace('.', '', $valor_produto_str);
            $valor_produto_str = str_replace(',', '.', $valor_produto_str);
            $valor_produto = floatval($valor_produto_str);
            
            if ($valor_produto <= 0) {
                throw new Exception('O valor do produto deve ser maior que zero.');
            }

            // Buscar dados do cliente + pessoa + endereço (UF) e natureza do documento
            $this->db->select("
                c.cln_id,
                c.cln_objetivo_comercial,
                p.pes_nome,
                p.pes_cpfcnpj,
                COALESCE(est.est_uf, '') as uf_cliente,
                COALESCE(doc.doc_natureza_contribuinte, 'Contribuinte ICMS') as natureza_contribuinte
            ");
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'c.pes_id = p.pes_id');
            $this->db->join('enderecos end', 'p.pes_id = end.pes_id AND end.end_padrao = 1', 'left');
            $this->db->join('municipios mun', 'end.mun_id = mun.mun_id', 'left');
            $this->db->join('estados est', 'mun.est_id = est.est_id', 'left');
            $this->db->join('documentos doc', 'end.end_id = doc.end_id AND doc.pes_id = p.pes_id', 'left');
            $this->db->where('c.cln_id', $cliente_id);
            $this->db->where('c.ten_id', $this->session->userdata('ten_id'));
            $query_cliente = $this->db->get();
            $this->ensureNoDbError('buscar cliente');
            $cliente = $query_cliente->row();
            
            if (!$cliente) {
                throw new Exception('Cliente não encontrado.');
            }

            // Buscar dados do produto
            $this->db->select('pro_id, pro_descricao, pro_preco_venda, tbp_id');
            $this->db->from('produtos');
            $this->db->where('pro_id', $produto_id);
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            $query_produto = $this->db->get();
            $this->ensureNoDbError('buscar produto');
            $produto = $query_produto->row();
            
            if (!$produto) {
                throw new Exception('Produto não encontrado.');
            }

            // Buscar dados da empresa
            // Removido filtro ten_id porque a tabela empresas normalmente não possui essa coluna
            $empresa = $this->db->limit(1)->get('empresas')->row();
            $this->ensureNoDbError('buscar empresa');
            if (!$empresa) {
                throw new Exception('Nenhuma empresa configurada. Cadastre em: Menu -> Configurações -> Empresas');
            }
            
            $uf_emitente = $empresa->emp_uf;
            $uf_cliente = $cliente->uf_cliente ?: $uf_emitente;
            
            // Determinar destinação (estadual ou interestadual)
            $destinacao = ($uf_emitente === $uf_cliente) ? 'estadual' : 'interestadual';

            // Buscar tributação (CST, CFOP)
            $tributacao = $this->ClassificacaoFiscal_model->getTributacao(
                $operacao_id,
                ($cliente->natureza_contribuinte ?: 'Contribuinte ICMS'),
                $destinacao,
                ($cliente->cln_objetivo_comercial ?: 'consumo')
            );
            $this->ensureNoDbError('buscar classificação fiscal');

            if (!$tributacao) {
                throw new Exception('Não foi encontrada classificação fiscal para os parâmetros informados. Verifique se existe uma classificação cadastrada.');
            }

            // Buscar configuração de tributação do produto (se existir)
            $tributacao_produto = null;
            if (isset($produto->tbp_id) && $produto->tbp_id) {
                $tributacao_produto = $this->TributacaoProduto_model->getById($produto->tbp_id);
            }

            // Calcular valores
            $valor_base = $valor_produto * intval($quantidade);

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
                    'tipo_icms' => isset($tributacao->tipo_icms) ? $tributacao->tipo_icms : (isset($tributacao->clf_tipo_tributacao) ? $tributacao->clf_tipo_tributacao : 'N/A'),

                    // Dados do Cliente
                    'cliente_nome' => $cliente->pes_nome,
                    'cliente_uf' => $uf_cliente,
                    'cliente_natureza' => ($cliente->natureza_contribuinte ?: 'Contribuinte ICMS'),
                    'cliente_objetivo' => ($cliente->cln_objetivo_comercial ?: 'consumo'),

                    // Dados do Produto
                    'produto_descricao' => $produto->pro_descricao,

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
            $dbError = $this->db->error();
            if (!empty($dbError['code'])) {
                log_message('error', 'Erro em simular (DB): ' . json_encode($dbError));
            }
            log_message('error', 'Erro em simular: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
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
            $termo = $this->input->get('term');
            
            if (empty($termo)) {
                echo json_encode([]);
                return;
            }
            
            $ten_id = $this->session->userdata('ten_id');
            
            $this->db->select('c.cln_id as id, p.pes_nome as label, p.pes_nome as value');
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'c.pes_id = p.pes_id');
            $this->db->where('c.ten_id', $ten_id);
            $this->db->group_start();
            $this->db->like('p.pes_nome', $termo);
            $this->db->or_like('p.pes_cpfcnpj', $termo);
            $this->db->group_end();
            $this->db->limit(10);
            
            $query = $this->db->get();
            echo json_encode($query->result());
        } catch (Exception $e) {
            log_message('error', 'Erro autoCompleteCliente: ' . $e->getMessage());
            echo json_encode([]);
        }
    }
    
    // Autocomplete para produtos
    public function autoCompleteProduto()
    {
        header('Content-Type: application/json');
        
        try {
            $termo = $this->input->get('term');
            
            if (empty($termo)) {
                echo json_encode([]);
                return;
            }
            
            $ten_id = $this->session->userdata('ten_id');
            
            $this->db->select('pro_id as id, pro_descricao as label, pro_descricao as value, pro_preco_venda as precoVenda');
            $this->db->from('produtos');
            $this->db->where('ten_id', $ten_id);
            $this->db->group_start();
            $this->db->like('pro_descricao', $termo);
            $this->db->or_like('pro_cod_barra', $termo);
            $this->db->group_end();
            $this->db->limit(10);
            
            $query = $this->db->get();
            echo json_encode($query->result());
        } catch (Exception $e) {
            log_message('error', 'Erro autoCompleteProduto: ' . $e->getMessage());
            echo json_encode([]);
        }
    }

    // M�todo para buscar clientes (para modal)
    public function buscarClientes()
    {
        header('Content-Type: application/json');
        
        try {
            $nome = $this->input->get('nome');
            $documento = $this->input->get('documento');
            $limite = $this->input->get('limite') ?: 50;
            
            $ten_id = $this->session->userdata('ten_id');
            
            $this->db->select('c.cln_id as id, p.pes_nome as nome, p.pes_cpfcnpj as documento, p.PES_UF as uf');
            $this->db->from('clientes c');
            $this->db->join('pessoas p', 'c.pes_id = p.pes_id');
            $this->db->where('c.ten_id', $ten_id);
            
            if (!empty($nome)) {
                $this->db->like('p.pes_nome', $nome);
            }
            if (!empty($documento)) {
                $this->db->like('p.pes_cpfcnpj', $documento);
            }
            
            $this->db->order_by('p.pes_nome', 'ASC');
            $this->db->limit($limite);
            
            $query = $this->db->get();
            echo json_encode($query->result());
        } catch (Exception $e) {
            log_message('error', 'Erro buscarClientes: ' . $e->getMessage());
            echo json_encode([]);
        }
    }

    // M�todo para buscar produtos (para modal)
    public function buscarProdutos()
    {
        header('Content-Type: application/json');
        
        try {
            $nome = $this->input->get('nome');
            $codigo = $this->input->get('codigo');
            $barras = $this->input->get('barras');
            $limite = $this->input->get('limite') ?: 50;
            
            $ten_id = $this->session->userdata('ten_id');
            
            $this->db->select('pro_id as id, pro_descricao as descricao, pro_cod_barra as codDeBarra, pro_preco_venda as precoVenda');
            $this->db->from('produtos');
            $this->db->where('ten_id', $ten_id);
            
            if (!empty($nome)) {
                $this->db->like('pro_descricao', $nome);
            }
            if (!empty($codigo)) {
                $this->db->like('pro_id', $codigo);
            }
            if (!empty($barras)) {
                $this->db->like('pro_cod_barra', $barras);
            }
            
            $this->db->order_by('pro_descricao', 'ASC');
            $this->db->limit($limite);
            
            $query = $this->db->get();
            $produtos = $query->result();
            
            // Formatar pre�o
            foreach ($produtos as $produto) {
                if ($produto->precoVenda) {
                    $produto->precoVenda = number_format($produto->precoVenda, 2, ',', '.');
                }
            }
            
            echo json_encode($produtos);
        } catch (Exception $e) {
            log_message('error', 'Erro buscarProdutos: ' . $e->getMessage());
            echo json_encode([]);
        }
    }
}

