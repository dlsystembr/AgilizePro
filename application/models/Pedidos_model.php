<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Model para Pedidos
 * Trabalha com as novas tabelas PEDIDOS e ITENS_PEDIDOS
 * Seguindo convenções de nomenclatura (PDS_, ITP_)
 */
class Pedidos_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Adicionar pedido completo (cabeçalho + itens) em uma transação
     */
    public function addPedidoCompleto($dadosPedido, $itensPedido)
    {
        $this->db->trans_start();

        // Inserir cabeçalho do pedido
        $this->db->insert('PEDIDOS', $dadosPedido);
        $pedidoId = $this->db->insert_id();

        if ($pedidoId && !empty($itensPedido)) {
            // Inserir itens do pedido
            foreach ($itensPedido as $item) {
                $item['PDS_ID'] = $pedidoId;
                $this->db->insert('ITENS_PEDIDOS', $item);
                
                // Atualizar estoque se controle estiver ativo
                if ($this->config->item('control_estoque')) {
                    $this->load->model('produtos_model');
                    $this->produtos_model->updateEstoque($item['PRO_ID'], $item['ITP_QUANTIDADE'], '-');
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $pedidoId;
    }

    /**
     * Buscar pedido por ID
     */
    public function getById($id)
    {
        $this->db->select('PEDIDOS.*, pessoas.*, pessoas.pes_contato as contato_cliente, pessoas.pes_email as emailCliente, 
                          pessoas.pes_nome as nomeCliente, lancamentos.data_vencimento, usuarios.telefone as telefone_usuario, 
                          usuarios.email as email_usuario, usuarios.nome as nome, usuarios.idUsuarios as usuarios_id,
                          operacao_comercial.OPC_NOME as operacao_comercial, operacao_comercial.OPC_ID as operacao_comercial_id,
                          PEDIDOS.PES_ID as clientes_id');
        $this->db->from('PEDIDOS');
        $this->db->join('pessoas', 'pessoas.PES_ID = PEDIDOS.PES_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = PEDIDOS.USU_ID');
        $this->db->join('lancamentos', 'PEDIDOS.PDS_ID = lancamentos.vendas_id', 'LEFT');
        $this->db->join('operacao_comercial', 'operacao_comercial.OPC_ID = PEDIDOS.PDS_OPERACAO_COMERCIAL', 'left');
        $this->db->where('PEDIDOS.PDS_ID', $id);
        $this->db->limit(1);

        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return null;
        }

        $result = $query->row();
        
        // Mapear campos novos para nomes antigos (compatibilidade com views)
        $result->idVendas = $result->PDS_ID;
        $result->dataVenda = $result->PDS_DATA;
        $result->valorTotal = $result->PDS_VALOR_TOTAL;
        $result->desconto = $result->PDS_DESCONTO;
        $result->valor_desconto = $result->PDS_VALOR_DESCONTO;
        $result->tipo_desconto = $result->PDS_TIPO_DESCONTO;
        $result->faturado = $result->PDS_FATURADO;
        $result->observacoes = $result->PDS_OBSERVACOES ?? '';
        $result->observacoes_cliente = $result->PDS_OBSERVACOES_CLIENTE ?? '';
        $result->status = $result->PDS_STATUS ?? '';
        $result->garantia = $result->PDS_GARANTIA;
        
        return $result;
    }

    /**
     * Buscar produtos/itens de um pedido
     */
    public function getProdutos($id = null)
    {
        $this->db->select('ITENS_PEDIDOS.*, produtos.*, tributacao_produto.*,
                          ITENS_PEDIDOS.ITP_ID as idItens,
                          ITENS_PEDIDOS.ITP_QUANTIDADE as quantidade,
                          ITENS_PEDIDOS.ITP_PRECO as preco,
                          ITENS_PEDIDOS.ITP_SUBTOTAL as subTotal,
                          produtos.PRO_ID as idProdutos,
                          produtos.PRO_DESCRICAO as descricao,
                          produtos.PRO_PRECO_VENDA as precoVenda');
        $this->db->from('ITENS_PEDIDOS');
        $this->db->join('produtos', 'produtos.PRO_ID = ITENS_PEDIDOS.PRO_ID');
        $this->db->join('tributacao_produto', 'tributacao_produto.id = produtos.tributacao_produto_id', 'left');
        $this->db->where('PDS_ID', $id);

        return $this->db->get()->result();
    }

    /**
     * Adicionar item ao pedido
     */
    public function addItem($data)
    {
        $this->db->insert('ITENS_PEDIDOS', $data);
        
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    /**
     * Remover item do pedido
     */
    public function deleteItem($itemId)
    {
        $this->db->where('ITP_ID', $itemId);
        $this->db->delete('ITENS_PEDIDOS');
        
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    /**
     * Atualizar pedido
     */
    public function edit($data, $id)
    {
        $this->db->where('PDS_ID', $id);
        $this->db->update('PEDIDOS', $data);

        if ($this->db->affected_rows() >= 0) {
            return true;
        }

        return false;
    }

    /**
     * Excluir pedido (e seus itens em cascata)
     */
    public function delete($id)
    {
        $this->db->where('PDS_ID', $id);
        $this->db->delete('PEDIDOS');
        
        if ($this->db->affected_rows() == '1') {
            return true;
        }

        return false;
    }

    /**
     * Verificar se pedido é editável
     */
    public function isEditable($id = null)
    {
        if ($pedido = $this->getById($id)) {
            if ($pedido->PDS_FATURADO) {
                return false; // Pedidos faturados não podem ser editados
            }
        }

        return true;
    }

    /**
     * Calcular total do pedido
     */
    public function getTotalPedido($idPedido)
    {
        $produtos = $this->getProdutos($idPedido);
        $total = 0;

        foreach ($produtos as $produto) {
            $total += $produto->quantidade * $produto->preco;
        }

        return $total;
    }

    /**
     * Listar pedidos com filtros
     */
    public function get($where = [], $perpage = 0, $start = 0)
    {
        $lista_pessoas = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('PES_ID');
                $this->db->from('pessoas');
                $this->db->like('pes_nome', $where['pesquisa']);
                $this->db->limit(25);
                $pessoas = $this->db->get()->result();

                foreach ($pessoas as $p) {
                    array_push($lista_pessoas, $p->PES_ID);
                }
            }
        }

        $this->db->select('PEDIDOS.*, pessoas.pes_nome as nomeCliente, pessoas.PES_ID as idClientes, 
                          usuarios.nome, operacao_comercial.OPC_NOME as operacao_comercial,
                          PEDIDOS.PDS_ID as idVendas');
        $this->db->from('PEDIDOS');
        $this->db->limit($perpage, $start);
        $this->db->join('pessoas', 'pessoas.PES_ID = PEDIDOS.PES_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = PEDIDOS.USU_ID');
        $this->db->join('operacao_comercial', 'operacao_comercial.OPC_ID = PEDIDOS.PDS_OPERACAO_COMERCIAL', 'left');
        $this->db->order_by('PDS_ID', 'desc');
        
        // Condicionais da pesquisa
        if ($where) {
            if (array_key_exists('status', $where)) {
                $this->db->where_in('PEDIDOS.PDS_STATUS', $where['status']);
            }

            if (array_key_exists('pesquisa', $where)) {
                if ($lista_pessoas != null) {
                    $this->db->where_in('PEDIDOS.PES_ID', $lista_pessoas);
                }
            }

            if (array_key_exists('de', $where)) {
                $this->db->where('PEDIDOS.PDS_DATA >=', $where['de']);
            }
            
            if (array_key_exists('ate', $where)) {
                $this->db->where('PEDIDOS.PDS_DATA <=', $where['ate']);
            }
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Contar total de pedidos
     */
    public function count()
    {
        return $this->db->count_all('PEDIDOS');
    }
}

/* End of file Pedidos_model.php */
/* Location: ./application/models/Pedidos_model.php */
