<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Import_ncms_data extends CI_Migration
{
    public function up()
    {
        // Carregar o arquivo JSON
        $json_file = APPPATH . 'database/json/Tabela_NCM_Vigente_20250530.json';
        
        if (!file_exists($json_file)) {
            log_message('error', 'Arquivo JSON de NCMs não encontrado: ' . $json_file);
            return;
        }

        $json_content = file_get_contents($json_file);
        $ncms_data = json_decode($json_content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'Erro ao decodificar JSON de NCMs: ' . json_last_error_msg());
            return;
        }

        // Preparar os dados para inserção
        $batch_data = [];
        $now = date('Y-m-d H:i:s');

        foreach ($ncms_data as $ncm) {
            $batch_data[] = [
                'codigo' => $ncm['codigo'],
                'descricao' => $ncm['descricao'],
                'data_inicio' => $ncm['data_inicio'] ?? null,
                'data_fim' => $ncm['data_fim'] ?? null,
                'tipo_ato' => $ncm['tipo_ato'] ?? null,
                'numero_ato' => $ncm['numero_ato'] ?? null,
                'ano_ato' => $ncm['ano_ato'] ?? null,
                'created_at' => $now,
                'updated_at' => $now
            ];
        }

        // Inserir os dados em lotes
        if (!empty($batch_data)) {
            $this->db->insert_batch('ncms', $batch_data);
        }
    }

    public function down()
    {
        $this->db->empty_table('ncms');
    }
} 