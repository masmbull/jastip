<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\WilayahService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WilayahTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_service_reads_cities_districts_villages(): void
    {
        $wilayah = app(WilayahService::class);

        $cities = $wilayah->cities();
        $this->assertNotEmpty($cities);
        $this->assertContains('Jakarta', $cities);

        $districts = $wilayah->districts('Jakarta');
        $this->assertNotEmpty($districts);

        $villages = $wilayah->villages('Jakarta', $districts[0]);
        $this->assertNotEmpty($villages);
    }

    public function test_endpoint_returns_districts_then_villages(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();
        $wilayah = app(WilayahService::class);
        $district = $wilayah->districts('Jakarta')[0];

        $this->actingAs($admin)
            ->getJson(route('admin.orders.wilayah', ['city' => 'Jakarta']))
            ->assertOk()
            ->assertJsonPath('districts.0', $district);

        $this->actingAs($admin)
            ->getJson(route('admin.orders.wilayah', ['city' => 'Jakarta', 'district' => $district]))
            ->assertOk()
            ->assertJsonStructure(['villages'])
            ->assertJsonCount(count($wilayah->villages('Jakarta', $district)), 'villages');
    }

    public function test_endpoint_empty_without_city(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->getJson(route('admin.orders.wilayah'))
            ->assertOk()
            ->assertExactJson(['districts' => [], 'villages' => []]);
    }

    public function test_store_persists_district_and_village(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.orders.store'), [
            'customer_name' => 'Wilayah Pelanggan',
            'customer_whatsapp' => '081200007777',
            'customer_address' => 'Jl. Wilayah',
            'shipping_city' => 'Jakarta',
            'shipping_district' => 'Cakung (Administrasi Jakarta Timur)',
            'shipping_village' => 'Jatinegara',
            'shipping_cost' => 10000,
            'items' => [
                ['product_name' => 'Barang', 'quantity' => 1, 'product_price' => 50000],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Wilayah Pelanggan',
            'city' => 'Jakarta',
            'district' => 'Cakung (Administrasi Jakarta Timur)',
            'village' => 'Jatinegara',
        ]);
    }
}
