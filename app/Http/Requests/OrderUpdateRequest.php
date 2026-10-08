<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Order::statuses()))],
            'admin_notes' => ['nullable', 'string'],
            'resi' => ['nullable', 'string', 'max:100'],
            'courier' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status pesanan wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ];
    }
}