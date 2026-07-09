<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Form request for creating a budget item.
 */
class StoreBudgetItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalise the paid checkbox before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'paid' => $this->boolean('paid'),
        ]);
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:255'],
            'concept' => ['required', 'string', 'max:255'],
            'estimated_amount' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'actual_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'paid' => ['boolean'],
            'paid_at' => ['nullable', 'date'],
            'vendor' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'category.required' => 'La categoría es obligatoria.',
            'concept.required' => 'El concepto es obligatorio.',
            'estimated_amount.required' => 'El monto estimado es obligatorio.',
            'estimated_amount.numeric' => 'El monto estimado debe ser un número.',
            'actual_amount.numeric' => 'El monto real debe ser un número.',
        ];
    }
}
