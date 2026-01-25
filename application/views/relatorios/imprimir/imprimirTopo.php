<?php 
// Sempre exibir o topo
$temEmitente = false;
$nome = '';
$cnpj = '';
$telefone = '';
$email = '';
$rua = '';
$numero = '';
$bairro = '';
$cidade = '';
$uf = '';
$cep = '';
$url_logo = '';
$logoPath = '';

if (isset($emitente) && $emitente) {
    $temEmitente = true;
    
    // Extrair dados - suporta objeto e array
    if (is_object($emitente)) {
        $nome = isset($emitente->nome) ? $emitente->nome : '';
        $cnpj = isset($emitente->cnpj) ? $emitente->cnpj : '';
        $telefone = isset($emitente->telefone) ? $emitente->telefone : '';
        $email = isset($emitente->email) ? $emitente->email : '';
        $rua = isset($emitente->rua) ? $emitente->rua : '';
        $numero = isset($emitente->numero) ? $emitente->numero : '';
        $bairro = isset($emitente->bairro) ? $emitente->bairro : '';
        $cidade = isset($emitente->cidade) ? $emitente->cidade : '';
        $uf = isset($emitente->uf) ? $emitente->uf : '';
        $cep = isset($emitente->cep) ? $emitente->cep : '';
        $url_logo = isset($emitente->url_logo) ? $emitente->url_logo : '';
    } else if (is_array($emitente)) {
        $nome = isset($emitente['nome']) ? $emitente['nome'] : '';
        $cnpj = isset($emitente['cnpj']) ? $emitente['cnpj'] : '';
        $telefone = isset($emitente['telefone']) ? $emitente['telefone'] : '';
        $email = isset($emitente['email']) ? $emitente['email'] : '';
        $rua = isset($emitente['rua']) ? $emitente['rua'] : '';
        $numero = isset($emitente['numero']) ? $emitente['numero'] : '';
        $bairro = isset($emitente['bairro']) ? $emitente['bairro'] : '';
        $cidade = isset($emitente['cidade']) ? $emitente['cidade'] : '';
        $uf = isset($emitente['uf']) ? $emitente['uf'] : '';
        $cep = isset($emitente['cep']) ? $emitente['cep'] : '';
        $url_logo = isset($emitente['url_logo']) ? $emitente['url_logo'] : '';
    }
    
    // Construir caminho da logo seguindo exatamente o padrão da NFCom
    if (!empty($url_logo)) {
        // Usar o mesmo padrão da NFCom: str_replace('/', DIRECTORY_SEPARATOR, FCPATH . $url_logo)
        $logoPath = str_replace('/', DIRECTORY_SEPARATOR, FCPATH . $url_logo);
    }
    
    // Se não houver logo personalizada ou não existir, tenta logo padrão
    if (empty($logoPath) || !file_exists($logoPath) || !is_readable($logoPath)) {
        $logoPath = FCPATH . 'assets/img/logo.png';
    }
}
?>
<div style="border-bottom: 2px solid #2D3E50; padding-bottom: 10px; margin-bottom: 10px; page-break-inside: avoid;">
    <table style="width: 100%; border: none; border-collapse: collapse;">
        <tr>
            <!-- Logo e informações à esquerda -->
            <td style="width: 30%; vertical-align: top; border: none; padding: 0;">
                <?php 
                if ($temEmitente && !empty($logoPath) && file_exists($logoPath) && is_readable($logoPath)) {
                    try {
                        // Detectar tipo de imagem
                        $imageInfo = @getimagesize($logoPath);
                        $mimeType = $imageInfo ? $imageInfo['mime'] : 'image/png';
                        $logoBase64 = 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($logoPath));
                ?>
                        <img style="max-width: 150px; max-height: 80px;" src="<?= $logoBase64 ?>" alt="<?= htmlspecialchars($nome ?: 'Logo') ?>">
                <?php 
                    } catch (Exception $e) {
                        // Se der erro, mostra "Sem Logo"
                ?>
                        <div style="width: 150px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; text-align: center; line-height: 80px; color: #999; font-size: 9px;">
                            Sem Logo
                        </div>
                <?php 
                    }
                } else { 
                ?>
                    <div style="width: 150px; height: 80px; background-color: #f0f0f0; border: 1px solid #ddd; text-align: center; line-height: 80px; color: #999; font-size: 9px;">
                        Sem Logo
                    </div>
                <?php } ?>
            </td>
            
            <!-- Informações da empresa ao centro -->
            <td style="width: 45%; vertical-align: top; border: none; padding: 0 10px;">
                <?php if ($temEmitente): ?>
                    <h3 style="margin: 0 0 5px 0; font-size: 16px; font-weight: bold; color: #2D3E50;"><?= htmlspecialchars($nome ?: 'Empresa') ?></h3>
                    <p style="margin: 2px 0; font-size: 10px; line-height: 1.4;">
                        <?php if ($cnpj): ?>
                            <strong>CNPJ:</strong> <?= htmlspecialchars($cnpj) ?><br>
                        <?php endif; ?>
                        <?php if ($telefone): ?>
                            <strong>Telefone:</strong> <?= htmlspecialchars($telefone) ?><br>
                        <?php endif; ?>
                        <?php if ($email): ?>
                            <strong>E-mail:</strong> <?= htmlspecialchars($email) ?><br>
                        <?php endif; ?>
                        <?php if ($rua): ?>
                            <strong>Endereço:</strong> <?= htmlspecialchars($rua) ?><?= $numero ? ', ' . htmlspecialchars($numero) : '' ?><?= $bairro ? ', ' . htmlspecialchars($bairro) : '' ?><br>
                            <?= $cidade ? htmlspecialchars($cidade) : '' ?><?= $uf ? ' - ' . htmlspecialchars($uf) : '' ?><?= $cep ? ' - CEP: ' . htmlspecialchars($cep) : '' ?>
                        <?php endif; ?>
                    </p>
                <?php else: ?>
                    <h3 style="margin: 0 0 5px 0; font-size: 16px; font-weight: bold; color: #2D3E50;">Empresa</h3>
                    <p style="margin: 2px 0; font-size: 10px; line-height: 1.4; color: #999;">
                        Dados da empresa não configurados
                    </p>
                <?php endif; ?>
            </td>
            
            <!-- Informações do relatório à direita -->
            <td style="width: 25%; vertical-align: top; border: none; padding: 0; text-align: right;">
                <?php if (isset($title) && $title): ?>
                    <h4 style="margin: 0 0 5px 0; font-size: 14px; font-weight: bold; color: #2D3E50;"><?= htmlspecialchars($title) ?></h4>
                <?php endif ?>
                
                <?php if (isset($dataInicial) && $dataInicial && $dataInicial != 'indefinida'): ?>
                    <p style="margin: 2px 0; font-size: 9px;"><strong>Data Inicial:</strong> <?= htmlspecialchars($dataInicial) ?></p>
                <?php endif ?>
                
                <?php if (isset($dataFinal) && $dataFinal && $dataFinal != 'indefinida'): ?>
                    <p style="margin: 2px 0; font-size: 9px;"><strong>Data Final:</strong> <?= htmlspecialchars($dataFinal) ?></p>
                <?php endif ?>
                
                <p style="margin: 2px 0; font-size: 9px;"><strong>Data de Emissão:</strong> <?= date('d/m/Y H:i:s') ?></p>
                
                <!-- Número de páginas (será preenchido pelo mPDF) -->
                <p style="margin: 5px 0 0 0; font-size: 9px; color: #666;">
                    Página {PAGENO} de {nbpg}
                </p>
            </td>
        </tr>
    </table>
</div>
