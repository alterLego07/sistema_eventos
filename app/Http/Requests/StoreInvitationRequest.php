<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for storing a new invitation.
 */
class StoreInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'guest_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email'],
            'table_number' => ['nullable', 'integer', 'min:1'],
            'allowed_guests' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'guest_name.required' => 'El nombre del invitado es obligatorio.',
            'guest_name.max' => 'El nombre del invitado no puede exceder los 255 caracteres.',
            'phone.max' => 'El teléfono no puede exceder los 20 caracteres.',
            'email.email' => 'El correo electrónico debe ser una dirección válida.',
            'table_number.integer' => 'El número de mesa debe ser un número entero.',
            'table_number.min' => 'El número de mesa debe ser al menos 1.',
            'allowed_guests.required' => 'El número de acompañantes permitidos es obligatorio.',
            'allowed_guests.integer' => 'El número de acompañantes debe ser un número entero.',
            'allowed_guests.min' => 'Debe permitir al menos 1 invitado.',
            'allowed_guests.max' => 'El máximo de acompañantes permitidos es 20.',
        ];
    }
}
