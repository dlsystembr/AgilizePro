<?php

defined('BASEPATH') or exit('No direct script access allowed');

class FaturamentoEntrada_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->order_by('id', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        if ($one) {
            $method = 'row';
        } else {
            $method = 'result';
        }

        if (!is_null($perpage) && $perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        if ($array == 'array') {
            return $this->db->$method();
        } else {
            return $this->db->$method('object');
        }
    }

    public function getById($id)
    {
        $this->db->select('faturamento_entrada.*, clientes.*');
        $this->db->from('faturamento_entrada');
        $this->db->join('clientes', 'clientes.idClientes = faturamento_entrada.fornecedor_id');
        $this->db->where('faturamento_entrada.id', $id);
        return $this->db->get()->row();
    }

    public function getItens($id)
    {
        $this->db->select('faturamento_entrada_itens.*, produtos.*, produtos.descricao as descricao');
        $this->db->from('faturamento_entrada_itens');
        $this->db->join('produtos', 'produtos.idProdutos = faturamento_entrada_itens.produto_id');
        $this->db->where('faturamento_entrada_id', $id);
        return $this->db->get()->result();
    }

    public function add($table, $data)
    {
        $this->db->insert($table, $data);
        if ($this->db->affected_rows() == '1') {
            return $this->db->insert_id();
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

    public function getFornecedores()
    {
        $this->db->select('idClientes, nomeCliente');
        $this->db->where('fornecedor', 1);
        $this->db->order_by('nomeCliente', 'asc');
        return $this->db->get('clientes')->result();
    }

    public function getFaturamentoEntrada($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields);
        $this->db->from($table);
        $this->db->join('clientes', 'clientes.idClientes = faturamento_entrada.fornecedor_id');
        $this->db->order_by('faturamento_entrada.data_entrada', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        if ($perpage > 0) {
            $this->db->limit($perpage, $start);
        }

        if ($one) {
            $method = 'row';
        } else {
            $method = 'result';
        }

        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->$method();
        }
        
        return null;
    }

    public function autoCompleteFornecedor($q)
    {
        $this->db->select('idClientes, nomeCliente, documento, telefone, celular');
        $this->db->from('clientes');
        $this->db->where('fornecedor', 1);
        $this->db->limit(25);
        $this->db->like('LOWER(nomeCliente)', strtolower($q));
        $this->db->or_like('LOWER(documento)', strtolower($q));
        $this->db->or_like('telefone', $q);
        $this->db->or_like('celular', $q);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = [
                    'label' => $row['nomeCliente'] . ' | Documento: ' . $row['documento'] . ' | Telefone: ' . $row['telefone'],
                    'id' => $row['idClientes']
                ];
            }
            echo json_encode($row_set);
        } else {
            $row_set[] = ['label' => 'Adicionar fornecedor...', 'id' => null];
            echo json_encode($row_set);
        }
    }

    public function autoCompleteProduto($q)
    {
        $this->db->select('idProdutos, descricao, codDeBarra, precoVenda as preco, estoque');
        $this->db->from('produtos');
        $this->db->limit(25);
        $this->db->like('LOWER(descricao)', strtolower($q));
        $this->db->or_like('codDeBarra', $q);
        
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
} 