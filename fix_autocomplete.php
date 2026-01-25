<?php
// Script para corrigir o autocomplete adicionando ten_id

$file = 'c:/xampp/htdocs/mapos/application/controllers/SimuladorTributacao.php';
$content = file_get_contents($file);

// Correção 1: Adicionar ten_id no autoCompleteCliente
$search1 = '// Query direta
            $termo_like = "%{$termo}%";
            $sql = "SELECT idClientes as id, nomeCliente as label, nomeCliente as value, 
                    estado, natureza_contribuinte, objetivo_comercial 
                    FROM clientes 
                    WHERE nomeCliente LIKE ? OR documento LIKE ?
                    LIMIT 10";
            
            $query = $this->db->query($sql, [$termo_like, $termo_like]);';

$replace1 = '// Pegar tenant_id da sessão
            $ten_id = $this->session->userdata(\'ten_id\');
            
            // Query direta com tenant
            $termo_like = "%{$termo}%";
            $sql = "SELECT idClientes as id, nomeCliente as label, nomeCliente as value, 
                    estado, natureza_contribuinte, objetivo_comercial 
                    FROM clientes 
                    WHERE ten_id = ? AND (nomeCliente LIKE ? OR documento LIKE ?)
                    LIMIT 10";
            
            $query = $this->db->query($sql, [$ten_id, $termo_like, $termo_like]);';

$content = str_replace($search1, $replace1, $content);

// Correção 2: Adicionar ten_id no autoCompleteProduto
$search2 = '// Query direta
            $termo_like = "%{$termo}%";
            $sql = "SELECT idProdutos as id, descricao as label, descricao as value, precoVenda
                    FROM produtos 
                    WHERE descricao LIKE ? OR codDeBarra LIKE ?
                    LIMIT 10";
            
            $query = $this->db->query($sql, [$termo_like, $termo_like]);';

$replace2 = '// Pegar tenant_id da sessão
            $ten_id = $this->session->userdata(\'ten_id\');
            
            // Query direta com tenant
            $termo_like = "%{$termo}%";
            $sql = "SELECT idProdutos as id, descricao as label, descricao as value, precoVenda
                    FROM produtos 
                    WHERE ten_id = ? AND (descricao LIKE ? OR codDeBarra LIKE ?)
                    LIMIT 10";
            
            $query = $this->db->query($sql, [$ten_id, $termo_like, $termo_like]);';

$content = str_replace($search2, $replace2, $content);

// Salvar arquivo
file_put_contents($file, $content);

echo "Arquivo corrigido com sucesso!\n";
?>