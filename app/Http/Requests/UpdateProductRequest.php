<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product');

        return $product instanceof Product
            && ($this->user()?->can('update', $product) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'extensions:jpeg,jpg,png,webp', 'max:2048'],
            'is_available' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'price.min' => 'Harga tidak boleh kurang dari 0.',
            'price.max' => 'Harga terlalu besar.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2 MB. Foto besar dikompres otomatis sebelum dikirim.',
            'photo.uploaded' => 'Foto gagal diunggah. Pilih foto yang ukurannya lebih kecil, lalu coba lagi.',
            'photo.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'photo.extensions' => 'Format foto harus JPG, PNG, atau WEBP.',
        ];
    }
}
