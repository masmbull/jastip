<?php

namespace App\Services;

class WhatsappService
{
    /**
     * Generate WhatsApp URL with order message.
     */
    public static function orderUrl(array $orderData): string
    {
        $phone = $orderData['whatsapp'] ?? setting('whatsapp', '6285123456789');
        $message = self::buildOrderMessage($orderData);

        return whatsapp_url($phone, $message);
    }

    /**
     * Build the WhatsApp message for an order.
     */
    public static function buildOrderMessage(array $orderData): string
    {
        $ownerName = setting('brand_owner', 'Nabila Adriyana');

        $message = "Halo Kak {$ownerName} 👋\n\n";
        $message .= "Saya mau nitip:\n\n";

        foreach ($orderData['items'] as $item) {
            $message .= "{$item['name']} x{$item['quantity']}\n";
        }

        $message .= "\nTotal barang: " . format_price($orderData['subtotal']) . "\n";

        if (($orderData['shipping_cost'] ?? 0) > 0) {
            $message .= "Ongkir: " . format_price($orderData['shipping_cost']) . "\n";
        }

        if (($orderData['fee'] ?? 0) > 0) {
            $message .= "Biaya Fee: " . format_price($orderData['fee']) . "\n";
        }

        $message .= "Total: " . format_price($orderData['total']) . "\n\n";

        $message .= "Nama:\n{$orderData['name']}\n\n";
        $message .= "WhatsApp:\n{$orderData['whatsapp']}\n\n";
        $message .= "Alamat:\n{$orderData['address']}\n\n";

        if (!empty($orderData['notes'])) {
            $message .= "Catatan:\n{$orderData['notes']}\n\n";
        }

        if (!empty($orderData['shipping_method'])) {
            $message .= "Metode pengiriman:\n{$orderData['shipping_method']}\n\n";
        }

        $message .= "Terima kasih 🙏";

        return $message;
    }

    /**
     * Generate a simple WhatsApp contact URL.
     */
    public static function contactUrl(string $message = ''): string
    {
        $phone = setting('whatsapp', '6285123456789');

        return whatsapp_url($phone, $message);
    }
}