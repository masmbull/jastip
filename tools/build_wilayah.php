<?php

/**
 * One-off: generate database/data/wilayah.json dari dump Kepmendagri
 * (cahyadsn/wilayah, db/wilayah.sql) — difilter hanya kota di config/ekspedisi.php.
 *
 * Pakai: php tools/build_wilayah.php /path/ke/wilayah.sql
 */

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$sqlFile = $argv[1] ?? (sys_get_temp_dir().'/wilayah.sql');
if (! is_file($sqlFile)) {
    fwrite(STDERR, "File tidak ada: {$sqlFile}\n");
    exit(1);
}

// Peta induk -> anak: '11.01' => [['11.01.01','Bakongan'], ...]
$children = [];
$all = [];
$fh = fopen($sqlFile, 'r');
while (($line = fgets($fh)) !== false) {
    if (! preg_match("/^\('([\d.]+)','((?:[^']|'')*)'\)/", $line, $m)) {
        continue;
    }
    $kode = $m[1];
    $nama = str_replace("''", "'", $m[2]);
    $all[$kode] = $nama;
    $pos = strrpos($kode, '.');
    if ($pos !== false) {
        $children[substr($kode, 0, $pos)][] = [$kode, $nama];
    }
}
fclose($fh);

// Kelompokkan nama resmi (tanpa prefix Kota/Kabupaten) -> kode kab/kota.
$byBase = [];
foreach ($all as $kode => $nama) {
    $parts = explode('.', $kode);
    if (count($parts) !== 2) {
        continue; // hanya level kabupaten/kota
    }
    $base = preg_replace('/^(Kota|Kabupaten)\s+/i', '', $nama);
    $byBase[mb_strtolower($base)][] = ['kode' => $kode, 'nama' => $nama];
}

$configCities = array_keys(config('ekspedisi.cities'));

// Nama kota config -> daftar nama kab/kota resmi. Jakarta digabung 5 wilayah.
$extra = ['jakarta' => ['Kota Administrasi Jakarta Pusat', 'Kota Administrasi Jakarta Utara', 'Kota Administrasi Jakarta Barat', 'Kota Administrasi Jakarta Selatan', 'Kota Administrasi Jakarta Timur']];

// Kasus nama yang tidak mirip: kota config => [kode kab/kota, prefix kecamatan
// yang diambil (null = semua)]. Purwokerto = 4 kecamatan berprefix di Kab. Banyumas.
$special = [
    'solo' => [['33.72', null]],
    'timika' => [['94.04', null]],
    'purwokerto' => [['33.02', 'Purwokerto']],
];

$out = [];
$unmatched = [];
foreach ($configCities as $city) {
    $key = mb_strtolower($city);
    $kecFilter = null;
    if (isset($special[$key])) {
        $targets = [];
        foreach ($special[$key] as [$kode, $prefix]) {
            $targets[] = ['kode' => $kode, 'nama' => $all[$kode] ?? $kode];
            $kecFilter = $prefix;
        }
    } elseif (isset($extra[$key])) {
        $targets = [];
        foreach ($extra[$key] as $resmi) {
            foreach ($byBase[mb_strtolower(preg_replace('/^(Kota|Kabupaten)\s+/i', '', $resmi))] ?? [] as $row) {
                if ($row['nama'] === $resmi) {
                    $targets[] = $row;
                }
            }
        }
    } else {
        $targets = $byBase[$key] ?? [];
    }

    if (! $targets) {
        $unmatched[] = $city;
        continue;
    }

    // kecamatan => [kelurahan]. Kalau nama kecamatan tabrakan antar kab/kota
    // dalam satu kota, kasih label asal supaya tidak tertukar.
    $multi = count($targets) > 1;
    $map = [];
    foreach ($targets as $t) {
        $label = $multi ? preg_replace('/^(Kota|Kabupaten)\s+/i', '', $t['nama']) : null;
        foreach ($children[$t['kode']] ?? [] as [$kecKode, $kecNama]) {
            if ($kecFilter !== null && ! str_starts_with($kecNama, $kecFilter)) {
                continue;
            }
            $kec = $multi ? $kecNama.' ('.$label.')' : $kecNama;
            $map[$kec] ??= [];
            foreach ($children[$kecKode] ?? [] as [, $kelNama]) {
                $map[$kec][] = $kelNama;
            }
        }
    }

    ksort($map);
    $out[$city] = array_map(fn ($v) => array_values($v), $map);
}

$dest = __DIR__.'/../database/data/wilayah.json';
@mkdir(dirname($dest), 0755, true);
file_put_contents($dest, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

$sizeKb = round(filesize($dest) / 1024);
$kec = array_sum(array_map('count', $out));
$kel = array_sum(array_map(fn ($m) => array_sum(array_map('count', $m)), $out));
echo "Kota: ".count($out).' | Kecamatan: '.$kec.' | Kelurahan: '.$kel." | {$sizeKb} KB\n";
if ($unmatched) {
    echo 'TIDAK COCOK: '.implode(', ', $unmatched)."\n";
}
