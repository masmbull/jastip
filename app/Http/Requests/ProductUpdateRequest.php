<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $product = $this->route('product');
        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product->id)],
            'description' => ['required', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'setbiaya_fee' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'stock' => ['required', 'integer', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'name.required' => 'Nama produk wajib diisi.',
            'slug.unique' => 'Slug ini sudah digunakan.',
            'description.required' => 'Deskripsi wajib diisi.',
            'price.required' => 'Harga wajib diisi.',
            'price.min' => 'Harga tidak boleh negatif.',
            'setbiaya_fee.min' => 'Biaya fee tidak boleh negatif.',
            'image.image' => 'File yang diunggah harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: jpeg, png, jpg, gif, webp.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
            'stock.required' => 'Stok wajib diisi.',
            'sku.unique' => 'SKU ini sudah digunakan.',
        ];
    }
}