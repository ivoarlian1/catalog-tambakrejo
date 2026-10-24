<?php

namespace App\Http\Requests;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UpdateCatalogAdminRequest extends FormRequest
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
        $admin = $this->route('admin');
        $adminId = $admin instanceof User ? $admin->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'required_without:username', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($adminId)],
            'username' => ['nullable', 'required_without:email', 'string', 'max:50', 'alpha_dash', Rule::unique('users', 'username')->ignore($adminId)],
            'password' => ['nullable', 'confirmed', Password::min(8)->letters()->numbers()],
            'catalog_id' => ['required', 'integer', 'exists:catalogs,id', Rule::unique('users', 'catalog_id')->ignore($adminId)],
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
            'email.required_without' => 'Email atau username wajib diisi.',
            'username.required_without' => 'Email atau username wajib diisi.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'catalog_id.exists' => 'Katalog yang dipilih tidak valid.',
            'catalog_id.required' => 'Katalog wajib dipilih.',
            'catalog_id.unique' => 'Katalog ini sudah memiliki Catalog Admin.',
        ];
    }
}
