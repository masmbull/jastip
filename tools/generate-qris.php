<?php

/**
 * Generate a clean black-and-white QR code (SVG data URI) from a QRIS payload.
 * Used by the invoice template so the QR is scannable, undistorted and
 * free of decorative artwork/logo/text inside the code itself.
 *
 * Usage: php tools/generate-qris.php "payload"
 */
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/helpers.php';

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    $payload = trim((string) ($argv[1] ?? ''));
    if ($payload === '') {
        fwrite(STDERR, "Usage: php tools/generate-qris.php \"payload\"\n");
        exit(1);
    }
    echo qris_qr_data_uri($payload), PHP_EOL;
}
