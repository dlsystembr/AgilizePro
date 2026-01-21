<?php
class TestTenant extends CI_Controller {
    public function index() {
        $this->load->database();
        if (!$this->db->table_exists('tenants')) {
             echo "Tabela tenants nao existe!";
             return;
        }
        $tenant = $this->db->get_where('tenants', ['ten_id' => 1])->row();
        if ($tenant) {
            echo "Tenant 1 existe: " . $tenant->ten_nome;
        } else {
            echo "Tenant 1 NAO existe. Tentando criar...";
            $this->db->query("INSERT INTO tenants (ten_id, ten_nome, ten_data_cadastro) VALUES (1, 'Matriz', NOW())");
            if ($this->db->affected_rows() > 0) {
                echo "Tenant 1 criado com sucesso.";
            } else {
                echo "Erro ao criar Tenant 1: " . $this->db->error()['message'];
            }
        }
    }
}
