<?php

namespace App;

use FPDF;
use Com\Tecnick\Barcode\Barcode;

class NFComPreview
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function gerarPdf(array $dados): array
    {
        $numero = $dados['numero'] ?? ($this->config['numero_inicial'] ?? 1);
        $serie = $this->config['serie'] ?? 1;
        $nSiteAutoriz = $dados['nSiteAutoriz'] ?? ($this->config['nSiteAutoriz'] ?? 0);
        $cUF = $this->getCodigoUF($this->config['empresa']['endereco']['uf']);
        $cnpj = preg_replace('/\D+/', '', $this->config['empresa']['cnpj']);
        $ano = date('y');
        $mes = date('m');

        // Gerar ou usar chave de acesso existente da NFCom
        $chave = $dados['chave'] ?? null;
        if (empty($chave)) {
            $chave = $this->buildChave(
                $cUF,
                $ano,
                $mes,
                $cnpj,
                '62',
                str_pad((string) $serie, 3, '0', STR_PAD_LEFT),
                str_pad((string) $numero, 9, '0', STR_PAD_LEFT),
                '1',
                $nSiteAutoriz
            );
        }

        $pdf = new LocalFpdf('P', 'mm', 'A4');
        $pdf->SetCompression(false);
        $pdf->SetMargins(8, 8, 8);
        $pdf->AddPage();

        $this->renderModeloSefaz($pdf, $dados, $numero, $serie, $chave);

        $output = $pdf->Output('S');

        // Debug temporário
        if (empty($output)) {
            error_log('PDF Output vazio! Páginas: ' . $pdf->PageNo());
            error_log('Buffer length: ' . strlen($pdf->buffer ?? ''));
        }

        return [
            'chave' => $chave,
            'numero' => $numero,
            'pdf' => $output
        ];
    }

    /**
     * Gera a chave de acesso da NFCom
     */
    private function buildChave(
        string $cUF,
        string $ano,
        string $mes,
        string $cnpj,
        string $mod,
        string $serie,
        string $numero,
        string $tpEmis,
        int $nSiteAutoriz
    ): string {
        // Código numérico aleatório de 8 dígitos
        $cNF = str_pad((string) rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        // Montar chave sem DV
        $chave = $cUF . $ano . $mes . $cnpj . $mod . $serie . $numero . $tpEmis . $cNF;

        // Calcular dígito verificador
        $dv = $this->calculateDV($chave);

        // Retornar chave completa
        return $chave . $dv;
    }

    /**
     * Calcula o dígito verificador da chave (módulo 11)
     */
    private function calculateDV(string $chave): int
    {
        $multiplicador = 2;
        $soma = 0;

        // Percorre a chave de trás para frente
        for ($i = strlen($chave) - 1; $i >= 0; $i--) {
            $soma += intval($chave[$i]) * $multiplicador;
            $multiplicador = ($multiplicador == 9) ? 2 : $multiplicador + 1;
        }

        $resto = $soma % 11;

        if ($resto == 0 || $resto == 1) {
            return 0;
        }

        return 11 - $resto;
    }

    private function renderModeloSefaz(Fpdf $pdf, array $dados, int $numero, int $serie, string $chave): void
    {
        $pageW = 210;
        $pageH = 297;
        $marginBottom = 8;

        $w = 200.7;
        $x = ($pageW - $w) / 2;
        $border = [170, 170, 170];
        $grayFill = [232, 232, 232];
        $tealFill = [78, 164, 188];

        // Desativa quebra automática de página
        $pdf->SetAutoPageBreak(false);

        // ===== CABEÇALHO =====
        $headerH = 35.27;
        $headerY = 2;
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $this->drawRoundedRect($pdf, $x, $headerY, $w, $headerH, 2.5, 'S');

        // Logo
        $logoX = $x;
        $logoY = $headerY;
        $logoW = 47.42;
        $logoH = $headerH;
        $logoPath = $this->config['empresa']['logo'] ?? null;

        if ($logoPath && file_exists($logoPath)) {
            $pdf->Image($logoPath, $logoX + 2, $logoY + 6, $logoW - 4, $logoH - 8);
        } else {
            $pdf->SetFont('helvetica', 'B', 7);
            $pdf->SetXY($logoX + 2, $logoY + 8);
            $pdf->MultiCell($logoW, 4, $this->safeText('LOGO' . PHP_EOL . 'EMPRESA' . PHP_EOL . 'OU' . PHP_EOL . 'NFCOM'), 0, 'C');
        }

        // Dados do Emitente
        $emitX = $x + $logoW + 6;
        $pdf->SetXY($emitX, $headerY + 4);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($w - $logoW - 10, 4, $this->safeText('DOCUMENTO AUXILIAR DA NOTA FISCAL FATURA DE SERVICOS DE COMUNICACAO ELETRONICA'), 0, 1, 'L');

        $pdf->SetXY($emitX, $headerY + 9);
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell(0, 9, $this->safeText($this->config['empresa']['razao_social']), 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 7.5);
        $end = $this->config['empresa']['endereco'];
        $linha = sprintf(
            '%s %s %s %s - %s %s %s',
            $end['logradouro'],
            $end['numero'],
            $end['complemento'] ?? '',
            $end['bairro'],
            $end['municipio'],
            $end['uf'],
            $end['cep']
        );
        $pdf->SetX($emitX);
        $pdf->Cell(0, 4, $this->safeText(trim(preg_replace('/\s+/', ' ', $linha))), 0, 1, 'L');

        $pdf->SetX($emitX);
        $pdf->Cell(0, 4, $this->safeText('CNPJ: ' . $this->formatCnpjCpf($this->config['empresa']['cnpj'])), 0, 1, 'L');

        $pdf->SetX($emitX);
        $pdf->Cell(0, 4, $this->safeText('INSCRICAO ESTADUAL: ' . $this->config['empresa']['ie']), 0, 1, 'L');

        // ===== BLOCO DESTINATÁRIO =====
        $blockY = $headerY + $headerH + 2;
        $leftW = 77;
        $leftH = 40;
        $rightW = $w - $leftW - 2;
        $rightH = 40;

        $dest = $dados['destinatario'];
        $doc = $dest['cnpj'] ?? ($dest['cpf'] ?? '');
        $endDest = $dest['endereco'];

        $pdf->SetXY($x + 1, $blockY + 1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(0, 4, $this->safeText($dest['nome']), 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $addr = sprintf('%s %s %s', $endDest['logradouro'], $endDest['numero'], $endDest['bairro']);
        $pdf->SetX($x + 1);
        $pdf->Cell(0, 4, $this->safeText($addr), 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 4, $this->safeText($endDest['municipio'] . ' - ' . $endDest['uf'] . ' ' . $endDest['cep']), 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 2, '', 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 4, $this->safeText('CNPJ/CPF: ' . $this->formatCnpjCpf($doc)), 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 4, $this->safeText('INSCRICAO ESTADUAL: ' . ($dest['ie'] ?? '')), 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 2, '', 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 4, $this->safeText('CODIGO DO CLIENTE: ' . ($dados['assinante']['iCodAssinante'] ?? '')), 0, 1, 'L');

        $pdf->SetX($x + 1);
        $pdf->Cell(0, 4, $this->safeText('N. TELEFONE: ' . ($endDest['telefone'] ?? '')), 0, 1, 'L');

        $periodo = '';
        if (!empty($dados['faturamento']['periodo_inicio']) && !empty($dados['faturamento']['periodo_fim'])) {
            $periodo = $dados['faturamento']['periodo_inicio'] . ' a ' . $dados['faturamento']['periodo_fim'];
        }
        if ($periodo) {
            $pdf->SetX($x + 1);
            $pdf->Cell(0, 4, $this->safeText('PERIODO: ' . $periodo), 0, 1, 'L');
        }

        // ===== ÁREA DIREITA (QR + DADOS DA NOTA) =====
        $rightX = $x + $leftW;
        $rightY = $blockY;

        // QR Code
        $qrSize = 32;
        $qrY = $rightY + 2;
        $qrX = $rightX + 2;
        $qrData = 'https://dfe-portal.svrs.rs.gov.br/nfcom/consulta?chNFCom=' . $chave;
        $this->drawQrCode($pdf, $qrData, $qrX, $qrY, $qrSize);

        // Informações ao lado do QR
        $gap = 4;
        $infoX = $qrX + $qrSize + $gap;
        $infoW = $rightW - ($qrSize + $gap + 2);

        $pdf->SetXY($infoX, $rightY + 1);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell($infoW, 4, $this->safeText('NOTA FISCAL N ' . $numero . ' - SERIE: ' . $serie), 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetX($infoX);
        $pdf->Cell($infoW, 4, $this->safeText('DATA DE EMISSAO: ' . date('d/m/Y')), 0, 1, 'L');
        $pdf->Cell(0, 1, '', 0, 1, 'L');

        $pdf->SetX($infoX);
        $pdf->MultiCell($infoW, 3.2, $this->safeText('CONSULTE PELA CHAVE DE ACESSO EM:' . PHP_EOL . 'https://dfe-portal.svrs.rs.gov.br/nfcom/consulta'), 0, 'L');
        $pdf->Cell(0, 1, '', 0, 1, 'L');

        $pdf->SetX($infoX);
        $pdf->MultiCell($infoW, 3.2, $this->safeText('CHAVE DE ACESSO:' . PHP_EOL . $this->formatChave($chave)), 0, 'L');

        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(0, 1, '', 0, 1, 'L');

        $pdf->SetX($infoX);
        $pdf->MultiCell($infoW, 3.2, $this->safeText('Protocolo de Autorizacao:'), 0, 'L');

        $pdf->SetX($infoX);
        $pdf->MultiCell($infoW, 3.2, $this->safeText('000000000000000 - ' . date('d/m/Y') . ' as 00:00:00'), 0, 'L');

        // ===== FAIXAS DE REFERÊNCIA (VENCIMENTO/TOTAL A PAGAR) =====
        $barW = 72;
        $barH = 27.04;
        $barY = $blockY + $leftH + 2;
        $pillH = 8;
        $gap = ($barH - ($pillH * 3)) / 2;

        $pdf->SetDrawColor($tealFill[0], $tealFill[1], $tealFill[2]);
        $pdf->SetFillColor($tealFill[0], $tealFill[1], $tealFill[2]);

        $labels = [
            'REFERENCIA (ANO/MES): ' . ($dados['faturamento']['competencia'] ?? date('Y-m')),
            'VENCIMENTO: ' . ($dados['faturamento']['vencimento'] ?? date('d/m/Y')),
            'TOTAL A PAGAR: R$ ' . $this->fmtMoney($dados['totais']['valor_total'] ?? 0.00),
        ];

        $pdf->SetTextColor(255, 255, 255);
        for ($i = 0; $i < 3; $i++) {
            $y = $barY + ($i * ($pillH + $gap));
            $this->drawRoundedRect($pdf, $x, $y, $barW, $pillH, 3.5, 'DF');
            $pdf->SetXY($x + 3, $y + 2);
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->Cell($barW - 6, 4, $this->safeText($labels[$i]), 0, 0, 'L');
        }
        $pdf->SetTextColor(0, 0, 0);

        // ===== ÁREA CONTRIBUINTE =====
        $msgX = $x + $barW + 4;
        $msgW = $w - $barW - 4;

        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $pdf->SetFillColor($grayFill[0], $grayFill[1], $grayFill[2]);
        $this->drawRoundedRect($pdf, $msgX, $barY, $msgW, $barH, 3.5, 'DF');
        $pdf->Line($msgX, $barY + 8.5, $msgX + $msgW, $barY + 8.5);

        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetXY($msgX + 2, $barY + 2);
        $pdf->Cell($msgW - 4, 4, $this->safeText('AREA CONTRIBUINTE:'), 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 7.2);
        $pdf->SetXY($msgX + 2, $barY + 11);
        $pdf->Cell($msgW - 4, 4, $this->safeText('N do Contrato: ' . ($dados['assinante']['numero_contrato'] ?? '')), 0, 1, 'L');

        // ===== CÁLCULO DAS POSIÇÕES DO RODAPÉ =====
        $yBottomLimit = $pageH - $marginBottom;

        $areaH = 50;
        $infoH = 20;
        $headerInfoH = 5;
        $infoTotalH = $infoH + $headerInfoH;
        $boxTotaisH = 28;
        $footerGap = 2;

        $areaY = $yBottomLimit - $areaH;
        $infoY = $areaY - $footerGap - $infoTotalH;
        $totY = $infoY - $footerGap - $boxTotaisH;

        // ===== TABELA DE ITENS =====
        $tableY = $barY + $barH + 2;
        $tableH = max(18, $totY - $tableY - 2);

        $this->renderItensTabela($pdf, $dados['itens'] ?? [], $x, $tableY, $w, $tableH, $border);

        // ===== BLOCO TOTAIS =====
        $totW = 60;
        $tribW = 55;
        $fiscoW = $w - $totW - $tribW - 2;

        $xTot = $x;
        $xTrib = $x + $totW + 1;
        $xFisco = $xTrib + $tribW + 1;

        // Fundo e borda Totais
        $pdf->SetFillColor(235, 235, 235);
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $this->drawRoundedRect($pdf, $xTot, $totY, $totW, $boxTotaisH, 2.5, 'DF');

        // Parte direita branca
        $lblPct = 0.60;
        $lblW = $totW * $lblPct;
        $valW = $totW - $lblW;
        $xVal = $xTot + $lblW;

        $pdf->SetFillColor(255, 255, 255);
        $pdf->Rect($xVal, $totY + 0.2, $valW - 0.2, $boxTotaisH - 0.4, 'F');
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $this->drawRoundedRect($pdf, $xTot, $totY, $totW, $boxTotaisH, 2.5, 'S');
        $pdf->Line($xVal, $totY, $xVal, $totY + $boxTotaisH);

        // Dados Totais
        $tot = $dados['totais'];
        $totLines = [
            ['VALOR TOTAL NF', $tot['valor_total'] ?? 0.00],
            ['BASE CÁLCULO', $tot['valor_base_calculo'] ?? ($tot['valor_produtos'] ?? 0.00)],
            ['VALOR ICMS', $tot['valor_icms'] ?? 0.00],
            ['VALOR ISENTO', $tot['valor_isento'] ?? 0.00],
            ['VALOR OUTROS', $tot['valor_outros'] ?? 0.00],
        ];

        $lineH = $boxTotaisH / 5;
        $currY = $totY;
        $pdf->SetFont('helvetica', 'B', 6.5);

        foreach ($totLines as $i => $row) {
            $pdf->SetXY($xTot + 1, $currY + ($lineH - 4) / 2);
            $pdf->Cell($lblW - 2, 4, $this->safeText($row[0]), 0, 0, 'L');
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetXY($xVal, $currY + ($lineH - 4) / 2);
            $pdf->Cell($valW - 1, 4, $this->safeText($this->fmtMoney($row[1])), 0, 0, 'R');
            $pdf->SetFont('helvetica', 'B', 6.5);
            if ($i < 4) {
                $yLine = $currY + $lineH;
                $pdf->Line($xTot, $yLine, $xTot + $totW, $yLine);
            }
            $currY += $lineH;
        }

        // ===== BLOCO TRIBUTOS =====
        $pdf->SetFillColor(255, 255, 255);
        $this->drawRoundedRect($pdf, $xTrib, $totY, $tribW, $boxTotaisH, 2.5, 'DF');

        $hTitle = 5;
        $hSub = 5;
        $hHeaderTotal = $hTitle + $hSub;

        $pdf->SetFillColor(235, 235, 235);
        $this->drawRoundedRectTop($pdf, $xTrib, $totY, $tribW, $hHeaderTotal, 2.5, 'F');
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $pdf->Line($xTrib, $totY + $hTitle, $xTrib + $tribW, $totY + $hTitle);
        $pdf->Line($xTrib, $totY + $hHeaderTotal, $xTrib + $tribW, $totY + $hHeaderTotal);

        $midTrib = $xTrib + ($tribW / 2);
        $pdf->Line($midTrib, $totY + $hTitle, $midTrib, $totY + $boxTotaisH);

        $pdf->SetXY($xTrib, $totY);
        $pdf->SetFont('helvetica', 'B', 6.5);
        $pdf->Cell($tribW, $hTitle, $this->safeText('INFORMAÇÃO DOS TRIBUTOS'), 0, 1, 'C');

        $pdf->SetXY($xTrib, $totY + $hTitle);
        $pdf->Cell($tribW / 2, $hSub, $this->safeText('TRIBUTO'), 0, 0, 'C');
        $pdf->Cell($tribW / 2, $hSub, $this->safeText('VALOR'), 0, 0, 'C');

        $tribData = [
            ['PIS', $tot['valor_pis'] ?? 0.00],
            ['COFINS', $tot['valor_cofins'] ?? 0.00],
            ['FUST', $tot['valor_fust'] ?? 0.00],
            ['FUNTTEL', $tot['valor_funtel'] ?? 0.00],
        ];

        $hRest = $boxTotaisH - $hHeaderTotal;
        $hRow = $hRest / 4;
        $currY = $totY + $hHeaderTotal;
        $pdf->SetFont('helvetica', '', 6.5);

        foreach ($tribData as $idx => $row) {
            $pdf->SetXY($xTrib, $currY);
            $pdf->Cell(($tribW / 2) - 1, $hRow, $this->safeText($row[0]), 0, 0, 'R');
            $pdf->SetXY($midTrib, $currY);
            $pdf->Cell(($tribW / 2) - 1, $hRow, $this->safeText($this->fmtMoney($row[1])), 0, 0, 'R');
            if ($idx < 3) {
                $pdf->Line($xTrib, $currY + $hRow, $xTrib + $tribW, $currY + $hRow);
            }
            $currY += $hRow;
        }

        $this->drawRoundedRect($pdf, $xTrib, $totY, $tribW, $boxTotaisH, 2.5, 'S');

        // ===== RESERVADO AO FISCO =====
        $pdf->SetFillColor(235, 235, 235);
        $this->drawRoundedRect($pdf, $xFisco, $totY, $fiscoW, $boxTotaisH, 2.5, 'DF');
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $pdf->Line($xFisco, $totY + 6, $xFisco + $fiscoW, $totY + 6);

        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetXY($xFisco, $totY + 2);
        $pdf->Cell($fiscoW, 4, $this->safeText('RESERVADO AO FISCO'), 0, 1, 'C');

        // ===== INFORMAÇÕES COMPLEMENTARES =====
        $pdf->SetFillColor(235, 235, 235);
        $this->drawRoundedRectTop($pdf, $x, $infoY, $w, $headerInfoH, 2.5, 'F');
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $this->drawRoundedRect($pdf, $x, $infoY, $w, $infoTotalH, 2.5, 'S');
        $pdf->Line($x, $infoY + $headerInfoH, $x + $w, $infoY + $headerInfoH);

        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetXY($x, $infoY);
        $pdf->Cell($w, $headerInfoH, $this->safeText('INFORMAÇÕES COMPLEMENTARES'), 0, 1, 'C');

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetXY($x + 2, $infoY + $headerInfoH + 2);
        $info = $dados['informacoes_adicionais'] ?? '';
        $pdf->MultiCell($w - 4, 3.5, $this->safeText($info), 0, 'L');

        // ===== ÁREA DO CONTRIBUINTE E ANATEL =====
        $headerAreaH = 5;

        $pdf->SetFillColor(235, 235, 235);
        $this->drawRoundedRectTop($pdf, $x, $areaY, $w, $headerAreaH, 2.5, 'F');
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $this->drawRoundedRect($pdf, $x, $areaY, $w, $areaH, 2.5, 'S');
        $pdf->Line($x, $areaY + $headerAreaH, $x + $w, $areaY + $headerAreaH);

        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetXY($x, $areaY);
        $pdf->Cell($w, $headerAreaH, $this->safeText('ÁREA DO CONTRIBUINTE E DETERMINAÇÕES DA ANATEL'), 0, 1, 'C');

        // Caixas internas
        $innerY = $areaY + $headerAreaH + 2;
        $gapBox = 3;
        $boxW = ($w - $gapBox - 2) / 2;
        $boxH = 11;

        $pdf->SetDrawColor($border[0], $border[1], $border[2]);
        $pdf->Rect($x + 1, $innerY, $boxW, $boxH);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetXY($x + 2, $innerY + 1);
        $pdf->Cell($boxW, 3, $this->safeText('Linha digitável'), 0, 1, 'L');

        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($x + 2, $innerY + 5);
        $linhaDig = $dados['faturamento']['linha_digitavel'] ?? ($dados['faturamento']['cod_barras'] ?? '');
        if (empty($linhaDig) || $linhaDig === '1') {
            $linhaDig = str_repeat('0', 48);
        }
        $pdf->Cell($boxW - 2, 4, $this->safeText($linhaDig), 0, 0, 'L');

        $box2X = $x + 1 + $boxW + $gapBox;
        $pdf->Rect($box2X, $innerY, $boxW, $boxH);
        $pdf->SetFont('helvetica', '', 6);
        $pdf->SetXY($box2X + 1, $innerY + 1);
        $pdf->Cell($boxW, 3, $this->safeText('N Identificador de débito automático'), 0, 1, 'L');

        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetXY($box2X + 1, $innerY + 5);
        $idDebito = $dados['assinante']['identificador_debito'] ?? '-';
        $pdf->Cell($boxW - 2, 4, $this->safeText($idDebito), 0, 0, 'L');

        // Código de Barras e Pix
        $barH = 26;
        $barY = $innerY + $boxH + 4;
        $pixAreaW = 55;
        $barAreaW = $w - $pixAreaW;

        $pdf->SetDrawColor(200, 200, 200);
        $lineX = $x + $barAreaW;
        $pdf->Line($lineX, $barY + 2, $lineX, $barY + $barH - 2);

        $codBarras = $dados['faturamento']['cod_barras'] ?? '';
        if (empty($codBarras) || $codBarras === '1') {
            $codBarras = str_repeat('0', 44);
        }

        $barcodeH = 13;
        $barcodeY = $barY + (($barH - $barcodeH) / 2);
        $barcodeW = $barAreaW - 22;
        $barcodeX = $x + 6;
        $this->drawBarcode($pdf, $codBarras, $barcodeX, $barcodeY, $barcodeW, $barcodeH);

        // Pix
        $pixPayload = $dados['pix_payload'] ?? null;
        if (empty($pixPayload)) {
            $pixKey = $dados['pix_key'] ?? ($this->config['pix_key'] ?? null);
            if (!empty($pixKey)) {
                $pixPayload = $pixKey;
            }
        }

        if ($pixPayload) {
            $qrSize = 19;
            $pixPadR = 8;
            $pixPadL = 4;

            $qrY = $barY + (($barH - $qrSize) / 2);
            $qrX = $x + $w - $qrSize - $pixPadR;

            $this->drawQrCode($pdf, $pixPayload, $qrX, $qrY, $qrSize);

            $textAreaW = $pixAreaW - $qrSize - ($pixPadL + $pixPadR);
            $textX = $qrX - $textAreaW - $pixPadL;
            $contentCenterY = $barY + ($barH / 2);

            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetXY($textX, $contentCenterY - 4);
            $pdf->MultiCell($textAreaW, 3.5, $this->safeText('Pague também via Pix:'), 0, 'R');

            $logoPath = $this->config['pix_logo'] ?? null;
            if ($logoPath && file_exists($logoPath)) {
                $logoW = 16;
                $logoH = 5;
                $pdf->Image($logoPath, $textX + ($textAreaW - $logoW), $contentCenterY + 1, $logoW);
            } else {
                $pdf->SetXY($textX, $contentCenterY + 1);
                $pdf->SetFont('helvetica', 'B', 12);
                $pdf->SetTextColor(50, 188, 173);
                $pdf->Cell($textAreaW, 5, 'pix', 0, 0, 'R');
                $pdf->SetTextColor(0, 0, 0);
            }
        }
    }

    private function renderItensTabela(Fpdf $pdf, array $itens, float $x, float $y, float $w, float $h, array $border): void
    {
        $headers = [
            'ITENS DA FATURA',
            'cClass',
            'UN',
            'QUANT',
            'VALOR UNIT',
            'VALOR DESC.',
            'VALOR ACR.',
            'VALOR TOTAL',
            'PIS/COFINS',
            'BC ICMS',
            'ALIQ (%)',
            'VALOR ICMS'
        ];

        $widths = [60, 12, 7, 11, 15, 14, 14, 14, 14, 12, 10, 13];
        $headerH = 6;
        $rowH = 6;
        $maxRows = (int) floor(($h - $headerH) / $rowH);

        $totalWidth = array_sum($widths);
        $scale = $w / $totalWidth;
        $scaled = array_map(function ($w) use ($scale) {
            return $w * $scale;
        }, $widths);

        $defaultCClass = $this->normalizeCClass($this->safeText($this->config['classe'] ?? '0101011'));

        $renderPage = function (array $pageItems) use ($pdf, $x, $y, $w, $h, $headers, $scaled, $border, $headerH, $rowH, $defaultCClass) {
            $maskR = 2.5;

            // Fundo do cabeçalho
            $pdf->SetFillColor(240, 240, 240);
            $this->drawRoundedRectTop($pdf, $x, $y, $w, $headerH, $maskR, 'F');

            // Borda externa
            $pdf->SetDrawColor($border[0], $border[1], $border[2]);
            $this->drawRoundedRect($pdf, $x, $y, $w, $h, $maskR, 'S');

            // Cabeçalho
            $pdf->SetFont('helvetica', 'B', 6.5);
            $cursorX = $x;
            $lastIndex = count($headers) - 1;

            foreach ($headers as $i => $label) {
                $this->drawHeaderCell($pdf, $cursorX, $y, $scaled[$i], $headerH, $label, $border, $i === 0, $i === $lastIndex);
                $cursorX += $scaled[$i];
            }

            // Itens
            $pdf->SetY($y + $headerH);
            $pdf->SetFont('helvetica', '', 7);

            foreach ($pageItems as $item) {
                $qtd = $item['quantidade'] ?? 1;
                $vTotal = $item['valor_total'] ?? 0.00;
                $vUnit = $item['valor_unitario'] ?? ($qtd > 0 ? $vTotal / $qtd : 0.00);
                $pis = $item['pis']['valor'] ?? 0.00;
                $cof = $item['cofins']['valor'] ?? 0.00;

                $row = [
                    $this->truncate($item['descricao'] ?? '', 28),
                    $item['cclass'] ?? ($item['cClass'] ?? ($item['classe'] ?? $defaultCClass)),
                    $item['unidade'] ?? 'UN',
                    $this->fmtQty($qtd),
                    $this->fmtMoney($vUnit),
                    $this->fmtMoney($item['desconto'] ?? 0.00),
                    $this->fmtMoney($item['outros'] ?? 0.00),
                    $this->fmtMoney($vTotal),
                    $this->fmtMoney($pis + $cof),
                    $this->fmtMoney($item['base_calculo'] ?? 0.00),
                    $this->fmtQty($item['aliquota_icms'] ?? 0.00),
                    $this->fmtMoney($item['valor_icms'] ?? 0.00),
                ];

                $cursorX = $x;
                $currentY = $pdf->GetY();

                if ($currentY + $rowH > $y + $h)
                    break;

                foreach ($row as $i => $val) {
                    $pdf->SetXY($cursorX, $currentY);
                    $pdf->Cell($scaled[$i], $rowH, $this->safeText((string) $val), 'LRB', 0, 'C');
                    $cursorX += $scaled[$i];
                }
                $pdf->SetY($currentY + $rowH);
            }
        };

        // Paginação
        if (count($itens) <= $maxRows) {
            $renderPage($itens);
        } else {
            $chunks = array_chunk($itens, $maxRows);
            foreach ($chunks as $index => $chunk) {
                if ($index > 0)
                    $pdf->AddPage();
                $renderPage($chunk);
            }
        }
    }

    private function drawHeaderCell(Fpdf $pdf, float $x, float $y, float $w, float $h, string $text, array $border, bool $isFirst, bool $isLast): void
    {
        $pdf->SetDrawColor($border[0], $border[1], $border[2]);

        $pdf->Line($x, $y + $h, $x + $w, $y + $h);

        if (!$isFirst) {
            $pdf->Line($x, $y, $x, $y + $h);
        }

        $lines = $this->wrapHeaderText($pdf, $text, $w - 1);
        $lineH = $h / max(1, count($lines));
        $pdf->SetXY($x, $y + 0.5);

        foreach ($lines as $line) {
            $pdf->Cell($w, $lineH, $this->safeText($line), 0, 2, 'C');
        }
    }

    private function wrapHeaderText(Fpdf $pdf, string $text, float $maxWidth): array
    {
        $words = preg_split('/\s+/', trim($text));
        $lines = [];
        $current = '';

        foreach ($words as $word) {
            $test = $current === '' ? $word : $current . ' ' . $word;
            if ($pdf->GetStringWidth($test) <= $maxWidth) {
                $current = $test;
            } else {
                if ($current !== '') {
                    $lines[] = $current;
                }
                $current = $word;
            }
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }

    private function formatCnpjCpf(string $doc): string
    {
        $doc = preg_replace('/\D+/', '', (string) $doc);
        if (strlen($doc) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $doc);
        }
        if (strlen($doc) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $doc);
        }
        return $doc;
    }

    private function formatChave(string $chave): string
    {
        return trim(chunk_split($chave, 4, ' '));
    }

    private function fmtMoney($value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    private function fmtQty($value): string
    {
        return number_format((float) $value, 2, ',', '.');
    }

    private function truncate(string $value, int $max): string
    {
        $value = $this->safeText($value);
        if (strlen($value) <= $max) {
            return $value;
        }
        return substr($value, 0, $max - 3) . '...';
    }

    private function safeText(string $text): string
    {
        $text = trim($text);
        $converted = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        if ($converted === false) {
            return preg_replace('/[^\x20-\x7E]/', '', $text);
        }
        return $converted;
    }

    private function normalizeCClass(string $value): string
    {
        $digits = preg_replace('/\D+/', '', (string) $value);
        if (strlen($digits) === 7) {
            return $digits;
        }
        if (strlen($digits) === 6) {
            return $digits . '0';
        }
        return '0101011';
    }

    private function drawQrCode(Fpdf $pdf, string $data, float $x, float $y, float $size): void
    {
        if (!$this->canRenderBarcode()) {
            $this->drawPlaceholder($pdf, $x, $y, $size, $size, 'QR indisponivel');
            return;
        }

        $barcode = new Barcode();
        $bobj = $barcode->getBarcodeObj('QRCODE,H', $data, -4, -4, 'black', [0, 0, 0, 0]);
        $png = $bobj->getPngData(false);
        $tmp = $this->tempFile('qr');
        file_put_contents($tmp, $png);
        $pdf->Image($tmp, $x, $y, $size, $size, 'PNG');
        @unlink($tmp);
    }

    private function drawBarcode(Fpdf $pdf, string $data, float $x, float $y, float $w, float $h): void
    {
        if (!$this->canRenderBarcode()) {
            $this->drawPlaceholder($pdf, $x, $y, $w, $h, 'Codigo de barras indisponivel');
            return;
        }

        $barcode = new Barcode();
        $bobj = $barcode->getBarcodeObj('C128', $data, -2, -$h, 'black', [0, 0, 0, 0]);
        $png = $bobj->getPngData(false);
        $tmp = $this->tempFile('bar');
        file_put_contents($tmp, $png);
        $pdf->Image($tmp, $x, $y, $w, $h, 'PNG');
        @unlink($tmp);
    }

    private function canRenderBarcode(): bool
    {
        return extension_loaded('gd') || extension_loaded('imagick');
    }

    private function drawPlaceholder(Fpdf $pdf, float $x, float $y, float $w, float $h, string $label): void
    {
        $pdf->SetDrawColor(120, 120, 120);
        $pdf->Rect($x, $y, $w, $h);
        $pdf->SetFont('helvetica', '', 6.5);
        $pdf->SetXY($x + 1, $y + ($h / 2) - 2);
        $pdf->Cell($w - 2, 4, $this->safeText($label), 0, 0, 'C');
    }

    private function tempFile(string $prefix): string
    {
        $dir = $this->config['diretorios']['temp'] ?? (__DIR__ . '/../../storage/temp');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir . '/' . $prefix . '-' . uniqid('', true) . '.png';
    }

    private function drawRoundedRect(Fpdf $pdf, float $x, float $y, float $w, float $h, float $r, string $style = ''): void
    {
        if (!method_exists($pdf, 'raw')) {
            $pdf->Rect($x, $y, $w, $h, $style);
            return;
        }

        $k = $pdf->getK();
        $hp = $pdf->getH();
        $op = $style === 'F' ? 'f' : ($style === 'FD' || $style === 'DF' ? 'B' : 'S');
        $myArc = 4 / 3 * (sqrt(2) - 1);
        $yb = $hp - $y;
        $ybm = $hp - ($y + $h);

        $pdf->raw(sprintf('%.2F %.2F m', ($x + $r) * $k, $yb * $k));
        $pdf->raw(sprintf('%.2F %.2F l', ($x + $w - $r) * $k, $yb * $k));
        $pdf->raw(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $w - $r + $r * $myArc) * $k,
            $yb * $k,
            ($x + $w) * $k,
            ($yb - $r + $r * $myArc) * $k,
            ($x + $w) * $k,
            ($yb - $r) * $k
        ));
        $pdf->raw(sprintf('%.2F %.2F l', ($x + $w) * $k, ($ybm + $r) * $k));
        $pdf->raw(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $w) * $k,
            ($ybm + $r - $r * $myArc) * $k,
            ($x + $w - $r + $r * $myArc) * $k,
            $ybm * $k,
            ($x + $w - $r) * $k,
            $ybm * $k
        ));
        $pdf->raw(sprintf('%.2F %.2F l', ($x + $r) * $k, $ybm * $k));
        $pdf->raw(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $r - $r * $myArc) * $k,
            $ybm * $k,
            ($x) * $k,
            ($ybm + $r - $r * $myArc) * $k,
            ($x) * $k,
            ($ybm + $r) * $k
        ));
        $pdf->raw(sprintf('%.2F %.2F l', ($x) * $k, ($yb - $r) * $k));
        $pdf->raw(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x) * $k,
            ($yb - $r + $r * $myArc) * $k,
            ($x + $r - $r * $myArc) * $k,
            $yb * $k,
            ($x + $r) * $k,
            $yb * $k
        ));
        $pdf->raw($op);
    }

    private function drawRoundedRectTop(Fpdf $pdf, float $x, float $y, float $w, float $h, float $r, string $style = ''): void
    {
        if (!method_exists($pdf, 'raw')) {
            $pdf->Rect($x, $y, $w, $h, $style);
            return;
        }

        $k = $pdf->getK();
        $hp = $pdf->getH();
        $op = $style === 'F' ? 'f' : ($style === 'FD' || $style === 'DF' ? 'B' : 'S');
        $myArc = 4 / 3 * (sqrt(2) - 1);
        $yb = $hp - $y;
        $ybm = $hp - ($y + $h);

        $pdf->raw(sprintf('%.2F %.2F m', ($x + $r) * $k, $yb * $k));
        $pdf->raw(sprintf('%.2F %.2F l', ($x + $w - $r) * $k, $yb * $k));
        $pdf->raw(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x + $w - $r + $r * $myArc) * $k,
            $yb * $k,
            ($x + $w) * $k,
            ($yb - $r + $r * $myArc) * $k,
            ($x + $w) * $k,
            ($yb - $r) * $k
        ));
        $pdf->raw(sprintf('%.2F %.2F l', ($x + $w) * $k, $ybm * $k));
        $pdf->raw(sprintf('%.2F %.2F l', ($x) * $k, $ybm * $k));
        $pdf->raw(sprintf('%.2F %.2F l', ($x) * $k, ($yb - $r) * $k));
        $pdf->raw(sprintf(
            '%.2F %.2F %.2F %.2F %.2F %.2F c',
            ($x) * $k,
            ($yb - $r + $r * $myArc) * $k,
            ($x + $r - $r * $myArc) * $k,
            $yb * $k,
            ($x + $r) * $k,
            $yb * $k
        ));
        $pdf->raw($op);
    }

    private function getCodigoUF($uf): int
    {
        $ufs = [
            'AC' => 12,
            'AL' => 27,
            'AM' => 13,
            'AP' => 16,
            'BA' => 29,
            'CE' => 23,
            'DF' => 53,
            'ES' => 32,
            'GO' => 52,
            'MA' => 21,
            'MG' => 31,
            'MS' => 50,
            'MT' => 51,
            'PA' => 15,
            'PB' => 25,
            'PE' => 26,
            'PI' => 22,
            'PR' => 41,
            'RJ' => 33,
            'RN' => 24,
            'RO' => 11,
            'RR' => 14,
            'RS' => 43,
            'SC' => 42,
            'SE' => 28,
            'SP' => 35,
            'TO' => 17
        ];

        return $ufs[$uf] ?? 0;
    }
}

class LocalFpdf extends FPDF
{
    public function raw(string $cmd): void
    {
        $this->_out($cmd);
    }

    public function getK(): float
    {
        return $this->k;
    }

    public function getH(): float
    {
        return $this->h;
    }
}
