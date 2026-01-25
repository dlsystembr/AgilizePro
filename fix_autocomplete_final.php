<?php
// Script para corrigir o autocomplete com os nomes corretos das colunas

$file = 'c:/xampp/htdocs/mapos/application/controllers/SimuladorTributacao.php';
$content = file_get_contents($file);

// Correção 1: autoCompleteCliente com JOIN
$search1 = '// Autocomplete para clientes
    public function autoCompleteCliente()
    {
        header(\'Content-Type: application/json\');
        
        try {
            $termo = isset($_GET[\'term\']) ? $_GET[\'term\'] : \'\';
            
            if (empty($termo)) {
                echo json_encode([]);
                return;
            }
            
            // Pegar tenant_id da sessão
            $ten_id = $this->session->userdata(\'ten_id\');
            
            // Query direta com tenant
            $termo_like = "%{$termo}%";
            $sql = "SELECT idClientes as id, nomeCliente as label, nomeCliente as value, 
                    estado, natureza_contribuinte, objetivo_comercial 
                    FROM clientes 
                    WHERE ten_id = ? AND (nomeCliente LIKE ? OR documento LIKE ?)
                    LIMIT 10";
            
            $query = $this->db->query($sql, [$ten_id, $termo_like, $termo_like]);
            $clientes = $query->result();
            
            echo json_encode($clientes);
        } catch (Exception $e) {
            log_message(\'error\', \'Erro em autoCompleteCliente: \' . $e->getMessage());
            echo json_encode([]);
        }
    }';

$replace1 = '// Autocomplete para clientes
    public function autoCompleteCliente()
    {
        header(\'Content-Type: application/json\');
        
        try {
            $termo = $this->input->get(\'term\');
            
            if (empty($termo)) {
                echo json_encode([]);
                return;
            }
            
            // Pegar tenant_id da sessão
            $ten_id = $this->session->userdata(\'ten_id\');
            
            // Query com JOIN entre clientes e pessoas
            $this->db->select(\'c.CLN_ID as id, p.PES_NOME as label, p.PES_NOME as value, c.CLN_OBJETIVO_COMERCIAL as objetivo_comercial\');
            $this->db->from(\'clientes c\');
            $this->db->join(\'pessoas p\', \'c.PES_ID = p.PES_ID\');
            $this->db->where(\'c.ten_id\', $ten_id);
            $this->db->group_start();
            $this->db->like(\'p.PES_NOME\', $termo);
            $this->db->or_like(\'p.PES_CPFCNPJ\', $termo);
            $this->db->group_end();
            $this->db->limit(10);
            
            $query = $this->db->get();
            $clientes = $query->result();
            
            echo json_encode($clientes);
        } catch (Exception $e) {
            log_message(\'error\', \'Erro em autoCompleteCliente: \' . $e->getMessage());
            echo json_encode([]);
        }
    }';

$content = str_replace($search1, $replace1, $content);

// Correção 2: autoCompleteProduto
$search2 = '// Autocomplete para produtos
    public function autoCompleteProduto()
    {
        header(\'Content-Type: application/json\');
        
        try {
            $termo = isset($_GET[\'term\']) ? $_GET[\'term\'] : \'\';
            
            if (empty($termo)) {
                echo json_encode([]);
                return;
            }
            
            // Pegar tenant_id da sessão
            $ten_id = $this->session->userdata(\'ten_id\');
            
            // Query direta com tenant
            $termo_like = "%{$termo}%";
            $sql = "SELECT idProdutos as id, descricao as label, descricao as value, precoVenda
                    FROM produtos 
                    WHERE ten_id = ? AND (descricao LIKE ? OR codDeBarra LIKE ?)
                    LIMIT 10";
            
            $query = $this->db->query($sql, [$ten_id, $termo_like, $termo_like]);
            $produtos = $query->result();
            
            echo json_encode($produtos);
        } catch (Exception $e) {
            log_message(\'error\', \'Erro em autoCompleteProduto: \' . $e->getMessage());
            echo json_encode([]);
        }
    }';

$replace2 = '// Autocomplete para produtos
    public function autoCompleteProduto()
    {
        header(\'Content-Type: application/json\');
        
        try {
            $termo = $this->input->get(\'term\');
            
            if (empty($termo)) {
                echo json_encode([]);
                return;
            }
            
            // Pegar tenant_id da sessão
            $ten_id = $this->session->userdata(\'ten_id\');
            
            // Query com nomes corretos das colunas
            $this->db->select(\'PRO_ID as id, PRO_DESCRICAO as label, PRO_DESCRICAO as value, PRO_PRECO_VENDA as precoVenda\');
            $this->db->from(\'produtos\');
            $this->db->where(\'ten_id\', $ten_id);
            $this->db->group_start();
            $this->db->like(\'PRO_DESCRICAO\', $termo);
            $this->db->or_like(\'PRO_COD_BARRA\', $termo);
            $this->db->group_end();
            $this->db->limit(10);
            
            $query = $this->db->get();
            $produtos = $query->result();
            
            echo json_encode($produtos);
        } catch (Exception $e) {
            log_message(\'error\', \'Erro em autoCompleteProduto: \' . $e->getMessage());
            echo json_encode([]);
        }
    }';

$content = str_replace($search2, $replace2, $content);

// Salvar arquivo
file_put_contents($file, $content);

echo "Arquivo corrigido com nomes corretos das colunas!\n";
echo "Clientes: CLN_ID, PES_NOME (JOIN com pessoas)\n";
echo "Produtos: PRO_ID, PRO_DESCRICAO, PRO_PRECO_VENDA\n";
?>