<?php

/**
 * Shipping / ongkir providers.
 *
 * Secrets belong in .env (SHIPPING_API_KEY), never in git or Blade.
 * Setting shipping_api_provider / shipping_api_key can override at runtime
 * from the admin panel; env is the source of truth when Setting is empty.
 */
return [
    'provider' => env('SHIPPING_API_PROVIDER', 'local'),

    'api_key' => env('SHIPPING_API_KEY'),

    // CA bundle for Windows/local PHP without system certs.
    // Drop cacert.pem in storage/app/private/ (gitignored) or set SSL path.
    'verify_ssl' => env('SHIPPING_VERIFY_SSL', true),
    'ca_bundle' => env('SHIPPING_CA_BUNDLE', storage_path('app/private/cacert.pem')),

    // api.co.id sample route for admin "Sinkronkan" (1 kg).
    // Default: Kebayoran Lama (Jaksel) -> Coblong (Bandung).
    'api_co_id' => [
        'base' => env('SHIPPING_API_CO_ID_BASE', 'https://use.api.co.id'),
        'origin_district' => env('SHIPPING_ORIGIN_DISTRICT', '317405'),
        'sample_district' => env('SHIPPING_SAMPLE_DISTRICT', '327301'),
        'sample_weight_kg' => (float) env('SHIPPING_SAMPLE_WEIGHT_KG', 1),
    ],

    // RajaOngkir Starter city IDs (legacy starter API).
    'rajaongkir' => [
        'origin' => env('SHIPPING_RAJAONGKIR_ORIGIN', '152'),
        'destination' => env('SHIPPING_RAJAONGKIR_DESTINATION', '23'),
    ],
];
