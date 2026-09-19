<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Get or set a setting value.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('format_price')) {
    /**
     * Format a price as Indonesian Rupiah.
     */
    function format_price(int|float|string|null $amount): string
    {
        return 'Rp ' . number_format((int) $amount, 0, ',', '.');
    }
}

if (!function_exists('whatsapp_url')) {
    /**
     * Generate a WhatsApp URL with optional pre-filled message.
     */
    function whatsapp_url(string $phone, string $message = ''): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        // Convert to international format if starts with 0
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $url = 'https://wa.me/' . $phone;

        if ($message) {
            $url .= '?text=' . urlencode($message);
        }

        return $url;
    }
}

if (!function_exists('generate_whatsapp_order')) {
    /**
     * Generate a WhatsApp order message from cart data.
     */
    function generate_whatsapp_order(array $cartItems, array $orderData, int $subtotal, int $shippingCost, int $total): string
    {
        $message = "Halo Kak " . ($orderData['owner_name'] ?? '') . " 👋\n\n";
        $message .= "Saya mau nitip:\n\n";

        foreach ($cartItems as $item) {
            $message .= $item['name'] . " x{$item['quantity']}\n";
        }

        $message .= "\nTotal barang: " . format_price($subtotal) . "\n";

        if ($shippingCost > 0) {
            $message .= "Ongkir: " . format_price($shippingCost) . "\n";
        }

        $message .= "Total: " . format_price($total) . "\n\n";

        $message .= "Nama:\n" . $orderData['name'] . "\n\n";
        $message .= "WhatsApp:\n" . $orderData['whatsapp'] . "\n\n";
        $message .= "Alamat:\n" . $orderData['address'] . "\n\n";

        if (!empty($orderData['notes'])) {
            $message .= "Catatan:\n" . $orderData['notes'] . "\n\n";
        }

        $message .= "Terima kasih 🙏";

        return $message;
    }
}
