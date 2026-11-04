<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        foreach (config('settings.groups', []) as $meta) {
            foreach ($meta['settings'] ?? [] as $key => $definition) {
                $rules[$key] = match ($definition['type'] ?? 'text') {
                    'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,gif,webp,ico', 'max:10240'],
                    'email' => ['nullable', 'email'],
                    'number' => ['nullable', 'numeric'],
                    'boolean' => ['nullable', 'boolean'],
                    'password' => ['nullable', 'string', 'max:255'],
                    'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                    default => ['nullable', 'string'],
                };
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'brand_logo.max' => 'Ukuran logo maksimal 10 MB.',
            'brand_favicon.max' => 'Ukuran favicon maksimal 10 MB.',
            'qris_image.max' => 'Ukuran gambar QRIS maksimal 10 MB.',
        ];
    }
}
