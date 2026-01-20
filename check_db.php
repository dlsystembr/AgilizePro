<?php
require_once 'index.php';
$CI =& get_instance();
$fields = $CI->db->field_data('nfecom_capa');
foreach ($fields as $field) {
    echo $field->name . "\n";
}
