<?php

namespace App\Http\Requests;

use Illuminate\Validation\Validator;

/**
 * Edit invoice manual.
 *
 * Aturan dasir pinjam dari ManualOrderStoreRequest; tambahannya satu:
 * total hasil edit tidak boleh lebih kecil dari yang sudah dibayar
 * pelanggan (deposit + pelunasan tercatat), supaya sisa nggak negatif.
 */
class ManualOrderUpdateRequest extends ManualOrderStoreRequest
{
    public function withValidator(Validator $validator): void
    {
        parent::withValidator($validator);

        $validator->after(function (Validator $validator) {
            $order = $this->route('order');
            $recorded = (int) $order->payments()->sum('amount');
            if ($recorded <= 0) {
                return;
            }

            $itemDeposit = (int) collect($this->input('items', []))
                ->sum(fn ($item) => (int) ($item['deposit'] ?? 0));
            $deposit = $itemDeposit > 0 ? $itemDeposit : (int) $this->input('down_payment', 0);
            $subtotal = collect($this->input('items', []))
                ->sum(fn ($item) => (int) ($item['quantity'] ?? 0) * (int) ($item['product_price'] ?? 0));
            $total = $subtotal + (int) $this->input('shipping_cost', 0) + (int) $this->input('fee', 0);

            if ($total < $deposit + $recorded) {
                $validator->errors()->add('items', 'Total baru '.format_price($total)
                    .' lebih kecil dari pembayaran yang sudah tercatat '.format_price($deposit + $recorded)
                    .'. Tambah item atau naikkan harga dulu sebelum mengecilkan tagihan.');
            }
        });
    }
}
