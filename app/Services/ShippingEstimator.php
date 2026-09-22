<?php

namespace App\Services;

/**
 * Estimator ongkir berbasis tarif referensi di config/ekspedisi.php.
 *
 * Dipakai halaman /ongkir untuk hitung cepat: berat + kota tujuan + ekspedisi.
 * Rumus: max(tarif minimum, tarif/kg × berat terbulatkan × faktor zona),
 * lalu dibulatkan ke atas ke kelipatan Rp 500.
 *
 * Untuk tarif live per kecamatan, sambungkan salah satu API di config
 * ('free_apis') — misalnya api.co.id Cek Ongkir v2 (10 kurir, 1 request).
 */
class ShippingEstimator
{
    /**
     * Data asal pengiriman.
     */
    public function origin(): array
    {
        return config('ekspedisi.origin', ['city' => 'Jakarta', 'province' => 'DKI Jakarta']);
    }

    /**
     * Kota tujuan: nama => ['zone' => int, 'province' => string] (urut A–Z).
     */
    public function cities(): array
    {
        $cities = config('ekspedisi.cities', []);
        ksort($cities);

        return $cities;
    }

    /**
     * Kota dikelompokkan per zona, untuk tampilan info.
     */
    public function citiesByZone(): array
    {
        $grouped = [];

        foreach ($this->cities() as $name => $meta) {
            $grouped[$meta['zone']][] = $name;
        }

        ksort($grouped);

        return $grouped;
    }

    public function zoneLabels(): array
    {
        return config('ekspedisi.zones', []);
    }

    public function zoneFactors(): array
    {
        return config('ekspedisi.zone_factors', []);
    }

    /**
     * Semua ekspedisi (untuk tabel referensi).
     */
    public function couriers(): array
    {
        return config('ekspedisi.couriers', []);
    }

    /**
     * Ekspedisi dikelompokkan per tipe: reguler, kargo, instant.
     */
    public function couriersByType(): array
    {
        $grouped = [];

        foreach ($this->couriers() as $courier) {
            $grouped[$courier['type']][] = $courier;
        }

        return $grouped;
    }

    public function courier(string $code): ?array
    {
        foreach ($this->couriers() as $courier) {
            if ($courier['code'] === $code) {
                return $courier;
            }
        }

        return null;
    }

    public function typeLabel(string $type): string
    {
        return match ($type) {
            'kargo' => 'Kargo',
            'instant' => 'Instant / Same Day',
            default => 'Reguler',
        };
    }

    public function trackingAggregator(): array
    {
        return config('ekspedisi.tracking_aggregator', [
            'name' => 'CekResi',
            'url' => 'https://cekresi.com/?noresi=%s',
        ]);
    }

    /**
     * Link lacak resi: template resmi kalau ada, ditambah aggregator CekResi
     * dan selalu disertai halaman resmi kurir.
     */
    public function trackingLinks(array $courier, ?string $awb = null): array
    {
        $links = [];
        $awb = $awb ? trim($awb) : null;

        if (!empty($courier['tracking_query']) && $awb) {
            $links[] = [
                'label' => 'Lacak di situs ' . $courier['name'],
                'url' => sprintf($courier['tracking_query'], urlencode($awb)),
            ];
        }

        if ($awb) {
            $aggregator = $this->trackingAggregator();
            $links[] = [
                'label' => 'Cek resi via ' . $aggregator['name'] . ' (' . $courier['name'] . ')',
                'url' => sprintf($aggregator['url'], urlencode($awb)),
            ];
        }

        if (!empty($courier['tracking_page'])) {
            $links[] = [
                'label' => 'Halaman resmi ' . $courier['name'],
                'url' => $courier['tracking_page'],
            ];
        }

        return $links;
    }

    /**
     * Hitung estimasi untuk satu ekspedisi.
     */
    public function estimate(string $courierCode, string $city, float $weightKg): array
    {
        $courier = $this->courier($courierCode);
        $cities = $this->cities();

        if (!$courier) {
            return ['ok' => false, 'message' => 'Ekspedisi tidak dikenal.'];
        }

        if (!isset($cities[$city])) {
            return ['ok' => false, 'message' => 'Kota tujuan belum terdaftar. Pilih dari daftar yang tersedia.'];
        }

        $weightKg = max(0.1, min($weightKg, 100));
        $zone = $cities[$city]['zone'];
        $minWeight = (float) ($courier['min_weight_kg'] ?? 1);

        $base = [
            'ok' => true,
            'courier' => $courier,
            'city' => $city,
            'province' => $cities[$city]['province'],
            'zone' => $zone,
            'zone_label' => $this->zoneLabels()[$zone] ?? ('Zona ' . $zone),
            'weight' => $weightKg,
            'min_weight_kg' => $minWeight,
            'tracking' => (bool) ($courier['tracking'] ?? false),
        ];

        // Ekspedisi instant hanya melayani zona tertentu (dalam kota/kota besar).
        if (!empty($courier['available_zones']) && !in_array($zone, $courier['available_zones'], true)) {
            return array_merge($base, [
                'ok' => false,
                'message' => $courier['name'] . ' belum melayani pengiriman ke ' . $city
                    . ' dari ' . $this->origin()['city'] . '.',
            ]);
        }

        // Pembulatan berat: kargo per 1 kg, lainnya per 0,5 kg (praktik kurir).
        $billable = $courier['type'] === 'kargo'
            ? max($minWeight, ceil($weightKg))
            : max($minWeight, ceil($weightKg * 2) / 2);

        $factor = $this->zoneFactors()[$zone] ?? 1.0;
        $raw = ($courier['per_kg'] * $billable) * $factor;
        $price = max((int) $courier['min_charge'], (int) ceil($raw));
        $price = (int) (ceil($price / 500) * 500);

        return array_merge($base, [
            'ok' => true,
            'billable_weight' => $billable,
            'price' => $price,
            'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.'),
            'per_kg_formatted' => 'Rp ' . number_format((int) $courier['per_kg'], 0, ',', '.') . '/kg',
            'etd' => $courier['etd'],
            'tracking_links' => $this->trackingLinks($courier),
            'message' => null,
        ]);
    }

    /**
     * Estimasi semua ekspedisi untuk rute & berat yang sama, diurutkan
     * dari yang termurah. Yang tidak melayani rute tetap dikembalikan
     * (ok = false) supaya bisa ditandai "tidak tersedia".
     */
    public function estimateAll(string $city, float $weightKg): array
    {
        $results = array_map(
            fn (array $courier) => $this->estimate($courier['code'], $city, $weightKg),
            $this->couriers()
        );

        usort($results, function ($a, $b) {
            if ($a['ok'] !== $b['ok']) {
                return $a['ok'] ? -1 : 1;
            }

            return ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX);
        });

        return $results;
    }

    /**
     * Sorotan: ekspedisi termurah & tercepat dari hasil estimateAll().
     */
    public function highlights(array $results): array
    {
        $available = array_values(array_filter($results, fn ($r) => $r['ok']));

        if (empty($available)) {
            return ['cheapest' => null, 'fastest' => null];
        }

        $cheapest = $available[0]; // sudah urut termurah
        $fastest = $cheapest;

        foreach ($available as $row) {
            $etd = mb_strtolower($row['etd']);

            if (str_contains($etd, 'hari yang sama') || str_contains($etd, 'jam')) {
                $fastest = $row;
                break;
            }
        }

        return ['cheapest' => $cheapest, 'fastest' => $fastest];
    }
}

