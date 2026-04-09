<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BirthdayEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => 'bday-' . $this->id,
            'title' => 'Cumpleaños 🎂 ' . $this->first_name.' '.$this->last_name,
            'start' => now()->year . '-' . date('m-d', strtotime($this->birthdate)),
            'all_day' => true,
            'extended_props' => [
                'category' => [
                    'key' => 'meru-birthdays',
                    'label' => 'Cumpleaños',
                    'color' => '#f472b6' // Rosa o color distintivo
                ],
                'department' => [
                    'id' => $this->subdepartment?->department?->id,
                    'name' => $this->subdepartment?->department?->name,
                ],
                'subDepartment' => $this->position->subdepartment ? [
                    'id' => $this->position->subdepartment->id,
                    'name' => $this->position->subdepartment->name,
                ] : [],
                'position' => [
                    'id' => $this->position->id,
                    'name' => $this->position->name
                ],
            ]
        ];
    }
}
