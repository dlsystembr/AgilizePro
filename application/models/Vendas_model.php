<?php

use Piggly\Pix\StaticPayload;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Vendas_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $lista_clientes = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idClientes');
                $this->db->like('nomeCliente', $where['pesquisa']);
                $this->db->limit(25);
                $clientes = $this->db->get('clientes')->result();

                foreach ($clientes as $c) {
                    array_push($lista_clientes, $c->idClientes);
                }
            }
        }
        $this->db->select($fields . ', pessoas.pes_nome as nomeCliente, clientes.CLN_ID as idClientes, operacao_comercial.OPC_NOME as operacao_comercial');
        $this->db->from($table);
        $this->db->limit($perpage, $start);
        $this->db->join('clientes', 'clientes.CLN_ID = ' . $table . '.clientes_id');
        $this->db->join('pessoas', 'pessoas.pes_id = clientes.PES_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ' . $table . '.usuarios_id');
        $this->db->join('operacao_comercial', 'operacao_comercial.OPC_ID = ' . $table . '.operacao_comercial_id', 'left');
        $this->db->order_by('idVendas', 'desc');
        
        // condicionais da pesquisa
        if ($where) {
            // condicional de status
            if (array_key_exists('status', $where)) {
                $this->db->where_in('vendas.status', $where['status']);
            }

            // condicional de clientes
            if (array_key_exists('pesquisa', $where)) {
                if ($lista_clientes != null) {
                    $this->db->where_in('vendas.clientes_id', $lista_clientes);
                }
            }

            // condicional data Venda
            if (array_key_exists('de', $where)) {
                $this->db->where('vendas.dataVenda >=', $where['de']);
            }
            // condicional data final
            if (array_key_exists('ate', $where)) {
                $this->db->where('vendas.dataVenda <=', $where['ate']);
            }
        }
        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    }

    public function getVendas($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select('vendas.*, pessoas.pes_nome as nomeCliente, pessoas.pes_cpf_cnpj as documento, usuarios.nome,
            (SELECT SUM(quantidade * preco) FROM itens_de_vendas WHERE vendas_id = vendas.idVendas) as valor_total');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.CLN_ID = vendas.clientes_id', 'left');
        $this->db->join('pessoas', 'pessoas.pes_id = clientes.PES_ID', 'left');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id', 'left');
        $this->db->where('vendas.status', 'Faturado');
        $this->db->where('vendas.emitida_nfe', false);
        $this->db->order_by('vendas.idVendas', 'desc');

        $query = $this->db->get();

        if (!$query) {
            return [];
        }

        return $query->result();
    }

    public function getById($id)
    {
        $this->db->select('vendas.*, pessoas.*, pessoas.pes_contato as contato_cliente, pessoas.pes_email as emailCliente, lancamentos.data_vencimento, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome as nome, operacao_comercial.OPC_NOME as operacao_comercial');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.CLN_ID = vendas.clientes_id');
        $this->db->join('pessoas', 'pessoas.pes_id = clientes.PES_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id');
        $this->db->join('lancamentos', 'vendas.idVendas = lancamentos.vendas_id', 'LEFT');
        $this->db->join('operacao_comercial', 'operacao_comercial.OPC_ID = vendas.operacao_comercial_id', 'left');
        $this->db->where('vendas.idVendas', $id);
        $this->db->limit(1);

        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return null;
        }

        $result = $query->row();
        
        // Garantir que todos os campos existam, mesmo que vazios
        if (!isset($result->status)) {
            $result->status = '';
        }
        if (!isset($result->faturado)) {
            $result->faturado = 0;
        }
        if (!isset($result->valorTotal)) {
            $result->valorTotal = 0;
        }
        if (!isset($result->desconto)) {
            $result->desconto = 0;
        }
        if (!isset($result->valor_desconto)) {
            $result->valor_desconto = 0;
        }
        if (!isset($result->tipo_desconto)) {
            $result->tipo_desconto = null;
        }
        if (!isset($result->observacoes)) {
            $result->observacoes = '';
        }
        if (!isset($result->observacoes_cliente)) {
            $result->observacoes_cliente = '';
        }
        if (!isset($result->garantia)) {
            $result->garantia = null;
        }

        return $result;
    }

    public function isEditable($id = null)
    {
        if ($vendas = $this->getById($id)) {
            if ($vendas->faturado) {
                return false; // Vendas faturadas não podem ser editadas
            }
        }

        return true; // Vendas não faturadas podem ser editadas
    }

    public function getByIdCobrancas($id)
    {
        $this->db->select('vendas.*, pessoas.*, pessoas.pes_email as emailCliente, lancamentos.data_vencimento, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome, usuarios.nome, cobrancas.vendas_id,cobrancas.idCobranca,cobrancas.status');
        $this->db->from('vendas');
        $this->db->join('clientes', 'clientes.CLN_ID = vendas.clientes_id');
        $this->db->join('pessoas', 'pessoas.pes_id = clientes.PES_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = vendas.usuarios_id');
        $this->db->join('cobrancas', 'cobrancas.vendas_id = vendas.idVendas');
        $this->db->join('lancamentos', 'vendas.idVendas = lancamentos.vendas_id', 'LEFT');
        $this->db->where('vendas.idVendas', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getProdutos($id = null)
    {
        $this->db->select('itens_de_vendas.*, produtos.*, tributacao_produto.*');
        $this->db->from('itens_de_vendas');
        $this->db->join('produtos', 'produtos.PRO_ID = itens_de_vendas.produtos_id');
        $this->db->join('tributacao_produto', 'tributacao_produto.id = produtos.tributacao_produto_id', 'left');
        $this->db->where('vendas_id', $id);

        return $this->db->get()->result();
    }

    public function getCobrancas($id = null)
    {
        $this->db->select('cobrancas.*');
        $this->db->from('cobrancas');
        $this->db->where('vendas_id', $id);

        return $this->db->get()->result();
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            if ($returnId == true) {
                return $this->db->insert_id($table);
            }

            return true;
        }

        return false;
    }

    public function edit($table, $data, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->update($table, $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    public function delete($table, $fieldID, $ID)
    {
        $this->db->where($fieldID, $ID);
        $this->db->delete($table);
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    public function count($table)
    {
        return $this->db->count_all($table);
    }

    public function autoCompleteProduto($q)
    {
        $this->db->select('PRO_ID as idProdutos, PRO_DESCRICAO as descricao, PRO_COD_BARRA as codDeBarra, PRO_PRECO_VENDA as preco, PRO_ESTOQUE as estoque');
        $this->db->from('produtos');
        $this->db->limit(25);
        $this->db->like('LOWER(PRO_DESCRICAO)', strtolower($q));
        $this->db->or_like('PRO_COD_BARRA', $q);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['descricao'] . ' | Código: ' . $row['codDeBarra'] . ' | Preço: R$ ' . number_format($row['preco'], 2, ',', '.') . ' | Estoque: ' . $row['estoque'],
                    'id' => $row['idProdutos'],
                    'preco' => $row['preco'],
                    'estoque' => $row['estoque']
                ];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteCliente($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nomeCliente', $q);
        $this->db->or_like('documento', $q);
        $query = $this->db->get('clientes');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label'=>$row['nomeCliente'].' | Celular: '.$row['celular'].' | Documento: '.$row['documento'],'id'=>$row['idClientes']];
            }
            echo json_encode($row_set);
        } else {
            $row_set[] = ['label' => 'Adicionar cliente...', 'id' => null];
            echo json_encode($row_set);
        }
    }

    public function autoCompleteUsuario($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('nome', $q);
        $this->db->where('situacao', 1);
        $query = $this->db->get('usuarios');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nome'] . ' | Telefone: ' . $row['telefone'], 'id' => $row['idUsuarios']];
            }
            echo json_encode($row_set);
        }
    }

    public function getQrCode($id, $pixKey, $emitente)
    {
        if (empty($id) || empty($pixKey) || empty($emitente)) {
            return;
        }

        $produtos = $this->getProdutos($id);
        $valorDesconto = $this->getById($id);
        $totalProdutos = array_reduce(
            $produtos,
            function ($carry, $produto) {
                return $carry + ($produto->quantidade * $produto->preco);
            },
            0
        );
        $amount = $valorDesconto->valor_desconto != 0 ? round(floatval($valorDesconto->valor_desconto), 2) : round(floatval($totalProdutos), 2);

        if ($amount <= 0) {
            return;
        }

        $pix = (new StaticPayload())
            ->setAmount($amount)
            ->setTid($id)
            ->setDescription(sprintf('%s Venda %s', substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }

    public function getTotalVendas($idVendas)
    {
        $produtos = $this->getProdutos($idVendas);
        $total = 0;

        foreach ($produtos as $produto) {
            $total += $produto->quantidade * $produto->preco;
        }

        return $total;
    }

    public function getCliente($venda_id)
    {
        try {
            $this->db->select('pessoas.*, pessoas.pes_contato as contato_cliente, pessoas.pes_email as emailCliente, pessoas.pes_logradouro as endereco');
            $this->db->from('vendas');
            $this->db->join('clientes', 'clientes.CLN_ID = vendas.clientes_id');
            $this->db->join('pessoas', 'pessoas.pes_id = clientes.PES_ID');
            $this->db->where('vendas.idVendas', $venda_id);
            $this->db->limit(1);
            
            $query = $this->db->get();
            
            if (!$query) {
                log_message('error', 'Erro na consulta getCliente: ' . $this->db->error()['message']);
                return null;
            }
            
            if ($query->num_rows() > 0) {
                $cliente = $query->row();
                // Garantir que todos os campos existam, mesmo que vazios
                if (!isset($cliente->inscricao_estadual)) {
                    $cliente->inscricao_estadual = '';
                }
                if (!isset($cliente->endereco)) {
                    $cliente->endereco = '';
                }
                if (!isset($cliente->numero)) {
                    $cliente->numero = '';
                }
                if (!isset($cliente->complemento)) {
                    $cliente->complemento = '';
                }
                if (!isset($cliente->bairro)) {
                    $cliente->bairro = '';
                }
                if (!isset($cliente->cidade)) {
                    $cliente->cidade = '';
                }
                if (!isset($cliente->estado)) {
                    $cliente->estado = '';
                }
                if (!isset($cliente->cep)) {
                    $cliente->cep = '';
                }
                if (!isset($cliente->contato_cliente)) {
                    $cliente->contato_cliente = '';
                }
                if (!isset($cliente->emailCliente)) {
                    $cliente->emailCliente = '';
                }
                return $cliente;
            }
            
            return null;
        } catch (Exception $e) {
            log_message('error', 'Exceção em getCliente: ' . $e->getMessage());
            return null;
        }
    }
}

/* End of file vendas_model.php */
/* Location: ./application/models/vendas_model.php */
