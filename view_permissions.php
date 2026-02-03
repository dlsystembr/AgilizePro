<?php
// Visualizador de Permissões do Banco de Dados
$conn = new mysqli('localhost', 'root', '', 'mapos');

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

echo "<h2>Permissões dos Perfis</h2>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
    .active { color: green; font-weight: bold; }
    .inactive { color: red; }
    .search { margin: 20px 0; padding: 10px; width: 300px; }
</style>";

// Buscar todos os perfis
$sql = "SELECT idPermissao, nome, permissoes FROM permissoes ORDER BY idPermissao";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($perfil = $result->fetch_assoc()) {
        // Tentar diferentes métodos de deserialização
        $perms = false;

        // Método 1: unserialize direto
        $perms = @unserialize($perfil['permissoes']);

        // Método 2: tentar com trim
        if ($perms === false) {
            $perms = @unserialize(trim($perfil['permissoes']));
        }

        // Método 3: tentar corrigir encoding
        if ($perms === false) {
            $clean = mb_convert_encoding($perfil['permissoes'], 'UTF-8', 'UTF-8');
            $perms = @unserialize($clean);
        }

        if ($perms === false || !is_array($perms)) {
            echo "<div style='background: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid red;'>";
            echo "<strong>Perfil: {$perfil['nome']} (ID: {$perfil['idPermissao']})</strong><br>";
            echo "Erro ao decodificar permissões!<br>";
            echo "<small>Tamanho: " . strlen($perfil['permissoes']) . " bytes</small><br>";
            echo "<details><summary>Ver dados brutos (primeiros 500 caracteres)</summary>";
            echo "<pre>" . htmlspecialchars(substr($perfil['permissoes'], 0, 500)) . "...</pre>";
            echo "</details>";
            echo "</div>";
            continue;
        }

        echo "<h3>Perfil: {$perfil['nome']} (ID: {$perfil['idPermissao']})</h3>";
        echo "<p><strong>Total de permissões:</strong> " . count($perms) . "</p>";

        // Verificar cSistema
        if (isset($perms['cSistema'])) {
            $status = $perms['cSistema'] === '1' ? 'ATIVA ✓' : 'INATIVA ✗';
            $color = $perms['cSistema'] === '1' ? 'green' : 'red';
            echo "<p style='font-size: 16px;'><strong>Permissão cSistema:</strong> <span style='color: $color;'>$status</span></p>";
        } else {
            echo "<p style='font-size: 16px; color: orange;'><strong>Permissão cSistema:</strong> NÃO ENCONTRADA</p>";
        }

        // Filtro de busca
        echo "<input type='text' class='search' id='search_{$perfil['idPermissao']}' placeholder='Buscar permissão...' onkeyup='filterTable({$perfil['idPermissao']})'>";

        // Tabela de permissões
        echo "<table id='table_{$perfil['idPermissao']}'>";
        echo "<thead><tr><th>Código</th><th>Tipo</th><th>Módulo</th><th>Status</th></tr></thead>";
        echo "<tbody>";

        ksort($perms);

        foreach ($perms as $code => $value) {
            $prefix = substr($code, 0, 1);
            $module = substr($code, 1);

            $types = [
                'v' => 'Visualizar',
                'a' => 'Adicionar',
                'e' => 'Editar',
                'd' => 'Deletar',
                'c' => 'Configurar',
                'r' => 'Relatório'
            ];

            $type = $types[$prefix] ?? 'Outro';
            $status = $value === '1' ? '✓ Ativo' : '✗ Inativo';
            $class = $value === '1' ? 'active' : 'inactive';

            echo "<tr>";
            echo "<td><strong>$code</strong></td>";
            echo "<td>$type</td>";
            echo "<td>$module</td>";
            echo "<td class='$class'>$status</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";

        // Estatísticas
        $ativas = count(array_filter($perms, fn($v) => $v === '1'));
        $inativas = count($perms) - $ativas;

        echo "<p><strong>Estatísticas:</strong> $ativas ativas | $inativas inativas</p>";
        echo "<hr>";
    }
} else {
    echo "<p>Nenhum perfil encontrado.</p>";
}

$conn->close();
?>

<script>
    function filterTable(perfil_id) {
        const input = document.getElementById('search_' + perfil_id);
        const filter = input.value.toUpperCase();
        const table = document.getElementById('table_' + perfil_id);
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[0];
            if (td) {
                const txtValue = td.textContent || td.innerText;
                tr[i].style.display = txtValue.toUpperCase().indexOf(filter) > -1 ? '' : 'none';
            }
        }
    }
</script>