<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /**
     * Get or set a setting value.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('media_url')) {
    /**
     * URL publik untuk file yang diunggah ke disk `public` (logo, favicon,
     * QRIS, dll), lengkap dengan cache-buster (?v=<mtime>).
     *
     * Tanpa cache-buster, browser bisa tetap menampilkan gambar lama setelah
     * admin menggantinya di Pengaturan.
     */
    function media_url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $url = asset('storage/'.ltrim($path, '/'));
        $full = public_path('storage/'.ltrim($path, '/'));

        return is_file($full) ? $url.'?v='.filemtime($full) : $url;
    }
}

if (! function_exists('qris_enabled')) {
    /**
     * Status saklar QRIS di Pengaturan -> Pembayaran QRIS.
     */
    function qris_enabled(): bool
    {
        return (bool) setting('qris_enabled', true);
    }
}

if (! function_exists('qris_limit')) {
    /**
     * Batas nominal untuk QRIS. Di atas angka ini QRIS tidak ditampilkan lagi
     * di invoice (hanya rekening), supaya tidak ada transfer besar lewat QRIS.
     */
    function qris_limit(): int
    {
        return (int) setting('qris_max_amount', 500000);
    }
}

if (! function_exists('qris_allowed_for')) {
    /**
     * Apakah QRIS boleh ditampilkan untuk invoice dengan total tertentu.
     * Rule: QRIS aktif DAN total <= batas (di atas batas -> rekening saja).
     */
    function qris_allowed_for(int $total): bool
    {
        return qris_enabled() && $total <= qris_limit() && qris_url() !== null;
    }
}

if (! function_exists('bank_account_lines')) {
    /**
     * Baris rekening pembayaran untuk ditampilkan di invoice.
     * Kosong kalau admin belum mengisi di Pengaturan.
     *
     * @return array<int, string>
     */
    function bank_account_lines(): array
    {
        $lines = array_filter([
            (string) setting('bank_name', ''),
            (string) setting('bank_account_number', ''),
            (string) setting('bank_account_name', ''),
        ], fn ($v) => trim($v) !== '');

        return array_values($lines);
    }
}

if (! function_exists('bank_account_text')) {
    /**
     * Rekening dalam satu baris: "BCA 1234567890 a/n Budi".
     */
    function bank_account_text(): string
    {
        $bank = trim((string) setting('bank_name', ''));
        $number = trim((string) setting('bank_account_number', ''));
        $name = trim((string) setting('bank_account_name', ''));

        $text = trim($bank.' '.$number);
        if ($name !== '') {
            $text = trim($text.' a/n '.$name);
        }

        return $text;
    }
}

if (! function_exists('qris_url')) {
    /**
     * URL gambar QRIS publik, atau null kalau QRIS dimatikan / belum diupload.
     *
     * Satu pintu supaya semua halaman (konfirmasi, bayar, cetak invoice,
     * detail admin) patuh pada saklar yang sama.
     */
    function qris_url(): ?string
    {
        $path = setting('qris_image');

        // Upload disimpan di disk `public` (lihat SettingController::update),
        // jadi URL HARUS diambil dari disk itu. Memakai Storage::url() polos
        // mengikuti disk default (di .env: `local`), sehingga URL-nya salah dan
        // gambar QRIS tidak muncul di halaman pembayaran.
        return qris_enabled() && $path ? media_url($path) : null;
    }
}

if (! function_exists('format_price')) {
    /**
     * Format a price as Indonesian Rupiah.
     */
    function format_price(int|float|string|null $amount): string
    {
        return 'Rp '.number_format((int) $amount, 0, ',', '.');
    }
}

if (! function_exists('wa_phone')) {
    /**
     * Normalisasi nomor Indonesia ke format internasional tanpa tanda plus,
     * siap dipakai di wa.me. Menangani input umum: "0812...", "+62 812...",
     * "62812...", "812...", termasuk spasi, tanda hubung, dan tanda kurung.
     *
     * Ini titik tunggal supaya link WhatsApp tidak pernah menunjuk nomor
     * yang salah format (WhatsApp menolaknya sebagai "nomor tidak terdaftar").
     */
    function wa_phone(?string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $phone);

        if ($digits === '') {
            return '';
        }

        // 08xxx -> 628xxx
        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        // Sudah 62xxx.
        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        // 8xxx (lupa awalan 0) -> 628xxx.
        if (str_starts_with($digits, '8')) {
            return '62'.$digits;
        }

        return $digits;
    }
}

if (! function_exists('whatsapp_url')) {
    /**
     * Generate a WhatsApp URL with optional pre-filled message.
     */
    function whatsapp_url(string $phone, string $message = ''): string
    {
        $url = 'https://wa.me/'.wa_phone($phone);

        if ($message) {
            $url .= '?text='.urlencode($message);
        }

        return $url;
    }
}

if (! function_exists('generate_whatsapp_order')) {
    /**
     * Generate a WhatsApp order message from cart data.
     */
    function generate_whatsapp_order(array $cartItems, array $orderData, int $subtotal, int $shippingCost, int $total): string
    {
        $message = 'Halo Kak '.($orderData['owner_name'] ?? '')." 👋\n\n";
        $message .= "Saya mau nitip:\n\n";

        foreach ($cartItems as $item) {
            $message .= $item['name']." x{$item['quantity']}\n";
        }

        $message .= "\nTotal barang: ".format_price($subtotal)."\n";

        if ($shippingCost > 0) {
            $message .= 'Ongkir: '.format_price($shippingCost)."\n";
        }

        $message .= 'Total: '.format_price($total)."\n\n";

        $message .= "Nama:\n".$orderData['name']."\n\n";
        $message .= "WhatsApp:\n".$orderData['whatsapp']."\n\n";
        $message .= "Alamat:\n".$orderData['address']."\n\n";

        if (! empty($orderData['notes'])) {
            $message .= "Catatan:\n".$orderData['notes']."\n\n";
        }

        $message .= 'Terima kasih 🙏';

        return $message;
    }
}

if (! function_exists('format_bytes')) {
    /**
     * Format bytes to human-readable format (KB, MB, GB, etc).
     */
    function format_bytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }
}
