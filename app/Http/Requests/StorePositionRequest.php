<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StorePositionRequest extends FormRequest
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
            'code' => [
                'required',
                'string',   
                Rule::unique('positions', 'code')->ignore($this->route('position')),
            ],
            'name' => [
                'required',
                'string',
                Rule::unique('positions', 'name')->ignore($this->route('position')),
            ],
            'department.id' => 'required|exists:departments,id',
            'subDepartment.id' => 'exists:sub_departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'           => 'El código del Cargo ya está en uso.',
            'code.required'         => 'El código de Cargo es obligatorio.',
            'code.string'           => 'El código de Cargo debe ser una cadena de texto válida.',
            'name.string'           => 'El nombre del Cargo debe ser una cadena de texto válida.',
            'name.required'         => 'El nombre del Cargo es obligatorio.',
            'name.unique'           => 'El nombre del Cargo ya está en uso.',
            'department.id.required' => 'El Departamento es obligatorio.',
            'department.id.exists'   => 'El Departamento seleccionado no existe.',
            'subDepartment.id.exists'   => 'El Subdepartamento seleccionado no existe.',

        ];
    }
}
