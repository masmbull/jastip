<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    /**
     * Daftar invoice manual (dibuat admin lewat tombol "Buat Invoice").
     * Order checkout biasa tidak muncul di sini.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Order::class);

        $query = Order::withCount('items')->where('payment_method', 'manual');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_whatsapp', 'like', "%{$term}%");
            });
        }

        $invoices = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Halaman invoice siap-cetak untuk satu pesanan.
     *
     * Tanpa dependency PDF: pakai cetak bawaan browser (Ctrl+P -> Save as PDF).
     */
    public function invoice(Request $request, Order $order)
    {
        Gate::authorize('view', $order);

        $order->load('items');

        // Gambar invoice disimpan di public/ (lihat config/invoice.php),
        // jadi pakai asset() bukan disk storage.
        $publicUrl = fn (?string $path) => $path && is_file(public_path($path)) ? asset($path) : null;

        $qrisPath = setting('qris_image');
        $qrisImage = $qrisPath ? Storage::url($qrisPath) : null;

        return view('admin.orders.invoice', [
            'order' => $order,
            'autoPrint' => $request->boolean('print'),
            'qrisImage' => $qrisImage,
            'qrisMerchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
            'rekeningImage' => $publicUrl(config('invoice.rekening_image')),
            'paidImage' => $publicUrl(config('invoice.paid_image')),
        ]);
    }
}
