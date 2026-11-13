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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'customer_name' => is_string($this->input('customer_name'))
                ? trim($this->input('customer_name'))
                : $this->input('customer_name'),
            'customer_whatsapp' => is_string($this->input('customer_whatsapp'))
                ? trim($this->input('customer_whatsapp'))
                : $this->input('customer_whatsapp'),
            'customer_email' => filled($this->input('customer_email'))
                ? strtolower(trim((string) $this->input('customer_email')))
                : null,
            'customer_address' => is_string($this->input('customer_address'))
                ? trim($this->input('customer_address'))
                : $this->input('customer_address'),
        ]);
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_whatsapp' => ['required', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')->where('role', 'customer')],
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
            'legacy_fee' => ['nullable', 'integer', 'min:0'],
            'show_fee' => ['nullable', 'boolean'],
            'mark_paid' => ['nullable', 'boolean'],
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
            'items.*.fee' => ['nullable', 'integer', 'min:0'],
            'items.*.status' => ['nullable', Rule::in([OrderItem::STATUS_READY, OrderItem::STATUS_PO])],
        ];
    }

    public function feeTotal(): int
    {
        // Fee dimasukkan per unit dan dikalikan qty, sama seperti DP.
        $itemFee = (int) collect($this->input('items', []))
            ->sum(fn ($item) => (int) ($item['fee'] ?? 0) * (int) ($item['quantity'] ?? 0));
        $order = $this->route('order');

        if ($this->exists('legacy_fee')) {
            $legacyFee = (int) $this->input('legacy_fee', 0);
        } elseif ($order instanceof Order && $this->exists('fee')) {
            $legacyFee = (int) $this->input('fee', 0);
        } elseif ($order instanceof Order) {
            // Fee item dihitung per unit x qty, jadi legacy fee dicari
            // sebagai selisih total fee historis dengan jumlah fee item x qty.
            $legacyFee = max(0, (int) $order->fee - (int) $order->items()->get()->sum(fn ($item) => (int) $item->fee * (int) $item->quantity));
        } else {
            $legacyFee = $itemFee === 0 ? (int) $this->input('fee', 0) : 0;
        }

        return $itemFee + max(0, $legacyFee);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Deposit is entered per unit, so the order deposit is the sum
            // of each item's unit deposit multiplied by its quantity.
            $itemDeposit = (int) collect($this->input('items', []))
                ->sum(fn ($item) => (int) ($item['deposit'] ?? 0) * (int) ($item['quantity'] ?? 0));

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
            $total = $subtotal + (int) $this->input('shipping_cost', 0) + $this->feeTotal();
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
