<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_usuarios_super_table extends CI_Migration {

    public function up()
    {
        // Criar tabela usuarios_super
        $this->dbforge->add_field(array(
            'uss_id' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'uss_nome' => array(
                'type' => 'VARCHAR',
                'constraint' => '80',
                'null' => FALSE,
            ),
            'uss_rg' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'uss_cpf' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => FALSE,
            ),
            'uss_email' => array(
                'type' => 'VARCHAR',
                'constraint' => '80',
                'null' => FALSE,
            ),
            'uss_senha' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => FALSE,
            ),
            'uss_telefone' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => FALSE,
            ),
            'uss_celular' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'uss_situacao' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 1,
            ),
            'uss_data_cadastro' => array(
                'type' => 'date',
                'null' => FALSE,
            ),
            'uss_data_expiracao' => array(
                'type' => 'date',
                'null' => TRUE,
            ),
            'uss_url_image_user' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
        ));
        
        $this->dbforge->add_key('uss_id', TRUE);
        $this->dbforge->create_table('usuarios_super', TRUE);
        
        // Criar índice único para email
        $this->db->query('ALTER TABLE `usuarios_super` ADD UNIQUE INDEX `uk_usuarios_super_email` (`uss_email`)');
        
        // Inserir usuário super padrão (senha: admin123)
        // Gerar hash da senha
        $senha_hash = password_hash('admin123', PASSWORD_DEFAULT);
        
        // Verificar se o hash foi gerado corretamente
        if (!$senha_hash || strlen($senha_hash) < 60) {
            log_message('error', 'Erro ao gerar hash da senha do super usuário');
            // Usar hash pré-gerado como fallback
            $senha_hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy';
        }
        
        $this->db->insert('usuarios_super', array(
            'uss_nome' => 'Administrador Super',
            'uss_cpf' => '000.000.000-00',
            'uss_email' => 'admin@super.com',
            'uss_senha' => $senha_hash,
            'uss_telefone' => '(00) 0000-0000',
            'uss_situacao' => 1,
            'uss_data_cadastro' => date('Y-m-d'),
        ));
    }

    public function down()
    {
        $this->dbforge->drop_table('usuarios_super', TRUE);
    }
}

