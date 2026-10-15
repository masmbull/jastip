<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Data wilayah (kota > kecamatan > kelurahan) dari database/data/wilayah.json.
 *
 * Struktur file: { "Jakarta": { "Cakung (Administrasi Jakarta Timur)": ["Jatinegara", ...] } }
 *
 * Dipakai untuk dropdown bertingkat di form invoice manual. File dibaca sekali
 * lalu di-cache (array besar, ~120KB), supaya tidak di-parse tiap request.
 */
class WilayahService
{
    private const CACHE_KEY = 'wilayah.data';

    /**
     * @return array<string, array<string, array<int, string>>>
     */
    public function all(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $path = database_path('data/wilayah.json');

            if (! is_file($path)) {
                return [];
            }

            $data = json_decode((string) file_get_contents($path), true);

            return is_array($data) ? $data : [];
        });
    }

    /**
     * Daftar kota (kunci teratas file).
     *
     * @return array<int, string>
     */
    public function cities(): array
    {
        return array_keys($this->all());
    }

    /**
     * Daftar kecamatan untuk satu kota.
     *
     * @return array<int, string>
     */
    public function districts(string $city): array
    {
        return array_keys($this->all()[$city] ?? []);
    }

    /**
     * Daftar kelurahan untuk satu kota + kecamatan.
     *
     * @return array<int, string>
     */
    public function villages(string $city, string $district): array
    {
        return $this->all()[$city][$district] ?? [];
    }
}
