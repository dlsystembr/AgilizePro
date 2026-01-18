<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QRCodeGenerator {
    
    public function generate($text, $size = 200) {
        if (empty($text)) {
            return '';
        }

        try {
            // Configurações do QR Code
            $options = new QROptions([
                'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                'eccLevel' => QRCode::ECC_L,
                'scale' => 5,
                'imageBase64' => false,
                'addQuietzone' => true,
                'quietzoneSize' => 4,
                'moduleValues' => [
                    // finder
                    1536 => '#000000', // dark (true)
                    6    => '#FFFFFF', // light (false)
                    // alignment
                    2560 => '#000000',
                    10   => '#FFFFFF',
                    // timing
                    3072 => '#000000',
                    12   => '#FFFFFF',
                    // format
                    3584 => '#000000',
                    14   => '#FFFFFF',
                    // version
                    4096 => '#000000',
                    16   => '#FFFFFF',
                    // data
                    1024 => '#000000',
                    4    => '#FFFFFF',
                    // darkmodule
                    512  => '#000000',
                    // separator
                    8    => '#FFFFFF',
                    // quietzone
                    18   => '#FFFFFF',
                ],
            ]);

            // Gera o QR Code
            $qrcode = new QRCode($options);
            return $qrcode->render($text);
        } catch (Exception $e) {
            log_message('error', 'Erro ao gerar QR Code: ' . $e->getMessage());
            return '';
        }
    }
} 