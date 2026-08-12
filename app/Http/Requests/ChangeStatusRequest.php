<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeStatusRequest extends FormRequest
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
        // Si el empleado está activo, la operación es "desactivar"
        $isDeactivating = (bool) $this->route('employee')?->status;

        return [
            // Fecha de efectividad: se usa para ambos casos (baja y reactivación)
            'effective_date' => ['required', 'date'],
            'retire_reason' => [
                Rule::requiredIf($isDeactivating),
                'nullable',
                'string',
            ],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'effective_date.required' => 'Debe indicar la fecha de efectividad.',
            'retire_reason.required'  => 'Debe indicar el tipo de egreso.',
        ];
    }
}
