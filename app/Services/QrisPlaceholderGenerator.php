<?php

namespace App\Services;

/**
 * Generates a visible QRIS-style placeholder PNG (cream/charcoal/amber)
 * using GD. The image is NOT a real scannable QR code — it is a demo slot
 * so the public checkout flow has something to show before the admin
 * uploads their actual static QRIS at /admin/settings.
 */
class QrisPlaceholderGenerator
{
    public static function generate(string $path): void
    {
        $dir = dirname($path);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (! extension_loaded('gd')) {
            // Last-ditch: write a tiny black PNG so the file exists.
            file_put_contents($path, base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII='
            ));
            return;
        }

        // ---- Init GD canvas ----
        $merchant = defined('APP_NAME') ? env('APP_NAME', 'NITIP DI END') : 'NITIP DI END';
        $merchant = preg_replace('/[^a-zA-Z0-9 ]/', '', $merchant ?: 'NITIP DI END');
        $merchant = $merchant ?: 'NITIP DI END';

        $img = imagecreatetruecolor(640, 800);
        $cream    = imagecolorallocate($img, 245, 240, 232); // #f5f0e8
        $charcoal = imagecolorallocate($img, 26, 26, 26);    // #1a1a1a
        $amber    = imagecolorallocate($img, 245, 166, 35);  // #F5A623


        imagefilledrectangle($img, 0, 0, 640, 800, $cream);

        // Header band
        imagefilledrectangle($img, 0, 0, 640, 90, $charcoal);
        imagefilledrectangle($img, 0, 0, 8, 90, $amber);  // left accent stripe
        imagestring($img, 5, 32, 32, 'QRIS - ' . $merchant, $cream);
        imagefilledrectangle($img, 0, 84, 640, 90, $amber); // bottom header accent

        // Label kecil: placeholder
        $smallGrey = imagecolorallocate($img, 170, 162, 150);
        imagestring($img, 3, 32, 62, 'PLACEHOLDER DEMO', $smallGrey);

        // 25x25 module grid
        $startX = 70; $startY = 130; $cell = 20;
        $size = 25;
        mt_srand(20250921); // deterministic
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                // Skip finder pattern positions (top-left, top-right, bottom-left 7x7 zones)
                if (($y < 7 && $x < 7) || ($y < 7 && $x >= $size - 7) || ($y >= $size - 7 && $x < 7)) {
                    continue;
                }
                if (mt_rand(0, 1)) {
                    imagefilledrectangle(
                        $img,
                        $startX + $x * $cell,
                        $startY + $y * $cell,
                        $startX + $x * $cell + $cell - 2,
                        $startY + $y * $cell + $cell - 2,
                        $charcoal
                    );
                }
            }
        }

        // Watermark text over QR area
        $white = imagecolorallocate($img, 255, 255, 255);
        $semiWhite = imagecolorallocate($img, 210, 210, 210);
        // Big diagonal "PLACEHOLDER" text across the QR area
        $fontWidth = imagefontwidth(5);
        $fontHeight = imagefontheight(5);
        $text = 'DEMO';
        $textW = $fontWidth * strlen($text);
        $qrCenterX = $startX + ($size * $cell) / 2;
        $qrCenterY = $startY + ($size * $cell) / 2;
        // Rotate text 90deg and place as subtle watermark
        imagestring($img, 5, (int)($qrCenterX - ($textW / 2)), (int)($qrCenterY - ($fontHeight / 2)), $text, $semiWhite);

        // Finder patterns (3 corners) overlay — drawn AFTER modules so they stand out
        $finderPositions = [[0,0],[20,0],[0,20]];
        foreach ($finderPositions as [$fy, $fx]) {
            imagefilledrectangle($img, $startX + $fx*$cell, $startY + $fy*$cell, $startX + $fx*$cell + 7*$cell, $startY + $fy*$cell + 7*$cell, $cream);
            imagefilledrectangle($img, $startX + $fx*$cell + $cell, $startY + $fy*$cell + $cell, $startX + $fx*$cell + 6*$cell, $startY + $fy*$cell + 6*$cell, $charcoal);
            imagefilledrectangle($img, $startX + $fx*$cell + 2*$cell, $startY + $fy*$cell + 2*$cell, $startX + $fx*$cell + 5*$cell, $startY + $fy*$cell + 5*$cell, $cream);
            imagefilledrectangle($img, $startX + $fx*$cell + 3*$cell, $startY + $fy*$cell + 3*$cell, $startX + $fx*$cell + 4*$cell, $startY + $fy*$cell + 4*$cell, $charcoal);
        }

        // Footer banner
        imagefilledrectangle($img, 0, 660, 640, 760, $amber);
        imagestring($img, 5, 80, 690, 'QRIS PLACEHOLDER DEMO', $charcoal);
        imagestring($img, 3, 40, 720, 'QRIS asli ada di /admin/settings', $charcoal);

        // Decorative corner dots
        $dotColor = $cream;
        foreach ([[0,0],[639,0],[0,799],[639,799]] as [$dx, $dy]) {
            imagefilledellipse($img, $dx, $dy, 10, 10, $dotColor);
            imagefilledellipse($img, $dx, $dy, 6, 6, $amber);
        }

        imagepng($img, $path, 6);
        imagedestroy($img);
    }
}