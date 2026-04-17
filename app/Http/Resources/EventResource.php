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
        $props = $this->extended_props ?? [];
        return [
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'allDay' => $this->all_day,
            'extendedProps' => [
                'category' => new EventCategoryResource($this->whenLoaded('eventCategory')),
                'location' => new LocationResource($this->whenLoaded('location')),
                
                'status' => $props['status'] ?? null,
                'repeatEvent' => $props['repeat_event'] ?? null,
                'repeatInterval' => $props['repeat_interval'] ?? null,
                'createAlert' => $props['create_alert'] ?? false,
                'coloringDay' => $props['coloring_day'] ?? false,
                'description' => $props['description'] ?? false,
                'comments' => $props['comments'] ?? false,
                'isFixed' => $props['is_fixed'] ?? false,
                'createdBy' => $props['created_by'] ?? false,

                'templateInfo' => $this->when($this->relationLoaded('templateOrigin') && $this->templateOrigin, function() {
                    return [
                        'has_template' => true,
                        'tid' => $this->templateOrigin->id,
                        'name' => $this->templateOrigin->name,
                        'route_path' => '/templates/edit/' . $this->templateOrigin->id 
                    ];
                }),
            ],
            
        ];
    }
}
