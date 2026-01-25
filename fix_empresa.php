<?php
$file = 'c:/xampp/htdocs/mapos/application/controllers/SimuladorTributacao.php';
$content = file_get_contents($file);

// Substituir a busca do emitente
$old_emitente = '$emitente = $this->Mapos_model->getEmitente();
        if (!$emitente) {
            throw new Exception(\'Emitente não configurado.\');
        }';

$new_emitente = '// Buscar dados da empresa (igual ao NFCom)
        $empresa = $this->db->limit(1)->get(\'empresas\')->row();
        if (!$empresa) {
            throw new Exception(\'Nenhuma empresa configurada. Por favor, cadastre uma empresa.\');
        }
        
        // Usar UF da empresa
        $uf_emitente = $empresa->EMP_UF;';

$content = str_replace($old_emitente, $new_emitente, $content);

// Substituir referência ao emitente->uf
$content = str_replace('$emitente->uf', '$uf_emitente', $content);

file_put_contents($file, $content);
echo "Método simular() corrigido para usar tabela empresas!\n";
?>