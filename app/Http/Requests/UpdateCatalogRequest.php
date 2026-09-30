<?php

namespace App\Http\Requests;

use App\Models\Catalog;
use Illuminate\Validation\Rule;

class UpdateCatalogRequest extends CatalogFormRequest
{
    public function authorize(): bool
    {
        $catalog = $this->route('catalog');

        return $catalog instanceof Catalog
            && ($this->user()?->can('update', $catalog) ?? false);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $catalog = $this->route('catalog');
        $catalogId = $catalog instanceof Catalog ? $catalog->id : null;

        return array_merge($this->catalogRules($catalogId), [
            'type' => ['required', 'string', Rule::exists('categories', 'slug')],
            'status' => ['required', Rule::in(['draft', 'published'])],
        ]);
    }
}
