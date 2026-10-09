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
            'order'         => $order,
            'autoPrint'     => $request->boolean('print'),
            'qrisImage'     => $qrisImage,
            'qrisMerchant'  => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
            'rekeningImage' => $publicUrl(config('invoice.rekening_image')),
            'paidImage'     => $publicUrl(config('invoice.paid_image')),
        ]);
    }
}