<?php
/**
 * Exemplo de uso da classe NFComPreview
 * 
 * Este arquivo demonstra como usar a classe NFComPreview para gerar
 * o PDF da DANFE da NFCom
 */

require_once APPPATH . 'libraries/NFComPreview.php';

use App\NFComPreview;

// Configuração da empresa
$config = [
    'empresa' => [
        'razao_social' => 'EMPRESA DE TELECOMUNICAÇÕES LTDA',
        'cnpj' => '12.345.678/0001-90',
        'ie' => '123456789',
        'logo' => FCPATH . 'assets/img/logo.png', // Caminho para o logo
        'endereco' => [
            'logradouro' => 'Rua Exemplo',
            'numero' => '123',
            'complemento' => 'Sala 1',
            'bairro' => 'Centro',
            'municipio' => 'São Paulo',
            'uf' => 'SP',
            'cep' => '01234-567'
        ]
    ],
    'serie' => 1,
    'numero_inicial' => 1,
    'nSiteAutoriz' => 0,
    'classe' => '0101011', // Classe padrão para itens
    'pix_key' => '12345678000190', // Chave Pix (opcional)
    'pix_logo' => FCPATH . 'assets/img/pix-logo.png', // Logo Pix (opcional)
    'diretorios' => [
        'temp' => FCPATH . 'assets/temp'
    ]
];

// Dados da NFCom
$dados = [
    'numero' => 123,
    'destinatario' => [
        'nome' => 'CLIENTE EXEMPLO LTDA',
        'cnpj' => '98.765.432/0001-10',
        'ie' => '987654321',
        'endereco' => [
            'logradouro' => 'Av. Cliente',
            'numero' => '456',
            'bairro' => 'Jardim',
            'municipio' => 'Rio de Janeiro',
            'uf' => 'RJ',
            'cep' => '20000-000',
            'telefone' => '(21) 9999-8888'
        ]
    ],
    'assinante' => [
        'iCodAssinante' => 'ASS123456',
        'numero_contrato' => 'CONT-2024-001',
        'identificador_debito' => 'DEB-123456'
    ],
    'faturamento' => [
        'competencia' => '2024-01',
        'periodo_inicio' => '01/01/2024',
        'periodo_fim' => '31/01/2024',
        'vencimento' => '10/02/2024',
        'linha_digitavel' => '12345678901234567890123456789012345678901234567890',
        'cod_barras' => '12345678901234567890123456789012345678901234'
    ],
    'itens' => [
        [
            'descricao' => 'Plano de Internet 100MB',
            'cclass' => '0101011',
            'unidade' => 'UN',
            'quantidade' => 1,
            'valor_unitario' => 100.00,
            'valor_total' => 100.00,
            'desconto' => 0.00,
            'outros' => 0.00,
            'base_calculo' => 100.00,
            'aliquota_icms' => 18.00,
            'valor_icms' => 18.00,
            'pis' => [
                'valor' => 1.65
            ],
            'cofins' => [
                'valor' => 7.60
            ]
        ],
        [
            'descricao' => 'Assinatura Mensal',
            'cclass' => '0101011',
            'unidade' => 'UN',
            'quantidade' => 1,
            'valor_unitario' => 50.00,
            'valor_total' => 50.00,
            'desconto' => 0.00,
            'outros' => 0.00,
            'base_calculo' => 50.00,
            'aliquota_icms' => 18.00,
            'valor_icms' => 9.00,
            'pis' => [
                'valor' => 0.83
            ],
            'cofins' => [
                'valor' => 3.80
            ]
        ]
    ],
    'totais' => [
        'valor_total' => 150.00,
        'valor_base_calculo' => 150.00,
        'valor_produtos' => 150.00,
        'valor_icms' => 27.00,
        'valor_isento' => 0.00,
        'valor_outros' => 0.00,
        'valor_pis' => 2.48,
        'valor_cofins' => 11.40,
        'valor_fust' => 1.50,
        'valor_funtel' => 0.75
    ],
    'informacoes_adicionais' => 'Informações complementares da nota fiscal. Pagamento via boleto ou Pix.',
    'pix_payload' => '00020126580014br.gov.bcb.pix0136123e4567-e12b-12d1-a456-426655440000'
];

// Gerar PDF
try {
    $nfcomPreview = new NFComPreview($config);
    $resultado = $nfcomPreview->gerarPdf($dados);

    // Salvar PDF em arquivo
    $filename = 'nfcom_' . $resultado['numero'] . '.pdf';
    $filepath = FCPATH . 'assets/temp/' . $filename;
    file_put_contents($filepath, $resultado['pdf']);

    echo "PDF gerado com sucesso!\n";
    echo "Chave: " . $resultado['chave'] . "\n";
    echo "Número: " . $resultado['numero'] . "\n";
    echo "Arquivo: " . $filepath . "\n";

    // Ou enviar para o navegador
    // header('Content-Type: application/pdf');
    // header('Content-Disposition: inline; filename="' . $filename . '"');
    // echo $resultado['pdf'];

} catch (Exception $e) {
    echo "Erro ao gerar PDF: " . $e->getMessage() . "\n";
}
