<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
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
        // $standardText = ['required', 'string'];
        return [
            'ci' => [
                'required',
                'numeric',   
                Rule::unique('employees', 'ci')->ignore($this->route('employee')),
            ],
            'num_employee' => [
                'required',
                'numeric',   
                Rule::unique('employees', 'num_employee')->ignore($this->route('employee')),
            ],
           'first_name' => [
                'required',
                'string',
            ],
            'second_name' => [
                'string',
            ],
            'last_name' => [
                'string',
            ],
            'second_last_name' => [
                'nullable',
                'string',
            ],
            'birthdate' => [
                'nullable',
                'date',
                'string',
            ],
            'place_of_birth' => [
                'nullable',
                'string',
            ],
            'nationality' => [
                'required',
                'string',
            ],
            'sex' => [
                'required',
                'string',
            ],
            'marital_status' => [
                'string',
            ],
            'blood_type' => [
                'nullable',
                'string',
            ],
            'email' => [
                'email',
                'required',
                'string',
                Rule::unique('employees', 'email')->ignore($this->route('employee')),
            ],
            
            'mobile_phone' => [
                'required',
                'string',
                'regex:/^[0-9]{4}-[0-9]{7}$/'
            ],

            'home_phone' => [
                'nullable',
                'string',
                'regex:/^[0-9]{4}-[0-9]{7}$/'
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'join_date' => [
                'required',
                'date',
            ],

            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
            ],

            'sub_department.id' => [
                'nullable',
                'integer',
                'exists:sub_departments,id',
            ],

            'position_id' => [
                'required',
                'integer',
                'exists:positions,id',
            ],

            'user_name' => [
                'nullable',
                'string',
            ],

            'user_pass' => [
                'nullable',
                'string',
            ],

            'change_pass_next_login' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'required',
                'boolean'
            ],

            'use_meru_link' => [
                'boolean'
            ],

            'use_hid_card' => [
                'boolean'
            ],

            'use_locker' => [
                'boolean'
            ],

            'use_transport' => [
                'boolean'
            ],

            'contacts' => [
                'nullable',
                'array',
                // 'min:1',
                'max:5',
            ],

            'assign_id' => [
                'nullable',
                'integer',
                'exists:assigns,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ci.required'         => 'La cédula es obligatoria.',    
            'ci.numeric' => 'La cédula debe contener solo números, sin puntos ni guiones.',
            'ci.unique'           => 'La cédula ingresada ya está en uso.',
            'num_employee.required'       => 'El número de empleado es obligatorio.',
            'num_employee.numeric'           => 'El número de trabajador debe ser un valor numérico.',
            'num_employee.unique'       => 'El número de empleado ya está en uso.',
            'first_name.required' => 'El primer nombre es obligatorio.',
            'first_name.string'   => 'El nombre debe ser una cadena de caracteres válida.',
            'last_name.required' => 'El segundo nombre es obligatorio.',
            'last_name.string'   => 'El segundo nombre debe ser una cadena de caracteres válida.',
            
            'join_date.required' => 'La fecha de ingreso es requerida',
            'email'           => 'El email ingresado ya está en uso.',
            'blood_type.string'   => 'El tipo de sangre debe ser una cadena de caracteres válida.',

            'department_id.required' => 'El departamento es requerido',

            'position_id.required' => 'El cargo es requerido',
            'mobile_phone.required' => 'El número de teléfono móvil es obligatorio.',
            'mobile_phone.regex' => 'El formato del teléfono móvil debe ser 04XX-XXXXXXX (incluyendo el guion).',
            'home_phone.regex' => 'El teléfono de habitación debe tener el formato 0286-XXXXXXX.',
            'assign_id.exists' => 'El locker seleccionado no tiene registro de emparejamiento',
        ];
    }
}
