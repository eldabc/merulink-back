<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubDepartmentRequest extends FormRequest
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
                Rule::unique('sub_departments', 'code')->ignore($this->route('subdepartment')),
            ],
           'name' => [
                'required',
                'string',
            ],
            'department.id' => 'required|exists:departments,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'           => 'El código del Subdepartamento ya está en uso.',
            'code.required'         => 'El código de Subdepartamento es obligatorio.',
            'code.string'           => 'El código de Subdepartamento debe ser una cadena de texto válida.',
            'name.required' => 'El nombre de Subdepartamento es obligatorio.',
            'name.string'   => 'El nombre de Subdepartamento debe ser una cadena de texto válida.',
            'department.id.required' => 'El Departamento es obligatorio.',
            'department.id.exists'   => 'El Departamento seleccionado no existe.',
        ];
    }
}
