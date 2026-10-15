<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use Dompdf\Dompdf;
use Dompdf\Options;
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

        $qty = OrderItem::whereIn('order_id', $ids)->sum('quantity');
        $paid = OrderPayment::whereIn('order_id', $ids)->sum('amount');

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
     * Versi PDF invoice untuk dikirim lewat WhatsApp.
     *
     * Route PUBLIK tanpa login, diakses lewat token acak pendek (link /i/{token}).
     * Token tak bisa ditebak, jadi data pelanggan tetap aman.
     */
    public function invoicePdf(string $token)
    {
        $order = Order::where('public_token', $token)->firstOrFail();

        $options = new Options;
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('invoices.pdf', ['doc' => $this->docFromOrder($order)])->render());
        $dompdf->setPaper('a4');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="invoice-'.$order->no.'.pdf"',
        ]);
    }

    /**
     * Pratinjau cetak (modal "Pratinjau Cetak Sampel Invoice").
     *
     * Memakai TEMPLATE YANG SAMA dengan PDF (invoices/_template). Bedanya hanya
     * sumber data: di sini data contoh, di PDF data pesanan nyata. Jadi apa yang
     * dilihat di pratinjau identik dengan hasil PDF.
     */
    public function sample(Request $request)
    {
        Gate::authorize('viewAny', Order::class);

        return view('admin.invoices.sample', [
            'preset' => strtoupper((string) $request->input('preset', 'COMPACT')),
            'doc' => $this->docFromFixture(),
        ]);
    }

    /**
     * Susun view-model dokumen dari pesanan nyata.
     *
     * @return array<string, mixed>
     */
    private function docFromOrder(Order $order): array
    {
        $order->loadMissing('items');

        $paid = (int) $order->down_payment + (int) $order->payments_sum;
        $left = max(0, (int) $order->total - $paid);
        $storeWa = setting('whatsapp');

        $totals = [
            ['label' => 'Subtotal', 'value' => format_price($order->subtotal)],
            ['label' => 'Ongkir', 'value' => format_price($order->shipping_cost)],
        ];
        if ((int) ($order->fee ?? 0) > 0) {
            $totals[] = ['label' => 'Biaya layanan', 'value' => format_price($order->fee)];
        }
        if ((int) ($order->discount ?? 0) > 0) {
            $totals[] = ['label' => 'Diskon', 'value' => '-'.format_price($order->discount), 'muted' => true];
        }

        $payment = $order->is_paid
            ? 'Pembayaran diterima. Terima kasih'.($order->paid_at ? ', '.$order->paid_at->format('d M Y, H:i') : '').'.'
            : 'Silakan transfer, lalu kirim bukti via WhatsApp'.($storeWa ? ' ('.$storeWa.')' : '').' dengan menyebut nomor invoice #'.$order->no.'.';

        return [
            'brand' => setting('brand_name', 'NITIP DI END'),
            'owner' => setting('brand_owner'),
            'store_wa' => $storeWa,
            'title' => $paid > 0 && ! $order->is_paid ? 'TAGIHAN' : 'INVOICE',
            'number' => $order->no,
            'date' => $order->created_at->format('d M Y, H:i'),
            'status_text' => $order->is_paid ? 'LUNAS' : 'BELUM BAYAR',
            'status_color' => $order->is_paid ? '#15803D' : '#B45309',
            'customer' => [
                'name' => $order->customer_name,
                'wa' => $order->customer_whatsapp,
                'address' => $order->customer_address,
            ],
            'shipping' => array_values(array_filter([
                ['label' => 'Ekspedisi', 'value' => $order->shipping_method ?: '-'],
                $order->resi ? ['label' => 'Resi', 'value' => $order->resi] : null,
                ['label' => 'Jumlah item', 'value' => $order->items->sum('quantity').' pcs'],
            ])),
            'items' => $order->items->map(fn ($i) => [
                'name' => $i->product_name,
                'unit' => $i->unit,
                'qty' => $i->quantity,
                'price' => format_price($i->product_price),
                'subtotal' => format_price($i->subtotal),
            ])->all(),
            'totals' => $totals,
            'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price($order->total)],
            'left' => $left > 0 ? ['label' => 'Sisa', 'value' => format_price($left)] : null,
            'payment_text' => $payment,
            'footer' => 'Terima kasih telah menitip di '.setting('brand_name', 'NITIP DI END').'.',
        ];
    }

    /**
     * Contoh data untuk pratinjau. Struktur ini SAMA dengan docFromOrder(),
     * jadi template identik. Angka dihitung dari baris item, bukan hardcode.
     *
     * @return array<string, mixed>
     */
    private function docFromFixture(): array
    {
        $items = [
            ['name' => 'Uniqlo Heattech Extra Warm Thermal', 'unit' => 'pcs', 'qty' => 2, 'price' => 299000],
            ['name' => 'Gentle Monster Her 01 Sunglasses', 'unit' => 'pcs', 'qty' => 1, 'price' => 3200000],
        ];

        $qty = 0;
        $subtotal = 0;
        $rows = [];
        foreach ($items as $item) {
            $line = $item['qty'] * $item['price'];
            $qty += $item['qty'];
            $subtotal += $line;
            $rows[] = [
                'name' => $item['name'],
                'unit' => $item['unit'],
                'qty' => $item['qty'],
                'price' => format_price($item['price']),
                'subtotal' => format_price($line),
            ];
        }

        $ongkir = 30000;
        $fee = 320000;

        return [
            'brand' => setting('brand_name', 'JASTIP IMOBY BANDUNG BY DANESHA'),
            'owner' => setting('brand_owner'),
            'store_wa' => setting('whatsapp'),
            'title' => 'INVOICE',
            'number' => 'INV-SAMPLE-2026',
            'date' => '28-09-2026 00:00',
            'status_text' => 'BELUM BAYAR',
            'status_color' => '#B45309',
            'customer' => [
                'name' => 'Nabila Putri (Sampel Pelanggan)',
                'wa' => '628123456789',
                'address' => 'Jl. Contoh No. 1, Bandung',
            ],
            'shipping' => [
                ['label' => 'Ekspedisi', 'value' => 'JNE YES (Express)'],
                ['label' => 'Jumlah item', 'value' => $qty.' pcs'],
            ],
            'items' => $rows,
            'totals' => [
                ['label' => 'Subtotal', 'value' => format_price($subtotal)],
                ['label' => 'Ongkir', 'value' => format_price($ongkir)],
                ['label' => 'Biaya layanan', 'value' => format_price($fee)],
            ],
            'total' => ['label' => 'TOTAL BAYAR', 'value' => format_price($subtotal + $ongkir + $fee)],
            'left' => ['label' => 'Sisa', 'value' => format_price($subtotal + $ongkir + $fee)],
            'payment_text' => 'Silakan transfer, lalu kirim bukti via WhatsApp dengan menyebut nomor invoice #INV-SAMPLE-2026.',
            'footer' => 'Terima kasih telah berbelanja jastip bersama Jastip Imoby By Damesha.',
        ];
    }
}
