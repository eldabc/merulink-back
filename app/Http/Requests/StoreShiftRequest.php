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
                Rule::unique('shifts', 'code')->ignore($this->route('shift')),
            ],
            'description' => 'required|string|max:100',
            'night_shift' => 'required|in:day,night',
            'department_id' => 'required|exists:departments,id',
            'type_shift' => 'required|string|in:operative,administrative',
            'check_in_time' => 'required|string',
            'check_out_time' => 'required|string',
            'rest_period_time' => 'required|integer|min:1|max:30',
            'rest_period_unit_time' => 'required|string',
            'active_period_time' => 'required|string',
            'active_period_unit_time' => 'required|string',
            'total_period_time' => 'required|string',
            'total_period_unit_time' => 'required|string', 
           
            'allow_exit' => 'required|in:yes,no',
            'allow_re_scanned' => 'required|in:yes,no',
            'available' => 'required|in:yes,no',
            
            'observation' => 'nullable|string',

            

        ];
    }

    public function attributes(): array
    {
        return [
            'code' => 'Código',
            'description' => 'Descripción',
            'active_period_time'   => 'Periodo Activo',
            'active_period_unit_time'   => 'Unidad de tiempo Periodo Activo',
            'rest_period_time'    => 'Periodo Descanso',
            'rest_period_unit_time'   => 'Unidad de tiempo Periodo Descanso',
            'total_period_time'        => 'Periodo Total',
            'total_period_unit_time'   => 'Unidad de tiempo Periodo Total',
            'check_in_time'       => 'Hora de entrada',
            'check_out_time' => 'Hora de salida',
            'allow_exit' => 'Permitir salida',
            'allow_re_scanned' => 'Remarcaje',
            'available' => 'Disponible',
            'night_shift' => 'Turno Nocturno',
            'type_shift' => 'Tipo de Turno',
            'observation' => 'Observaciones',
            'department_id' => 'Id de Departamento',
        ];
    }

    public function messages(): array
    {
        return [
            'night_shift.in'      => 'El valor de Turno debe ser: Diurno o Nocturno.',
            'type_shift.in'       => 'El tipo de Turno debe ser: Operativo o Administrativo.',
            'allow_exit.in'       => 'El campo permitir salida debe ser: Sí o No.',
            'allow_re_scanned.in' => 'El campo permitir remarcaje debe ser: Sí o No.',
            'vailable.in'         => 'El campo disponible debe ser: Sí o No.',
        ];
    }
}
