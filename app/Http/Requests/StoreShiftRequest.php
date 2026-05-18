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
            'night_shift' => 'boolean',
            'department_id' => 'required|exists:departments,id',
            'type_shift' => 'required|string',
            'check_in_time' => 'required|string',
            'check_out_time' => 'required|string',
            'time_rest_period' => 'integer|string',
            'duration_unit_rest_period' => 'required|string',
            'time_active_period' => 'required|string',
            'duration_unit_active_period' => 'required|string',
            'time_total_period' => 'required|string',
            'duration_unit_total_period' => 'required|string', 
           
            'allow_exit' => 'boolean',
            'allow_re_scanned' => 'boolean',
            'available' => 'boolean',
            
            'observation' => 'nullable|string',

            

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
            'allow_exit' => 'Permitir salida',
            'allow_re_scanned' => 'Remarcaje',
            'available' => 'Disponible',
            'night_shift' => 'Turno Nocturno',
            'observations' => 'Observaciones',
            'department_id' => 'Id de Departamento',
        ];
    }
}
