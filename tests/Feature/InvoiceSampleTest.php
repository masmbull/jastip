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

        // Header brand + nomor + tanggal (fixture).
        $res->assertSee('JASTIP IMOBY BANDUNG BY');
        $res->assertSee('DANESHA');
        $res->assertSee('STRUK INVOICE JASTIP ESTIMASI (SAMPEL)');
        $res->assertSee('INV-SAMPLE-2026');
        $res->assertSee('28-09-2026 00:00');

        // Pelanggan.
        $res->assertSee('Nabila Putri (Sampel Pelanggan)');
        $res->assertSee('628123456789');
        $res->assertSee('JNE YES (Express)');

        // Produk + detail.
        $res->assertSee('Uniqlo Heattech Extra Warm Thermal');
        $res->assertSee('Gentle Monster Her 01 Sunglasses');
        $res->assertSee('Black [L]');
        $res->assertSee('Glossy Black [Free Size]');

        // Perhitungan: 2x299000=598000, 1x3200000=3200000,
        // jastip 2x35000 + 1x250000 = 320000, packing 15000,
        // total = 3798000 + 320000 + 15000 = 4133000.
        $res->assertSee('Rp 598.000');
        $res->assertSee('Rp 3.200.000');
        $res->assertSee('Rp 3.798.000');
        $res->assertSee('Rp 320.000');
        $res->assertSee('Rp 15.000');
        $res->assertSee('Rp 4.133.000');
        $res->assertSee('3 pcs');

        // Pembayaran + footer.
        $res->assertSee('REK. PEMBAYARAN:');
        $res->assertSee('BCA 098765432');
        $res->assertSee('An Damesha');
        $res->assertSee('=== SIMPAN STRUK INI SEBAGAI BUKTI PESANAN ===');
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
