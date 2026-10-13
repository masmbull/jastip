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
            'embed' => $request->boolean('embed'),
            'qrisImage' => $qrisImage,
            'qrisMerchant' => setting('qris_merchant_name', setting('brand_name', 'NITIP DI END')),
            'rekeningImage' => $publicUrl(config('invoice.rekening_image')),
            'paidImage' => $publicUrl(config('invoice.paid_image')),
        ]);
    }

    /**
     * Dokumen pratinjau cetak untuk SAMPEL invoice (struk thermal).
     *
     * Dimuat di dalam <iframe> modal "Pratinjau Cetak Sampel Invoice" pada
     * halaman daftar invoice. Isi memakai data contoh (fixture): preview dan
     * PDF identik karena keduanya merender halaman yang sama.
     *
     * Ganti isi sampleInvoice() dengan data transaksi nyata bila sudah tersedia;
     * struktur data sudah dipisah dari view supaya view tidak perlu diubah.
     */
    public function sample(Request $request)
    {
        Gate::authorize('viewAny', Order::class);

        return view('admin.invoices.sample', [
            'preset' => strtoupper((string) $request->input('preset', 'COMPACT')),
            'invoice' => $this->sampleInvoice(),
        ]);
    }

    /**
     * Data contoh (fixture) untuk pratinjau sampel. Perhitungan dilakukan dari
     * baris item (qty x harga, fee jastip per pc), bukan angka total hardcoded.
     *
     * @return array<string, mixed>
     */
    private function sampleInvoice(): array
    {
        $items = [
            ['name' => 'Uniqlo Heattech Extra Warm Thermal', 'qty' => 2, 'price' => 299000, 'jastip' => 35000, 'variant' => 'Black [L]'],
            ['name' => 'Gentle Monster Her 01 Sunglasses', 'qty' => 1, 'price' => 3200000, 'jastip' => 250000, 'variant' => 'Glossy Black [Free Size]'],
        ];

        $qty = 0;
        $subtotal = 0;
        $feeJastip = 0;

        foreach ($items as &$item) {
            $item['subtotal'] = $item['qty'] * $item['price'];
            $item['fee_total'] = $item['qty'] * $item['jastip'];
            $qty += $item['qty'];
            $subtotal += $item['subtotal'];
            $feeJastip += $item['fee_total'];
        }
        unset($item);

        $packing = 15000;

        return [
            'brand_line1' => 'JASTIP IMOBY BANDUNG BY',
            'brand_line2' => 'DANESHA',
            'logo_text' => 'D',
            'subtitle' => 'STRUK INVOICE JASTIP ESTIMASI (SAMPEL)',
            'number' => 'INV-SAMPLE-2026',
            'date' => '28-09-2026 00:00',
            'customer' => [
                'name' => 'Nabila Putri (Sampel Pelanggan)',
                'wa' => '628123456789',
                'courier' => 'JNE YES (Express)',
            ],
            'items' => $items,
            'qty' => $qty,
            'subtotal' => $subtotal,
            'fee_jastip' => $feeJastip,
            'packing' => $packing,
            'total' => $subtotal + $feeJastip + $packing,
            'payment' => ['title' => 'REK. PEMBAYARAN:', 'lines' => ['BCA 098765432', 'An Damesha']],
            'thanks' => 'Terima kasih telah berbelanja jastip bersama Jastip Imoby By Damesha!',
            'footer_note' => '=== SIMPAN STRUK INI SEBAGAI BUKTI PESANAN ===',
        ];
    }
}
