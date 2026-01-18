<?php
/**
 * Script para importar municípios do CSV para o banco de dados
 * 
 * Estrutura do CSV:
 * CÓDIGO DO MUNICÍPIO - TOM;CÓDIGO DO MUNICÍPIO - IBGE;MUNICÍPIO - TOM;MUNICÍPIO - IBGE;UF
 */

// Configurar codificação para UTF-8
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'mapos'; // Ajuste conforme seu banco
$username = 'root';
$password = '';

try {
    // Conectar ao banco
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Conectado ao banco de dados com sucesso!\n";
    
    // Ler o arquivo CSV
    $csvFile = 'import/municipios.csv';
    if (!file_exists($csvFile)) {
        throw new Exception("Arquivo CSV não encontrado: $csvFile");
    }
    
    // Detectar codificação do arquivo
    $fileContent = file_get_contents($csvFile);
    $encoding = mb_detect_encoding($fileContent, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    echo "Codificação detectada: $encoding\n";
    
    // Converter para UTF-8 se necessário
    if ($encoding !== 'UTF-8') {
        $fileContent = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
        file_put_contents($csvFile . '.utf8', $fileContent);
        $csvFile = $csvFile . '.utf8';
        echo "Arquivo convertido para UTF-8: $csvFile\n";
    }
    
    $handle = fopen($csvFile, 'r');
    if (!$handle) {
        throw new Exception("Não foi possível abrir o arquivo CSV");
    }
    
    // Pular o cabeçalho
    fgetcsv($handle, 1000, ';');
    
    $imported = 0;
    $errors = 0;
    $batch = [];
    $batchSize = 1000;
    
    echo "Iniciando importação dos municípios...\n";
    
    // Preparar statement para inserção
    $stmt = $pdo->prepare("
        INSERT INTO municipios (EST_ID, MUN_NOME, MUN_IBGE) 
        VALUES (?, ?, ?)
    ");
    
    // Mapear UF para EST_ID (baseado na ordem de inserção na tabela estados)
    $ufToEstId = [
        'AC' => 1, 'AL' => 2, 'AP' => 3, 'AM' => 4, 'BA' => 5, 'CE' => 6, 'DF' => 7,
        'ES' => 8, 'GO' => 9, 'MA' => 10, 'MT' => 11, 'MS' => 12, 'MG' => 13, 'PA' => 14,
        'PB' => 15, 'PR' => 16, 'PE' => 17, 'PI' => 18, 'RJ' => 19, 'RN' => 20, 'RS' => 21,
        'RO' => 22, 'RR' => 23, 'SC' => 24, 'SP' => 25, 'SE' => 26, 'TO' => 27
    ];
    
    while (($data = fgetcsv($handle, 1000, ';')) !== FALSE) {
        // Verificar se a linha tem dados válidos
        if (count($data) < 5 || empty($data[1]) || empty($data[3]) || empty($data[4])) {
            continue;
        }
        
        $codigoTom = trim($data[0]);
        $codigoIbge = trim($data[1]);
        $municipioTom = trim($data[2]);
        $municipioIbge = trim($data[3]);
        $uf = trim($data[4]);
        
        // Verificar se a UF existe no mapeamento
        if (!isset($ufToEstId[$uf])) {
            echo "UF não encontrada: $uf\n";
            $errors++;
            continue;
        }
        
        $estId = $ufToEstId[$uf];
        
        // Usar o nome do município do IBGE (mais padronizado)
        $nomeMunicipio = $municipioIbge;
        
        // Limpar e normalizar o nome do município
        $nomeMunicipio = trim($nomeMunicipio);
        $nomeMunicipio = mb_convert_case($nomeMunicipio, MB_CASE_TITLE, 'UTF-8');
        
        // Corrigir caracteres especiais comuns
        $nomeMunicipio = str_replace(['Ã¡', 'Ã©', 'Ã­', 'Ã³', 'Ãº', 'Ã¢', 'Ãª', 'Ã´', 'Ã£', 'Ã§'], 
                                    ['á', 'é', 'í', 'ó', 'ú', 'â', 'ê', 'ô', 'ã', 'ç'], $nomeMunicipio);
        
        try {
            $stmt->execute([$estId, $nomeMunicipio, $codigoIbge]);
            $imported++;
            
            if ($imported % 1000 == 0) {
                echo "Importados: $imported municípios\n";
            }
            
        } catch (PDOException $e) {
            // Se for erro de duplicata, apenas pular
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                continue;
            }
            echo "Erro ao importar $nomeMunicipio: " . $e->getMessage() . "\n";
            $errors++;
        }
    }
    
    fclose($handle);
    
    // Limpar arquivo temporário se foi criado
    if (file_exists('import/municipios.csv.utf8')) {
        unlink('import/municipios.csv.utf8');
        echo "Arquivo temporário removido.\n";
    }
    
    echo "\n=== RESUMO DA IMPORTAÇÃO ===\n";
    echo "Municípios importados: $imported\n";
    echo "Erros encontrados: $errors\n";
    echo "Importação concluída!\n";
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
?>
