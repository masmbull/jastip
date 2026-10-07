<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    /**
     * Guard: only the order owner, an admin, or the guest who just
     * created this order (order_data in session) may touch it.
     */
    private function authorizeOrder(Request $request, Order $order): void
    {
        $user = $request->user();
        $owns = $user && ($order->user_id === $user->id || $user->isAdmin());
        $guestOrder = session('order_data.order_number') === $order->order_number;

        abort_unless($owns || $guestOrder, 403);
    }

    /**
     * Show QRIS payment waiting page with countdown timer
     */
    public function waiting(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        // Check if order exists and is awaiting payment
        if ($order->status !== Order::STATUS_AWAITING_PAYMENT) {
            return redirect()->route('home')
                ->with('error', 'Pesanan ini tidak menunggu pembayaran.');
        }

        // Get payment expiry time (24 hours from creation)
        $expiresAt = $order->created_at->addHours(24);
        $isExpired = now()->isAfter($expiresAt);

        if ($isExpired) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
            return redirect()->route('home')
                ->with('error', 'Waktu pembayaran telah berakhir. Silakan buat pesanan baru.');
        }

        $qrisImage = \Illuminate\Support\Facades\Storage::url(setting('qris_image'));
        $qrisMerchant = setting('qris_merchant_name', setting('brand_name', 'NITIP DI END'));
        $paymentTimeout = 24; // hours

        return view('frontend.payment.waiting', compact(
            'order',
            'expiresAt',
            'qrisImage',
            'qrisMerchant',
            'paymentTimeout'
        ));
    }

    /**
     * Upload payment proof (receipt/screenshot)
     */
    public function uploadProof(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        // Validate order
        if ($order->status !== Order::STATUS_AWAITING_PAYMENT) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dalam status menunggu pembayaran.'
            ], 422);
        }

        $validated = $request->validate([
            'proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120'], // 5MB
        ], [
            'proof.required' => 'Upload bukti pembayaran wajib diisi.',
            'proof.image' => 'File harus berupa gambar.',
            'proof.mimes' => 'Format gambar hanya jpg, jpeg, atau png.',
            'proof.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        try {
            // Delete old proof if exists
            if ($order->paymentProof) {
                Storage::disk('public')->delete($order->paymentProof->file_path);
                $order->paymentProof->delete();
            }

            // Store new proof
            $file = $validated['proof'];
            $path = $file->store('payment-proofs', 'public');
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();

            // Create payment proof record
            $proof = PaymentProof::create([
                'order_id' => $order->id,
                'file_path' => $path,
                'file_name' => $fileName,
                'file_size' => $fileSize,
                'status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bukti pembayaran berhasil diunggah. Tunggu konfirmasi admin.',
                'proof_id' => $proof->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment proof upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah bukti pembayaran. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Download QRIS as image
     */
    public function downloadQris(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        if ($order->status !== Order::STATUS_AWAITING_PAYMENT) {
            return redirect()->route('home')
                ->with('error', 'Pesanan tidak valid.');
        }

        $qrisPath = setting('qris_image');
        if (!$qrisPath || !Storage::disk('public')->exists($qrisPath)) {
            return redirect()->back()
                ->with('error', 'QRIS tidak tersedia.');
        }

        return Storage::disk('public')->download(
            $qrisPath,
            'QRIS-' . $order->order_number . '.png'
        );
    }

    /**
     * Get payment status API (for AJAX polling)
     */
    public function status(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        $proof = $order->paymentProof;
        $expiresAt = $order->created_at->addHours(24);
        $remainingSeconds = max(0, $expiresAt->diffInSeconds(now(), false));

        return response()->json([
            'order_number' => $order->order_number,
            'status' => $order->status,
            'has_proof' => $proof !== null,
            'proof_status' => $proof?->status,
            'expires_at' => $expiresAt->toIso8601String(),
            'remaining_seconds' => $remainingSeconds,
            'is_expired' => $remainingSeconds <= 0,
        ]);
    }
}
