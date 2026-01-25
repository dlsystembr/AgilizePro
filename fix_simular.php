<?php
$file = 'c:/xampp/htdocs/mapos/application/controllers/SimuladorTributacao.php';
$content = file_get_contents($file);

// Substituir a busca do cliente
$old_cliente = '$cliente = $this->Clientes_model->getById($cliente_id);';
$new_cliente = '// Buscar dados do cliente com JOIN
        $this->db->select(\'c.CLN_ID, c.CLN_OBJETIVO_COMERCIAL, p.PES_NOME, p.PES_CPFCNPJ\');
        $this->db->from(\'clientes c\');
        $this->db->join(\'pessoas p\', \'c.PES_ID = p.PES_ID\');
        $this->db->where(\'c.CLN_ID\', $cliente_id);
        $query_cliente = $this->db->get();
        $cliente = $query_cliente->row();';

$content = str_replace($old_cliente, $new_cliente, $content);

// Substituir a busca do produto
$old_produto = '$produto = $this->Produtos_model->getById($produto_id);';
$new_produto = '// Buscar dados do produto
        $this->db->select(\'PRO_ID, PRO_DESCRICAO, PRO_PRECO_VENDA, TBP_ID\');
        $this->db->from(\'produtos\');
        $this->db->where(\'PRO_ID\', $produto_id);
        $query_produto = $this->db->get();
        $produto = $query_produto->row();';

$content = str_replace($old_produto, $new_produto, $content);

// Substituir referências aos campos do cliente
$content = str_replace('$cliente->nomeCliente', '$cliente->PES_NOME', $content);
$content = str_replace('$cliente->estado', '\'GO\'', $content); // Fixo por enquanto
$content = str_replace('$cliente->natureza_contribuinte', '\'Contribuinte ICMS\'', $content);
$content = str_replace('$cliente->objetivo_comercial', '($cliente->CLN_OBJETIVO_COMERCIAL ?: \'consumo\')', $content);

// Substituir referências aos campos do produto
$content = str_replace('$produto->descricao', '$produto->PRO_DESCRICAO', $content);
$content = str_replace('$produto->tributacao_produto_id', '$produto->TBP_ID', $content);

file_put_contents($file, $content);
echo "Método simular() corrigido com sucesso!\n";
?>