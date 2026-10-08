<?php

namespace Tests\Feature;

use App\Services\ShippingEstimator;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ShippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ongkir_page_renders(): void
    {
        $this->get(route('shipping.index'))->assertOk()->assertSee('Cek Ongkir');
        $this->get(route('shipping.index', ['city' => 'Bandung', 'weight' => 2]))->assertOk();
        // Unknown city falls back to the default instead of 500.
        $this->get(route('shipping.index', ['city' => 'Atlantis']))->assertOk();
        $this->get(route('shipping.index', ['weight' => 999]))->assertOk();
    }

    public function test_check_endpoint_returns_priced_rows(): void
    {
        $r = $this->getJson(route('shipping.check', ['city' => 'Bandung', 'weight' => 1]))
            ->assertOk()
            ->json();

        $this->assertNotEmpty($r['rows']);

        foreach ($r['rows'] as $row) {
            $this->assertArrayHasKey('code', $row);
            if ($row['ok']) {
                $this->assertIsInt($row['price']);
                $this->assertGreaterThan(0, $row['price']);
            }
        }
    }

    public function test_check_endpoint_validates_input(): void
    {
        $this->getJson(route('shipping.check', ['city' => 'Atlantis', 'weight' => 1]))
            ->assertStatus(422);
        $this->getJson(route('shipping.check', ['city' => 'Bandung', 'weight' => 0]))
            ->assertStatus(422);
    }

    public function test_estimator_pricing_edges(): void
    {
        $svc = new ShippingEstimator;

        $cheap = $svc->estimate('jne', 'Jakarta', 1);
        $far = $svc->estimate('jne', 'Jayapura', 1);
        $this->assertTrue($cheap['ok'] && $far['ok']);
        $this->assertGreaterThan($cheap['price'], $far['price'], 'far zone must cost more');

        $this->assertFalse($svc->estimate('nope', 'Jakarta', 1)['ok']);
        $this->assertFalse($svc->estimate('jne', 'Atlantis', 1)['ok']);

        $heavy = $svc->estimate('jne', 'Jakarta', 100);
        $this->assertTrue($heavy['ok']);
    }

    public function test_tracking_links_include_awb_and_plain_page(): void
    {
        $svc = new ShippingEstimator;
        $jne = $svc->courier('jne');

        $withAwb = $svc->trackingLinks($jne, 'JNE123456789');
        $this->assertNotEmpty($withAwb);
        // The aggregator link must carry the AWB.
        $this->assertTrue(
            collect($withAwb)->contains(fn ($l) => str_contains($l['url'], 'JNE123456789')),
            'tracking links must include the AWB in at least one link'
        );

        // No AWB: no unresolved %s placeholder.
        foreach ($svc->trackingLinks($jne) as $link) {
            $this->assertStringNotContainsString('%s', $link['url']);
        }
    }

    public function test_detect_courier_from_resi_prefix(): void
    {
        $svc = new ShippingEstimator;

        $this->assertSame('jne', $svc->detectCourier('JNE1234567890'));
        $this->assertSame('jnt', $svc->detectCourier('JT1234567890'));
        $this->assertSame('sicepat', $svc->detectCourier('001234567890'));
        $this->assertNull($svc->detectCourier(''));
        $this->assertNull($svc->detectCourier('ZZZ999999'));

        // Unknown prefixes never guess a courier that isn't registered.
        foreach (config('ekspedisi.resi_prefixes', []) as $code => $prefixes) {
            $this->assertNotNull($svc->courier($code), "prefix courier {$code} must exist");
        }
    }

    public function test_resi_tab_renders_auto_detect_payload(): void
    {
        $this->get(route('shipping.index'))
            ->assertOk()
            ->assertSee('Lacak Resi')
            ->assertSee('prefixes', false) // Alpine payload present
            ->assertSee('CekResi', false);
    }

    public function test_refresh_without_api_config_reports_not_configured(): void
    {
        $result = app(ShippingService::class)->updateLivePrices();

        $this->assertFalse($result['success']);
        $this->assertSame('API not configured', $result['message']);
    }

    public function test_live_prices_cache_overrides_estimator_base_rate(): void
    {
        Cache::flush();

        // Simulasi hasil sinkronisasi API: JNE jadi 9.999/kg.
        Cache::put('shipping_prices_cache', ['jne' => 9999], 3600);

        $jne = collect(app(ShippingService::class)->enabledCouriers())
            ->firstWhere('code', 'jne');

        $this->assertSame(9999, (int) $jne['per_kg']);
    }

    public function test_admin_pricing_setting_overrides_live_api_price(): void
    {
        Cache::flush();

        Cache::put('shipping_prices_cache', ['jne' => 9999], 3600);
        \App\Models\Setting::set('courier_per_kg_jne', '7000', 'integer', 'shipping', true);

        $jne = collect(app(ShippingService::class)->enabledCouriers())
            ->firstWhere('code', 'jne');

        // Manual admin selalu menang atas harga API.
        $this->assertSame(7000, (int) $jne['per_kg']);
    }

    public function test_fetch_from_api_caches_prices_and_clears_courier_cache(): void
    {
        Cache::flush();

        Http::fake([
            'api.co.id/*' => Http::response([
                'results' => [
                    ['code' => 'jne', 'price' => 12345],
                    ['code' => 'jnt', 'price' => 11000],
                ],
            ], 200),
        ]);

        \App\Models\Setting::set('shipping_api_provider', 'api.co.id', 'string', 'shipping', true);
        \App\Models\Setting::set('shipping_api_key', 'secret', 'string', 'shipping', true);

        $result = app(ShippingService::class)->updateLivePrices();

        $this->assertTrue($result['success']);
        $this->assertSame(['jne' => 12345, 'jnt' => 11000], $result['prices']);
        $this->assertSame(['jne' => 12345, 'jnt' => 11000], Cache::get('shipping_prices_cache'));

        // Kurir cache dilepas supaya tarif baru langsung dipakai.
        $this->assertNull(Cache::get('shipping_couriers_enabled'));
    }
}
