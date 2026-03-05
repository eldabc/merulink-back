<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'code'        => 'required|string|unique:lockers,code',
            'status'      => 'required|string|in:Disponible,Ocupado',
            'category.id' => 'required|exists:locker_categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'code.unique'           => 'El código del locker ya está en uso.',
            'code.required'         => 'El código de locker es obligatorio.',
            'status.in'             => 'El estado seleccionado no es válido. Los valores permitidos son: Disponible y Ocupado.',
            'status.required'       => 'El estado del locker es obligatorio.',
            'status.string'         => 'Estatus debe contener solo letras.',
            'category.id.required' => 'La categoría es obligatoria.',
            'category.id.exists'   => 'La categoría seleccionada no existe.',
        ];
    }
}
