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
                    'image' => $key === 'qris_image'
                        ? ['nullable', 'file', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240']
                        : ['nullable', 'file', 'mimes:jpeg,png,jpg,svg,gif,webp,ico', 'max:10240'],
                    'email' => ['nullable', 'email:rfc', 'max:255'],
                    'url' => ['nullable', 'url:http,https', 'max:2048'],
                    'phone' => ['nullable', 'regex:/^\+?[0-9]{8,15}$/'],
                    'number' => ['nullable', 'integer', 'min:0'],
                    'boolean' => ['nullable', 'boolean'],
                    'password' => ['nullable', 'string', 'max:255'],
                    'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
                    'textarea' => ['nullable', 'string', 'max:5000'],
                    default => ['nullable', 'string', 'max:255'],
                };
            }
        }

        return $rules;
    }

    public function messages(): array
    {
        $messages = [
            'brand_logo.max' => 'Ukuran logo maksimal 10 MB.',
            'brand_favicon.max' => 'Ukuran favicon maksimal 10 MB.',
            'qris_image.max' => 'Ukuran gambar QRIS maksimal 10 MB.',
            'brand_logo.uploaded' => 'Logo tidak berhasil diunggah. Pastikan ukuran maksimal 10 MB.',
            'brand_favicon.uploaded' => 'Favicon tidak berhasil diunggah. Pastikan ukuran maksimal 10 MB.',
            'qris_image.uploaded' => 'Gambar QRIS tidak berhasil diunggah. Pastikan ukuran maksimal 10 MB.',
            'brand_logo.mimes' => 'Logo harus berupa gambar PNG, JPG, SVG, GIF, WEBP, atau ICO.',
            'brand_favicon.mimes' => 'Favicon harus berupa gambar PNG, JPG, SVG, GIF, WEBP, atau ICO.',
            'qris_image.mimes' => 'Gambar QRIS harus berupa PNG, JPG, GIF, atau WEBP.',
            'whatsapp.regex' => 'Nomor WhatsApp harus berisi 8 sampai 15 digit, tanpa spasi atau tanda hubung.',
            'email.email' => 'Alamat email harus valid.',
            'wa_gateway_url.url' => 'Alamat gateway harus berupa URL HTTP atau HTTPS yang valid.',
        ];

        foreach (config('settings.groups', []) as $meta) {
            foreach ($meta['settings'] ?? [] as $key => $definition) {
                $label = $definition['label'] ?? $key;

                switch ($definition['type'] ?? 'text') {
                    case 'image':
                        break;
                    case 'number':
                        $messages[$key.'.integer'] = $label.' harus berupa angka bulat.';
                        $messages[$key.'.min'] = $label.' tidak boleh kurang dari 0.';
                        break;
                    case 'textarea':
                        $messages[$key.'.max'] = $label.' maksimal 5.000 karakter.';
                        break;
                    case 'email':
                        $messages[$key.'.max'] = $label.' maksimal 255 karakter.';
                        break;
                    case 'url':
                        $messages[$key.'.max'] = $label.' maksimal 2.048 karakter.';
                        break;
                    case 'password':
                        $messages[$key.'.max'] = $label.' maksimal 255 karakter.';
                        break;
                    case 'color':
                        $messages[$key.'.regex'] = $label.' harus menggunakan format warna #RRGGBB.';
                        break;
                    default:
                        $messages[$key.'.max'] = $label.' maksimal 255 karakter.';
                        break;
                }
            }
        }

        return $messages;
    }
}
