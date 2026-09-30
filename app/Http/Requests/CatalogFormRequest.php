<?php

namespace App\Http\Requests;

use App\Models\Catalog;
use App\Models\Category;
use App\Support\WhatsAppNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

abstract class CatalogFormRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    protected function catalogRules(?int $catalogId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sub_type' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:5000'],
            'owner_name' => ['nullable', 'string', 'max:255'],
            'responsible_person' => ['nullable', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'email' => ['nullable', 'email', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'other_contact' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'sub_type.in' => 'Subkategori tidak sesuai dengan kategori yang dipilih.',
            'whatsapp' => 'Nomor WhatsApp tidak valid.',
        ];
    }

    /**
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $type = $this->input('type');

                if (is_string($type) && $type !== '') {
                    $category = Category::query()->where('slug', $type)->first();
                    $currentCatalog = $this->route('catalog');

                    if (! $currentCatalog instanceof Catalog && $this->user()?->isCatalogAdmin()) {
                        $currentCatalog = $this->user()->catalog;
                    }

                    $keepsInactiveCategory = $currentCatalog instanceof Catalog && $currentCatalog->type === $type;

                    if ($category === null || (! $category->is_active && ! $keepsInactiveCategory)) {
                        $validator->errors()->add('type', 'Kategori tidak tersedia.');
                    }

                    if ($category?->slug === 'umkm' && ! $this->filled('owner_name')) {
                        $validator->errors()->add('owner_name', 'Nama pemilik wajib diisi untuk UMKM.');
                    }

                    if ($category !== null && $category->slug !== 'umkm' && ! $this->filled('responsible_person')) {
                        $validator->errors()->add('responsible_person', 'Nama penanggung jawab wajib diisi.');
                    }
                }

                if ($this->filled('whatsapp') && ! WhatsAppNumber::isValid($this->string('whatsapp')->toString())) {
                    $validator->errors()->add('whatsapp', 'Nomor WhatsApp tidak valid. Gunakan nomor Indonesia, misalnya 08123456789.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('sub_type')) {
            $this->merge(['sub_type' => trim($this->string('sub_type')->toString())]);
        }
    }
}
