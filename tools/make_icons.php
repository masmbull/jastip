<?php

/**
 * Generator favicon/PWA icon dari satu gambar sumber (JPG/PNG).
 *
 * Dipakai sekali untuk membuat set ikon di public/images/icons dari
 * logo brand. Jalankan: php tools/make_icons.php <sumber> [tujuan]
 */
$src = $argv[1] ?? '2bcf5a61-fb6d-45ff-98cc-76d0269db680.jpg';
$outDir = $argv[2] ?? __DIR__.'/../public/images/icons';

if (! is_file($src)) {
    fwrite(STDERR, "Sumber tidak ditemukan: {$src}\n");
    exit(1);
}

$info = getimagesize($src);
$image = match ($info['mime']) {
    'image/jpeg' => imagecreatefromjpeg($src),
    'image/png' => imagecreatefrompng($src),
    'image/webp' => imagecreatefromwebp($src),
    default => null,
};

if (! $image) {
    fwrite(STDERR, "Format tidak didukung: {$info['mime']}\n");
    exit(1);
}

$w = imagesx($image);
$h = imagesy($image);
$side = min($w, $h);

// Latar untuk ikon maskable/apple: warna pojok gambar (biar menyatu).
$corner = imagecolorat($image, 2, 2);
$br = ($corner >> 16) & 0xFF;
$bg = ($corner >> 8) & 0xFF;
$bb = $corner & 0xFF;

if (! is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}

/**
 * @param  bool  $padded  true = gambar dikecilkan (80%) lalu ditaruh di tengah
 *                        latar solid. Dipakai untuk maskable & apple-touch-icon
 *                        supaya tidak terpotong saat dibulatkan OS.
 */
function makeIcon($image, int $size, string $path, int $bgR, int $bgG, int $bgB, bool $padded): void
{
    $canvas = imagecreatetruecolor($size, $size);

    if ($padded) {
        $bgColor = imagecolorallocate($canvas, $bgR, $bgG, $bgB);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $bgColor);
        $inner = (int) round($size * 0.8);
        $offset = (int) round(($size - $inner) / 2);
        imagecopyresampled($canvas, $image, $offset, $offset, 0, 0, $inner, $inner, imagesx($image), imagesy($image));
    } else {
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefilledrectangle($canvas, 0, 0, $size, $size, $transparent);
        imagealphablending($canvas, true);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));
    }

    imagepng($canvas, $path);
    imagedestroy($canvas);
    echo "  {$path} ({$size}x{$size})\n";
}

echo "Membuat ikon dari {$src} ({$w}x{$h}) ke {$outDir}\n";

makeIcon($image, 16, "{$outDir}/favicon-16.png", $br, $bg, $bb, true);
makeIcon($image, 32, "{$outDir}/favicon-32.png", $br, $bg, $bb, true);
makeIcon($image, 180, "{$outDir}/apple-touch-icon.png", $br, $bg, $bb, true);
makeIcon($image, 192, "{$outDir}/icon-192.png", $br, $bg, $bb, false);
makeIcon($image, 512, "{$outDir}/icon-512.png", $br, $bg, $bb, false);
makeIcon($image, 192, "{$outDir}/maskable-192.png", $br, $bg, $bb, true);
makeIcon($image, 512, "{$outDir}/maskable-512.png", $br, $bg, $bb, true);

imagedestroy($image);
echo "Selesai.\n";
