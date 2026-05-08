<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

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
            'start' => $this->start ? Carbon::parse($this->start)->format('Y-m-d\TH:i:s') : null,
            'end'   => $this->end ? Carbon::parse($this->end)->format('Y-m-d\TH:i:s') : null,
            'allDay' => $this->all_day,
            'rrule' => $this->rrule,
            'extendedProps' => [
                'category' => new EventCategoryResource($this->whenLoaded('eventCategory')),
                'location' => new LocationResource($this->whenLoaded('location')),
                
                'status' => $props['status'] ?? '',
                'eventType' => $props['event_type'] ?? null,
                'repeatEvent' => $props['repeat_event'] ?? null,
                'repeatInterval' => $props['repeat_interval'] ?? '',
                'createAlert' => $props['create_alert'] ?? false,
                'coloringDay' => $props['coloring_day'] ?? false,
                'description' => $props['description'] ?? '',
                'comments' => $props['comments'] ?? '',
                'isFixed' => $props['is_fixed'] ?? false,
                'createdBy' => $props['created_by'] ?? '',
                'specialLabel' => $this->when(
                        !empty($props['special_label'] ?? null),
                        $props['special_label'] ?? null
                ),
                'isTemplate' => (bool) $this->templateRecord()->exists(),
                'templateInfo' => $this->when($this->relationLoaded('templateOrigin') && $this->templateOrigin, function() {
                    return [
                        'hasTemplate' => true,
                        'id' => $this->templateOrigin->event_id,
                        'name' => $this->templateOrigin->name,
                        'routePath' => '/eventos/ver/' . $this->templateOrigin->event_id 
                    ];
                }),
            ],

           'contacts' => ContactResource::collection($this->whenLoaded('contacts')),
        ];
    }
}
