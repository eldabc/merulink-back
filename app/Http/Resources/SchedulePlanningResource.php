<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SchedulePlanningResource extends JsonResource
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
            'start' => $this->start,
            'end' => $this->end,
            'status' => $this->status,
            'observations' => $this->observations,
            'department' => new ScheduleSnapshotResource($this->whenLoaded('department')),
            'schedules' => $this->when(ScheduleResource::collection($this->whenLoaded('schedules'))),
        ];
    }
}
