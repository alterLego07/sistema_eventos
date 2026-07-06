<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

/**
 * Form request for creating a company together with its first admin user.
 */
class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'active' => ['nullable', 'boolean'],

            // First admin user of the company
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la empresa es obligatorio.',
            'admin_name.required' => 'El nombre del administrador es obligatorio.',
            'admin_email.required' => 'El correo del administrador es obligatorio.',
            'admin_email.unique' => 'Ya existe un usuario con ese correo.',
            'admin_password.required' => 'La contraseña del administrador es obligatoria.',
            'admin_password.confirmed' => 'La confirmación de contraseña no coincide.',
            'logo.image' => 'El logo debe ser una imagen válida.',
            'logo.max' => 'El logo no debe exceder los 2 MB.',
        ];
    }
}
