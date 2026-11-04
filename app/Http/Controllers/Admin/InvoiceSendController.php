<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\InvoiceImageRenderer;
use App\Services\WhatsappGatewayService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Kirim invoice sebagai GAMBAR langsung ke WhatsApp pelanggan lewat gateway
 * self-hosted (OpenWA). Berbeda dari tombol lama yang membuka share sheet
 * browser: di sini server yang merender PNG dan mengirimnya, jadi admin cukup
 * satu klik dan tidak perlu memilih kontak.
 */
class InvoiceSendController extends Controller
{
    /**
     * Kirim invoice satu pesanan ke nomor pelanggan.
     */
    public function send(Request $request, Order $order): RedirectResponse
    {
        Gate::authorize('update', $order);

        $back = redirect()->back();

        if ($order->wa_invoice_sent_at && ! $request->boolean('force')) {
            return $back->with('error', 'Invoice #'.$order->no.' sudah dikirim ke WhatsApp pada '
                .$order->wa_invoice_sent_at->format('d M Y, H:i').'. Kirim ulang hanya bila memang perlu.');
        }

        $phone = (string) $order->customer_whatsapp;
        if (wa_chat_id($phone) === '') {
            return $this->fail($order, $back, 'Nomor WhatsApp pelanggan kosong atau tidak valid.');
        }

        $gateway = WhatsappGatewayService::fromSettings();
        if (! $gateway->isConfigured()) {
            return $this->fail($order, $back, 'Gateway WhatsApp belum diatur. Isi di Pengaturan lalu coba lagi.');
        }

        $status = $gateway->status();
        if (! $status['connected']) {
            return $this->fail($order, $back, $status['error'] ?? 'Sesi WhatsApp belum siap.');
        }

        $renderer = app(InvoiceImageRenderer::class);
        if (! $renderer->isAvailable()) {
            return $this->fail($order, $back, 'Server belum punya Chrome/Edge headless untuk membuat gambar invoice.');
        }

        InvoiceImageRenderer::ensureTmpDirectory();

        // HTML-nya memakai template yang sama dengan pratinjau/PDF.
        $html = view('invoices.html', ['doc' => app(InvoiceController::class)->docFromOrder($order)])->render();
        $png = $renderer->render($html);

        if ($png === null) {
            return $this->fail($order, $back, 'Gagal membuat gambar invoice di server.');
        }

        $result = $gateway->sendImage($phone, $png, $this->caption($order));

        if (! $result['ok']) {
            return $this->fail($order, $back, $result['error'] ?? 'Gagal mengirim invoice.');
        }

        $order->forceFill([
            'wa_invoice_sent_at' => now(),
            'wa_invoice_error' => null,
        ])->save();

        return $back->with('success', 'Invoice #'.$order->no.' terkirim ke WhatsApp '.$order->customer_whatsapp.'.');
    }

    /**
     * Ringkasan yang ikut sebagai caption, supaya pelanggan bisa membaca
     * total/deposit/sisa tanpa harus membuka gambarnya.
     */
    private function caption(Order $order): string
    {
        $paid = (int) $order->totalPaid;
        $left = max(0, (int) $order->total - $paid);
        $brand = setting('brand_name', 'NITIP DI END');

        $lines = [
            'Halo '.$order->customer_name.', berikut invoice '.$order->no.' dari '.$brand.'.',
            '',
            'Total: '.format_price($order->total),
        ];

        if ($paid > 0) {
            $lines[] = 'Sudah dibayar: '.format_price($paid);
        }

        $lines[] = $left > 0 ? 'Sisa: '.format_price($left) : 'Status: LUNAS';

        $bank = bank_account_text();
        if ($left > 0 && $bank !== '') {
            $lines[] = '';
            $lines[] = 'TRANSFER HANYA KE:';
            $lines[] = $bank;
            $lines[] = 'Selain itu di luar tanggung jawab kami.';
        }

        $lines[] = '';
        $lines[] = 'Terima kasih.';

        return implode("\n", $lines);
    }

    private function fail(Order $order, RedirectResponse $back, string $message): RedirectResponse
    {
        $order->forceFill(['wa_invoice_error' => mb_substr($message, 0, 255)])->save();

        return $back->with('error', $message);
    }
}
