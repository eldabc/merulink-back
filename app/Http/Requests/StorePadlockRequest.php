<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\padlockStatus;

class StorePadlockRequest extends FormRequest
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
            'serial' => [
                'required',
                'string',
                // Busca serial en padlocks, pero ignora el ID en url
                Rule::unique('padlocks', 'serial')->ignore($this->route('padlock')),
            ],
            'pass'   => [
                'required',
                'string',
                'regex:/^\d{2}-\d{2}-\d{2}$/' 
            ],
           'status' => [
                'required',
                Rule::enum(PadlockStatus::class)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'serial.unique'           => 'El código del locker ya está en uso.',
            'serial.required'         => 'El código de locker es obligatorio.',
            'pass.required'         => 'Contraseña es requerida.',
            'pass.required' => 'Clave del candado es obligatoria.',
            'pass.string'   => 'Clave debe ser una cadena de texto válida.',
            'pass.regex'    => 'Clave debe tener el formato numérico 00-00-00 (ejemplo: 12-34-56).',
            'status.enum'           => 'El estado seleccionado no es válido. Los valores permitidos son: Disponible y Ocupado.',
            'status.required'       => 'El estatus del locker es obligatorio.',
        ];
    }
}
