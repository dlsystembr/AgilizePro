<?php

use Piggly\Pix\StaticPayload;

class Os_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get($table, $fields, $where = '', $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $this->db->select($fields . ',pessoas.pes_nome as nomeCliente');
        $this->db->from($table);
        $this->db->join('pessoas', 'pessoas.pes_id = ordem_servico.orv_pess_id');
        $this->db->where('ordem_servico.ten_id', $this->session->userdata('ten_id'));
        $this->db->limit($perpage, $start);
        $this->db->order_by('orv_id', 'desc');
        if ($where) {
            $this->db->where($where);
        }

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getOs($table, $fields, $where = [], $perpage = 0, $start = 0, $one = false, $array = 'array')
    {
        $lista_pessoas = [];
        if ($where) {
            if (array_key_exists('pesquisa', $where)) {
                $this->db->select('pes_id');
                $this->db->like('pes_nome', $where['pesquisa']);
                $this->db->or_like('pes_cpf_cnpj', $where['pesquisa']);
                $this->db->limit(25);
                $pessoas = $this->db->get('pessoas')->result();

                foreach ($pessoas as $p) {
                    array_push($lista_pessoas, $p->pes_id);
                }
            }
        }

        $this->db->select($fields . ',pessoas.pes_id as idClientes, pessoas.pes_nome as nomeCliente, usuarios.nome, garantias.*');
        $this->db->from($table);
        $this->db->join('pessoas', 'pessoas.pes_id = ordem_servico.orv_pess_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ordem_servico.orv_usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = ordem_servico.orv_garantias_id', 'left');
        $this->db->join('produtos_os', 'produtos_os.os_id = ordem_servico.orv_id', 'left');
        $this->db->join('servicos_os', 'servicos_os.os_id = ordem_servico.orv_id', 'left');
        $this->db->where('ordem_servico.ten_id', $this->session->userdata('ten_id'));

        // condicionais da pesquisa

        // condicional de status
        if (array_key_exists('status', $where)) {
            $this->db->where_in('orv_status', $where['status']);
        }

        // condicional de pessoas
        if (array_key_exists('pesquisa', $where)) {
            if ($lista_pessoas != null) {
                $this->db->where_in('ordem_servico.orv_pess_id', $lista_pessoas);
            }
        }

        // condicional data inicial
        if (array_key_exists('de', $where)) {
            $this->db->where('orv_data_inicial >=', $where['de']);
        }
        // condicional data final
        if (array_key_exists('ate', $where)) {
            $this->db->where('orv_data_final <=', $where['ate']);
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('ordem_servico.orv_id', 'desc');
        $this->db->group_by('ordem_servico.orv_id');

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('ordem_servico.*, 
            pessoas.pes_nome as nomeCliente, 
            pessoas.pes_id as idClientes,
            pessoas.pes_cpfcnpj as documento,
            pessoas.pes_observacao as contato_cliente,
            garantias.refGarantia, 
            garantias.textoGarantia, 
            usuarios.telefone as telefone_usuario, 
            usuarios.email as email_usuario, 
            usuarios.nome, 
            usuarios.idUsuarios as usuarios_id, 
            telefones_celular.tel_numero as celular_cliente,
            telefones_celular.tel_numero as celular,
            telefones_residencial.tel_numero as telefone,
            telefones_residencial.tel_numero as telefone_cliente,
            (SELECT eml_email FROM emails WHERE pes_id = pessoas.pes_id AND eml_tipo = "Geral" LIMIT 1) as email,
            enderecos.end_logradouro as rua,
            enderecos.end_numero as numero,
            enderecos.end_complemento as complemento,
            enderecos.end_cep as cep,
            bairros.bai_nome as bairro,
            municipios.mun_nome as cidade,
            estados.est_uf as estado');
        $this->db->from('ordem_servico');
        $this->db->join('pessoas', 'pessoas.pes_id = ordem_servico.orv_pess_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ordem_servico.orv_usuarios_id');
        $this->db->join('garantias', 'garantias.idGarantias = ordem_servico.orv_garantias_id', 'left');
        $this->db->join('telefones as telefones_celular', 'telefones_celular.pes_id = pessoas.pes_id AND telefones_celular.tel_tipo = "Celular"', 'left');
        $this->db->join('telefones as telefones_residencial', 'telefones_residencial.pes_id = pessoas.pes_id AND telefones_residencial.tel_tipo = "Residencial"', 'left');
        $this->db->join('enderecos', 'enderecos.pes_id = pessoas.pes_id AND enderecos.end_padrao = 1', 'left');
        $this->db->join('bairros', 'bairros.bai_id = enderecos.bai_id', 'left');
        $this->db->join('municipios', 'municipios.mun_id = enderecos.mun_id', 'left');
        $this->db->join('estados', 'estados.est_id = enderecos.est_id', 'left');
        $this->db->where('ordem_servico.orv_id', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByIdCobrancas($id)
    {
        $this->db->select('ordem_servico.*, 
            pessoas.pes_nome as nomeCliente, 
            pessoas.pes_id as idClientes,
            pessoas.pes_cpfcnpj as documento,
            pessoas.pes_observacao as contato_cliente,
            garantias.refGarantia, 
            garantias.textoGarantia, 
            usuarios.telefone as telefone_usuario, 
            usuarios.email as email_usuario, 
            usuarios.nome, 
            usuarios.idUsuarios as usuarios_id, 
            cobrancas.os_id, 
            cobrancas.idCobranca, 
            cobrancas.status, 
            telefones_celular.tel_numero as celular_cliente,
            telefones_celular.tel_numero as celular,
            telefones_residencial.tel_numero as telefone,
            telefones_residencial.tel_numero as telefone_cliente,
            (SELECT eml_email FROM emails WHERE pes_id = pessoas.pes_id AND eml_tipo = "Geral" LIMIT 1) as email,
            enderecos.end_logradouro as rua,
            enderecos.end_numero as numero,
            enderecos.end_complemento as complemento,
            enderecos.end_cep as cep,
            bairros.bai_nome as bairro,
            municipios.mun_nome as cidade,
            estados.est_uf as estado');
        $this->db->from('ordem_servico');
        $this->db->join('pessoas', 'pessoas.pes_id = ordem_servico.orv_pess_id');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ordem_servico.orv_usuarios_id');
        $this->db->join('cobrancas', 'cobrancas.os_id = ordem_servico.orv_id');
        $this->db->join('garantias', 'garantias.idGarantias = ordem_servico.orv_garantias_id', 'left');
        $this->db->join('telefones as telefones_celular', 'telefones_celular.pes_id = pessoas.pes_id AND telefones_celular.tel_tipo = "Celular"', 'left');
        $this->db->join('telefones as telefones_residencial', 'telefones_residencial.pes_id = pessoas.pes_id AND telefones_residencial.tel_tipo = "Residencial"', 'left');
        $this->db->join('enderecos', 'enderecos.pes_id = pessoas.pes_id AND enderecos.end_padrao = 1', 'left');
        $this->db->join('bairros', 'bairros.bai_id = enderecos.bai_id', 'left');
        $this->db->join('municipios', 'municipios.mun_id = enderecos.mun_id', 'left');
        $this->db->join('estados', 'estados.est_id = enderecos.est_id', 'left');
        $this->db->where('ordem_servico.orv_id', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getProdutos($id = null)
    {
        $this->db->select('produtos_os.*, produtos.*');
        $this->db->from('produtos_os');
        $this->db->join('produtos', 'produtos.pro_id = produtos_os.pro_id');
        $this->db->where('os_id', $id);

        return $this->db->get()->result();
    }

    public function getServicos($id = null)
    {
        $this->db->select('servicos_os.*, servicos.srv_nome as nome, servicos.srv_preco as precoVenda');
        $this->db->from('servicos_os');
        $this->db->join('servicos', 'servicos.srv_id = servicos_os.pro_id');
        $this->db->where('os_id', $id);

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
        // Verificar qual tabela está sendo contada
        if ($table == 'os' || $table == 'ordem_servico') {
            $this->db->where('ten_id', $this->session->userdata('ten_id'));
            return $this->db->count_all_results('os');
        }
        $this->db->where('ten_id', $this->session->userdata('ten_id'));
        return $this->db->count_all_results($table);
    }

    public function autoCompleteProduto($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('pro_cod_barra', $q);
        $this->db->or_like('pro_descricao', $q);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['pro_descricao'] . ' | Preço: R$ ' . $row['pro_preco_venda'] . ' | Estoque: ' . $row['pro_estoque'], 'estoque' => $row['pro_estoque'], 'id' => $row['pro_id'], 'preco' => $row['pro_preco_venda']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteProdutoSaida($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('pro_cod_barra', $q);
        $this->db->or_like('pro_descricao', $q);
        $this->db->where('pro_saida', 1);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['pro_descricao'] . ' | Preço: R$ ' . $row['pro_preco_venda'] . ' | Estoque: ' . $row['pro_estoque'], 'estoque' => $row['pro_estoque'], 'id' => $row['pro_id'], 'preco' => $row['pro_preco_venda']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteCliente($q)
    {
        $this->db->select('pessoas.*, clientes.cln_id as cliente_id');
        $this->db->from('pessoas');
        $this->db->join('clientes', 'clientes.pes_id = pessoas.pes_id', 'left');
        $this->db->limit(25);
        $this->db->like('pes_nome', $q);
        $this->db->or_like('pes_telefone', $q);
        $this->db->or_like('pes_celular', $q);
        $this->db->or_like('pes_cpf_cnpj', $q);
        $this->db->where('clientes.cln_id IS NOT NULL'); // Apenas pessoas que são clientes
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['pes_nome'] . ' | Telefone: ' . $row['pes_telefone'] . ' | Celular: ' . $row['pes_celular'] . ' | Documento: ' . $row['pes_cpf_cnpj'], 'id' => $row['pes_id']];
            }
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

    public function autoCompleteTermoGarantia($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('LOWER(refGarantia)', $q);
        $query = $this->db->get('garantias');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['refGarantia'], 'id' => $row['idGarantias']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteServico($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('srv_nome', $q);
        $query = $this->db->get('servicos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['srv_nome'] . ' | Preço: R$ ' . $row['srv_preco'], 'id' => $row['srv_id'], 'preco' => $row['srv_preco']];
            }
            echo json_encode($row_set);
        }
    }

    public function anexar($os, $anexo, $url, $thumb, $path)
    {
        $this->db->set('anexo', $anexo);
        $this->db->set('url', $url);
        $this->db->set('thumb', $thumb);
        $this->db->set('path', $path);
        $this->db->set('os_id', $os);

        return $this->db->insert('anexos');
    }

    public function getAnexos($os)
    {
        $this->db->where('os_id', $os);

        return $this->db->get('anexos')->result();
    }

    public function getAnotacoes($os)
    {
        $this->db->where('os_id', $os);
        $this->db->order_by('idAnotacoes', 'desc');

        return $this->db->get('anotacoes_os')->result();
    }

    public function getCobrancas($id = null)
    {
        $this->db->select('cobrancas.*');
        $this->db->from('cobrancas');
        $this->db->where('os_id', $id);

        return $this->db->get()->result();
    }

    public function criarTextoWhats($textoBase, $troca)
    {
        $procura = ['{CLIENTE_NOME}', '{NUMERO_OS}', '{STATUS_OS}', '{VALOR_OS}', '{DESCRI_PRODUTOS}', '{EMITENTE}', '{TELEFONE_EMITENTE}', '{OBS_OS}', '{DEFEITO_OS}', '{LAUDO_OS}', '{DATA_FINAL}', '{DATA_INICIAL}', '{DATA_GARANTIA}'];
        $textoBase = str_replace($procura, $troca, $textoBase);
        $textoBase = strip_tags($textoBase);
        $textoBase = htmlentities(urlencode($textoBase));

        return $textoBase;
    }

    public function valorTotalOS($id = null)
    {
        $totalServico = 0;
        $totalProdutos = 0;
        $valorDesconto = 0;
        if ($servicos = $this->getServicos($id)) {
            foreach ($servicos as $s) {
                $preco = $s->sos_preco ?: $s->precoVenda;
                $totalServico = $totalServico + ($preco * ($s->sos_quantidade ?: 1));
            }
        }
        if ($produtos = $this->getProdutos($id)) {
            foreach ($produtos as $p) {
                $totalProdutos = $totalProdutos + $p->pro_os_subtotal;
            }
        }
        if ($valorDescontoBD = $this->getById($id)) {
            $valorDesconto = $valorDescontoBD->orv_valor_desconto;
        }

        return ['totalServico' => $totalServico, 'totalProdutos' => $totalProdutos, 'valor_desconto' => $valorDesconto];
    }

    public function isEditable($id = null)
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            return false;
        }
        if ($os = $this->getById($id)) {
            $osT = (int) ($os->orv_status === 'Faturado' || $os->orv_status === 'Cancelado' || $os->orv_faturado == 1);
            if ($osT) {
                return $this->data['configuration']['control_editos'] == '1';
            }
        }

        return true;
    }

    public function getQrCode($id, $pixKey, $emitente)
    {
        if (empty($id) || empty($pixKey) || empty($emitente)) {
            return;
        }

        $result = $this->valorTotalOS($id);
        $amount = $result['valor_desconto'] != 0 ? round(floatval($result['valor_desconto']), 2) : round(floatval($result['totalServico'] + $result['totalProdutos']), 2);

        if ($amount <= 0) {
            return;
        }

        $pix = (new StaticPayload())
            ->setAmount($amount)
            ->setTid($id)
            ->setDescription(sprintf('%s OS %s', substr($emitente->nome, 0, 18), $id), true)
            ->setPixKey(getPixKeyType($pixKey), $pixKey)
            ->setMerchantName($emitente->nome)
            ->setMerchantCity($emitente->cidade);

        return $pix->getQRCode();
    }
}
