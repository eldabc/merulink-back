<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDepartmentRequest extends FormRequest
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
                Rule::unique('departments', 'code')->ignore($this->route('department')),
            ],
           'departmentName' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'           => 'El código del Departamento ya está en uso.',
            'code.required'         => 'El código de Departamento es obligatorio.',
            'code.string'           => 'El código de Departamento debe ser una cadena de texto válida.',
            'departmentName.required' => 'El nombre de Departamento es obligatorio.',
            'departmentName.string'   => 'El nombre de Departamento debe ser una cadena de texto válida.',
        ];
    }
}
