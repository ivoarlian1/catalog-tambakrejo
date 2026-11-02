<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCatalogActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('activity')) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'event_date' => ['nullable', 'date'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'extensions:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 2 MB. Foto besar dikompres otomatis sebelum dikirim.',
            'photo.uploaded' => 'Foto gagal diunggah. Pilih foto yang ukurannya lebih kecil, lalu coba lagi.',
            'photo.mimes' => 'Format foto harus JPG, PNG, atau WEBP.',
            'photo.extensions' => 'Format foto harus JPG, PNG, atau WEBP.',
        ];
    }
}