<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class PedidosCompra_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $lista_fornecedores = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('idFornecedores');
                $this->db->like('nomeFornecedor', $where['pesquisa']);
                $this->db->limit(25);
                $fornecedores = $this->db->get('fornecedores')->result();

                foreach ($fornecedores as $f) {
                    array_push($lista_fornecedores, $f->idFornecedores);
                }
            }
        }

        $this->db->select($fields . ',fornecedores.idFornecedores, fornecedores.nomeFornecedor, fornecedores.telefone as telefone_fornecedor, usuarios.nome');
        $this->db->from($table);
        $this->db->join('fornecedores', 'fornecedores.idFornecedores = pedidos_compra.fornecedor_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = pedidos_compra.usuario_id');
        $this->db->join('itens_pedido', 'itens_pedido.pedido_id = pedidos_compra.idPedido', 'left');

        // condicionais da pesquisa
        if ($where) {
            // condicional de status
            if (array_key_exists('status', $where)) {
                $this->db->where_in('pedidos_compra.status', $where['status']);
            }

            // condicional de fornecedores
            if (array_key_exists('pesquisa', $where)) {
                if ($lista_fornecedores != null) {
                    $this->db->where_in('pedidos_compra.fornecedor_id', $lista_fornecedores);
                }
            }

            // condicional data inicial
            if (array_key_exists('de', $where)) {
                $this->db->where('pedidos_compra.data_pedido >=', $where['de']);
            }
            // condicional data final
            if (array_key_exists('ate', $where)) {
                $this->db->where('pedidos_compra.data_pedido <=', $where['ate']);
            }
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('pedidos_compra.idPedido', 'desc');
        $this->db->group_by('pedidos_compra.idPedido');

        $query = $this->db->get();

        $result = !$one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('pedidos_compra.*, fornecedores.*, fornecedores.telefone as telefone_fornecedor, fornecedores.email as email_fornecedor, usuarios.telefone as telefone_usuario, usuarios.email as email_usuario, usuarios.nome');
        $this->db->from('pedidos_compra');
        $this->db->join('fornecedores', 'fornecedores.idFornecedores = pedidos_compra.fornecedor_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = pedidos_compra.usuario_id');
        $this->db->where('pedidos_compra.idPedido', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function isEditable($id = null)
    {
        if ($pedido = $this->getById($id)) {
            return $pedido->status != 'Aprovado';
        }
        return true;
    }

    public function getProdutos($id = null)
    {
        $this->db->select('itens_pedido.*, produtos.*');
        $this->db->from('itens_pedido');
        $this->db->join('produtos', 'produtos.idProdutos = itens_pedido.produto_id');
        $this->db->where('pedido_id', $id);

        return $this->db->get()->result();
    }

    public function getTotalPedido($id = null)
    {
        $this->db->select('SUM(subtotal) as total');
        $this->db->from('itens_pedido');
        $this->db->where('pedido_id', $id);

        return $this->db->get()->row()->total;
    }

    public function add($table, $data, $returnId = false)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            if ($returnId == true) {
                return $this->db->insert_id();
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
        $this->db->select('*');
        $this->db->limit(5);
        $this->db->like('descricao', $q);
        $this->db->where('ativo', 1);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['descricao'] . ' | Preço: R$ ' . number_format($row['precoCompra'], 2, ',', '.') . ' | Estoque: ' . $row['estoque'], 'id' => $row['idProdutos'], 'preco' => $row['precoCompra']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteFornecedor($q)
    {
        $this->db->select('*');
        $this->db->limit(5);
        $this->db->like('nomeFornecedor', $q);
        $this->db->where('fornecedor', 1);
        $query = $this->db->get('fornecedores');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['nomeFornecedor'] . ' | Telefone: ' . $row['telefone'], 'id' => $row['idFornecedores']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteUsuario($q)
    {
        $this->db->select('*');
        $this->db->limit(5);
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

    public function aprovar($id)
    {
        $this->db->where('idPedido', $id);
        $this->db->update('pedidos_compra', ['status' => 'aprovado', 'data_aprovacao' => date('Y-m-d')]);
        
        return $this->db->affected_rows();
    }
} 