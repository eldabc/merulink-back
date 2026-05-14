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
            'repeatEvent' => $this->repeat_event ?? null,
            'repeatInterval' => $this->repeat_interval ?? '',
            'repeatUntil' => $this->repeat_until ? Carbon::parse($this->repeat_until)->format('Y-m-d\TH:i:s') : null,
            'repeatAlways' => $this->repeat_always ?? false,
            'isRepeatActive' => $this->is_repeat_active ?? false,
            'extendedProps' => [
                'category' => new EventCategoryResource($this->whenLoaded('eventCategory')),
                'location' => new LocationResource($this->whenLoaded('location')),
                'status' => $props['status'] ?? '',
                'eventType' => $props['event_type'] ?? null,
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
                'routePath' => '/eventos/ver/' . $this->id,
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
