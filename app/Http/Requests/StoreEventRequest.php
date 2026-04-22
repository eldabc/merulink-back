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
            // 'start' => $this->input('is_template') ? 'nullable|date' : 'required|date',
            // 'end'   => $this->input('is_template') ? 'nullable|date' : 'required|date|after_or_equal:start',
            'start' => [
                'date',
                'required',
            ],
            'end' => [
                'date',
                'nullable',
            ],
            'all_day' => [
                'boolean',
            ],

            'extended_props' => ['required', 'array', 'min:1'],
            'extended_props.status' => ['nullable', 'string', 'in:Creado,Tentativo,Confirmado'],
            'extended_props.repeat_event' => ['nullable', 'boolean'],
            'extended_props.repeat_interval' => ['nullable', 'string'],
            'extended_props.create_alert' => ['nullable', 'boolean'],
            'extended_props.coloring_day' => ['nullable', 'boolean'],
            'extended_props.description' => ['nullable', 'string'],
            'extended_props.comments' => ['nullable', 'string'],
            'extended_props.is_fixed' => ['nullable', 'boolean'],
            'extended_props.created_by' => ['nullable', 'string'],
            'extended_props.external_source' => ['nullable', 'string'],
            'extended_props.external_id' => ['nullable', 'string'],
            'extended_props.special_label' => ['nullable', 'string'],

            'category_key' => ['required', 'string', 'exists:event_categories,key'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'template_name' => ['nullable', 'string'],
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
            
            // 'extended_props.status.required'       => 'El estado del evento es obligatorio.',
            'extended_props.status.string'         => 'El estado debe ser uja cadena de caracteres.',
            'unlockSequence.status.in'             => 'El Estatus debe ser: Creado,Tentativo o Confirmado.',
            'extended_props.repeat_event.boolean'   => 'El campo repetir evento debe ser sí o no.',
            'extended_props.create_alert.boolean'   => 'El campo crear alerta debe ser sí o no.',
            'extended_props.coloring_day.boolean'   => 'El campo resaltar día debe ser sí o no.',
            'extended_props.is_fixed.boolean'       => 'El campo de evento fijo debe ser verdadero o falso.',
            
            'category_key.required' => 'La categoría del evento es obligatoria.',
            'category_key.exists'   => 'La categoría seleccionada no es válida.',
            'special_label.string'   => 'El campo special_label debe ser una cadena de caracteres.',
            'location_id.exists'     => 'La ubicación seleccionada no existe en nuestros registros.',
            'location_id.integer'    => 'El identificador de ubicación debe ser un número.',
            'template_name.string'   => 'El nombre del template debe ser una cadena de caracteres.',
        ];
    }
}
