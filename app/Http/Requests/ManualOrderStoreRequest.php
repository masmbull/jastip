<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ManualOrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_whatsapp' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_address' => ['required', 'string', 'max:2000'],
            'customer_notes' => ['nullable', 'string'],
            'admin_notes' => ['nullable', 'string'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_district' => ['nullable', 'string', 'max:150'],
            'shipping_village' => ['nullable', 'string', 'max:150'],
            'shipping_method' => ['nullable', 'string', 'max:100'],
            'shipping_cost' => ['nullable', 'integer', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'fee' => ['nullable', 'integer', 'min:0'],
            'mode' => ['nullable', Rule::in(['deposit', 'lunas'])],
            'down_payment' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', Rule::in(array_keys(Order::statuses()))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.unit' => ['nullable', 'string', 'max:30'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.product_price' => ['required', 'integer', 'min:0'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'items.*.deposit' => ['nullable', 'integer', 'min:0'],
            'items.*.status' => ['nullable', Rule::in([OrderItem::STATUS_READY, OrderItem::STATUS_PO])],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Deposit total = jumlah deposit per barang (kalau ada); kalau
            // tidak, pakai input manual `down_payment` (kompat order lama).
            $itemDeposit = (int) collect($this->input('items', []))
                ->sum(fn ($item) => (int) ($item['deposit'] ?? 0));

            $dp = $itemDeposit > 0 ? $itemDeposit : (int) $this->input('down_payment', 0);

            // Mode deposit = invoice untuk menagih DP, jadi DP-nya wajib ada.
            // Mode lunas / tanpa mode (API lama) bebas.
            if ((string) $this->input('mode', '') === 'deposit' && $dp <= 0) {
                $validator->errors()->add('down_payment', 'Mode Deposit butuh nilai deposit. Isi depositnya, atau ganti ke mode Lunas.');
            }

            if ($dp <= 0) {
                return;
            }
            $subtotal = collect($this->input('items', []))
                ->sum(fn ($item) => (int) ($item['quantity'] ?? 0) * (int) ($item['product_price'] ?? 0));
            $total = $subtotal + (int) $this->input('shipping_cost', 0) + (int) $this->input('fee', 0);
            if ($dp > $total) {
                $validator->errors()->add('down_payment', 'Deposit tidak boleh lebih besar dari total tagihan.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'customer_whatsapp.required' => 'Nomor WhatsApp pelanggan wajib diisi.',
            'customer_address.required' => 'Alamat wajib diisi untuk hitung ongkir.',
            'items.required' => 'Tambahkan minimal satu item.',
            'items.min' => 'Tambahkan minimal satu item.',
            'items.*.product_name.required' => 'Nama barang wajib diisi.',
            'items.*.quantity.required' => 'Jumlah wajib diisi.',
            'items.*.quantity.min' => 'Jumlah minimal 1.',
            'items.*.product_price.required' => 'Harga wajib diisi.',
        ];
    }
}
