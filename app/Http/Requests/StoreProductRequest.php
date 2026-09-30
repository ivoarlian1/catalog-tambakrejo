<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Product::class) ?? false;
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
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2MB.',
            'photo.mimes' => 'Foto harus berformat jpeg, jpg, png, atau webp.',
            'photo.extensions' => 'Foto harus berformat jpeg, jpg, png, atau webp.',
        ];
    }
}
