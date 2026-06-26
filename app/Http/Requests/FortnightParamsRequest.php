<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FortnightParamsRequest extends FormRequest
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
            'departmentId' => [
                'required',
                'integer',
                'exists:departments,id',
            ],
            'start' => [
                'date',
                'required',
            ],
            'end' => [
                'date',
                'required',
            ],
            'shift' => [
                'required',
                'array'
            ],
            'id' => [
                'nullable'
            ],
            'autofillFortnight' => [
                'boolean'
            ]
        ];
    }


    public function attributes(): array
    {
        return [
            'start' => 'Fecha inicio Quincena',
            'end' => 'Fecha fin Quincena',
            'departmentId' => 'Id de Departamento',
            'shift' => 'Turno',
            'id' => 'ID de Horario',
            'autofillFortnight' => 'Indicador Rellenar Quincena Automáticamente'
        ];
    }
}
