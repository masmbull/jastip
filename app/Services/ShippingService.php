<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * ShippingService: manages courier pricing, enable/disable, and price sync.
 *
 * Default provider = "local" (gratis): sinkron dari estimator zona di
 * config/ekspedisi.php — tanpa API key. Opsional: api.co.id / rajaongkir
 * kalau admin isi key di /admin/ongkir.
 */
class ShippingService
{
    private const CACHE_KEY_COURIERS = 'shipping_couriers_enabled';

    private const CACHE_KEY_PRICES = 'shipping_prices_cache';

    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly ShippingEstimator $estimator
    ) {}

    /**
     * Get enabled couriers with custom pricing from database
     */
    public function enabledCouriers(): array
    {
        $cache = Cache::get(self::CACHE_KEY_COURIERS);
        if ($cache !== null) {
            return $cache;
        }

        $couriers = $this->estimator->couriers();
        $enabled = [];
        $livePrices = Cache::get(self::CACHE_KEY_PRICES, []);

        foreach ($couriers as $courier) {
            $code = $courier['code'];
            if (! $this->isCourierEnabled($code)) {
                continue;
            }

            $courier['per_kg'] = $this->effectivePerKg($code, $courier, $livePrices);
            $courier['min_charge'] = $this->effectiveMinCharge($code, $courier);
            $enabled[] = $courier;
        }

        Cache::put(self::CACHE_KEY_COURIERS, $enabled, self::CACHE_TTL);

        return $enabled;
    }

    public function cities(): array
    {
        return $this->estimator->cities();
    }

    public function enabledCouriersByType(): array
    {
        $grouped = [];

        foreach ($this->enabledCouriers() as $courier) {
            $grouped[$courier['type']][] = $courier;
        }

        return $grouped;
    }

    public function estimate(string $courierCode, string $city, float $weightKg): array
    {
        $courier = collect($this->enabledCouriers())->firstWhere('code', $courierCode);

        if (! $courier) {
            return ['ok' => false, 'message' => 'Ekspedisi tidak dikenal atau sedang nonaktif.'];
        }

        return $this->estimateWithCourier($courier, $city, $weightKg)
            ?? ['ok' => false, 'message' => 'Kota tujuan belum terdaftar. Pilih dari daftar yang tersedia.'];
    }

    /**
     * Get all couriers with enable/disable + effective pricing for admin table.
     */
    public function courierSettings(): array
    {
        $livePrices = Cache::get(self::CACHE_KEY_PRICES, []);
        $settings = [];

        foreach ($this->estimator->couriers() as $courier) {
            $code = $courier['code'];
            $settings[] = [
                'code' => $code,
                'name' => $courier['name'],
                'type' => $courier['type'],
                'enabled' => $this->isCourierEnabled($code),
                'per_kg' => $this->effectivePerKg($code, $courier, $livePrices),
                'min_charge' => $this->effectiveMinCharge($code, $courier),
                'default_per_kg' => (int) ($courier['per_kg'] ?? 0),
                'default_min_charge' => (int) ($courier['min_charge'] ?? 0),
                'source' => $this->priceSource($code, $livePrices),
            ];
        }

        return $settings;
    }

    public function toggleCourier(string $code, bool $enabled): void
    {
        Setting::set('courier_enabled_'.$code, $enabled ? 'true' : 'false', 'string', 'shipping', true);
        Cache::forget(self::CACHE_KEY_COURIERS);
    }

    public function updateCourierPricing(string $code, int $perKg, int $minCharge): void
    {
        Setting::set('courier_per_kg_'.$code, (string) $perKg, 'integer', 'shipping', true);
        Setting::set('courier_min_charge_'.$code, (string) $minCharge, 'integer', 'shipping', true);
        Cache::forget(self::CACHE_KEY_COURIERS);
    }

    public function estimateAllEnabled(string $city, float $weightKg): array
    {
        $results = [];

        foreach ($this->enabledCouriers() as $courier) {
            $result = $this->estimateWithCourier($courier, $city, $weightKg);
            if ($result) {
                $results[] = $result;
            }
        }

        usort($results, function ($a, $b) {
            if ($a['ok'] !== $b['ok']) {
                return $a['ok'] ? -1 : 1;
            }

            return ($a['price'] ?? PHP_INT_MAX) <=> ($b['price'] ?? PHP_INT_MAX);
        });

        return $results;
    }

    private function estimateWithCourier(array $courier, string $city, float $weightKg): ?array
    {
        $cities = $this->estimator->cities();

        if (! isset($cities[$city])) {
            return null;
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
            'zone_label' => $this->estimator->zoneLabels()[$zone] ?? ('Zona '.$zone),
            'weight' => $weightKg,
            'min_weight_kg' => $minWeight,
            'tracking' => (bool) ($courier['tracking'] ?? false),
        ];

        if (! empty($courier['available_zones']) && ! in_array($zone, $courier['available_zones'], true)) {
            return array_merge($base, [
                'ok' => false,
                'message' => $courier['name'].' belum melayani pengiriman ke '.$city.'.',
            ]);
        }

        $billable = $courier['type'] === 'kargo'
            ? max($minWeight, ceil($weightKg))
            : max($minWeight, ceil($weightKg * 2) / 2);

        $factor = $this->estimator->zoneFactors()[$zone] ?? 1.0;
        $raw = ($courier['per_kg'] * $billable) * $factor;
        $price = max((int) $courier['min_charge'], (int) ceil($raw));
        $price = (int) (ceil($price / 500) * 500);

        return array_merge($base, [
            'ok' => true,
            'billable_weight' => $billable,
            'price' => $price,
            'price_formatted' => 'Rp '.number_format($price, 0, ',', '.'),
            'per_kg_formatted' => 'Rp '.number_format((int) $courier['per_kg'], 0, ',', '.').'/kg',
            'etd' => $courier['etd'] ?? 'N/A',
            'tracking_links' => $this->estimator->trackingLinks($courier),
            'message' => null,
        ]);
    }

    /**
     * Sinkron harga. Default provider "local" = estimator zona gratis.
     * Provider/key: Setting admin, fallback config/env.
     */
    public function updateLivePrices(): array
    {
        $apiProvider = $this->resolveProvider();
        $apiKey = $this->resolveApiKey();

        try {
            $result = match ($apiProvider) {
                'local', '', 'estimator' => $this->refreshFromLocalEstimator(),
                'api.co.id' => $this->fetchFromApiCoId($apiKey),
                'rajaongkir' => $this->fetchFromRajaOngkir($apiKey),
                default => ['success' => false, 'message' => 'Provider API tidak dikenal: '.$apiProvider],
            };

            if ($result['success'] ?? false) {
                $result['changes'] = $this->describePriceChanges($result['prices'] ?? []);
                Cache::put(self::CACHE_KEY_PRICES, $result['prices'], self::CACHE_TTL);
                Cache::forget(self::CACHE_KEY_COURIERS);
                Setting::set('shipping_api_last_update', now()->toDateTimeString(), 'string', 'shipping', true);
                Setting::set('shipping_api_provider', $apiProvider === '' ? 'local' : $apiProvider, 'string', 'shipping', true);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Shipping price update failed: '.$e->getMessage());

            return ['success' => false, 'message' => 'Gagal sinkron: '.$e->getMessage()];
        }
    }

    public function resolveProvider(): string
    {
        $fromSetting = Setting::get('shipping_api_provider');
        if (is_string($fromSetting) && $fromSetting !== '') {
            return $fromSetting;
        }

        return (string) config('shipping.provider', 'local');
    }

    public function resolveApiKey(): string
    {
        $fromSetting = Setting::get('shipping_api_key');
        if (is_string($fromSetting) && $fromSetting !== '') {
            return $fromSetting;
        }

        return (string) config('shipping.api_key', '');
    }

    /**
     * Estimator lokal gratis: ambil tarif/kg dari config/ekspedisi.php.
     */
    private function refreshFromLocalEstimator(): array
    {
        $prices = [];

        foreach ($this->estimator->couriers() as $courier) {
            $prices[$courier['code']] = (int) ($courier['per_kg'] ?? 0);
        }

        return [
            'success' => true,
            'prices' => $prices,
            'provider' => 'local',
            'message' => 'Tarif disinkronkan dari estimator lokal (gratis, tanpa API key). Zona × berat tetap dipakai di kalkulator.',
            'sample' => 'Sumber: tarif referensi di config/ekspedisi.php',
        ];
    }

    /**
     * api.co.id Cek Ongkir v2 — butuh key (tier gratis 1.000 req/bulan).
     */
    private function fetchFromApiCoId(string $apiKey): array
    {
        if ($apiKey === '') {
            return [
                'success' => false,
                'message' => 'Isi API key api.co.id dulu (gratis 1.000 request/bulan), atau pilih provider Lokal.',
            ];
        }

        $origin = (string) (Setting::get('shipping_origin_district')
            ?: config('shipping.api_co_id.origin_district', '317405'));
        $destination = (string) (Setting::get('shipping_sample_district')
            ?: config('shipping.api_co_id.sample_district', '327301'));
        $weight = (float) (Setting::get('shipping_sample_weight_kg')
            ?: config('shipping.api_co_id.sample_weight_kg', 1));
        $base = rtrim((string) config('shipping.api_co_id.base', 'https://use.api.co.id'), '/');

        $response = $this->httpClient()
            ->withHeaders(['x-api-co-id' => $apiKey])
            ->get($base.'/courier/v2/rates', [
                'origin_district_code' => $origin,
                'destination_district_code' => $destination,
                'weight' => max(0.1, $weight),
            ]);

        if (! $response->successful()) {
            return [
                'success' => false,
                'message' => 'api.co.id error HTTP '.$response->status().'. Cek key / kuota.',
            ];
        }

        $rates = $response->json('data.rates')
            ?? $response->json('rates')
            ?? $response->json('results')
            ?? [];

        if (! is_array($rates) || $rates === []) {
            return ['success' => false, 'message' => 'api.co.id tidak mengembalikan tarif.'];
        }

        $prices = $this->collapseRatesToPerKg($rates, max(0.1, $weight));

        if ($prices === []) {
            return ['success' => false, 'message' => 'Tidak ada kurir yang bisa dipetakan ke daftar ekspedisi toko.'];
        }

        return [
            'success' => true,
            'prices' => $prices,
            'provider' => 'api.co.id',
            'message' => 'Tarif live dari api.co.id ('.count($prices).' kurir). Override manual admin tetap diutamakan.',
            'sample' => 'Sampel kecamatan '.$origin.' → '.$destination.', berat '.$weight.' kg',
        ];
    }

    /**
     * RajaOngkir Starter — gratis terbatas (jne/pos/tiki), butuh key.
     */
    private function fetchFromRajaOngkir(string $apiKey): array
    {
        if ($apiKey === '') {
            return [
                'success' => false,
                'message' => 'Isi API key RajaOngkir dulu, atau pilih provider Lokal.',
            ];
        }

        $origin = (string) (Setting::get('shipping_rajaongkir_origin')
            ?: config('shipping.rajaongkir.origin', '152'));
        $destination = (string) (Setting::get('shipping_rajaongkir_destination')
            ?: config('shipping.rajaongkir.destination', '23'));
        $weightGram = (int) max(100, (float) (Setting::get('shipping_sample_weight_kg')
            ?: config('shipping.api_co_id.sample_weight_kg', 1)) * 1000);
        $prices = [];

        foreach (['jne', 'pos', 'tiki'] as $courier) {
            $response = $this->httpClient()
                ->withHeaders(['key' => $apiKey])
                ->asForm()
                ->post('https://api.rajaongkir.com/starter/cost', [
                    'origin' => $origin,
                    'destination' => $destination,
                    'weight' => $weightGram,
                    'courier' => $courier,
                ]);

            if (! $response->successful()) {
                continue;
            }

            $results = $response->json('rajaongkir.results.0.costs') ?? [];
            $cheapest = null;
            foreach ($results as $service) {
                $value = (int) ($service['cost'][0]['value'] ?? 0);
                if ($value <= 0) {
                    continue;
                }
                $cheapest = $cheapest === null ? $value : min($cheapest, $value);
            }

            if ($cheapest !== null) {
                $kg = max(0.1, $weightGram / 1000);
                $prices[$courier] = (int) max(1000, round($cheapest / $kg));
            }
        }

        if ($prices === []) {
            return ['success' => false, 'message' => 'RajaOngkir tidak mengembalikan tarif. Cek key / kuota Starter.'];
        }

        return [
            'success' => true,
            'prices' => $prices,
            'provider' => 'rajaongkir',
            'message' => 'Tarif live dari RajaOngkir Starter ('.implode(', ', array_keys($prices)).').',
            'sample' => 'Sampel kota '.$origin.' → '.$destination.', berat '.$weightGram.' g',
        ];
    }

    /**
     * Ambil harga termurah per courier_code, normalisasi ke Rp/kg.
     *
     * @param  array<int, array<string, mixed>>  $rates
     * @return array<string, int>
     */
    private function collapseRatesToPerKg(array $rates, float $weightKg): array
    {
        $best = [];

        foreach ($rates as $rate) {
            if (! is_array($rate)) {
                continue;
            }

            $code = strtolower((string) ($rate['courier_code'] ?? $rate['code'] ?? ''));
            $code = match ($code) {
                'j&t', 'jnt', 'jet' => 'jnt',
                'pos indonesia', 'pos' => 'pos',
                'spx', 'shopee', 'shopeeexpress', 'shopee-express', 'spxexpress' => 'spx',
                default => $code,
            };

            if ($code === '' || ! $this->estimator->courier($code)) {
                continue;
            }

            $price = (int) ($rate['total_price'] ?? $rate['price'] ?? $rate['cost'] ?? 0);
            if ($price <= 0) {
                continue;
            }

            $best[$code] = isset($best[$code]) ? min($best[$code], $price) : $price;
        }

        $perKg = [];
        foreach ($best as $code => $price) {
            $perKg[$code] = (int) max(1000, round($price / max(0.1, $weightKg)));
        }

        return $perKg;
    }

    public function saveApiSettings(?string $provider, ?string $apiKey): void
    {
        $provider = $provider ?: 'local';
        Setting::set('shipping_api_provider', $provider, 'string', 'shipping', true);

        // Key kosong = jangan hapus; biarkan fallback .env / key lama.
        // Ganti ke lokal: clear Setting key (env tetap dipakai kalau provider diganti lagi).
        if ($provider === 'local') {
            Setting::set('shipping_api_key', '', 'string', 'shipping', false);

            return;
        }

        if (is_string($apiKey) && trim($apiKey) !== '') {
            Setting::set('shipping_api_key', trim($apiKey), 'string', 'shipping', false);
        }
    }

    public function getApiStatus(): array
    {
        $apiProvider = $this->resolveProvider();
        $apiKey = $this->resolveApiKey();
        $lastUpdate = Setting::get('shipping_api_last_update');
        $cached = Cache::get(self::CACHE_KEY_PRICES);
        $keySource = Setting::get('shipping_api_key') ? 'database' : (config('shipping.api_key') ? 'env' : 'none');

        if ($apiProvider === 'local' || $apiProvider === 'estimator') {
            return [
                'provider' => 'local',
                'label' => 'Estimator lokal (gratis)',
                'status' => 'online',
                'message' => 'Tanpa API key. Tarif dari config/ekspedisi.php × faktor zona.',
                'last_update' => $lastUpdate,
                'has_key' => false,
                'key_source' => $keySource,
                'cached_couriers' => is_array($cached) ? count($cached) : 0,
            ];
        }

        if ($apiKey === '') {
            return [
                'provider' => $apiProvider,
                'label' => $apiProvider,
                'status' => 'not_configured',
                'message' => 'Provider dipilih tapi API key masih kosong (.env atau form).',
                'last_update' => $lastUpdate,
                'has_key' => false,
                'key_source' => $keySource,
                'cached_couriers' => is_array($cached) ? count($cached) : 0,
            ];
        }

        return [
            'provider' => $apiProvider,
            'label' => $apiProvider,
            'status' => 'configured',
            'message' => 'API key siap ('.$keySource.'). Tekan Sinkronkan untuk tarik tarif live.',
            'last_update' => $lastUpdate,
            'has_key' => true,
            'key_source' => $keySource,
            'cached_couriers' => is_array($cached) ? count($cached) : 0,
        ];
    }

    /**
     * HTTP client with CA bundle when PHP/OpenSSL on Windows lacks system roots.
     */
    private function httpClient(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::timeout(20);
        $verify = config('shipping.verify_ssl', true);
        $ca = (string) config('shipping.ca_bundle', '');

        if ($verify === false || $verify === 'false' || $verify === '0') {
            return $client->withOptions(['verify' => false]);
        }

        if ($ca !== '' && is_file($ca)) {
            return $client->withOptions(['verify' => $ca]);
        }

        return $client;
    }

    private function isCourierEnabled(string $code): bool
    {
        $value = Setting::get('courier_enabled_'.$code, true);

        return $value === true || $value === 'true' || $value === 1 || $value === '1';
    }

    /**
     * @param  array<string, int>  $livePrices
     */
    private function effectivePerKg(string $code, array $courier, array $livePrices): int
    {
        $perKg = (int) ($courier['per_kg'] ?? 0);

        if (isset($livePrices[$code])) {
            $perKg = (int) $livePrices[$code];
        }

        $custom = Setting::get('courier_per_kg_'.$code);
        if ($custom !== null && $custom !== '') {
            $perKg = (int) $custom;
        }

        return $perKg;
    }

    private function effectiveMinCharge(string $code, array $courier): int
    {
        $min = (int) ($courier['min_charge'] ?? 0);
        $custom = Setting::get('courier_min_charge_'.$code);
        if ($custom !== null && $custom !== '') {
            $min = (int) $custom;
        }

        return $min;
    }

    /**
     * Bandingkan tarif baru dengan cache sebelumnya, sebelum cache ditimpa.
     *
     * @param  array<string, int>  $newPrices
     * @return array<int, array{code: string, name: string, before: int, after: int|null, status: string}>
     */
    private function describePriceChanges(array $newPrices): array
    {
        $old = Cache::get(self::CACHE_KEY_PRICES, []);
        $old = is_array($old) ? $old : [];
        $rows = [];

        foreach ($this->estimator->couriers() as $courier) {
            $code = (string) ($courier['code'] ?? '');
            if ($code === '') {
                continue;
            }

            $default = (int) ($courier['per_kg'] ?? 0);
            $before = array_key_exists($code, $old) ? (int) $old[$code] : $default;
            $manual = Setting::get('courier_per_kg_'.$code);
            $name = (string) ($courier['name'] ?? $code);

            if (! array_key_exists($code, $newPrices)) {
                $rows[] = [
                    'code' => $code,
                    'name' => $name,
                    'before' => $before,
                    'after' => null,
                    'status' => 'missing',
                ];

                continue;
            }

            $after = (int) $newPrices[$code];
            if ($manual !== null && $manual !== '') {
                $rows[] = [
                    'code' => $code,
                    'name' => $name,
                    'before' => (int) $manual,
                    'after' => $after,
                    'status' => 'manual',
                ];

                continue;
            }

            $rows[] = [
                'code' => $code,
                'name' => $name,
                'before' => $before,
                'after' => $after,
                'status' => $before === $after ? 'unchanged' : 'updated',
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, int>  $livePrices
     */
    private function priceSource(string $code, array $livePrices): string
    {
        $custom = Setting::get('courier_per_kg_'.$code);
        if ($custom !== null && $custom !== '') {
            return 'manual';
        }
        if (isset($livePrices[$code])) {
            return 'sync';
        }

        return 'default';
    }
}
