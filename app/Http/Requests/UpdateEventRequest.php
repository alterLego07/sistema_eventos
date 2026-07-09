<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for updating an existing event.
 */
class UpdateEventRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_date' => ['required', 'date'],
            'event_time' => ['required', 'date_format:H:i'],
            'location' => ['nullable', 'string', 'max:500'],
            'location_url' => ['nullable', 'url'],
            'template_id' => ['nullable', 'exists:templates,id'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['nullable', 'in:draft,published'],
            'currency' => ['nullable', 'in:MXN,USD,EUR,COP,ARS,CLP,PEN,BRL,GBP'],
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
            'name.required' => 'El nombre del evento es obligatorio.',
            'name.max' => 'El nombre del evento no puede exceder los 255 caracteres.',
            'event_date.required' => 'La fecha del evento es obligatoria.',
            'event_time.required' => 'La hora del evento es obligatoria.',
            'event_time.date_format' => 'La hora debe tener el formato HH:MM.',
            'location.max' => 'La ubicación no puede exceder los 500 caracteres.',
            'location_url.url' => 'La URL de ubicación debe ser una URL válida.',
            'template_id.exists' => 'La plantilla seleccionada no existe.',
            'cover_image.image' => 'La imagen de portada debe ser una imagen válida.',
            'cover_image.max' => 'La imagen de portada no debe exceder los 2 MB.',
            'status.in' => 'El estado debe ser borrador o publicado.',
        ];
    }
}
