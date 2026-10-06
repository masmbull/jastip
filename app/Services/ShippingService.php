<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * ShippingService: manages courier pricing, enable/disable, and auto-updates from external APIs.
 * Extends ShippingEstimator with database persistence and live pricing updates.
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

        foreach ($couriers as $courier) {
            $code = $courier['code'];
            $isEnabled = Setting::get('courier_enabled_' . $code, true) === true || 
                         Setting::get('courier_enabled_' . $code) === 'true';

            if ($isEnabled) {
                $customPrice = Setting::get('courier_per_kg_' . $code);
                $customMinCharge = Setting::get('courier_min_charge_' . $code);

                if ($customPrice !== null) {
                    $courier['per_kg'] = (int) $customPrice;
                }
                if ($customMinCharge !== null) {
                    $courier['min_charge'] = (int) $customMinCharge;
                }

                $enabled[] = $courier;
            }
        }

        Cache::put(self::CACHE_KEY_COURIERS, $enabled, self::CACHE_TTL);

        return $enabled;
    }

    /**
     * Get all couriers with their enable/disable status
     */
    public function courierSettings(): array
    {
        $couriers = $this->estimator->couriers();
        $settings = [];

        foreach ($couriers as $courier) {
            $code = $courier['code'];
            $settings[] = [
                'code' => $code,
                'name' => $courier['name'],
                'type' => $courier['type'],
                'enabled' => Setting::get('courier_enabled_' . $code, true) === true || 
                            Setting::get('courier_enabled_' . $code) === 'true',
                'per_kg' => (int) Setting::get('courier_per_kg_' . $code, $courier['per_kg'] ?? 0),
                'min_charge' => (int) Setting::get('courier_min_charge_' . $code, $courier['min_charge'] ?? 0),
                'default_per_kg' => (int) ($courier['per_kg'] ?? 0),
                'default_min_charge' => (int) ($courier['min_charge'] ?? 0),
            ];
        }

        return $settings;
    }

    /**
     * Enable/disable a courier
     */
    public function toggleCourier(string $code, bool $enabled): void
    {
        Setting::set('courier_enabled_' . $code, $enabled ? 'true' : 'false', 'string', 'shipping', true);
        Cache::forget(self::CACHE_KEY_COURIERS);
    }

    /**
     * Update courier pricing
     */
    public function updateCourierPricing(string $code, int $perKg, int $minCharge): void
    {
        Setting::set('courier_per_kg_' . $code, (string) $perKg, 'integer', 'shipping', true);
        Setting::set('courier_min_charge_' . $code, (string) $minCharge, 'integer', 'shipping', true);
        Cache::forget(self::CACHE_KEY_COURIERS);
    }

    /**
     * Estimate with only enabled couriers
     */
    public function estimateAllEnabled(string $city, float $weightKg): array
    {
        $results = [];

        foreach ($this->enabledCouriers() as $courier) {
            // Create a temporary estimator result
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

    /**
     * Estimate for a specific courier
     */
    private function estimateWithCourier(array $courier, string $city, float $weightKg): ?array
    {
        $cities = $this->estimator->cities();

        if (!isset($cities[$city])) {
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
            'zone_label' => $this->estimator->zoneLabels()[$zone] ?? ('Zona ' . $zone),
            'weight' => $weightKg,
            'min_weight_kg' => $minWeight,
            'tracking' => (bool) ($courier['tracking'] ?? false),
        ];

        if (!empty($courier['available_zones']) && !in_array($zone, $courier['available_zones'], true)) {
            return array_merge($base, [
                'ok' => false,
                'message' => $courier['name'] . ' belum melayani pengiriman ke ' . $city . '.',
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
            'price_formatted' => 'Rp ' . number_format($price, 0, ',', '.'),
            'per_kg_formatted' => 'Rp ' . number_format((int) $courier['per_kg'], 0, ',', '.') . '/kg',
            'etd' => $courier['etd'] ?? 'N/A',
            'tracking_links' => $this->estimator->trackingLinks($courier),
            'message' => null,
        ]);
    }

    /**
     * Fetch live prices from external API (if configured)
     * TODO: Implement integration with actual APIs like api.co.id
     */
    public function updateLivePrices(): array
    {
        $apiProvider = Setting::get('shipping_api_provider');
        $apiKey = Setting::get('shipping_api_key');

        if (!$apiProvider || !$apiKey) {
            return ['success' => false, 'message' => 'API not configured'];
        }

        try {
            $result = match ($apiProvider) {
                'api.co.id' => $this->fetchFromApiCoId($apiKey),
                default => ['success' => false, 'message' => 'Unknown API provider'],
            };

            if ($result['success']) {
                Cache::put(self::CACHE_KEY_PRICES, $result['prices'], self::CACHE_TTL);
            }

            return $result;
        } catch (\Exception $e) {
            \Log::error('Shipping price update failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'API request failed: ' . $e->getMessage()];
        }
    }

    /**
     * Fetch prices from api.co.id
     */
    private function fetchFromApiCoId(string $apiKey): array
    {
        $response = Http::timeout(10)->get('https://api.co.id/ongkir/check', [
            'api_key' => $apiKey,
            'origin' => '1',
            'destination' => '2',
            'weight' => '1000',
        ]);

        if (!$response->successful()) {
            return ['success' => false, 'message' => 'API returned error: ' . $response->status()];
        }

        $prices = [];
        foreach ($response->json('results', []) as $result) {
            $prices[$result['code']] = $result['price'];
        }

        return ['success' => true, 'prices' => $prices];
    }

    /**
     * Get API service status
     */
    public function getApiStatus(): array
    {
        $apiProvider = Setting::get('shipping_api_provider');
        $apiKey = Setting::get('shipping_api_key');

        if (!$apiProvider || !$apiKey) {
            return [
                'provider' => $apiProvider ?? 'none',
                'status' => 'not_configured',
                'message' => 'API not configured',
                'last_update' => null,
            ];
        }

        $lastUpdate = Setting::get('shipping_api_last_update');

        try {
            $response = Http::timeout(5)->head('https://api.co.id/ongkir/check');
            $status = $response->successful() ? 'online' : 'offline';
            $message = $response->successful() ? 'API is reachable' : 'API returned error: ' . $response->status();
        } catch (\Exception $e) {
            $status = 'offline';
            $message = 'Connection error: ' . $e->getMessage();
        }

        return [
            'provider' => $apiProvider,
            'status' => $status,
            'message' => $message,
            'last_update' => $lastUpdate,
        ];
    }
}
