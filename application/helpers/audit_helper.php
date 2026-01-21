<?php

// log info
function log_info($task)
{
    $ci = &get_instance();
    $ci->load->model('Audit_model');

    $data = [
        'ten_id' => $ci->session->userdata('ten_id') ?: 1,
        'usuario' => $ci->session->userdata('nome_admin'),
        'ip' => $ci->input->ip_address(),
        'tarefa' => $task,
        'data' => date('Y-m-d'),
        'hora' => date('H:i:s'),
    ];

    $ci->Audit_model->add($data);
}
