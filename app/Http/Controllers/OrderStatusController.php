<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    /**
     * Show order status page (accessible by order number)
     */
    public function show(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        return view('frontend.order-status', compact('order'));
    }

    /**
     * Get order status as JSON (for AJAX polling)
     */
    public function api(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->with('items', 'paymentProof')
            ->firstOrFail();

        $paymentProof = $order->paymentProof;
        $expiresAt = $order->created_at->copy()->addHours(24);
        $remainingSeconds = max(0, (int) now()->diffInSeconds($expiresAt, false));

        return response()->json([
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'status' => $order->status,
            'status_label' => $order->status_label,
            'total' => $order->total,
            'created_at' => $order->created_at->toIso8601String(),
            'paid_at' => $order->paid_at?->toIso8601String(),
            'payment_method' => $order->payment_method,
            'has_proof' => $paymentProof !== null,
            'proof_status' => $paymentProof?->status,
            'expires_at' => $expiresAt->toIso8601String(),
            'remaining_seconds' => $remainingSeconds,
            'is_expired' => $remainingSeconds <= 0,
            'items_count' => $order->items()->count(),
        ]);
    }
}
