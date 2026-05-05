<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
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
            'event_id' => $this->event_id,
            'fisrtName' => $this->fisrt_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'phones' => PhoneResource::collection($this->whenLoaded('phones')),
        ];
    }
}
