<?php

namespace App\Http\Requests;

use App\Models\Catalog;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCatalogRequest extends CatalogFormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Catalog::class) ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->filled('slug') && $this->filled('name')) {
            $this->merge([
                'slug' => Catalog::uniqueSlugFromName($this->string('name')->toString()),
            ]);
        }

        if ($this->filled('slug')) {
            $this->merge([
                'slug' => Str::slug($this->string('slug')->toString()),
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge($this->catalogRules(), [
            'slug' => ['required', 'string', 'max:255', 'unique:catalogs,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'type' => ['required', 'string', Rule::exists('categories', 'slug')],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);
    }
}
