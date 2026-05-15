<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreShiftRequest extends FormRequest
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
            'description' => 'required|string|max:100',
            'active_period' => 'required|string',
            'rest_period' => 'required|string',
            'total_period' => 'required|string',
            'check_in_time' => 'required|string',
            'check_out_time' => 'required|string',
            'allow_check_out' => 'boolean',
            're_scanned' => 'boolean',
            'available' => 'boolean',
            'night_shift' => 'boolean',
            'observations' => 'nullable|string',

            'department_id' => 'required|exists:departments,id',

        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Código',
            'description' => 'Descripción',
            'active_period'   => 'Periodo Activo',
            'rest_period'    => 'Periodo de descanso',
            'total_period'        => 'Periodo Total',
            'check_in_time'       => 'Hora de entrada',
            'check_out_time' => 'Hora de salida',
            'allow_check_out' => 'Permitir salida',
            're_scanned' => 'Remarcaje',
            'available' => 'Disponible',
            'night_shift' => 'Turno Nocturno',
            'observations' => 'Observaciones',
            'department_id' => 'Id de Departamento',
        ];
    }
}
