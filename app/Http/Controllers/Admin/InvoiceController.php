<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    /**
     * Rekapan pesanan (invoice manual dari admin): tabel ledger harian
     * dengan deposit, pelunasan, sisa otomatis, ringkasan, filter, dan export.
     * Order checkout biasa tidak muncul di sini.
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Order::class);

        $query = Order::withCount('items')
            ->withSum('items as items_qty', 'quantity')
            ->withSum('payments as payments_sum', 'amount')
            ->where('payment_method', 'manual');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('pay_status') && $request->input('pay_status') !== 'all') {
            match ($request->input('pay_status')) {
                'lunas' => $query->whereNotNull('paid_at'),
                'dp' => $query->whereNull('paid_at')->where(fn ($q) => $q->where('down_payment', '>', 0)->orWhereHas('payments')),
                'belum' => $query->whereNull('paid_at')->where('down_payment', 0)->whereDoesntHave('payments'),
                default => null,
            };
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_whatsapp', 'like', "%{$term}%");
            });
        }

        $summary = $this->ledgerSummary(clone $query);

        $invoices = $query->with('items')->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.invoices.index', compact('invoices', 'summary'));
    }

    /**
     * Ringkasan ledger dari query yang sudah difilter (tanpa pagination).
     */
    private function ledgerSummary($query): array
    {
        $ids = (clone $query)->pluck('orders.id');

        // select() me-reset kolom bawaan (orders.* + subselect withCount),
        // sehingga query agregat valid di MySQL strict mode (only_full_group_by).
        $base = (clone $query)->select([
            DB::raw('COUNT(*) as orders'),
            DB::raw('COALESCE(SUM(total), 0) as grand'),
            DB::raw('COALESCE(SUM(down_payment), 0) as dp'),
            DB::raw('COUNT(DISTINCT customer_whatsapp) as customers'),
        ])->first();

        $qty = \App\Models\OrderItem::whereIn('order_id', $ids)->sum('quantity');
        $paid = \App\Models\OrderPayment::whereIn('order_id', $ids)->sum('amount');

        $grand = (int) ($base->grand ?? 0);
        $dp = (int) ($base->dp ?? 0);

        return [
            'customers' => (int) ($base->customers ?? 0),
            'orders' => (int) ($base->orders ?? 0),
            'qty' => (int) $qty,
            'grand' => $grand,
            'dp' => $dp,
            'paid' => $dp + (int) $paid,
            'remaining' => max(0, $grand - $dp - (int) $paid),
        ];
    }

    /**
     * Export CSV ledger dengan filter yang sama seperti daftar.
     */
    public function export(Request $request)
    {
        Gate::authorize('viewAny', Order::class);

        $query = Order::withCount('items')
            ->withSum('items as items_qty', 'quantity')
            ->withSum('payments as payments_sum', 'amount')
            ->where('payment_method', 'manual');

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('pay_status') && $request->input('pay_status') !== 'all') {
            match ($request->input('pay_status')) {
                'lunas' => $query->whereNotNull('paid_at'),
                'dp' => $query->whereNull('paid_at')->where(fn ($q) => $q->where('down_payment', '>', 0)->orWhereHas('payments')),
                'belum' => $query->whereNull('paid_at')->where('down_payment', 0)->whereDoesntHave('payments'),
                default => null,
            };
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                    ->orWhere('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_whatsapp', 'like', "%{$term}%");
            });
        }

        $rows = $query->with('items')->orderBy('created_at', 'desc')->get();

        $filename = 'rekapan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['No Pesanan', 'Tanggal', 'Customer', 'WhatsApp', 'Alamat', 'Barang', 'Qty', 'Subtotal', 'Ongkir', 'Fee Jastip', 'Deposit', 'Pelunasan', 'Grand Total', 'Sisa', 'Status Bayar', 'Status Pesanan']);
            foreach ($rows as $order) {
                $paid = (int) $order->down_payment + (int) $order->payments_sum;
                $items = $order->items->map(fn ($i) => $i->product_name.' x'.$i->quantity.' @ '.format_price($i->product_price))->implode('; ');
                fputcsv($out, [
                    '#'.$order->no,
                    $order->created_at->format('Y-m-d H:i'),
                    $order->customer_name,
                    $order->customer_whatsapp,
                    $order->customer_address,
                    $items,
                    $order->items_qty,
                    $order->subtotal,
                    $order->shipping_cost,
                    $order->fee,
                    $order->down_payment,
                    $order->payments_sum,
                    $order->total,
                    max(0, $order->total - $paid),
                    $order->payment_status_label,
                    $order->status_label,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
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

        $qrisImage = qris_url();

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
