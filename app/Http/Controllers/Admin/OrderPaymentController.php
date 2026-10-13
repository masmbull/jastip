<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrderPaymentController extends Controller
{
    /**
     * Catat pelunasan tambahan. Sisa dihitung otomatis dari total
     * dikurangi DP dan seluruh pelunasan. Lunas otomatis bila sisa nol.
     */
    public function store(Request $request, Order $order)
    {
        Gate::authorize('update', $order);

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:1', 'max:'.$order->remaining],
            'paid_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ], [
            'amount.required' => 'Nominal wajib diisi.',
            'amount.min' => 'Nominal minimal Rp 1.',
            'amount.max' => 'Nominal tidak boleh lebih dari sisa tagihan.',
        ]);

        $order->payments()->create([
            'amount' => $data['amount'],
            'paid_at' => $data['paid_at'] ?? now(),
            'note' => $data['note'] ?? null,
        ]);

        $order->refresh();

        if (! $order->is_paid && $order->remaining === 0 && in_array($order->status, [Order::STATUS_AWAITING_PAYMENT, Order::STATUS_PENDING], true)) {
            $order->update(['paid_at' => now(), 'status' => Order::STATUS_CONFIRMED]);
        }

        return back()->with('success', 'Pelunasan '.format_price($data['amount']).' tercatat. Sisa: '.format_price($order->fresh()->remaining).'.');
    }

    public function destroy(Order $order, OrderPayment $payment)
    {
        Gate::authorize('update', $order);

        abort_unless($payment->order_id === $order->id, 404);
        $payment->delete();

        return back()->with('success', 'Catatan pelunasan dihapus. Sisa dihitung ulang otomatis.');
    }
}
