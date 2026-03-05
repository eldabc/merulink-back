<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LockerResource extends JsonResource
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
            'code' => $this->code,
            'status' => $this->status,
            'category' => [
                'id' => $this->lockerCategory?->id,
                'key' => $this->lockerCategory?->key,
                'name' => $this->lockerCategory?->name,
            ],
        ];
        // return parent::toArray($request);
    }
}
