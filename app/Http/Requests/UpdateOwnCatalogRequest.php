<?php

namespace App\Http\Requests;

use App\Models\Catalog;

class UpdateOwnCatalogRequest extends CatalogFormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null || ! $user->isCatalogAdmin() || $user->catalog_id === null) {
            return false;
        }

        $catalog = Catalog::query()->find($user->catalog_id);

        return $catalog !== null && $user->can('update', $catalog);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return $this->catalogRules();
    }

    public function type(): string
    {
        return (string) $this->user()?->catalog?->type;
    }

    protected function prepareForValidation(): void
    {
        parent::prepareForValidation();

        $this->merge([
            'type' => $this->user()?->catalog?->type,
        ]);
    }
}
