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
            if (! self::isNumericType($col['type'] ?? 'text') || empty($col['total'])) {
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

    public static function isNumericType(string $type): bool
    {
        return in_array($type, ['number', 'currency'], true);
    }

    /**
     * Susun data invoice untuk SATU baris rekapan.
     *
     * Dipakai tombol "Kirim Invoice via WhatsApp" di halaman Rekapan. Nilai
     * finansial dihitung di server (bukan di JS) memakai aturan kolom yang
     * sama dengan totals(), supaya angka yang dikirim konsisten dengan tabel.
     * Kolom dikenali lewat label (mis. "Pelanggan", "Qty", "Harga", "DP"),
     * sehingga template rekap apa pun tetap jalan tanpa kolom baru.
     *
     * @return array<string, mixed>
     */
    public function invoiceForRow(array $row, int $index): array
    {
        $cells = (array) ($row['cells'] ?? []);
        $cols = collect($this->columns ?? [])->keyBy('key');

        $find = function (array $patterns) use ($cells, $cols): array {
            foreach ($cols as $key => $col) {
                $label = mb_strtolower((string) ($col['label'] ?? ''));
                foreach ($patterns as $p) {
                    if ($label !== '' && preg_match($p, $label)) {
                        return ['key' => $key, 'label' => $col['label'], 'value' => $cells[$key] ?? ''];
                    }
                }
            }

            return ['key' => null, 'label' => null, 'value' => ''];
        };

        $customer = $find(['/pelanggan|customer|pembeli|nama/']);
        $wa = $find(['/\bwa\b|whatsapp|telp|telepon|\bhp\b|no\.?\s*hp/']);
        $product = $find(['/produk|barang|item/']);
        $qty = $find(['/\bqty\b|jumlah|banyak/']);
        $price = $find(['/harga|price|jual|modal|nominal/']);
        $dpField = $find(['/\bdp\b|deposit|down\s*payment|panjar/']);

        // Total baris = jumlah kolom angka yang ditandai "total" (aturan sama
        // dengan totals()), agar konsisten dengan Grand Total di tabel.
        $total = 0.0;
        $feeTotal = 0.0;
        foreach ($this->columns ?? [] as $col) {
            if (! self::isNumericType($col['type'] ?? 'text')) {
                continue;
            }
            $value = (float) ($cells[$col['key']] ?? 0);
            if (! empty($col['total'])) {
                $total += $value;
            }
        }

        $dp = (float) $dpField['value'];
        $sisa = max(0, $total - $dp);

        $items = [];
        if ($product['value'] !== '' || $qty['value'] !== '' || $price['value'] !== '') {
            $unitPrice = (float) $price['value'];
            $qtyValue = (float) $qty['value'] ?: 1;
            $items[] = [
                'name' => (string) $product['value'],
                'qty' => $qtyValue,
                'price' => format_price((int) $unitPrice),
                'subtotal' => format_price((int) ($unitPrice * $qtyValue)),
            ];
        }

        return [
            'row' => $index,
            'brand' => setting('brand_name', 'NITIP DI END'),
            'footer' => 'Terima kasih telah menitip di '.setting('brand_name', 'NITIP DI END').'.',
            'number' => $this->name,
            'date' => optional($this->updated_at)->format('d M Y, H:i') ?? now()->format('d M Y, H:i'),
            'customer_name' => (string) $customer['value'] ?: 'Pelanggan',
            'wa' => (string) $wa['value'],
            'items' => $items,
            'total' => format_price((int) $total),
            'dp' => format_price((int) $dp),
            'sisa' => format_price((int) $sisa),
            'status' => $dp > 0 ? ($sisa <= 0 ? 'LUNAS' : 'DEPOSIT') : 'BELUM BAYAR',
        ];
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
                'type' => in_array($col['type'] ?? 'text', ['text', 'number', 'currency'], true) ? $col['type'] : 'text',
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
