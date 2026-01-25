<?php
/**
 * Script para listar arquivos modificados para atualização
 * Execute este script para gerar uma lista dos arquivos que precisam ser atualizados
 */

// Lista de arquivos modificados/criados
$arquivos = [
    // Controllers
    'application/controllers/Relatorios.php',
    'application/controllers/Permissoes.php',
    
    // Models
    'application/models/Relatorios_model.php',
    
    // Views - Novos
    'application/views/relatorios/rel_contratos.php',
    'application/views/relatorios/imprimir/imprimirContratos.php',
    
    // Views - Modificados
    'application/views/relatorios/imprimir/imprimirTopo.php',
    'application/views/permissoes/adicionarPermissao.php',
    'application/views/permissoes/editarPermissao.php',
    'application/views/permissoes/permissoes.php',
    'application/views/tema/topo.php',
    'application/views/menu.php',
    
    // Config
    'application/config/permission.php',
    
    // Helpers
    'application/helpers/mpdf_helper.php',
    
    // Vendor (mPDF - Correções - OPCIONAL)
    'application/vendor/mpdf/mpdf/src/Language/ScriptToLanguage.php',
    'application/vendor/mpdf/mpdf/src/Mpdf.php',
];

// Verificar quais arquivos existem
$existentes = [];
$inexistentes = [];

foreach ($arquivos as $arquivo) {
    $caminhoCompleto = __DIR__ . '/' . $arquivo;
    if (file_exists($caminhoCompleto)) {
        $existentes[] = [
            'arquivo' => $arquivo,
            'tamanho' => filesize($caminhoCompleto),
            'modificado' => date('d/m/Y H:i:s', filemtime($caminhoCompleto))
        ];
    } else {
        $inexistentes[] = $arquivo;
    }
}

// Gerar saída
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Arquivos para Atualização - Relatório de Contratos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2D3E50;
            border-bottom: 3px solid #2D3E50;
            padding-bottom: 10px;
        }
        h2 {
            color: #34495e;
            margin-top: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2D3E50;
            color: white;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-ok {
            color: #27ae60;
            font-weight: bold;
        }
        .status-erro {
            color: #e74c3c;
            font-weight: bold;
        }
        .info-box {
            background-color: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #2D3E50;
            margin-top: 20px;
        }
        .copy-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 5px;
        }
        .copy-btn:hover {
            background-color: #2980b9;
        }
        textarea {
            width: 100%;
            height: 200px;
            font-family: monospace;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 Lista de Arquivos para Atualização</h1>
        <p><strong>Relatório de Contratos - v1.0</strong></p>
        <p><strong>Data:</strong> <?= date('d/m/Y H:i:s') ?></p>

        <div class="info-box">
            <strong>ℹ️ Instruções:</strong> Esta lista contém todos os arquivos que foram modificados ou criados para o relatório de contratos. 
            Faça upload destes arquivos via FTP mantendo a estrutura de pastas.
        </div>

        <?php if (!empty($inexistentes)): ?>
        <div class="warning-box">
            <strong>⚠️ Atenção:</strong> Alguns arquivos não foram encontrados no sistema local. Verifique se estão corretos.
        </div>
        <?php endif; ?>

        <h2>✅ Arquivos Encontrados (<?= count($existentes) ?>)</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Caminho do Arquivo</th>
                    <th>Tamanho</th>
                    <th>Última Modificação</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($existentes as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><code><?= htmlspecialchars($item['arquivo']) ?></code></td>
                    <td><?= number_format($item['tamanho'] / 1024, 2) ?> KB</td>
                    <td><?= $item['modificado'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <?php if (!empty($inexistentes)): ?>
        <h2>❌ Arquivos Não Encontrados (<?= count($inexistentes) ?>)</h2>
        <ul>
            <?php foreach ($inexistentes as $arquivo): ?>
            <li><code><?= htmlspecialchars($arquivo) ?></code></li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <div class="total">
            Total de arquivos para atualização: <strong><?= count($existentes) ?></strong>
        </div>

        <h2>📝 Lista para Copiar (FTP)</h2>
        <p>Copie a lista abaixo para facilitar o upload via FTP:</p>
        <textarea id="listaArquivos" readonly><?php
foreach ($existentes as $item) {
    echo $item['arquivo'] . "\n";
}
?></textarea>
        <button class="copy-btn" onclick="copiarLista()">📋 Copiar Lista</button>

        <h2>📦 Estrutura de Pastas</h2>
        <div class="info-box">
            <strong>Importante:</strong> Mantenha a estrutura de pastas ao fazer upload via FTP.
            <pre style="background: #f5f5f5; padding: 15px; border-radius: 4px; overflow-x: auto;">
application/
├── controllers/
│   ├── Relatorios.php
│   └── Permissoes.php
├── models/
│   └── Relatorios_model.php
├── views/
│   ├── relatorios/
│   │   ├── rel_contratos.php (NOVO)
│   │   └── imprimir/
│   │       ├── imprimirContratos.php (NOVO)
│   │       └── imprimirTopo.php
│   ├── permissoes/
│   │   ├── adicionarPermissao.php
│   │   ├── editarPermissao.php
│   │   └── permissoes.php
│   ├── tema/
│   │   └── topo.php
│   └── menu.php
├── config/
│   └── permission.php
└── helpers/
    └── mpdf_helper.php
            </pre>
        </div>

        <h2>⚠️ Observações Importantes</h2>
        <div class="warning-box">
            <ul>
                <li><strong>Backup obrigatório:</strong> Sempre faça backup antes de atualizar</li>
                <li><strong>Banco de dados:</strong> Após upload, execute a migração via interface ou terminal</li>
                <li><strong>Vendor (mPDF):</strong> Os arquivos do vendor são opcionais, apenas se houver problemas com PDF</li>
                <li><strong>Permissões:</strong> Verifique as permissões de arquivo após o upload (Linux/Unix)</li>
            </ul>
        </div>
    </div>

    <script>
        function copiarLista() {
            const textarea = document.getElementById('listaArquivos');
            textarea.select();
            document.execCommand('copy');
            alert('Lista copiada para a área de transferência!');
        }
    </script>
</body>
</html>
