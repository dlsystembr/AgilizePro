<?php
$file = 'c:/xampp/htdocs/mapos/application/models/ClassificacaoFiscal_model.php';
$content = file_get_contents($file);

// Substituir o SELECT com COALESCE pelo SELECT com nomes corretos
$old_select = "        \$this->db->select(
            'COALESCE(CLF_ID, id) as id,
             COALESCE(OPC_ID, `OPC_ID`, operacao_comercial_id) as operacao_comercial_id,
             COALESCE(CLF_CST, cst) as cst,
             COALESCE(CLF_CSOSN, csosn) as csosn,
             COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte) as natureza_contribuinte,
             COALESCE(CLF_CFOP, cfop) as cfop,
             COALESCE(CLF_DESTINACAO, destinacao) as destinacao,
             COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial) as objetivo_comercial,
             COALESCE(CLF_TIPO_ICMS, NULL) as tipo_icms'
        );";

$new_select = "        \$this->db->select(
            'CLF_ID as id,
             OPC_ID as operacao_comercial_id,
             CLF_CST as cst,
             CLF_CSOSN as csosn,
             CLF_NATUREZA_CONTRIB as natureza_contribuinte,
             CLF_CFOP as cfop,
             CLF_DESTINACAO as destinacao,
             CLF_OBJETIVO_COMERCIAL as objetivo_comercial,
             CLF_TIPO_ICMS as tipo_icms'
        );";

$content = str_replace($old_select, $new_select, $content);

// Também corrigir o SELECT do debug (linha ~249)
$old_debug_select = "\$this->db->select('COALESCE(CLF_ID, id) as CLF_ID, COALESCE(OPC_ID, OPC_ID, operacao_comercial_id) as OPC_ID, COALESCE(CLF_NATUREZA_CONTRIB, natureza_contribuinte) as CLF_NATUREZA_CONTRIB, COALESCE(CLF_DESTINACAO, destinacao) as CLF_DESTINACAO, COALESCE(CLF_OBJETIVO_COMERCIAL, objetivo_comercial) as CLF_OBJETIVO_COMERCIAL, COALESCE(CLF_CST, cst) as CLF_CST, COALESCE(CLF_CFOP, cfop) as CLF_CFOP');";

$new_debug_select = "\$this->db->select('CLF_ID, OPC_ID, CLF_NATUREZA_CONTRIB, CLF_DESTINACAO, CLF_OBJETIVO_COMERCIAL, CLF_CST, CLF_CFOP');";

$content = str_replace($old_debug_select, $new_debug_select, $content);

file_put_contents($file, $content);
echo "ClassificacaoFiscal_model SELECT corrigido!\n";
echo "Agora usa apenas os nomes corretos das colunas CLF_*\n";
?>