<?php

namespace App\Http\Requests;

use App\Services\ShippingEstimator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'whatsapp' => ['required', 'string', 'regex:/^(\+62|0)[0-9]{9,12}$/', 'max:15'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['required', 'string', 'min:10'],
            'notes' => ['nullable', 'string'],
            'shipping_method' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', Rule::in(array_keys(app(ShippingEstimator::class)->cities()))],
            'courier' => ['required', 'string'],
            'weight' => ['nullable', 'numeric', 'min:0.1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid. Gunakan format: 0812xxxxxxxx atau +628xxxxxxxxx',
            'email.email' => 'Alamat email tidak valid.',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.min' => 'Alamat terlalu pendek, minimal 10 karakter.',
            'shipping_method.required' => 'Metode pengiriman wajib dipilih.',
            'city.required' => 'Kota tujuan wajib dipilih untuk hitung ongkir.',
            'city.in' => 'Kota tujuan tidak dikenal.',
            'courier.required' => 'Pilih salah satu ekspedisi untuk ongkir.',
        ];
    }
}