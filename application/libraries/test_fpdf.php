<?php
// Teste simples de FPDF
require_once __DIR__ . '/../vendor/autoload.php';

use NFePHP\DA\Legacy\FPDF\Fpdf;

$pdf = new Fpdf('P', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(40, 10, 'Hello World!');

$output = $pdf->Output('S');

echo "Tamanho do PDF: " . strlen($output) . " bytes\n";

if (strlen($output) > 0) {
    echo "✅ FPDF está funcionando!\n";
    file_put_contents(__DIR__ . '/test_fpdf.pdf', $output);
    echo "PDF salvo em: " . __DIR__ . "/test_fpdf.pdf\n";
} else {
    echo "❌ FPDF não está gerando conteúdo\n";
}
