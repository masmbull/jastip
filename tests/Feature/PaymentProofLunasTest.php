<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentProof;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentProofLunasTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('role', 'admin')->firstOrFail();
    }

    private function makeOrder(array $overrides = []): Order
    {
        $order = Order::create(array_merge([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => 'Pelanggan A',
            'customer_whatsapp' => '081234567001',
            'customer_address' => 'Jl. Melati No. 3, Bandung',
            'shipping_method' => 'JNE',
            'subtotal' => 200000,
            'shipping_cost' => 15000,
            'total' => 215000,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'payment_method' => 'manual',
        ], $overrides));

        OrderItem::create([
            'order_id' => $order->id,
            'product_name' => 'Tas titipan',
            'product_price' => 200000,
            'quantity' => 1,
            'unit' => 'pcs',
            'subtotal' => 200000,
        ]);

        return $order;
    }

    private function makePendingProof(Order $order): PaymentProof
    {
        return PaymentProof::create([
            'order_id' => $order->id,
            'file_path' => 'payment-proofs/pending.jpg',
            'file_name' => 'pending.jpg',
            'file_size' => 12345,
            'status' => 'pending',
        ]);
    }

    public function test_verify_marks_order_as_paid_and_records_full_settlement(): void
    {
        $order = $this->makeOrder();
        $proof = $this->makePendingProof($order);

        $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.verify', $proof), ['notes' => 'Sudah masuk rekening BCA'])
            ->assertRedirect();

        $order->refresh();

        $this->assertTrue($order->is_paid);
        $this->assertNotNull($order->paid_at);
        $this->assertSame(0, $order->remaining);
        $this->assertSame(215000, (int) $order->payments()->sum('amount'));
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);
        $this->assertSame('lunas', $order->payment_status);
        $this->assertSame('verified', $proof->fresh()->status);
    }

    public function test_verify_with_deposit_records_only_the_remaining(): void
    {
        $order = $this->makeOrder(['down_payment' => 50000]);
        $proof = $this->makePendingProof($order);

        $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.verify', $proof))
            ->assertRedirect();

        $this->assertSame(165000, (int) $order->payments()->sum('amount'));
        $this->assertSame('lunas', $order->fresh()->payment_status);
    }

    public function test_admin_can_upload_proof_from_wa_and_order_becomes_lunas(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder();

        // Form upload tampil di halaman bukti pembayaran.
        $this->actingAs($this->admin)
            ->get(route('admin.payment-proofs.index'))
            ->assertOk()
            ->assertSee('Upload bukti dari WhatsApp');

        // Pencarian menemukan kandidat pesanan.
        $this->actingAs($this->admin)
            ->get(route('admin.payment-proofs.index', ['q' => 'Pelanggan A']))
            ->assertOk()
            ->assertSee($order->order_number);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.upload'), [
                'order_id' => $order->id,
                'proof' => UploadedFile::fake()->image('bukti-wa.jpg', 100, 100),
                'notes' => 'Kirim via WA jam 10:32',
            ]);

        $response->assertRedirect(route('admin.payment-proofs.index', ['status' => 'verified']));

        $order->refresh();

        $this->assertTrue($order->is_paid);
        $this->assertSame('lunas', $order->payment_status);
        $this->assertSame(215000, (int) $order->payments()->sum('amount'));
        $this->assertSame(Order::STATUS_CONFIRMED, $order->status);

        $proof = $order->paymentProof;
        $this->assertNotNull($proof);
        $this->assertSame('verified', $proof->status);
        $this->assertSame('Kirim via WA jam 10:32', $proof->admin_notes);
        Storage::disk('public')->assertExists($proof->file_path);
    }

    public function test_admin_upload_with_deposit_settles_only_remaining(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder(['down_payment' => 75000]);

        $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.upload'), [
                'order_id' => $order->id,
                'proof' => UploadedFile::fake()->image('bukti-dp.png', 100, 100),
            ])
            ->assertRedirect();

        // Pelunasan = total - deposit = 140.000
        $this->assertSame(140000, (int) $order->payments()->sum('amount'));
        $this->assertSame('lunas', $order->fresh()->payment_status);
    }

    public function test_admin_upload_replaces_existing_pending_proof(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder();
        $oldProof = $this->makePendingProof($order);
        Storage::disk('public')->put($oldProof->file_path, 'old-bytes');

        $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.upload'), [
                'order_id' => $order->id,
                'proof' => UploadedFile::fake()->image('bukti-baru.jpg', 100, 100),
            ])
            ->assertRedirect();

        // Bukti lama diganti, bukan dobel.
        $this->assertSame(1, $order->paymentProof()->count());
        $this->assertSame('verified', $order->paymentProof->fresh()->status);
        Storage::disk('public')->assertMissing($oldProof->file_path);
    }

    public function test_admin_upload_for_already_paid_order_is_rejected(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder(['paid_at' => now()]);

        $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.upload'), [
                'order_id' => $order->id,
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
            ])
            ->assertRedirect(route('admin.payment-proofs.index'))
            ->assertSessionHas('error');

        $this->assertSame(0, $order->payments()->count());
    }

    public function test_admin_upload_requires_image_file(): void
    {
        Storage::fake('public');

        $order = $this->makeOrder();

        $this->actingAs($this->admin)
            ->post(route('admin.payment-proofs.upload'), [
                'order_id' => $order->id,
                'proof' => UploadedFile::fake()->create('dokumen.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasErrors('proof');

        $this->assertFalse($order->fresh()->is_paid);
    }

    public function test_customer_cannot_use_admin_upload(): void
    {
        Storage::fake('public');

        $customer = User::factory()->create(['role' => 'customer']);
        $order = $this->makeOrder();

        $this->actingAs($customer)
            ->post(route('admin.payment-proofs.upload'), [
                'order_id' => $order->id,
                'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
            ])
            ->assertForbidden();

        $this->assertFalse($order->fresh()->is_paid);
    }

    public function test_guest_is_redirected_from_admin_upload(): void
    {
        $order = $this->makeOrder();

        $this->post(route('admin.payment-proofs.upload'), [
            'order_id' => $order->id,
            'proof' => UploadedFile::fake()->image('bukti.jpg', 100, 100),
        ])->assertRedirect(route('admin.login'));

        $this->assertFalse($order->fresh()->is_paid);
    }
}
