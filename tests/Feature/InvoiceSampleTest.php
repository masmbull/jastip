<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceSampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_sample_preview_renders_fixture(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $res = $this->actingAs($admin)->get(route('admin.invoices.sample'));
        $res->assertOk();

        // Header dokumen (pratinjau memakai template yang sama dengan PDF).
        $res->assertSee('INVOICE');
        $res->assertSee('INV-SAMPLE-2026');
        $res->assertSee('28-09-2026 00:00');
        $res->assertSee('BELUM BAYAR');

        // Pelanggan.
        $res->assertSee('Nabila Putri (Sampel Pelanggan)');
        $res->assertSee('628123456789');
        $res->assertSee('JNE YES (Express)');

        // Produk.
        $res->assertSee('Uniqlo Heattech Extra Warm Thermal');
        $res->assertSee('Gentle Monster Her 01 Sunglasses');

        // Perhitungan: 2x299000=598000 + 1x3200000=3200000 = 3798000,
        // ongkir 30000, layanan 320000, total = 4148000.
        $res->assertSee('Rp 598.000');
        $res->assertSee('Rp 3.200.000');
        $res->assertSee('Rp 3.798.000');
        $res->assertSee('Rp 30.000');
        $res->assertSee('Rp 320.000');
        $res->assertSee('Rp 4.148.000');
        $res->assertSee('3 pcs');

        // Pembayaran + footer.
        $res->assertSee('Silakan transfer');
        $res->assertSee('Terima kasih');
    }

    public function test_sample_preview_honours_preset_label(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.invoices.sample', ['preset' => 'compact']))
            ->assertOk();
    }

    public function test_non_admin_cannot_open_sample_preview(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('admin.invoices.sample'))
            ->assertForbidden();
    }
}
