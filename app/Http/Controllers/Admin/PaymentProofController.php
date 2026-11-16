<?php

namespace App\Http\Controllers\Admin;

use App\Events\OrderStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentProof;
use App\Services\StockService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function __construct(
        protected StockService $stock
    ) {}

    /**
     * List all payment proofs with filtering, plus opsional hasil pencarian
     * pesanan untuk form upload bukti oleh admin (bukti yang masuk lewat
     * WhatsApp, bukan lewat checkout pelanggan).
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $query = PaymentProof::query();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $proofs = $query
            ->with('order')
            ->latest()
            ->paginate(15);

        $counts = [
            'pending' => PaymentProof::where('status', 'pending')->count(),
            'verified' => PaymentProof::where('status', 'verified')->count(),
            'rejected' => PaymentProof::where('status', 'rejected')->count(),
            'all' => PaymentProof::count(),
        ];

        // Kandidat pesanan untuk form upload admin: belum lunas, cocok
        // dengan nomor pesanan / nama / WA pelanggan.
        $q = trim((string) $request->query('q', ''));
        $candidates = null;
        if ($q !== '') {
            $like = '%'.str_replace(['%', '_'], ['\%', '\_'], $q).'%';
            $candidates = Order::query()
                ->where('status', '!=', Order::STATUS_CANCELLED)
                ->whereNull('paid_at')
                ->where(fn ($w) => $w->where('order_number', 'like', $like)
                    ->orWhere('customer_name', 'like', $like)
                    ->orWhere('customer_whatsapp', 'like', $like))
                ->latest()
                ->limit(8)
                ->get();
        }

        return view('admin.payment-proofs.index', compact('proofs', 'status', 'counts', 'candidates', 'q'));
    }

    /**
     * Show detail of a single payment proof
     */
    public function show(PaymentProof $paymentProof)
    {
        $paymentProof->load('order');

        return view('admin.payment-proofs.show', compact('paymentProof'));
    }

    /**
     * Verify payment proof (accept payment)
     */
    public function verify(Request $request, PaymentProof $paymentProof)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string|max:500',
        ], [
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ]);

        if ($paymentProof->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Bukti pembayaran ini sudah '.$paymentProof->status.'.');
        }

        try {
            DB::transaction(function () use ($paymentProof, $validated) {
                $paymentProof->verify($validated['notes'] ?? null);
                $this->settleOrder($paymentProof->order->fresh(), $validated['notes'] ?? 'Bukti pembayaran diverifikasi admin');
            });

            // Pembayaran lunas -> kunci stok (decrement real).
            $this->stock->commit($paymentProof->order->fresh());

            // Notify customer via WhatsApp
            $this->notifyCustomer($paymentProof->order, 'verified');

            return redirect()->route('admin.payment-proofs.show', $paymentProof)
                ->with('success', 'Bukti pembayaran berhasil diverifikasi. Pesanan ditandai LUNAS.');
        } catch (\Exception $e) {
            \Log::error('Payment proof verification failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal memverifikasi bukti pembayaran.');
        }
    }

    /**
     * Upload bukti pembayaran oleh admin (bukti yang masuk lewat WhatsApp,
     * bukan lewat checkout pelanggan). Pesanan langsung ditandai LUNAS:
     * sisa tagihan dicatat sebagai pelunasan dan paid_at diisi.
     */
    public function adminUpload(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // 5MB
            'notes' => 'nullable|string|max:500',
        ], [
            'order_id.required' => 'Pilih pesanan yang akan dibayar.',
            'order_id.exists' => 'Pesanan tidak ditemukan.',
            'proof.required' => 'Upload bukti pembayaran wajib diisi.',
            'proof.image' => 'File harus berupa gambar.',
            'proof.mimes' => 'Format gambar hanya jpg, jpeg, atau png.',
            'proof.max' => 'Ukuran gambar maksimal 5MB.',
            'notes.max' => 'Catatan maksimal 500 karakter.',
        ]);

        $order = Order::findOrFail($validated['order_id']);

        if ($order->is_paid) {
            return redirect()->route('admin.payment-proofs.index')
                ->with('error', 'Pesanan '.$order->order_number.' sudah lunas.');
        }

        try {
            $previousStatus = $order->status;

            DB::transaction(function () use ($validated, $order, $previousStatus) {
                // Gantikan bukti lama milik pesanan ini (bila ada), pola sama
                // dengan upload pelanggan.
                if ($order->paymentProof) {
                    Storage::disk('public')->delete($order->paymentProof->file_path);
                    $order->paymentProof->delete();
                }

                $file = $validated['proof'];
                $path = $file->store('payment-proofs', 'public');

                PaymentProof::create([
                    'order_id' => $order->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'status' => 'verified',
                    'admin_notes' => $validated['notes'] ?? 'Diunggah oleh admin (bukti dari WhatsApp).',
                    'verified_at' => now(),
                ]);

                $this->settleOrder($order->fresh(), $validated['notes'] ?? 'Bukti pembayaran diunggah admin (dari WhatsApp)');

                // Sama seperti verify(): beri tahu listener (in-app notification + log WA).
                OrderStatusChanged::dispatch(
                    $order->fresh(),
                    $previousStatus,
                    Order::STATUS_CONFIRMED
                );
            });

            // Pembayaran lunas -> kunci stok (decrement real).
            $this->stock->commit($order->fresh());

            // Notify customer via WhatsApp
            $this->notifyCustomer($order, 'verified');

            return redirect()->route('admin.payment-proofs.index', ['status' => 'verified'])
                ->with('success', 'Bukti pembayaran diunggah. Pesanan '.$order->order_number.' ditandai LUNAS.');
        } catch (\Exception $e) {
            \Log::error('Admin payment proof upload failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal mengunggah bukti pembayaran.');
        }
    }

    /**
     * Lunaskan pesanan: catat pelunasan sebesar sisa tagihan, isi paid_at,
     * dan set status CONFIRMED bila masih menunggu pembayaran. Idempoten —
     * keluar diam-diam bila pesanan sudah lunas.
     */
    private function settleOrder(Order $order, ?string $note): void
    {
        if ($order->is_paid) {
            return;
        }

        $remaining = $order->remaining;

        if ($remaining > 0) {
            $order->payments()->create([
                'amount' => $remaining,
                'paid_at' => now(),
                'note' => $note,
            ]);
        }

        $update = ['paid_at' => now()];
        if (in_array($order->status, [Order::STATUS_AWAITING_PAYMENT, Order::STATUS_PENDING], true)) {
            $update['status'] = Order::STATUS_CONFIRMED;
        }

        $order->update($update);
    }

    /**
     * Reject payment proof (deny payment)
     */
    public function reject(Request $request, PaymentProof $paymentProof)
    {
        $validated = $request->validate([
            'notes' => 'required|string|max:500',
        ], [
            'notes.required' => 'Alasan penolakan wajib diisi.',
            'notes.max' => 'Alasan maksimal 500 karakter.',
        ]);

        if ($paymentProof->status !== 'pending') {
            return redirect()->back()
                ->with('error', 'Bukti pembayaran ini sudah '.$paymentProof->status.'.');
        }

        try {
            $paymentProof->reject($validated['notes']);

            // Notify customer via WhatsApp
            $this->notifyCustomer($paymentProof->order, 'rejected', $validated['notes']);

            return redirect()->route('admin.payment-proofs.show', $paymentProof)
                ->with('success', 'Bukti pembayaran ditolak. Pelanggan diminta mengunggah ulang.');
        } catch (\Exception $e) {
            \Log::error('Payment proof rejection failed: '.$e->getMessage());

            return redirect()->back()
                ->with('error', 'Gagal menolak bukti pembayaran.');
        }
    }

    /**
     * Download payment proof file
     */
    public function download(PaymentProof $paymentProof)
    {
        if (! Storage::disk('public')->exists($paymentProof->file_path)) {
            return redirect()->back()
                ->with('error', 'File tidak ditemukan.');
        }

        return Storage::disk('public')->download(
            $paymentProof->file_path,
            $paymentProof->file_name
        );
    }

    /**
     * Notify customer about payment proof status
     */
    private function notifyCustomer(Order $order, string $status, ?string $reason = null): void
    {
        if (! $order->customer_whatsapp) {
            return;
        }

        try {
            if ($status === 'verified') {
                // Create WhatsApp link for verified status
                $whatsappUrl = WhatsappService::sendPaymentNotification(
                    $order->customer_whatsapp,
                    $order->order_number,
                    'verified'
                );
            } elseif ($status === 'rejected') {
                // Create WhatsApp link for rejected status with reason
                $whatsappUrl = WhatsappService::sendPaymentNotification(
                    $order->customer_whatsapp,
                    $order->order_number,
                    'rejected',
                    $reason
                );
            }

            // Log the notification URL for audit trail
            if (isset($whatsappUrl)) {
                \Log::info("Payment notification sent to {$order->customer_whatsapp} for order {$order->order_number}");
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send payment notification: '.$e->getMessage());
        }
    }
}
