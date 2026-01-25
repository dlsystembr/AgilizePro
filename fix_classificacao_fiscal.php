<?php
$file = 'c:/xampp/htdocs/mapos/application/models/ClassificacaoFiscal_model.php';
$content = file_get_contents($file);

// Substituir os WHEREs com COALESCE pelos nomes corretos das colunas
$content = str_replace(
    "\$this->db->where('COALESCE(OPC_ID, OPC_ID, operacao_comercial_id)', \$operacao_id);",
    "\$this->db->where('OPC_ID', \$operacao_id);",
    $content
);

$content = str_replace(
    "\$this->db->where('COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte)', \$natureza_contribuinte);",
    "\$this->db->where('CLF_NATUREZA_CONTRIB', \$natureza_contribuinte);",
    $content
);

$content = str_replace(
    "\$this->db->where('COALESCE(CLF_DESTINACAO, destinacao)', \$destinacao);",
    "\$this->db->where('CLF_DESTINACAO', \$destinacao);",
    $content
);

$content = str_replace(
    "\$this->db->where('COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial)', \$objetivo_comercial);",
    "\$this->db->where('CLF_OBJETIVO_COMERCIAL', \$objetivo_comercial);",
    $content
);

file_put_contents($file, $content);
echo "ClassificacaoFiscal_model corrigido!\n";
echo "WHEREs agora usam nomes corretos das colunas.\n";
?>