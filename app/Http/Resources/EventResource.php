<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'allDay' => $this->all_day,
            'extendedProps' => [
                ...(is_array($this->extended_props) ? $this->extended_props : []), 
                'category' => new EventCategoryResource($this->whenLoaded('eventCategory')),
                'location' => new LocationResource($this->whenLoaded('location')),
            ],
            
        ];
    }
}
