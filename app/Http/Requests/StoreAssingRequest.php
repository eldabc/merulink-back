<?php

namespace App\Http\Requests;
use App\Models\Assign;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssingRequest extends FormRequest
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
        // Usamos camelCase para coincidir con tu JSON
        'assignCode' => [
            'nullable',
            'string',
            'unique:assigns,assign_code', // Sigue validando contra la columna de la BD
            'max:20'
        ],
        'assignDate' => [
            'nullable',
            'date',
            'date_format:Y-m-d',
        ],
        // Accedemos al ID dentro del objeto locker
        'locker.id' => [
            'required',
            'integer',
            'exists:lockers,id',
            function ($attribute, $value, $fail) {
                if (Assign::where('locker_id', $value)->exists()) {
                    $fail('Este locker ya está asignado a otro empleado.');
                }
            },
        ],
        // Accedemos al ID dentro del objeto padlock (que está dentro de locker)
        'locker.padlock.id' => [
            'required',
            'integer',
            'exists:padlocks,id',
            function ($attribute, $value, $fail) {
                if (Assign::where('padlock_id', $value)->exists()) {
                    $fail('Este candado ya está en uso en otra asignación.');
                }
            },
        ],
        // Accedemos al ID dentro del objeto employee
        'employee.id' => [
            'nullable',
            'integer',
            'exists:employees,id',
        ],
    ];
}

    public function messages(): array
    {
        return [
            'assignCode.unique'      => 'Este código de asignación ya está registrado.',
            'locker.id.exists'       => 'El locker seleccionado no es válido.',
            'locker.id.required'     => 'El locker es obligatorio.',
            'locker.padlock.id.required' => 'El candado es obligatorio.',
            'employee.id.exists'     => 'El empleado seleccionado no existe.',
            'employee.id.integer'    => 'El id de empleado debe ser un número entero',
            'assignDate.date'        => 'La fecha de asignación no tiene un formato válido.',
        ];
    }
}
