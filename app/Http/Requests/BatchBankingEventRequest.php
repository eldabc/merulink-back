<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchBankingEventRequest extends FormRequest
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
            '*.title'          => 'required|string',
            '*.start'          => 'required|date',
            '*.all_day'        => 'nullable|boolean',
            '*.extended_props' => 'nullable|array',
            '*.extended_props.status' => ['nullable', 'string', 'in:Creado'],
            '*.extended_props.description' => ['nullable', 'string'],
            '*.category_key'   => 'required|string|exists:event_categories,key',
        ];
    }

    public function attributes(): array
    {
        return [
            '*.title' => 'título del evento',
            '*.start' => 'fecha',
            '*.extended_props.status' => 'estatus del evento',
            '*.extended_props.description' => 'descripción del evento',
            '*.category_key' => 'categoría',
        ];
    }
}
