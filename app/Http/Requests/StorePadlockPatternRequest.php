<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePadlockPatternRequest extends FormRequest
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
            'modelName' => [
                'required',
                'string',
            ],
            'resetInstructions' => [
                'required',
                'string',
            ],
            'unlockSequence' => ['required', 'array', 'min:1'],

            // Valida cada objeto dentro del array
            'unlockSequence.*.action' => ['required', 'string', 'in:girar,presionar,halar'],
            'unlockSequence.*.direction' => ['required', 'string', 'in:arriba,abajo,derecha,izquierda'],
            'unlockSequence.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages()
    {
        return [
            'modelName.required'         => 'El Nombre de modelo es obligatorio.',
            'modelName.string'   => 'El nombre de modelo debe ser una cadena de texto válida.',
            'resetInstructions.required' => 'Las indicaciones de reinicio son obligatorias.',
            'resetInstructions.string'   => 'Las indicaciones de reinicio debe ser una cadena de texto válida.',
            'unlockSequence.*.action.in' => 'La acción debe ser: girar, halar o presionar.',
            'unlockSequence.*.direction.required' => 'Debes indicar una dirección para cada paso.',
            'unlockSequence.*.amount.integer' => 'La cantidad de movimientos debe ser un número.',
        ];
    }
}
