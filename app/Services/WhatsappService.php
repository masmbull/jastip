<?php

namespace App\Services;

class WhatsappService
{
    /**
     * Generate WhatsApp URL with order message.
     */
    public static function orderUrl(array $orderData): string
    {
        // Chat harus ke nomor toko, bukan nomor pembeli.
        $phone = setting('whatsapp', '6285123456789');
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
     * Send payment status notification to customer
     */
    public static function sendPaymentNotification(string $phone, string $orderNumber, string $status, string $reason = null): string
    {
        $message = '';

        if ($status === 'verified') {
            $message = "Halo 👋\n\n";
            $message .= "Pembayaran untuk pesanan {$orderNumber} telah berhasil diverifikasi! ✓\n\n";
            $message .= "Pesanan kamu sudah dikonfirmasi dan akan segera diproses.\n\n";
            $message .= "Cek status pesananmu di sini:\n";
            $message .= "🔗 " . route('order.show', $orderNumber) . "\n\n";
            $message .= "Terima kasih 🙏";
        } elseif ($status === 'rejected') {
            $message = "Halo 👋\n\n";
            $message .= "Sayangnya bukti pembayaran untuk pesanan {$orderNumber} tidak dapat diterima.\n\n";
            $message .= "Alasan: {$reason}\n\n";
            $message .= "Silakan unggah bukti pembayaran yang benar melalui:\n";
            $message .= "🔗 " . route('payment.waiting', $orderNumber) . "\n\n";
            $message .= "Jika ada pertanyaan, hubungi kami ya. Terima kasih 🙏";
        }

        return whatsapp_url($phone, $message);
    }

    /**
     * Send order shipped notification
     */
    public static function sendShippedNotification(string $phone, string $orderNumber, string $trackingNumber = null): string
    {
        $message = "Halo 👋\n\n";
        $message .= "Pesanan {$orderNumber} sudah dikirim! 📦\n\n";

        if ($trackingNumber) {
            $message .= "Nomor resi: {$trackingNumber}\n";
            $message .= "Cek tracking: https://tracking.logistik.co.id\n\n";
        }

        $message .= "Cek status pesananmu di sini:\n";
        $message .= "🔗 " . route('order.show', $orderNumber) . "\n\n";
        $message .= "Terima kasih 🙏";

        return whatsapp_url($phone, $message);
    }
}