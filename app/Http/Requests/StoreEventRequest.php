<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
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
            'title'  => [
                'required',
                'string',
            ],
            'start' => [
                'date',
                'required',
            ],
            'end' => [
                'date',
                'nullable',
            ],
            'extended_props' => ['required', 'array', 'min:1'],
            'extended_props.category_key' => ['required', 'string', 'exists:event_categories,key'],
            'extended_props.special_label' => ['nullable', 'string'],
            'extended_props.status' => ['required', 'string', 'in:Tentativo,Confirmado'],
            'extended_props.location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'extended_props.repeat_event' => ['nullable', 'boolean'],
            'extended_props.repeat_interval' => ['nullable', 'string'],
            'extended_props.create_alert' => ['nullable', 'boolean'],
            'extended_props.coloring_day' => ['nullable', 'boolean'],
            'extended_props.description' => ['nullable', 'string'],
            'extended_props.comments' => ['nullable', 'string'],
            'extended_props.is_fixed' => ['nullable', 'boolean'],
            'extended_props.created_by' => ['nullable', 'string'],
            'extended_props.template_name' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'El título del evento es obligatorio.',
            'title.string'      => 'El título debe ser un texto válido.',
            'start.required'    => 'La fecha de inicio es obligatoria.',
            'start.date'        => 'La fecha de inicio no tiene un formato válido.',
            'end.date'          => 'La fecha de finalización no tiene un formato válido.',
            
            'extended_props.required' => 'Es necesario incluir las propiedades del evento.',
            'extended_props.array'    => 'El formato enviado no correcto, debe ser un array.',
            'extended_props.min'      => 'Debe incluir al menos una propiedad en el evento.',

            'extended_props.category_key.required' => 'La categoría del evento es obligatoria.',
            'extended_props.category_key.exists'   => 'La categoría seleccionada no es válida.',
            
            'extended_props.status.required'       => 'El estado del evento es obligatorio.',
            'extended_props.status.string'         => 'El estado debe ser uja cadena de caracteres.',
            'unlockSequence.status.in'             => 'El Estatus debe ser: Tentativo o Confirmado.',
            
            'extended_props.location_id.exists'     => 'La ubicación seleccionada no existe en nuestros registros.',
            'extended_props.location_id.integer'    => 'El identificador de ubicación debe ser un número.',
            
            'extended_props.repeat_event.boolean'   => 'El campo repetir evento debe ser sí o no.',
            'extended_props.create_alert.boolean'   => 'El campo crear alerta debe ser sí o no.',
            'extended_props.coloring_day.boolean'   => 'El campo resaltar día debe ser sí o no.',
            'extended_props.is_fixed.boolean'       => 'El campo de evento fijo debe ser verdadero o falso.',
        ];
    }
}
