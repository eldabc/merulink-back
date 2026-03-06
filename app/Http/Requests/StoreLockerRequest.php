<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Enums\LockerStatus;

class StoreLockerRequest extends FormRequest
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
                // Busca code en lockers, pero ignora el ID actual
                Rule::unique('lockers', 'code')->ignore($this->route('locker')),
            ],
           'status' => [
                'required',
                Rule::enum(LockerStatus::class) // Valida casos Enum
            ],
            'category.id' => 'required|exists:locker_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'           => 'El código del locker ya está en uso.',
            'code.required'         => 'El código de locker es obligatorio.',
            'status.enum'           => 'El estado seleccionado no es válido. Los valores permitidos son: Disponible y Ocupado.',
            'status.required'       => 'El estado del locker es obligatorio.',
            'category.id.required' => 'La categoría es obligatoria.',
            'category.id.exists'   => 'La categoría seleccionada no existe.',
        ];
    }
}
