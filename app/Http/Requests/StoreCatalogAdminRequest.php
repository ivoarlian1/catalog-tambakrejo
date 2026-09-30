<?php

namespace App\Http\Requests;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class StoreCatalogAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manageAdmins', Catalog::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'catalog_id' => ['required', 'integer', 'exists:catalogs,id', 'unique:users,catalog_id'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'catalog_id.exists' => 'Katalog yang dipilih tidak valid.',
            'catalog_id.required' => 'Katalog wajib dipilih.',
            'catalog_id.unique' => 'Katalog ini sudah memiliki Catalog Admin.',
        ];
    }

    /**
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('catalog_id') || ! $this->filled('catalog_id')) {
                    return;
                }

                $exists = User::query()
                    ->where('role', 'catalog_admin')
                    ->where('catalog_id', $this->integer('catalog_id'))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('catalog_id', 'Katalog ini sudah memiliki Catalog Admin.');
                }
            },
        ];
    }
}
