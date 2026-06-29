<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleAutofillParamsRequest extends FormRequest
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
            'departmentId'      => 'required|integer|exists:departments,id',
            'autofillFortnight' => 'present|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'autofillFortnight' => 'Indicador automatización',
            'departmentId' => 'Id de Departamento',
        ];
    }
}
