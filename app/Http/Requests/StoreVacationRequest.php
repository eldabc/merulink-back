<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreVacationRequest extends FormRequest
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
            'type' => [
                'required',
                'string',
                Rule::in(['vacation', 'medical_leave']),
            ],
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after_or_equal:start'],
            'observations' => ['nullable', 'string'],
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'        => 'El tipo de ausencia es obligatorio.',
            'type.in'              => 'El tipo de ausencia seleccionado no es válido.',
            'start.required'       => 'La fecha de inicio es obligatoria.',
            'start.date'           => 'La fecha de inicio no es válida.',
            'end.required'         => 'La fecha de fin es obligatoria.',
            'end.date'             => 'La fecha de fin no es válida.',
            'end.after_or_equal'   => 'La fecha de fin no puede ser anterior a la de inicio.',
            'observations.string'  => 'Las observaciones no son válidas.',
            'employee_id.required' => 'El empleado es obligatorio.',
            'employee_id.integer'  => 'El empleado no es válido.',
            'employee_id.exists'   => 'El empleado seleccionado no existe.',
        ];
    }
}
