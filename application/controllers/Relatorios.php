<?php

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Relatorios extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Relatorios_model');
        $this->load->model('Usuarios_model');
        $this->load->model('Mapos_model');

        $this->data['menuRelatorios'] = 'Relatórios';
    }

    public function index()
    {
        redirect(base_url());
    }

    public function clientes()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de clientes.');
            redirect(base_url());
        }
        $this->data['view'] = 'relatorios/rel_clientes';

        return $this->layout();
    }

    public function produtos()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de produtos.');
            redirect(base_url());
        }
        $this->data['view'] = 'relatorios/rel_produtos';

        return $this->layout();
    }

    public function clientesCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de clientes.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial');
        $dataFinal = $this->input->get('dataFinal');

        $data['dataInicial'] = date('d/m/Y', strtotime($dataInicial));
        $data['dataFinal'] = date('d/m/Y', strtotime($dataFinal));

        $data['clientes'] = $this->Relatorios_model->clientesCustom($dataInicial, $dataFinal, $this->input->get('tipocliente'));
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Clientes Customizado';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirClientes', $data, true);
        pdf_create($html, 'relatorio_clientes' . date('d/m/y'), true);
    }

    public function clientesRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rCliente')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de clientes.');
            redirect(base_url());
        }

        $format = $this->input->get('format');

        if ($format == 'xls') {
            $clientes = $this->Relatorios_model->clientesRapid($array = true);
            $cabecalho = [
                'Código' => 'integer',
                'Nome' => 'string',
                'Sexo' => 'string',
                'Pessoa Física' => 'string',
                'Documento' => 'string',
                'Telefone' => 'string',
                'Celular' => 'string',
                'Contato' => 'string',
                'E-mail' => 'string',
                'Fornecedor' => 'string',
                'Data de Cadastro' => 'YYYY-MM-DD',
                'Rua' => 'string',
                'Número' => 'string',
                'Complemento' => 'string',
                'Bairro' => 'string',
                'Cidade' => 'string',
                'Estado' => 'string',
                'CEP' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($clientes as $cliente) {
                if ($cliente['fornecedor']) {
                    $cliente['fornecedor'] = 'sim';
                } else {
                    $cliente['fornecedor'] = 'não';
                }
                if ($cliente['pessoa_fisica']) {
                    $cliente['pessoa_fisica'] = 'sim';
                } else {
                    $cliente['pessoa_fisica'] = 'não';
                }
                $writer->writeSheetRow('Sheet1', $cliente);
            }

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_clientes.xlsx', $arquivo);

            return;
        }

        $data['clientes'] = $this->Relatorios_model->clientesRapid();
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Clientes';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');

        $html = $this->load->view('relatorios/imprimir/imprimirClientes', $data, true);
        pdf_create($html, 'relatorio_clientes' . date('d/m/y'), true);
    }

    public function produtosRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de produtos.');
            redirect(base_url());
        }

        $data['produtos'] = $this->Relatorios_model->produtosRapid();
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Produtos';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirProdutos', $data, true);
        pdf_create($html, 'relatorio_produtos' . date('d/m/y'), true);
    }

    public function produtosRapidMin()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de produtos.');
            redirect(base_url());
        }

        $data['produtos'] = $this->Relatorios_model->produtosRapidMin();
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Produtos Com Estoque Mínimo';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirProdutos', $data, true);
        pdf_create($html, 'relatorio_produtos' . date('d/m/y'), true);
    }

    public function produtosCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rProduto')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de produtos.');
            redirect(base_url());
        }

        $precoInicial = $this->input->get('precoInicial');
        $precoFinal = $this->input->get('precoFinal');
        $estoqueInicial = $this->input->get('estoqueInicial');
        $estoqueFinal = $this->input->get('estoqueFinal');

        $data['produtos'] = $this->Relatorios_model->produtosCustom($precoInicial, $precoFinal, $estoqueInicial, $estoqueFinal);
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Produtos Customizado';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirProdutos', $data, true);
        pdf_create($html, 'relatorio_produtos' . date('d/m/y'), true);
    }

    public function produtosEtiquetas()
    {
        $de = $this->input->get('de_id');
        $ate = $this->input->get('ate_id');
        try {
            if ($de <= $ate) {
                $data['produtos'] = $this->Relatorios_model->produtosEtiquetas($de, $ate);
                $this->load->helper('mpdf');
                $html = $this->load->view('relatorios/imprimir/imprimirEtiquetas', $data, true);
                pdf_create($html, 'etiquetas_' . $de . '_' . $ate, true);
            } else {
                $this->session->set_flashdata('error', 'O campo "<b>De</b>" não pode ser maior doque o campo "<b>Até</b>"!');
                redirect('produtos');
            }
        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('produtos');
        }
    }

    public function sku()
    {
        if (! ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')
            && $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs'))) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatório SKU.');
            redirect(base_url());
        }

        $this->data['view'] = 'relatorios/rel_sku';

        return $this->layout();
    }

    public function skuRapid()
    {
        if (! ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')
            && $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs'))) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatório SKU.');
            redirect(base_url());
        }

        $format = $this->input->get('format');

        if ($format == 'xls') {
            $vendas = $this->Relatorios_model->skuRapid(true);

            $cabecalho = [
                'ID Cliente' => 'integer',
                'Nome Cliente' => 'string',
                'ID Produto' => 'integer',
                'Descrição Produto' => 'string',
                'Quantidade' => 'integer',
                'ID Relacionado' => 'integer',
                'Data' => 'YYYY-MM-DD',
                'Preço Unitário' => 'price',
                'Preço Total' => 'price',
                'Origem' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($vendas as $venda) {
                $writer->writeSheetRow('Sheet1', $venda);
            }

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_sku.xlsx', $arquivo);

            return;
        }

        $data['resultados'] = $this->Relatorios_model->skuRapid();
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório SKU';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirSKU', $data, true);
        pdf_create($html, 'relatorio_produtos' . date('d/m/y'), true);
    }

    public function skuCustom()
    {
        if (! ($this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')
            && $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs'))) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatório SKU.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial');
        $dataFinal = $this->input->get('dataFinal');
        $cliente = $this->input->get('clientes_id');
        $format = $this->input->get('format');
        $origem = $this->input->get('origem');

        if ($format == 'xls') {
            $vendas = $this->Relatorios_model->skuCustom($dataInicial, $dataFinal, $cliente, $origem, true);

            $cabecalho = [
                'ID Cliente' => 'integer',
                'Nome Cliente' => 'string',
                'ID Produto' => 'integer',
                'Descrição Produto' => 'string',
                'Quantidade' => 'integer',
                'ID Relacionado' => 'integer',
                'Data' => 'YYYY-MM-DD',
                'Preço Unitário' => 'price',
                'Preço Total' => 'price',
                'Origem' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($vendas as $venda) {
                $writer->writeSheetRow('Sheet1', $venda);
            }

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_sku.xlsx', $arquivo);

            return;
        }

        $data['resultados'] = $this->Relatorios_model->skuCustom($dataInicial, $dataFinal, $cliente, $origem);
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório SKU';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirSKU', $data, true);
        pdf_create($html, 'relatorio_produtos' . date('d/m/y'), true);
    }

    public function servicos()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de serviços.');
            redirect(base_url());
        }
        $this->data['view'] = 'relatorios/rel_servicos';

        return $this->layout();
    }

    public function servicosCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de serviços.');
            redirect(base_url());
        }

        $precoInicial = $this->input->get('precoInicial');
        $precoFinal = $this->input->get('precoFinal');

        $data['servicos'] = $this->Relatorios_model->servicosCustom($precoInicial, $precoFinal);
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Serviços Customizado';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirServicos', $data, true);
        pdf_create($html, 'relatorio_servicos' . date('d/m/y'), true);
    }

    public function servicosRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rServico')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de serviços.');
            redirect(base_url());
        }

        $data['servicos'] = $this->Relatorios_model->servicosRapid();
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Serviços';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirServicos', $data, true);
        pdf_create($html, 'relatorio_servicos' . date('d/m/y'), true);
    }

    public function os()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de OS.');
            redirect(base_url());
        }
        $this->data['view'] = 'relatorios/rel_os';

        return $this->layout();
    }

    public function osRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de OS.');
            redirect(base_url());
        }

        $format = $this->input->get('format');

        $isXls = $format === 'xls';
        $os = $this->Relatorios_model->osRapid($isXls);
        $totalProdutos = 0;
        $totalServicos = 0;
        $totalDesconto = 0;
        $totalValorDesconto = 0;
        $valorTotal = 0;
        foreach ($os as $o) {
            $totalProdutos += $isXls
                ? floatval($o['total_produto'])
                : floatval($o->total_produto);
            $totalServicos += $isXls
                ? floatval($o['total_servico'])
                : floatval($o->total_servico);
            $totalDesconto += $isXls
                ? floatval($o['desconto'])
                : floatval($o->desconto);

            $isXls
                ?
                $totalValorDesconto += $o['valor_desconto'] != 0
                ? floatval($o['valor_desconto'])
                : floatval($o['total_servico']) + floatval($o['total_produto'])
                :
                $totalValorDesconto += $o->valor_desconto != 0
                ? floatval($o->valor_desconto)
                : floatval($o->total_produto) + floatval($o->total_servico);
        }

        if ($isXls) {
            $osFormatadas = array_map(function ($item) {
                $subTotal = floatval($item['total_servico']) + floatval($item['total_produto']);
                $total = floatval($item['valor_desconto']) ?: floatval($item['total_servico']) + floatval($item['total_produto']);

                return [
                    'idOs' => $item['idOs'],
                    'nomeCliente' => $item['nomeCliente'],
                    'status' => $item['status'],
                    'dataFinal' => $item['dataInicial'],
                    'descricaoProduto' => $item['descricaoProduto'],
                    'total_produto' => $item['total_produto'] ? $item['total_produto'] : 0,
                    'total_servico' => $item['total_servico'] ? $item['total_servico'] : 0,
                    'valorSubTotal' => $subTotal ? $subTotal : 0,
                    'valorTotal' => $total ? $total : 0,
                    'total_geral_desconto' => $item['desconto'] ?: 0,
                    'tipo_desconto' => $item['tipo_desconto'] ?: '-',
                ];
            }, $os);

            $cabecalho = [
                'ID OS' => 'integer',
                'Cliente' => 'string',
                'Status' => 'string',
                'Data' => 'YYYY-MM-DD',
                'Descrição' => 'string',
                'Total Produtos' => 'price',
                'Total Serviços' => 'price',
                'Total' => 'price',
                'Total Com Desconto' => 'price',
                'Desconto' => 'number',
                'Tipo Desconto' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($osFormatadas as $os) {
                $writer->writeSheetRow('Sheet1', $os);
            }
            $writer->writeSheetRow('Sheet1', []);
            $writer->writeSheetRow('Sheet1', [
                null,
                null,
                null,
                null,
                null,
                $totalProdutos,
                $totalServicos,
                $totalProdutos + $totalServicos,
                $totalValorDesconto + $valorTotal,
            ]);

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_os.xlsx', $arquivo);

            return;
        }

        $data['os'] = $os;
        $data['total_produtos'] = $totalProdutos;
        $data['total_servicos'] = $totalServicos;
        $data['total_geral_desconto'] = $totalDesconto;
        $data['total_geral'] = $totalValorDesconto + $valorTotal;
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de OS';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirOs', $data, true);
        pdf_create($html, 'relatorio_os' . date('d/m/y'), true, true);
    }

    public function osCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rOs')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de OS.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial');
        $dataFinal = $this->input->get('dataFinal');
        $cliente = $this->input->get('cliente');
        $responsavel = $this->input->get('responsavel');
        $status = $this->input->get('status');
        $format = $this->input->get('format');

        $isXls = $format === 'xls';
        $os = $this->Relatorios_model->osCustom($dataInicial, $dataFinal, $cliente, $responsavel, $status, $isXls);
        $totalProdutos = 0;
        $totalServicos = 0;
        $totalDesconto = 0;
        $totalValorDesconto = 0;
        $valorTotal = 0;
        foreach ($os as $o) {
            $totalProdutos += $isXls
                ? floatval($o['total_produto'])
                : floatval($o->total_produto);
            $totalServicos += $isXls
                ? floatval($o['total_servico'])
                : floatval($o->total_servico);
            $totalDesconto += $isXls
                ? floatval($o['desconto'])
                : floatval($o->desconto);
            $isXls
                ?
                $totalValorDesconto += $o['valor_desconto'] != 0
                ? floatval($o['valor_desconto'])
                : floatval($o['total_servico']) + floatval($o['total_produto'])
                :
                $totalValorDesconto += $o->valor_desconto != 0
                ? floatval($o->valor_desconto)
                : floatval($o->total_produto) + floatval($o->total_servico);
        }

        if ($isXls) {
            $osFormatadas = array_map(function ($item) {
                $subTotal = floatval($item['total_servico']) + floatval($item['total_produto']);
                $total = floatval($item['valor_desconto']) ?: floatval($item['total_servico']) + floatval($item['total_produto']);

                return [
                    'idOs' => $item['idOs'],
                    'nomeCliente' => $item['nomeCliente'],
                    'status' => $item['status'],
                    'dataFinal' => $item['dataInicial'],
                    'descricaoProduto' => $item['descricaoProduto'],
                    'total_produto' => $item['total_produto'] ? $item['total_produto'] : 0,
                    'total_servico' => $item['total_servico'] ? $item['total_servico'] : 0,
                    'valorSubTotal' => $subTotal ? $subTotal : 0,
                    'valorTotal' => $total ? $total : 0,
                    'total_geral_desconto' => $item['desconto'] ?: 0,
                    'tipo_desconto' => $item['tipo_desconto'] ?: '-',
                ];
            }, $os);

            $cabecalho = [
                'ID OS' => 'integer',
                'Cliente' => 'string',
                'Status' => 'string',
                'Data' => 'YYYY-MM-DD',
                'Descrição' => 'string',
                'Total Produtos' => 'price',
                'Total Serviços' => 'price',
                'Total' => 'price',
                'Total Com Desconto' => 'price',
                'Desconto' => 'number',
                'Tipo Desconto' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($osFormatadas as $os) {
                $writer->writeSheetRow('Sheet1', $os);
            }
            $writer->writeSheetRow('Sheet1', []);
            $writer->writeSheetRow('Sheet1', [
                null,
                null,
                null,
                null,
                null,
                $totalProdutos,
                $totalServicos,
                $totalProdutos + $totalServicos,
                $totalValorDesconto + $valorTotal,
            ]);

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_os_custom.xlsx', $arquivo);

            return;
        }

        $this->load->helper('mpdf');

        $title = $status == null ? 'Todas' : $status;
        $user = $responsavel == null ? 'Não foi selecionado' : $this->Usuarios_model->get(1, intval($responsavel) - 1);

        $emitente = $this->Mapos_model->getEmitente();
        $usuario = is_array($user) ? $user[0]->nome : $user;

        $data['title'] = 'Relatório de OS - ' . $title;
        $data['os'] = $os;
        $data['total_produtos'] = $totalProdutos;
        $data['total_servicos'] = $totalServicos;
        $data['total_geral_desconto'] = $totalDesconto;
        $data['total_geral'] = $totalValorDesconto + $valorTotal;
        $data['res_nome'] = $usuario;

        $data['dataInicial'] = $dataInicial != null ? date('d-m-Y', strtotime($dataInicial)) : 'indefinida';
        $data['dataFinal'] = $dataFinal != null ? date('d-m-Y', strtotime($dataFinal)) : 'indefinida';
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $html = $this->load->view('relatorios/imprimir/imprimirOs', $data, true);
        pdf_create($html, 'relatorio_os' . date('d/m/y'), true, true);
    }

    public function financeiro()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios financeiros.');
            redirect(base_url());
        }

        $this->data['view'] = 'relatorios/rel_financeiro';

        return $this->layout();
    }

    public function financeiroRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios financeiros.');
            redirect(base_url());
        }

        $format = $this->input->get('format');

        if ($format == 'xls') {
            $lancamentos = $this->Relatorios_model->financeiroRapid(true);

            $lancamentosFormatados = array_map(function ($item) {
                return [
                    'idLancamentos' => $item['idLancamentos'],
                    'descricao' => $item['descricao'],
                    'valor' => $item['valor'],
                    'desconto' => $item['desconto'],
                    'valor_desconto' => $item['valor_desconto'],
                    'tipo_desconto' => $item['tipo_desconto'],
                    'data_vencimento' => $item['data_vencimento'],
                    'data_pagamento' => $item['data_pagamento'],
                    'baixado' => $item['baixado'],
                    'cliente_fornecedor' => $item['cliente_fornecedor'],
                    'forma_pgto' => $item['forma_pgto'],
                    'tipo' => $item['tipo'],
                ];
            }, $lancamentos);

            $cabecalho = [
                'ID Lançamentos' => 'integer',
                'Descricao' => 'string',
                'Valor' => 'price',
                'Desconto' => 'price',
                'Tipo Desconto' => 'string',
                'Valor Com Desc.' => 'price',
                'Data Vencimento' => 'YYYY-MM-DD',
                'Data Pagamento' => 'YYYY-MM-DD',
                'Baixado' => 'integer',
                'Cliente/Fornecedor' => 'string',
                'Forma Pagamento' => 'string',
                'Tipo' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($lancamentosFormatados as $lancamento) {
                $writer->writeSheetRow('Sheet1', $lancamento);
            }

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_financeiro.xlsx', $arquivo);

            return;
        }

        $data['lancamentos'] = $this->Relatorios_model->financeiroRapid();
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório Financeiro';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirFinanceiro', $data, true);
        pdf_create($html, 'relatorio_os' . date('d/m/y'), true);
    }

    public function financeiroCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios financeiros.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial');
        $dataFinal = $this->input->get('dataFinal');
        $tipo = $this->input->get('tipo');
        $situacao = $this->input->get('situacao');
        $format = $this->input->get('format');

        if ($format == 'xls') {
            $lancamentos = $this->Relatorios_model->financeiroCustom($dataInicial, $dataFinal, $tipo, $situacao, true);

            $lancamentosFormatados = array_map(function ($item) {
                return [
                    'idLancamentos' => $item['idLancamentos'],
                    'descricao' => $item['descricao'],
                    'valor' => $item['valor'],
                    'desconto' => $item['desconto'],
                    'valor_desconto' => $item['valor_desconto'],
                    'tipo_desconto' => $item['tipo_desconto'],
                    'data_vencimento' => $item['data_vencimento'],
                    'data_pagamento' => $item['data_pagamento'],
                    'baixado' => $item['baixado'],
                    'cliente_fornecedor' => $item['cliente_fornecedor'],
                    'forma_pgto' => $item['forma_pgto'],
                    'tipo' => $item['tipo'],
                ];
            }, $lancamentos);

            $cabecalho = [
                'ID Lançamentos' => 'integer',
                'Descricao' => 'string',
                'Valor' => 'price',
                'Desconto' => 'price',
                'Tipo Desconto' => 'string',
                'Valor Com Desc.' => 'price',
                'Data Vencimento' => 'YYYY-MM-DD',
                'Data Pagamento' => 'YYYY-MM-DD',
                'Baixado' => 'integer',
                'Cliente/Fornecedor' => 'string',
                'Forma Pagamento' => 'string',
                'Tipo' => 'string',
            ];

            $writer = new XLSXWriter();

            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($lancamentosFormatados as $lancamento) {
                $writer->writeSheetRow('Sheet1', $lancamento);
            }

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_financeiro_custom.xlsx', $arquivo);

            return;
        }

        $data['lancamentos'] = $this->Relatorios_model->financeiroCustom($dataInicial, $dataFinal, $tipo, $situacao);
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório Financeiro Customizado';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirFinanceiro', $data, true);
        pdf_create($html, 'relatorio_financeiro' . date('d/m/y'), true);
    }

    public function vendas()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de vendas.');
            redirect(base_url());
        }

        $this->data['view'] = 'relatorios/rel_vendas';

        return $this->layout();
    }

    public function vendasRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de vendas.');
            redirect(base_url());
        }

        $format = $this->input->get('format');
        $isXls = $format === 'xls';
        $vendas = $this->Relatorios_model->vendasRapid($isXls);
        $totalVendas = 0;
        $totalDesconto = 0;
        $totalValorDesconto = 0;
        foreach ($vendas as $venda) {
            $totalVendas += $isXls
                ? floatval($venda['valorTotal'])
                : floatval($venda->valorTotal);
            $totalDesconto += $isXls
                ? floatval($venda['desconto'])
                : floatval($venda->desconto);

            $isXls
                ?
                $totalValorDesconto += $venda['valor_desconto'] != 0 ? floatval($venda['valor_desconto']) : floatval($venda['valorTotal'])
                :
                $totalValorDesconto += $venda->valor_desconto != 0 ? floatval($venda->valor_desconto) : floatval($venda->valorTotal);
        }

        if ($format == 'xls') {
            $vendasFormatadas = array_map(function ($item) {
                return [
                    '#' => $item['idVendas'],
                    'cliente' => $item['nomeCliente'],
                    'vendedor' => $item['nome'],
                    'data' => $item['dataVenda'],
                    'total' => $item['valorTotal'] ?: 0,
                    'totalDesconto' => $item['valor_desconto'] ?: 0,
                    'desconto' => $item['desconto'] ?: 0,
                    'tipo_desconto' => $item['tipo_desconto'] ?: '-',
                ];
            }, $vendas);

            $cabecalho = [
                '#' => 'string',
                'Cliente' => 'string',
                'Vendedor' => 'string',
                'Data' => 'DD-MM-YYYY',
                'Total' => 'price',
                'Total Com Desconto' => 'price',
                'Desconto' => 'number',
                'Tipo Desconto' => 'string',
            ];

            $writer = new XLSXWriter();
            $writer->writeSheetRow(null, []);
            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($vendasFormatadas as $venda) {
                $writer->writeSheetRow('Sheet1', $venda);
            }
            $writer->writeSheetRow(null, []);
            $writer->writeSheetRow(null, []);
            $writer->writeSheetRow(null, []);
            $writer->writeSheetRow('Sheet1', []);
            $writer->writeSheetRow('Sheet1', [
                null,
                null,
                null,
                null,
                $totalVendas,
                $totalValorDesconto,
            ]);

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_vendas.xlsx', $arquivo);

            return;
        }

        $data['vendas'] = $vendas;
        $data['total_vendas'] = $totalVendas;
        $data['total_geral_desconto'] = $totalDesconto;
        $data['total_geral'] = $totalValorDesconto;
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Vendas Rápido';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirVendas', $data, true);
        pdf_create($html, 'relatorio_vendas' . date('d/m/y'), true);
    }

    public function vendasCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rVenda')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios de vendas.');
            redirect(base_url());
        }
        $dataInicial = $this->input->get('dataInicial');
        $dataFinal = $this->input->get('dataFinal');
        $cliente = $this->input->get('cliente');
        $responsavel = $this->input->get('responsavel');
        $format = $this->input->get('format');

        $isXls = $format === 'xls';
        $vendas = $this->Relatorios_model->vendasCustom($dataInicial, $dataFinal, $cliente, $responsavel, $isXls);
        $totalVendas = 0;
        $totalDesconto = 0;
        $totalValorDesconto = 0;
        foreach ($vendas as $venda) {
            $totalVendas += $isXls
                ? floatval($venda['valorTotal'])
                : floatval($venda->valorTotal);
            $totalDesconto += $isXls
                ? floatval($venda['desconto'])
                : floatval($venda->desconto);

            $isXls
                ?
                $totalValorDesconto += $venda['valor_desconto'] != 0 ? floatval($venda['valor_desconto']) : floatval($venda['valorTotal'])
                :
                $totalValorDesconto += $venda->valor_desconto != 0 ? floatval($venda->valor_desconto) : floatval($venda->valorTotal);
        }

        if ($format == 'xls') {
            $vendasFormatadas = array_map(function ($item) {
                return [
                    '#' => $item['idVendas'],
                    'cliente' => $item['nomeCliente'],
                    'vendedor' => $item['nome'],
                    'data' => $item['dataVenda'],
                    'total' => $item['valorTotal'] ?: 0,
                    'totalDesconto' => $item['valor_desconto'] ?: 0,
                    'desconto' => $item['desconto'] ?: 0,
                    'tipo_desconto' => $item['tipo_desconto'] ?: '-',
                ];
            }, $vendas);

            $cabecalho = [
                '#' => 'string',
                'Cliente' => 'string',
                'Vendedor' => 'string',
                'Data' => 'DD-MM-YYYY',
                'Total' => 'price',
                'Total Com Desconto' => 'price',
                'Desconto' => 'number',
            ];

            $writer = new XLSXWriter();
            $writer->writeSheetHeader('Sheet1', $cabecalho);
            foreach ($vendasFormatadas as $venda) {
                $writer->writeSheetRow('Sheet1', $venda);
            }
            $writer->writeSheetRow(null, []);
            $writer->writeSheetRow(null, []);
            $writer->writeSheetRow(null, []);
            $writer->writeSheetRow('Sheet1', []);
            $writer->writeSheetRow('Sheet1', [
                null,
                null,
                null,
                null,
                $totalVendas,
                $totalValorDesconto,
            ]);

            $arquivo = $writer->writeToString();
            $this->load->helper('download');
            force_download('relatorio_vendas_custom.xlsx', $arquivo);

            return;
        }

        $data['vendas'] = $vendas;
        $data['total_vendas'] = $totalVendas;
        $data['emitente'] = $this->Mapos_model->getEmitente();
        $data['title'] = 'Relatório de Vendas Customizado';
        $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

        $this->load->helper('mpdf');
        $html = $this->load->view('relatorios/imprimir/imprimirVendas', $data, true);
        pdf_create($html, 'relatorio_vendas' . date('d/m/y'), true);
    }

    public function receitasBrutasMei()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios financeiros.');
            redirect(base_url());
        }

        $this->data['view'] = 'relatorios/rel_receitas_brutas_mei';

        return $this->layout();
    }

    public function receitasBrutasRapid()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios financeiros.');
            redirect(base_url());
        }

        $this->load->helper('download');
        $this->load->helper('file');

        $format = $this->input->get('format') ?: 'docx';

        $templatePath = realpath(FCPATH . 'assets/relatorios/RELATORIO_MENSAL_DAS_RECEITAS_BRUTAS_MEI.docx');
        if (! $templatePath) {
            $this->session->set_flashdata('error', 'Modelo de relatório não encontrado!');

            return redirect('/relatorios/receitasBrutasMei');
        }

        $tempFilePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'relatorios' . DIRECTORY_SEPARATOR . 'temp.docx';
        $generatedFilePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'relatorios' . DIRECTORY_SEPARATOR . "RELATORIO_MENSAL_DAS_RECEITAS_BRUTAS_MEI_GERADO.$format";

        $templateProcessor = new TemplateProcessor($templatePath);
        $data = $this->Relatorios_model->receitasBrutasRapid();
        $templateProcessor->setValues($data);

        if ($format === 'docx') {
            $templateProcessor->saveAs($generatedFilePath);

            $fileContents = file_get_contents($generatedFilePath);
            unlink($generatedFilePath);

            return force_download("relatorio_receitas_brutas_mei_rapido.$format", $fileContents);
        } else {
            Settings::setPdfRendererName(Settings::PDF_RENDERER_MPDF);
            Settings::setPdfRendererPath('.');

            $templateProcessor->saveAs($tempFilePath);
            $template = IOFactory::load($tempFilePath);
            $pdfWriter = IOFactory::createWriter($template, 'PDF');
            $pdfWriter->save($generatedFilePath);

            $fileContents = file_get_contents($generatedFilePath);
            unlink($tempFilePath);
            unlink($generatedFilePath);

            return $this->output
                ->set_header('Content-disposition: inline;filename=' . "relatorio_receitas_brutas_mei_rapido.$format")
                ->set_content_type(get_mime_by_extension($generatedFilePath))
                ->set_status_header(200)
                ->set_output($fileContents)
                ->_display();
        }
    }

    public function receitasBrutasCustom()
    {
        if (! $this->permission->checkPermission($this->session->userdata('permissao'), 'rFinanceiro')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para gerar relatórios financeiros.');
            redirect(base_url());
        }

        $this->load->helper('download');
        $this->load->helper('file');

        $format = $this->input->get('format') ?: 'docx';
        $dataInicial = $this->input->get('dataInicial');
        $dataFinal = $this->input->get('dataFinal');

        $templatePath = realpath(FCPATH . 'assets/relatorios/RELATORIO_MENSAL_DAS_RECEITAS_BRUTAS_MEI.docx');
        if (! $templatePath) {
            $this->session->set_flashdata('error', 'Modelo de relatório não encontrado!');

            return redirect('/relatorios/receitasBrutasMei');
        }

        $tempFilePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'relatorios' . DIRECTORY_SEPARATOR . 'temp.docx';
        $generatedFilePath = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'relatorios' . DIRECTORY_SEPARATOR . "RELATORIO_MENSAL_DAS_RECEITAS_BRUTAS_MEI_GERADO.$format";

        $templateProcessor = new TemplateProcessor($templatePath);
        $data = $this->Relatorios_model->receitasBrutasCustom($dataInicial, $dataFinal);
        $templateProcessor->setValues($data);

        if ($format === 'docx') {
            $templateProcessor->saveAs($generatedFilePath);

            $fileContents = file_get_contents($generatedFilePath);
            unlink($generatedFilePath);

            return force_download(
                sprintf(
                    "relatorio_receitas_brutas_mei_custom_%s_até_%s.$format",
                    $dataInicial,
                    $dataFinal
                ),
                $fileContents
            );
        } else {
            Settings::setPdfRendererName(Settings::PDF_RENDERER_MPDF);
            Settings::setPdfRendererPath('.');

            $templateProcessor->saveAs($tempFilePath);
            $template = IOFactory::load($tempFilePath);
            $pdfWriter = IOFactory::createWriter($template, 'PDF');
            $pdfWriter->save($generatedFilePath);

            $fileContents = file_get_contents($generatedFilePath);
            unlink($tempFilePath);
            unlink($generatedFilePath);

            return $this->output
                ->set_header('Content-disposition: inline;filename=' . "relatorio_receitas_brutas_mei_custom_%s_até_%s.$format")
                ->set_content_type(get_mime_by_extension($generatedFilePath))
                ->set_status_header(200)
                ->set_output($fileContents)
                ->_display();
        }
    }

    public function nfe_emitidas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para visualizar relatórios de NF-e.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial') ?: date('Y-m-01');
        $dataFinal = $this->input->get('dataFinal') ?: date('Y-m-d');
        $status = $this->input->get('status');
        $modelo = $this->input->get('modelo');
        $format = $this->input->get('format');

        $this->load->model('nfe_model');
        $this->load->library('pagination');
        
        // Primeiro, vamos buscar todos os registros para calcular os totais
        $this->db->select('nfe_emitidas.*, clientes.nomeCliente, nfe_emitidas.xml');
        $this->db->from('nfe_emitidas');
        $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        $this->db->where('nfe_emitidas.created_at >=', $dataInicial . ' 00:00:00');
        $this->db->where('nfe_emitidas.created_at <=', $dataFinal . ' 23:59:59');
        
        if ($status) {
            $this->db->where('nfe_emitidas.status', $status);
        }
        
        if ($modelo) {
            $this->db->where('nfe_emitidas.modelo', $modelo);
        }
        
        $all_nfe = $this->db->get()->result();

        // Calcula os totais de todos os registros
        $total = 0;
        $total_devolucao = 0;
        $total_cancelamento = 0;
        $total_liquido = 0;

        foreach ($all_nfe as $n) {
            $valor = floatval($n->valor_total);
            $total += $valor;

            // Verifica se é uma devolução (finNFe = 4)
            $xml = simplexml_load_string($n->xml);
            
            if ($xml) {
                // Verifica finNFe na estrutura correta
                if (isset($xml->infNFe->ide->finNFe)) {
                    $finNFe = (string)$xml->infNFe->ide->finNFe;
                    if ($finNFe == '4') {
                        $total_devolucao += $valor;
                    }
                }
            }

            // Se for um cancelamento (status 2)
            if ($n->status == 2) {
                $total_cancelamento += $valor;
            }
        }

        // Total líquido = Total - Devoluções - Cancelamentos
        $total_liquido = $total - $total_devolucao - $total_cancelamento;

        // Se for exportação para Excel ou PDF
        if ($format == 'xls' || $format == 'pdf') {
            $data['nfe'] = $all_nfe;
            $data['total'] = $total;
            $data['total_devolucao'] = $total_devolucao;
            $data['total_cancelamento'] = $total_cancelamento;
            $data['total_liquido'] = $total_liquido;
            $data['dataInicial'] = $dataInicial;
            $data['dataFinal'] = $dataFinal;
            $data['emitente'] = $this->Mapos_model->getEmitente();
            $data['title'] = 'Relatório de NF-e Emitidas';
            $data['topo'] = $this->load->view('relatorios/imprimir/imprimirTopo', $data, true);

            if ($format == 'xls') {
                $nfeFormatadas = array_map(function ($item) {
                    return [
                        'id' => $item->id,
                        'data_emissao' => date('d/m/Y H:i', strtotime($item->created_at)),
                        'numero_nfe' => $item->numero_nfe,
                        'chave_nfe' => $item->chave_nfe,
                        'cliente' => $item->nomeCliente,
                        'valor_total' => number_format($item->valor_total, 2, ',', '.'),
                        'modelo' => $item->modelo == 65 ? 'NFC-e' : 'NFe',
                        'status' => $item->status == 1 ? 'Autorizada' : ($item->status == 2 ? 'Cancelada' : 'Rejeitada'),
                        'protocolo' => $item->protocolo,
                        'retorno' => $item->chave_retorno_evento
                    ];
                }, $all_nfe);

                $cabecalho = [
                    'ID' => 'integer',
                    'Data Emissão' => 'string',
                    'Número NFe' => 'string',
                    'Chave NFe' => 'string',
                    'Cliente' => 'string',
                    'Valor Total' => 'price',
                    'Modelo' => 'string',
                    'Status' => 'string',
                    'Protocolo' => 'string',
                    'Retorno' => 'string'
                ];

                $writer = new XLSXWriter();
                $writer->writeSheetHeader('Sheet1', $cabecalho);
                foreach ($nfeFormatadas as $nfe) {
                    $writer->writeSheetRow('Sheet1', $nfe);
                }

                // Adiciona linhas em branco
                $writer->writeSheetRow('Sheet1', []);
                $writer->writeSheetRow('Sheet1', []);

                // Adiciona o resumo
                $writer->writeSheetRow('Sheet1', ['', '', '', '', 'Total NFe', number_format($total, 2, ',', '.')]);
                $writer->writeSheetRow('Sheet1', ['', '', '', '', 'Total Devolução', number_format($total_devolucao, 2, ',', '.')]);
                $writer->writeSheetRow('Sheet1', ['', '', '', '', 'Total Cancelamento', number_format($total_cancelamento, 2, ',', '.')]);
                $writer->writeSheetRow('Sheet1', ['', '', '', '', 'Total Líquido', number_format($total_liquido, 2, ',', '.')]);

                $arquivo = $writer->writeToString();
                $this->load->helper('download');
                force_download('relatorio_nfe_emitidas.xlsx', $arquivo);
                return;
            }
            // PDF
            $this->load->helper('mpdf');
            $html = $this->load->view('relatorios/imprimir/imprimirNfe', $data, true);
            pdf_create($html, 'relatorio_nfe_emitidas_' . date('d/m/y'), true);
            return;
        }
        
        // Configuração da paginação
        $config['base_url'] = site_url('relatorios/nfe_emitidas/');
        $config['total_rows'] = count($all_nfe);
        $config['per_page'] = 60;
        $config['page_query_string'] = true;
        $config['query_string_segment'] = 'per_page';
        $config['reuse_query_string'] = true;
        $config['num_links'] = 5;
        $config['first_link'] = 'Primeira';
        $config['last_link'] = 'Última';
        $config['next_link'] = 'Próxima';
        $config['prev_link'] = 'Anterior';
        $config['full_tag_open'] = '<div class="pagination alternate"><ul>';
        $config['full_tag_close'] = '</ul></div>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li><a style="color: #2D335B"><b>';
        $config['cur_tag_close'] = '</b></a></li>';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        
        $this->pagination->initialize($config);
        
        // Agora busca apenas os registros da página atual
        $this->db->select('nfe_emitidas.*, clientes.nomeCliente, nfe_emitidas.xml');
        $this->db->from('nfe_emitidas');
        $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        $this->db->where('nfe_emitidas.created_at >=', $dataInicial . ' 00:00:00');
        $this->db->where('nfe_emitidas.created_at <=', $dataFinal . ' 23:59:59');
        
        if ($status) {
            $this->db->where('nfe_emitidas.status', $status);
        }
        
        if ($modelo) {
            $this->db->where('nfe_emitidas.modelo', $modelo);
        }
        
        $this->db->order_by('nfe_emitidas.id', 'desc');
        $this->db->limit($config['per_page'], $this->input->get('per_page') ?: 0);
        $nfe = $this->db->get()->result();

        $this->data['nfe'] = $nfe;
        $this->data['total'] = $total;
        $this->data['total_devolucao'] = $total_devolucao;
        $this->data['total_cancelamento'] = $total_cancelamento;
        $this->data['total_liquido'] = $total_liquido;
        $this->data['dataInicial'] = $dataInicial;
        $this->data['dataFinal'] = $dataFinal;
        $this->data['status'] = $status;
        $this->data['modelo'] = $modelo;
        $this->data['pagination'] = $this->pagination->create_links();
        $this->data['view'] = 'relatorios/nfe_emitidas';
        return $this->layout();
    }

    public function exportarNfeEmitidas()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para exportar relatórios de NF-e.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial') ?: date('Y-m-01');
        $dataFinal = $this->input->get('dataFinal') ?: date('Y-m-d');
        $status = $this->input->get('status');
        $modelo = $this->input->get('modelo');

        $this->load->model('nfe_model');
        
        $this->db->select('nfe_emitidas.*, clientes.nomeCliente, nfe_emitidas.xml');
        $this->db->from('nfe_emitidas');
        $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        $this->db->where('nfe_emitidas.created_at >=', $dataInicial . ' 00:00:00');
        $this->db->where('nfe_emitidas.created_at <=', $dataFinal . ' 23:59:59');
        
        if ($status) {
            $this->db->where('nfe_emitidas.status', $status);
        }
        
        if ($modelo) {
            $this->db->where('nfe_emitidas.modelo', $modelo);
        }
        
        $this->db->order_by('nfe_emitidas.id', 'desc');
        $nfe = $this->db->get()->result();

        // Calcula os totais
        $total = 0;
        $total_devolucao = 0;
        $total_cancelamento = 0;
        $total_liquido = 0;

        foreach ($nfe as $n) {
            $valor = floatval($n->valor_total);
            $total += $valor;

            // Verifica se é uma devolução (finNFe = 4)
            $xml = simplexml_load_string($n->xml);
            
            if ($xml) {
                // Verifica finNFe na estrutura correta
                if (isset($xml->infNFe->ide->finNFe)) {
                    $finNFe = (string)$xml->infNFe->ide->finNFe;
                    if ($finNFe == '4') {
                        $total_devolucao += $valor;
                    }
                }
            }

            // Se for um cancelamento (status 2)
            if ($n->status == 2) {
                $total_cancelamento += $valor;
            }
        }

        // Total líquido = Total - Devoluções - Cancelamentos
        $total_liquido = $total - $total_devolucao - $total_cancelamento;

        // Cria o arquivo Excel
        $this->load->library('excel');
        
        $this->excel->setActiveSheetIndex(0);
        $this->excel->getActiveSheet()->setTitle('NF-e Emitidas');
        
        // Cabeçalho
        $this->excel->getActiveSheet()->setCellValue('A1', 'ID');
        $this->excel->getActiveSheet()->setCellValue('B1', 'Data Emissão');
        $this->excel->getActiveSheet()->setCellValue('C1', 'Número NFe');
        $this->excel->getActiveSheet()->setCellValue('D1', 'Chave NFe');
        $this->excel->getActiveSheet()->setCellValue('E1', 'Cliente');
        $this->excel->getActiveSheet()->setCellValue('F1', 'Valor Total');
        $this->excel->getActiveSheet()->setCellValue('G1', 'Modelo');
        $this->excel->getActiveSheet()->setCellValue('H1', 'Status');
        $this->excel->getActiveSheet()->setCellValue('I1', 'Protocolo');
        $this->excel->getActiveSheet()->setCellValue('J1', 'Retorno');

        // Dados
        $row = 2;
        foreach ($nfe as $n) {
            $this->excel->getActiveSheet()->setCellValue('A' . $row, $n->id);
            $this->excel->getActiveSheet()->setCellValue('B' . $row, date('d/m/Y H:i', strtotime($n->created_at)));
            $this->excel->getActiveSheet()->setCellValue('C' . $row, $n->numero_nfe);
            $this->excel->getActiveSheet()->setCellValue('D' . $row, $n->chave_nfe);
            $this->excel->getActiveSheet()->setCellValue('E' . $row, $n->nomeCliente);
            $this->excel->getActiveSheet()->setCellValue('F' . $row, number_format($n->valor_total, 2, ',', '.'));
            $this->excel->getActiveSheet()->setCellValue('G' . $row, $n->modelo == 65 ? 'NFC-e' : 'NFe');
            $this->excel->getActiveSheet()->setCellValue('H' . $row, $n->status == 1 ? 'Autorizada' : ($n->status == 2 ? 'Cancelada' : 'Rejeitada'));
            $this->excel->getActiveSheet()->setCellValue('I' . $row, $n->protocolo);
            $this->excel->getActiveSheet()->setCellValue('J' . $row, $n->chave_retorno_evento);
            $row++;
        }

        // Resumo
        $row += 2;
        $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Total NFe');
        $this->excel->getActiveSheet()->setCellValue('F' . $row, number_format($total, 2, ',', '.'));
        $row++;
        $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Total Devolução');
        $this->excel->getActiveSheet()->setCellValue('F' . $row, number_format($total_devolucao, 2, ',', '.'));
        $row++;
        $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Total Cancelamento');
        $this->excel->getActiveSheet()->setCellValue('F' . $row, number_format($total_cancelamento, 2, ',', '.'));
        $row++;
        $this->excel->getActiveSheet()->setCellValue('E' . $row, 'Total Líquido');
        $this->excel->getActiveSheet()->setCellValue('F' . $row, number_format($total_liquido, 2, ',', '.'));

        // Formatação
        $this->excel->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('A1:J' . ($row - 5))->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('A1:J1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
        
        // Formatação do resumo
        $this->excel->getActiveSheet()->getStyle('E' . ($row - 4) . ':F' . $row)->getFont()->setBold(true);
        $this->excel->getActiveSheet()->getStyle('E' . ($row - 4) . ':F' . $row)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $this->excel->getActiveSheet()->getStyle('E' . ($row - 4) . ':E' . $row)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('CCCCCC');
        
        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $this->excel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
        }

        // Gera o arquivo
        $filename = 'relatorio_nfe_emitidas_' . date('Y-m-d_H-i-s') . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }

    public function exportarNfeXml()
    {
        if (!$this->permission->checkPermission($this->session->userdata('permissao'), 'vNfe')) {
            $this->session->set_flashdata('error', 'Você não tem permissão para exportar XMLs de NF-e.');
            redirect(base_url());
        }

        $dataInicial = $this->input->get('dataInicial') ?: date('Y-m-01');
        $dataFinal = $this->input->get('dataFinal') ?: date('Y-m-d');
        $status = $this->input->get('status');
        $modelo = $this->input->get('modelo');

        $this->load->model('nfe_model');
        
        // Busca as NFes no período
        $this->db->select('nfe_emitidas.*, clientes.nomeCliente');
        $this->db->from('nfe_emitidas');
        $this->db->join('vendas', 'vendas.idVendas = nfe_emitidas.venda_id', 'left');
        $this->db->join('clientes', 'clientes.idClientes = vendas.clientes_id', 'left');
        $this->db->where('nfe_emitidas.created_at >=', $dataInicial . ' 00:00:00');
        $this->db->where('nfe_emitidas.created_at <=', $dataFinal . ' 23:59:59');
        $this->db->where('nfe_emitidas.status !=', 3); // Exclui rejeitadas
        
        if ($status) {
            $this->db->where('nfe_emitidas.status', $status);
        }
        
        if ($modelo) {
            $this->db->where('nfe_emitidas.modelo', $modelo);
        }
        
        $this->db->order_by('nfe_emitidas.id', 'desc');
        $nfe = $this->db->get()->result();

        if (empty($nfe)) {
            $this->session->set_flashdata('error', 'Nenhuma NF-e encontrada para exportar.');
            redirect('relatorios/nfe_emitidas');
        }

        // Cria o diretório temporário se não existir
        $tempDir = FCPATH . 'assets' . DIRECTORY_SEPARATOR . 'temp';
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        // Cria um arquivo ZIP temporário
        $zip = new ZipArchive();
        $zipName = 'nfe_xml_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $zipName;

        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($nfe as $n) {
                if (!empty($n->xml)) {
                    // Gera um nome de arquivo baseado no número da NFe e chave
                    $fileName = sprintf(
                        'NFe%s_%s.xml',
                        $n->numero_nfe,
                        $n->chave_nfe
                    );
                    
                    // Remove a declaração XML do XML da nota se existir
                    $nfeXml = preg_replace('/<\?xml[^>]+\?>/', '', $n->xml);
                    
                    // Monta o XML completo
                    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
                    $xml .= '<nfeProc versao="4.00" xmlns="http://www.portalfiscal.inf.br/nfe">';
                    $xml .= '<NFe>';
                    $xml .= $nfeXml;
                    $xml .= '</NFe>';
                    
                    // Adiciona o protocolo se existir
                    if (!empty($n->xml_protocolo)) {
                        // Remove a declaração XML do protocolo se existir
                        $protXml = preg_replace('/<\?xml[^>]+\?>/', '', $n->xml_protocolo);
                        $xml .= $protXml;
                    }
                    
                    $xml .= '</nfeProc>';
                    
                    // Remove quebras de linha e espaços extras
                    $xml = preg_replace('/\s+/', ' ', $xml);
                    $xml = trim($xml);
                    
                    // Adiciona o XML completo ao ZIP
                    $zip->addFromString($fileName, $xml);
                }
            }
            $zip->close();

            // Força o download do arquivo
            $this->load->helper('download');
            $data = file_get_contents($zipPath);
            force_download($zipName, $data);

            // Remove o arquivo temporário
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }
        } else {
            $this->session->set_flashdata('error', 'Erro ao criar arquivo ZIP. Verifique as permissões do diretório: ' . $tempDir);
            redirect('relatorios/nfe_emitidas');
        }
    }
}
