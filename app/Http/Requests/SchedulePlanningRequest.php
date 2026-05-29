<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchedulePlanningRequest extends FormRequest
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
            'start' => 'required|string',
            'end' => 'required|string',
            'status' => 'required|in:created,reviewed,approved,closed',
            'observations' => 'nullable|string',
            'department_id' => 'required|exists:departments,id',
            'schedules'     => 'required|array',
            'schedules.*.employeeId' => 'required|integer',
            'schedules.*.dates'      => 'required|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'start' => 'Fecha Inicio quincena',
            'end' => 'Fecha Fin quincena',
            'status'   => 'Estatus del Horario',
            'observations' => 'Observaciones',
            'department_id' => 'Id de Departamento',
            'schedules' => 'Horarios',
            'schedules.*.employeeId' => 'Id de Empleado',
            'schedules.*.dates' => 'Días en el horario'
        ];
    }

}
