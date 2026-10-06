<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'address' => ['required', 'string', 'min:10'],
            'notes' => ['nullable', 'string'],
            'shipping_method' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid. Gunakan format: 0812xxxxxxxx atau +628xxxxxxxxx',
            'address.required' => 'Alamat lengkap wajib diisi.',
            'address.min' => 'Alamat terlalu pendek, minimal 10 karakter.',
            'shipping_method.required' => 'Metode pengiriman wajib dipilih.',
        ];
    }
}