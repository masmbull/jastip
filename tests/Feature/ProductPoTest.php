<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductPoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function basePayload(array $overrides = []): array
    {
        $category = Category::firstOrFail();

        return array_merge([
            'category_id' => $category->id,
            'name' => 'Produk Uji PO',
            'price' => 150000,
            'stock' => 0,
            'description' => 'Deskripsi uji',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_admin_can_create_po_product_with_ready_date(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.products.store'), $this->basePayload([
            'is_po' => '1',
            'ready_date' => '2026-12-01',
            'image' => UploadedFile::fake()->image('produk.png'),
        ]))->assertSessionHasNoErrors();

        $product = Product::where('name', 'Produk Uji PO')->firstOrFail();
        $this->assertTrue((bool) $product->is_po);
        $this->assertSame('2026-12-01', optional($product->ready_date)->format('Y-m-d'));
    }

    public function test_ready_date_is_cleared_for_non_po_product(): void
    {
        Storage::fake('public');
        $admin = User::where('role', 'admin')->firstOrFail();

        // Ready stock tapi tetap kirim tanggal -> harus dikosongkan.
        $this->actingAs($admin)->post(route('admin.products.store'), $this->basePayload([
            'name' => 'Produk Ready Uji',
            'stock' => 5,
            'ready_date' => '2026-12-01',
            'image' => UploadedFile::fake()->image('ready.png'),
        ]))->assertSessionHasNoErrors();

        $product = Product::where('name', 'Produk Ready Uji')->firstOrFail();
        $this->assertFalse((bool) $product->is_po);
        $this->assertNull($product->ready_date);
    }

    public function test_add_product_form_shows_po_option(): void
    {
        $admin = User::where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertSee('Pre-Order (PO)')
            ->assertSee('name="ready_date"', false)
            ->assertSee('Ketersediaan Barang');
    }
}
