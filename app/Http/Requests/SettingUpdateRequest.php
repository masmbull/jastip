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
                    'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg,gif,webp,ico', 'max:2048'],
                    'email' => ['nullable', 'email'],
                    'number' => ['nullable', 'numeric'],
                    default => ['nullable', 'string'],
                };
            }
        }

        return $rules;
    }
}