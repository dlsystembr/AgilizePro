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
                $item['pds_id'] = $pedidoId;
                $this->db->insert('ITENS_PEDIDOS', $item);
                
                // Atualizar estoque se controle estiver ativo
                if ($this->config->item('control_estoque')) {
                    $this->load->model('produtos_model');
                    $this->produtos_model->updateEstoque($item['pro_id'], $item['itp_quantidade'], '-');
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
                          pessoas.pes_nome as nomeCliente, lancamentos.data_vencimento,                           usuarios.usu_email as email_usuario, usuarios.usu_nome as nome, usuarios.usu_id as usuarios_id,
                          operacao_comercial.opc_nome as operacao_comercial, operacao_comercial.opc_id as operacao_comercial_id,
                          PEDIDOS.pes_id as clientes_id');
        $this->db->from('PEDIDOS');
        $this->db->join('pessoas', 'pessoas.pes_id = PEDIDOS.pes_id');
        $this->db->join('usuarios', 'usuarios.usu_id = PEDIDOS.usu_id');
        $this->db->join('lancamentos', 'PEDIDOS.pds_id = lancamentos.vendas_id', 'LEFT');
        $this->db->join('operacao_comercial', 'operacao_comercial.opc_id = PEDIDOS.pds_operacao_comercial', 'left');
        $this->db->where('PEDIDOS.pds_id', $id);
        $this->db->limit(1);

        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return null;
        }

        $result = $query->row();
        
        // Mapear campos novos para nomes antigos (compatibilidade com views)
        $result->idVendas = $result->pds_id;
        $result->dataVenda = $result->pds_data;
        $result->valorTotal = $result->pds_valor_total;
        $result->desconto = $result->pds_desconto;
        $result->valor_desconto = $result->pds_valor_desconto;
        $result->tipo_desconto = $result->pds_tipo_desconto;
        $result->faturado = $result->pds_faturado;
        $result->observacoes = $result->pds_observacoes ?? '';
        $result->observacoes_cliente = $result->pds_observacoes_cliente ?? '';
        $result->status = $result->pds_status ?? '';
        $result->garantia = $result->pds_garantia;
        
        return $result;
    }

    /**
     * Buscar produtos/itens de um pedido
     */
    public function getProdutos($id = null)
    {
        $this->db->select('ITENS_PEDIDOS.*, produtos.*, tributacao_produto.*,
                          ITENS_PEDIDOS.itp_id as idItens,
                          ITENS_PEDIDOS.itp_quantidade as quantidade,
                          ITENS_PEDIDOS.itp_preco as preco,
                          ITENS_PEDIDOS.itp_subtotal as subTotal,
                          produtos.pro_id as idProdutos,
                          produtos.pro_descricao as descricao,
                          produtos.pro_preco_venda as precoVenda');
        $this->db->from('ITENS_PEDIDOS');
        $this->db->join('produtos', 'produtos.pro_id = ITENS_PEDIDOS.pro_id');
        $this->db->join('tributacao_produto', 'tributacao_produto.id = produtos.tributacao_produto_id', 'left');
        $this->db->where('pds_id', $id);

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
        $this->db->where('itp_id', $itemId);
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
        $this->db->where('pds_id', $id);
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
        $this->db->where('pds_id', $id);
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
            if ($pedido->pds_faturado) {
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
                $this->db->select('pes_id');
                $this->db->from('pessoas');
                $this->db->like('pes_nome', $where['pesquisa']);
                $this->db->limit(25);
                $pessoas = $this->db->get()->result();

                foreach ($pessoas as $p) {
                    array_push($lista_pessoas, $p->pes_id);
                }
            }
        }

        $this->db->select('PEDIDOS.*, pessoas.pes_nome as nomeCliente, pessoas.pes_id as idClientes, 
                          usuarios.usu_nome as nome, operacao_comercial.opc_nome as operacao_comercial,
                          PEDIDOS.pds_id as idVendas');
        $this->db->from('PEDIDOS');
        $this->db->limit($perpage, $start);
        $this->db->join('pessoas', 'pessoas.pes_id = PEDIDOS.pes_id');
        $this->db->join('usuarios', 'usuarios.usu_id = PEDIDOS.usu_id');
        $this->db->join('operacao_comercial', 'operacao_comercial.opc_id = PEDIDOS.pds_operacao_comercial', 'left');
        $this->db->order_by('pds_id', 'desc');
        
        // Condicionais da pesquisa
        if ($where) {
            if (array_key_exists('status', $where)) {
                $this->db->where_in('PEDIDOS.pds_status', $where['status']);
            }

            if (array_key_exists('pesquisa', $where)) {
                if ($lista_pessoas != null) {
                    $this->db->where_in('PEDIDOS.pes_id', $lista_pessoas);
                }
            }

            if (array_key_exists('de', $where)) {
                $this->db->where('PEDIDOS.pds_data >=', $where['de']);
            }
            
            if (array_key_exists('ate', $where)) {
                $this->db->where('PEDIDOS.pds_data <=', $where['ate']);
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
