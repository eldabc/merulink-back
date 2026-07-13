<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ScrapeEmployeeRequest extends FormRequest
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
           'source'       => ['required', 'string', 'in:ivss,seniat'],
            'ci'          => ['required', 'string', 'min:5', 'max:10'],
            'birthdate'   => ['required_if:source,ivss', 'string', 'min:8', 'max:10'],
            'seniat_code' => ['required_if:source,seniat', 'string', 'max:10'],  
        ];

    }

    public function attributes(): array
    {
        return [
            'source' => 'Tipo de recurso',
            'ci' => 'Cédula',
            'birthdate'   => 'Fecha de nacimiento',
            'seniat_code'   => 'Código SENIAT',
        ];
    }
}
