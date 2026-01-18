<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

if (!function_exists('generate_qrcode')) {
    function generate_qrcode($text, $size = 5) {
        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => $size,
            'imageBase64' => false,
        ]);

        $qrcode = new QRCode($options);
        return $qrcode->render($text);
    }
} 