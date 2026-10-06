<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Services\WhatsappService;
use Illuminate\Support\Facades\Log;

class SendOrderStatusNotification
{
    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        $order = $event->order;
        $newStatus = $event->newStatus;

        if (!$order->customer_whatsapp) {
            return;
        }

        try {
            $whatsappUrl = match ($newStatus) {
                'confirmed', 'verified' => WhatsappService::sendPaymentNotification(
                    $order->customer_whatsapp,
                    $order->order_number,
                    'verified'
                ),
                'shipped' => WhatsappService::sendShippedNotification(
                    $order->customer_whatsapp,
                    $order->order_number
                ),
                default => null,
            };

            if ($whatsappUrl) {
                Log::info("Order status notification sent", [
                    'order_number' => $order->order_number,
                    'customer_phone' => $order->customer_whatsapp,
                    'status' => $newStatus,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Failed to send order status notification", [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
