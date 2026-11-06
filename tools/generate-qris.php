<?php

/**
 * Generate a clean black-and-white QR code (SVG data URI) from a QRIS payload.
 * Used by the invoice template so the QR is scannable, undistorted and
 * free of decorative artwork/logo/text inside the code itself.
 *
 * Usage: php tools/generate-qris.php "payload"   (or reads docs/invoice-qa/qr-payload-decoded.txt)
 */
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $payload = $argv[1] ?? trim((string) file_get_contents(__DIR__ . '/../docs/invoice-qa/qr-payload-decoded.txt'));
    echo qris_qr_data_uri($payload), PHP_EOL;
}
