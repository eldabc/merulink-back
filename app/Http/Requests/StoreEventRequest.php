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

            'external_source' => ['nullable', 'string', 'max:100'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'repeat_event' => ['nullable', 'boolean'],
            'repeat_interval' => ['nullable', 'string', 'in:WEEKLY,WEEKLY_2,MONTHLY,YEARLY'],

            'extended_props' => ['required', 'array', 'min:1'],
            'extended_props.status' => ['nullable', 'string', 'in:Creado,Tentativo,Confirmado'],
            'extended_props.event_type' => ['nullable', 'string', 'in:paid,courtesy'],
            'extended_props.create_alert' => ['nullable', 'boolean'],
            'extended_props.coloring_day' => ['nullable', 'boolean'],
            'extended_props.description' => ['nullable', 'string'],
            'extended_props.comments' => ['nullable', 'string'],
            'extended_props.is_fixed' => ['nullable', 'boolean'],
            'extended_props.created_by' => ['nullable', 'string'],
            'extended_props.special_label' => ['nullable', 'string'],

            'category_key' => ['required', 'string', 'exists:event_categories,key'],
            'location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'template_name' => ['nullable', 'string'],
            
            // array de contactos es opcional (puede venir nulo o vacío)
            'contacts' => 'nullable|array',
            // Si hay un contacto, el nombre y apellido son obligatorios
            'contacts.*.first_name' => 'required_with:contacts|string|max:30',
            'contacts.*.last_name'  => 'required_with:contacts|string|max:30',
            'contacts.*.email'     => 'nullable|email|max:100',
            
            // Si hay un contacto, el array de teléfonos DEBE existir y tener al menos uno
            'contacts.*.phones' => 'required_with:contacts|array|min:1',
            
            // Si hay una fila de teléfono, el número es obligatorio
            'contacts.*.phones.*.phone_number' => 'required|string|max:20',
        ];
    }

    public function attributes(): array
    {
        return [
            'repeat_interval' => 'intérvalo de repetición',
            'contacts' => 'lista de contactos',
            'contacts.*.first_name'   => 'nombre del contacto',
            'contacts.*.last_name'    => 'apellido del contacto',
            'contacts.*.email'        => 'correo electrónico',
            'contacts.*.phones'       => 'teléfonos',
            'contacts.*.phones.*.phone_number' => 'número de teléfono',
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
            'repeat_event.boolean'   => 'El campo repetir evento debe ser sí o no.',
            'repeat_interval.string'   => 'El intérvalo de repeticón de evento debe ser Quincenal, Mensual o Anual.',

            'extended_props.required' => 'Es necesario incluir las propiedades del evento.',
            'extended_props.array'    => 'El formato enviado no correcto, debe ser un array.',
            'extended_props.min'      => 'Debe incluir al menos una propiedad en el evento.',
                        
            'extended_props.status.string'         => 'El estado debe ser una cadena de caracteres.',
            'unlockSequence.status.in'             => 'El Estatus debe ser: Creado,Tentativo o Confirmado.',
            'extended_props.eventType.string'         => 'El tipo de evento debe ser una cadena de caracteres.',
            'unlockSequence.eventType.in'             => 'El tipo de evento debe ser: Pagado o Cortesía.',
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
