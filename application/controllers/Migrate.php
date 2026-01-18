<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('migration');
        $this->load->database();
    }

    public function index() {
        try {
            // Verifica se a tabela migrations existe
            if (!$this->db->table_exists('migrations')) {
                echo "Criando tabela migrations...<br>";
                $this->dbforge->add_field([
                    'version' => [
                        'type' => 'BIGINT',
                        'constraint' => 20,
                    ],
                ]);
                $this->dbforge->create_table('migrations');
            }

            // Lista todas as migrations disponíveis
            $migrations = $this->migration->find_migrations();
            echo "Migrations encontradas:<br>";
            foreach ($migrations as $version => $migration) {
                echo "- $version: $migration<br>";
            }

            // Força a execução da migration mais recente
            $latest_migration = max(array_keys($migrations));
            echo "<br>Executando migration: $latest_migration<br>";
            
            if ($this->migration->version($latest_migration) === FALSE) {
                show_error($this->migration->error_string());
            } else {
                echo "<br>Migration executada com sucesso!<br>";
                
                // Verifica se a tabela marcas foi criada
                if ($this->db->table_exists('marcas')) {
                    echo "<br>Tabela 'marcas' criada com sucesso!<br>";
                    $fields = $this->db->list_fields('marcas');
                    echo "<br>Campos na tabela marcas:<br>";
                    foreach ($fields as $field) {
                        echo "- $field<br>";
                    }
                } else {
                    echo "<br>ERRO: Tabela 'marcas' não foi criada!<br>";
                }
            }
        } catch (Exception $e) {
            show_error('Erro ao executar migration: ' . $e->getMessage());
        }
    }

    public function rollback() {
        if ($this->migration->version(0) === FALSE) {
            show_error($this->migration->error_string());
        } else {
            echo 'Rollback executado com sucesso!';
        }
    }
} 