<?php

namespace App\Http\Requests;

use App\Models\CatalogActivity;
use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', CatalogActivity::class) ?? false;
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
}