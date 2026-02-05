<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model para tabela parametros (parâmetros por empresa).
 * Valor em prm_valor (TEXT); conversão na aplicação conforme prm_tipo_dado.
 */
class Parametros_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Retorna todos os parâmetros da empresa agrupados por prm_grupo (para a tela).
     * @param int $emp_id
     * @return array [ 'grupo1' => [ row, row ], 'grupo2' => [ ... ] ]
     */
    public function getTodosPorEmpresaAgrupados($emp_id)
    {
        if (!$this->db->table_exists('parametros')) {
            return [];
        }
        $this->db->from('parametros');
        $this->db->where('emp_id', (int) $emp_id);
        $this->db->where('prm_visivel', 1);
        $this->db->order_by('prm_grupo', 'ASC');
        $this->db->order_by('prm_ordem', 'ASC');
        $this->db->order_by('prm_nome', 'ASC');
        $rows = $this->db->get()->result();
        $agrupados = [];
        foreach ($rows as $row) {
            $grupo = $row->prm_grupo ?: 'geral';
            if (!isset($agrupados[$grupo])) {
                $agrupados[$grupo] = [];
            }
            $agrupados[$grupo][] = $row;
        }
        return $agrupados;
    }

    /**
     * Retorna um parâmetro por empresa e nome.
     * @param int $emp_id
     * @param string $nome
     * @return object|null
     */
    public function getPorEmpresaENome($emp_id, $nome)
    {
        if (!$this->db->table_exists('parametros')) {
            return null;
        }
        return $this->db->get_where('parametros', [
            'emp_id' => (int) $emp_id,
            'prm_nome' => $nome,
        ])->row();
    }

    /**
     * Retorna o valor convertido conforme o tipo (para uso na aplicação).
     * @param int $emp_id
     * @param string $nome
     * @return mixed
     */
    public function getValor($emp_id, $nome)
    {
        $row = $this->getPorEmpresaENome($emp_id, $nome);
        if (!$row) {
            return null;
        }
        return $this->castValor($row->prm_valor, $row->prm_tipo_dado);
    }

    /**
     * Converte string para o tipo indicado.
     */
    public function castValor($valor, $tipo)
    {
        if ($valor === null || $valor === '') {
            return $tipo === 'boolean' ? 0 : null;
        }
        switch ($tipo) {
            case 'integer':
                return (int) $valor;
            case 'float':
                return (float) str_replace(',', '.', $valor);
            case 'boolean':
                return in_array($valor, ['1', 'true', 'on', 'sim', 'yes'], true) ? 1 : 0;
            case 'datetime':
                return $valor; // manter string ou converter para timestamp na app
            case 'text':
            case 'json':
            case 'string':
            default:
                return $valor;
        }
    }

    /**
     * Serializa valor para gravar em prm_valor.
     */
    public function valorParaString($valor, $tipo)
    {
        if ($valor === null || $valor === '') {
            return '';
        }
        if ($tipo === 'boolean') {
            return $valor ? '1' : '0';
        }
        if ($tipo === 'json' && is_array($valor)) {
            return json_encode($valor);
        }
        return (string) $valor;
    }

    /**
     * Atualiza ou insere um parâmetro.
     * @param int $emp_id
     * @param string $nome
     * @param mixed $valor
     * @param string $tipo
     * @return bool
     */
    public function setValor($emp_id, $nome, $valor, $tipo = 'string')
    {
        if (!$this->db->table_exists('parametros')) {
            return false;
        }
        $valorStr = $this->valorParaString($valor, $tipo);
        $row = $this->getPorEmpresaENome($emp_id, $nome);
        $now = date('Y-m-d H:i:s');
        if ($row) {
            $this->db->where('prm_id', $row->prm_id);
            return $this->db->update('parametros', [
                'prm_valor' => $valorStr,
                'prm_data_atualizacao' => $now,
            ]);
        }
        return $this->db->insert('parametros', [
            'emp_id' => (int) $emp_id,
            'prm_nome' => $nome,
            'prm_tipo_dado' => $tipo,
            'prm_valor' => $valorStr,
            'prm_visivel' => 1,
            'prm_grupo' => 'geral',
            'prm_ordem' => 0,
            'prm_data_atualizacao' => $now,
        ]);
    }

    /**
     * Salva em lote a partir de array [ prm_nome => valor ].
     * Atualiza apenas os parâmetros que existem para esta empresa.
     * @param int $emp_id
     * @param array $dados
     * @return bool
     */
    public function salvarLote($emp_id, $dados)
    {
        if (!$this->db->table_exists('parametros') || empty($dados)) {
            return true;
        }
        $this->db->trans_start();
        foreach ($dados as $nome => $valor) {
            $row = $this->getPorEmpresaENome($emp_id, $nome);
            if ($row) {
                $valorStr = $this->valorParaString($valor, $row->prm_tipo_dado);
                $this->db->where('prm_id', $row->prm_id);
                $this->db->update('parametros', [
                    'prm_valor' => $valorStr,
                    'prm_data_atualizacao' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== false;
    }
}
