<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    /**
     * List all payment proofs with filtering
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

        return view('admin.payment-proofs.index', compact('proofs', 'status', 'counts'));
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
                ->with('error', 'Bukti pembayaran ini sudah ' . $paymentProof->status . '.');
        }

        try {
            $paymentProof->verify($validated['notes'] ?? null);

            // Notify customer via WhatsApp
            $this->notifyCustomer($paymentProof->order, 'verified');

            return redirect()->route('admin.payment-proofs.show', $paymentProof)
                ->with('success', 'Bukti pembayaran berhasil diverifikasi. Pesanan dikonfirmasi.');
        } catch (\Exception $e) {
            \Log::error('Payment proof verification failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memverifikasi bukti pembayaran.');
        }
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
                ->with('error', 'Bukti pembayaran ini sudah ' . $paymentProof->status . '.');
        }

        try {
            $paymentProof->reject($validated['notes']);

            // Notify customer via WhatsApp
            $this->notifyCustomer($paymentProof->order, 'rejected', $validated['notes']);

            return redirect()->route('admin.payment-proofs.show', $paymentProof)
                ->with('success', 'Bukti pembayaran ditolak. Pelanggan diminta mengunggah ulang.');
        } catch (\Exception $e) {
            \Log::error('Payment proof rejection failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menolak bukti pembayaran.');
        }
    }

    /**
     * Download payment proof file
     */
    public function download(PaymentProof $paymentProof)
    {
        if (!Storage::disk('public')->exists($paymentProof->file_path)) {
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
    private function notifyCustomer(Order $order, string $status, string $reason = null): void
    {
        $message = '';

        if ($status === 'verified') {
            $message = "Halo {$order->customer_name}, pembayaran untuk pesanan {$order->order_number} telah berhasil diverifikasi! ✓ Pesanan kamu sudah dikonfirmasi dan akan segera diproses.";
        } elseif ($status === 'rejected') {
            $message = "Halo {$order->customer_name}, bukti pembayaran untuk pesanan {$order->order_number} ditolak.\n\nAlasan: {$reason}\n\nSilakan unggah bukti pembayaran yang benar melalui halaman pembayaran.";
        }

        if ($message && $order->customer_whatsapp) {
            // TODO: Implement WhatsApp notification via WhatsappService
            // WhatsappService::sendMessage($order->customer_whatsapp, $message);
        }
    }
}
