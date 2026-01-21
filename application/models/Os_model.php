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
        $this->db->join('pessoas', 'pessoas.pes_id = ordem_servico.ORV_PESS_ID');
        $this->db->where('ordem_servico.ten_id', $this->session->userdata('ten_id'));
        $this->db->limit($perpage, $start);
        $this->db->order_by('ORV_ID', 'desc');
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
        $this->db->join('pessoas', 'pessoas.pes_id = ordem_servico.ORV_PESS_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ordem_servico.ORV_USUARIOS_ID');
        $this->db->join('garantias', 'garantias.idGarantias = ordem_servico.ORV_GARANTIAS_ID', 'left');
        $this->db->join('produtos_os', 'produtos_os.os_id = ordem_servico.ORV_ID', 'left');
        $this->db->join('servicos_os', 'servicos_os.os_id = ordem_servico.ORV_ID', 'left');
        $this->db->where('ordem_servico.ten_id', $this->session->userdata('ten_id'));

        // condicionais da pesquisa

        // condicional de status
        if (array_key_exists('status', $where)) {
            $this->db->where_in('ORV_STATUS', $where['status']);
        }

        // condicional de pessoas
        if (array_key_exists('pesquisa', $where)) {
            if ($lista_pessoas != null) {
                $this->db->where_in('ordem_servico.ORV_PESS_ID', $lista_pessoas);
            }
        }

        // condicional data inicial
        if (array_key_exists('de', $where)) {
            $this->db->where('ORV_DATA_INICIAL >=', $where['de']);
        }
        // condicional data final
        if (array_key_exists('ate', $where)) {
            $this->db->where('ORV_DATA_FINAL <=', $where['ate']);
        }

        $this->db->limit($perpage, $start);
        $this->db->order_by('ordem_servico.ORV_ID', 'desc');
        $this->db->group_by('ordem_servico.ORV_ID');

        $query = $this->db->get();

        $result = ! $one ? $query->result() : $query->row();

        return $result;
    }

    public function getById($id)
    {
        $this->db->select('ordem_servico.*, 
            pessoas.PES_NOME as nomeCliente, 
            pessoas.PES_ID as idClientes,
            pessoas.PES_CPFCNPJ as documento,
            pessoas.PES_OBSERVACAO as contato_cliente,
            garantias.refGarantia, 
            garantias.textoGarantia, 
            usuarios.telefone as telefone_usuario, 
            usuarios.email as email_usuario, 
            usuarios.nome, 
            usuarios.idUsuarios as usuarios_id, 
            telefones_celular.TEL_NUMERO as celular_cliente,
            telefones_celular.TEL_NUMERO as celular,
            telefones_residencial.TEL_NUMERO as telefone,
            telefones_residencial.TEL_NUMERO as telefone_cliente,
            (SELECT EML_EMAIL FROM emails WHERE PES_ID = pessoas.PES_ID AND EML_TIPO = "Geral" LIMIT 1) as email,
            enderecos.END_LOGRADOURO as rua,
            enderecos.END_NUMERO as numero,
            enderecos.END_COMPLEMENTO as complemento,
            enderecos.END_CEP as cep,
            bairros.BAI_NOME as bairro,
            municipios.MUN_NOME as cidade,
            estados.EST_UF as estado');
        $this->db->from('ordem_servico');
        $this->db->join('pessoas', 'pessoas.PES_ID = ordem_servico.ORV_PESS_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ordem_servico.ORV_USUARIOS_ID');
        $this->db->join('garantias', 'garantias.idGarantias = ordem_servico.ORV_GARANTIAS_ID', 'left');
        $this->db->join('telefones as telefones_celular', 'telefones_celular.PES_ID = pessoas.PES_ID AND telefones_celular.TEL_TIPO = "Celular"', 'left');
        $this->db->join('telefones as telefones_residencial', 'telefones_residencial.PES_ID = pessoas.PES_ID AND telefones_residencial.TEL_TIPO = "Residencial"', 'left');
        $this->db->join('enderecos', 'enderecos.PES_ID = pessoas.PES_ID AND enderecos.END_PADRAO = 1', 'left');
        $this->db->join('bairros', 'bairros.BAI_ID = enderecos.BAI_ID', 'left');
        $this->db->join('municipios', 'municipios.MUN_ID = enderecos.MUN_ID', 'left');
        $this->db->join('estados', 'estados.EST_ID = enderecos.EST_ID', 'left');
        $this->db->where('ordem_servico.ORV_ID', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getByIdCobrancas($id)
    {
        $this->db->select('ordem_servico.*, 
            pessoas.PES_NOME as nomeCliente, 
            pessoas.PES_ID as idClientes,
            pessoas.PES_CPFCNPJ as documento,
            pessoas.PES_OBSERVACAO as contato_cliente,
            garantias.refGarantia, 
            garantias.textoGarantia, 
            usuarios.telefone as telefone_usuario, 
            usuarios.email as email_usuario, 
            usuarios.nome, 
            usuarios.idUsuarios as usuarios_id, 
            cobrancas.os_id, 
            cobrancas.idCobranca, 
            cobrancas.status, 
            telefones_celular.TEL_NUMERO as celular_cliente,
            telefones_celular.TEL_NUMERO as celular,
            telefones_residencial.TEL_NUMERO as telefone,
            telefones_residencial.TEL_NUMERO as telefone_cliente,
            (SELECT EML_EMAIL FROM emails WHERE PES_ID = pessoas.PES_ID AND EML_TIPO = "Geral" LIMIT 1) as email,
            enderecos.END_LOGRADOURO as rua,
            enderecos.END_NUMERO as numero,
            enderecos.END_COMPLEMENTO as complemento,
            enderecos.END_CEP as cep,
            bairros.BAI_NOME as bairro,
            municipios.MUN_NOME as cidade,
            estados.EST_UF as estado');
        $this->db->from('ordem_servico');
        $this->db->join('pessoas', 'pessoas.PES_ID = ordem_servico.ORV_PESS_ID');
        $this->db->join('usuarios', 'usuarios.idUsuarios = ordem_servico.ORV_USUARIOS_ID');
        $this->db->join('cobrancas', 'cobrancas.os_id = ordem_servico.ORV_ID');
        $this->db->join('garantias', 'garantias.idGarantias = ordem_servico.ORV_GARANTIAS_ID', 'left');
        $this->db->join('telefones as telefones_celular', 'telefones_celular.PES_ID = pessoas.PES_ID AND telefones_celular.TEL_TIPO = "Celular"', 'left');
        $this->db->join('telefones as telefones_residencial', 'telefones_residencial.PES_ID = pessoas.PES_ID AND telefones_residencial.TEL_TIPO = "Residencial"', 'left');
        $this->db->join('enderecos', 'enderecos.PES_ID = pessoas.PES_ID AND enderecos.END_PADRAO = 1', 'left');
        $this->db->join('bairros', 'bairros.BAI_ID = enderecos.BAI_ID', 'left');
        $this->db->join('municipios', 'municipios.MUN_ID = enderecos.MUN_ID', 'left');
        $this->db->join('estados', 'estados.EST_ID = enderecos.EST_ID', 'left');
        $this->db->where('ordem_servico.ORV_ID', $id);
        $this->db->limit(1);

        return $this->db->get()->row();
    }

    public function getProdutos($id = null)
    {
        $this->db->select('produtos_os.*, produtos.*');
        $this->db->from('produtos_os');
        $this->db->join('produtos', 'produtos.PRO_ID = produtos_os.PRO_ID');
        $this->db->where('os_id', $id);

        return $this->db->get()->result();
    }

    public function getServicos($id = null)
    {
        $this->db->select('servicos_os.*, servicos.SRV_NOME as nome, servicos.SRV_PRECO as precoVenda');
        $this->db->from('servicos_os');
        $this->db->join('servicos', 'servicos.SRV_ID = servicos_os.PRO_ID');
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
        $this->db->like('PRO_COD_BARRA', $q);
        $this->db->or_like('PRO_DESCRICAO', $q);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['PRO_DESCRICAO'] . ' | Preço: R$ ' . $row['PRO_PRECO_VENDA'] . ' | Estoque: ' . $row['PRO_ESTOQUE'], 'estoque' => $row['PRO_ESTOQUE'], 'id' => $row['PRO_ID'], 'preco' => $row['PRO_PRECO_VENDA']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteProdutoSaida($q)
    {
        $this->db->select('*');
        $this->db->limit(25);
        $this->db->like('PRO_COD_BARRA', $q);
        $this->db->or_like('PRO_DESCRICAO', $q);
        $this->db->where('PRO_SAIDA', 1);
        $query = $this->db->get('produtos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['PRO_DESCRICAO'] . ' | Preço: R$ ' . $row['PRO_PRECO_VENDA'] . ' | Estoque: ' . $row['PRO_ESTOQUE'], 'estoque' => $row['PRO_ESTOQUE'], 'id' => $row['PRO_ID'], 'preco' => $row['PRO_PRECO_VENDA']];
            }
            echo json_encode($row_set);
        }
    }

    public function autoCompleteCliente($q)
    {
        $this->db->select('pessoas.*, clientes.CLN_ID as cliente_id');
        $this->db->from('pessoas');
        $this->db->join('clientes', 'clientes.PES_ID = pessoas.pes_id', 'left');
        $this->db->limit(25);
        $this->db->like('pes_nome', $q);
        $this->db->or_like('pes_telefone', $q);
        $this->db->or_like('pes_celular', $q);
        $this->db->or_like('pes_cpf_cnpj', $q);
        $this->db->where('clientes.CLN_ID IS NOT NULL'); // Apenas pessoas que são clientes
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
        $this->db->like('SRV_NOME', $q);
        $query = $this->db->get('servicos');
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $row_set[] = ['label' => $row['SRV_NOME'] . ' | Preço: R$ ' . $row['SRV_PRECO'], 'id' => $row['SRV_ID'], 'preco' => $row['SRV_PRECO']];
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
                $preco = $s->SOS_PRECO ?: $s->precoVenda;
                $totalServico = $totalServico + ($preco * ($s->SOS_QUANTIDADE ?: 1));
            }
        }
        if ($produtos = $this->getProdutos($id)) {
            foreach ($produtos as $p) {
                $totalProdutos = $totalProdutos + $p->PRO_OS_SUBTOTAL;
            }
        }
        if ($valorDescontoBD = $this->getById($id)) {
            $valorDesconto = $valorDescontoBD->ORV_VALOR_DESCONTO;
        }

        return ['totalServico' => $totalServico, 'totalProdutos' => $totalProdutos, 'valor_desconto' => $valorDesconto];
    }

    public function isEditable($id = null)
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'eOs')) {
            return false;
        }
        if ($os = $this->getById($id)) {
            $osT = (int) ($os->ORV_STATUS === 'Faturado' || $os->ORV_STATUS === 'Cancelado' || $os->ORV_FATURADO == 1);
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
