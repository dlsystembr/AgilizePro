<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_usuarios_super_table extends CI_Migration {

    public function up()
    {
        // Criar tabela usuarios_super
        $this->dbforge->add_field(array(
            'USS_ID' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ),
            'USS_NOME' => array(
                'type' => 'VARCHAR',
                'constraint' => '80',
                'null' => FALSE,
            ),
            'USS_RG' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'USS_CPF' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => FALSE,
            ),
            'USS_EMAIL' => array(
                'type' => 'VARCHAR',
                'constraint' => '80',
                'null' => FALSE,
            ),
            'USS_SENHA' => array(
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => FALSE,
            ),
            'USS_TELEFONE' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => FALSE,
            ),
            'USS_CELULAR' => array(
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => TRUE,
            ),
            'USS_SITUACAO' => array(
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => FALSE,
                'default' => 1,
            ),
            'USS_DATA_CADASTRO' => array(
                'type' => 'DATE',
                'null' => FALSE,
            ),
            'USS_DATA_EXPIRACAO' => array(
                'type' => 'DATE',
                'null' => TRUE,
            ),
            'USS_URL_IMAGE_USER' => array(
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => TRUE,
            ),
        ));
        
        $this->dbforge->add_key('USS_ID', TRUE);
        $this->dbforge->create_table('usuarios_super', TRUE);
        
        // Criar índice único para email
        $this->db->query('ALTER TABLE `usuarios_super` ADD UNIQUE INDEX `uk_usuarios_super_email` (`USS_EMAIL`)');
        
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
            'USS_NOME' => 'Administrador Super',
            'USS_CPF' => '000.000.000-00',
            'USS_EMAIL' => 'admin@super.com',
            'USS_SENHA' => $senha_hash,
            'USS_TELEFONE' => '(00) 0000-0000',
            'USS_SITUACAO' => 1,
            'USS_DATA_CADASTRO' => date('Y-m-d'),
        ));
    }

    public function down()
    {
        $this->dbforge->drop_table('usuarios_super', TRUE);
    }
}

