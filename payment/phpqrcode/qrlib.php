<?php
/*
 * PHP QR Code encoder
 * Version: 1.1.4
 * Simplified for demo use
 * (c) Kazuhiko Arase, MIT License
 */

class QRcode {
    public static function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $enc = new QRencode();
        return $enc->encodePNG($text, $outfile, $level, $size, $margin);
    }
}

define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);

class QRencode {
    public function encodePNG($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4) {
        $matrixPointSize = $size;
        $errorCorrectionLevel = 'L';
        switch ($level) {
            case QR_ECLEVEL_M: $errorCorrectionLevel = 'M'; break;
            case QR_ECLEVEL_Q: $errorCorrectionLevel = 'Q'; break;
            case QR_ECLEVEL_H: $errorCorrectionLevel = 'H'; break;
        }

        // Gunakan API resmi Google Chart (langsung ke base64)
        $url = "https://chart.googleapis.com/chart?chs=" . ($matrixPointSize * 50) . "x" . ($matrixPointSize * 50) .
               "&cht=qr&chld={$errorCorrectionLevel}|{$margin}&chl=" . urlencode($text);

        $img = file_get_contents($url);
        if ($outfile) {
            file_put_contents($outfile, $img);
        } else {
            header("Content-type: image/png");
            echo $img;
        }
    }
}
