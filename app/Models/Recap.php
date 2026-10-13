<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lembar rekapan manual admin (mirip spreadsheet).
 *
 * `columns`: daftar kolom, tiap kolom { key, label, type: text|number, total: bool }.
 *            `total` menandai kolom angka yang ikut dijumlah di grand total.
 * `rows`:    daftar baris, tiap baris { key, cells: { <columnKey>: value } }.
 *
 * Disimpan sebagai JSON supaya admin bebas menambah/menghapus field tanpa migrasi.
 */
class Recap extends Model
{
    protected $fillable = ['name', 'columns', 'rows'];

    protected function casts(): array
    {
        return [
            'columns' => 'array',
            'rows' => 'array',
        ];
    }

    /**
     * Jumlah per kolom yang ditandai `total`, plus grand total keseluruhan.
     *
     * @return array{perColumn: array<string, float>, grand: float}
     */
    public function totals(): array
    {
        $perColumn = [];

        foreach ($this->columns ?? [] as $col) {
            if (($col['type'] ?? 'text') !== 'number' || empty($col['total'])) {
                continue;
            }

            $sum = 0.0;
            foreach ($this->rows ?? [] as $row) {
                $sum += (float) ($row['cells'][$col['key']] ?? 0);
            }
            $perColumn[$col['key']] = $sum;
        }

        return ['perColumn' => $perColumn, 'grand' => array_sum($perColumn)];
    }

    /**
     * Normalisasi input kolom dari form: buang label kosong, pastikan key unik.
     *
     * @param  array<int, array<string, mixed>>  $columns
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeColumns(array $columns): array
    {
        $out = [];
        foreach ($columns as $i => $col) {
            $label = trim((string) ($col['label'] ?? ''));
            if ($label === '') {
                continue;
            }

            $key = preg_replace('/[^a-zA-Z0-9_]/', '', (string) ($col['key'] ?? '')) ?: 'c'.($i + 1);

            $out[] = [
                'key' => $key,
                'label' => $label,
                'type' => ($col['type'] ?? 'text') === 'number' ? 'number' : 'text',
                'total' => ! empty($col['total']),
            ];
        }

        return $out;
    }

    /**
     * Susun ulang baris agar hanya menyimpan key kolom yang masih ada.
     *
     * @param  array<int, array<string, mixed>>  $columns
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<int, array<string, mixed>>
     */
    public static function normalizeRows(array $columns, array $rows): array
    {
        $keys = array_column($columns, 'key');
        $out = [];

        foreach ($rows as $i => $row) {
            $cells = [];
            foreach ($keys as $key) {
                $cells[$key] = $row['cells'][$key] ?? '';
            }

            // Lewati baris yang benar-benar kosong.
            $hasContent = collect($cells)->contains(fn ($v) => trim((string) $v) !== '');
            if (! $hasContent) {
                continue;
            }

            $out[] = ['key' => (string) ($row['key'] ?? 'r'.($i + 1)), 'cells' => $cells];
        }

        return $out;
    }
}
