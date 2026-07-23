<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
            'role_name' => 'required|string|min:3',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'string',
        ];
    }

    public function attributes(): array
    {
        return [
            'role_name' => 'Nombre rol',
            'permissions' => 'Permisos',
        ];
    }
}
