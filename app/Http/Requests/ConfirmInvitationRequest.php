<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

/**
 * Form request for confirming an invitation (RSVP).
 */
class ConfirmInvitationRequest extends FormRequest
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
            'confirmed' => ['required', 'boolean'],
            'confirmed_guests' => ['required_if:confirmed,true', 'integer', 'min:1'],
            'dietary_restrictions' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:1000'],
            'song_suggestion' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * Adds a custom rule: confirmed_guests must not exceed the invitation's allowed_guests.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $invitation = $this->route('invitation');

            if (
                $invitation
                && $this->filled('confirmed_guests')
                && (int) $this->input('confirmed_guests') > $invitation->allowed_guests
            ) {
                $validator->errors()->add(
                    'confirmed_guests',
                    "El número de invitados confirmados no puede exceder el máximo permitido ({$invitation->allowed_guests}).",
                );
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirmed.required' => 'Debe indicar si confirma su asistencia.',
            'confirmed.boolean' => 'La confirmación debe ser verdadero o falso.',
            'confirmed_guests.required_if' => 'Debe indicar el número de invitados que asistirán.',
            'confirmed_guests.integer' => 'El número de invitados debe ser un número entero.',
            'confirmed_guests.min' => 'Debe confirmar al menos 1 invitado.',
            'dietary_restrictions.max' => 'Las restricciones dietéticas no pueden exceder los 500 caracteres.',
            'message.max' => 'El mensaje no puede exceder los 1000 caracteres.',
            'song_suggestion.max' => 'La sugerencia de canción no puede exceder los 255 caracteres.',
        ];
    }
}
