<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistoryResource extends JsonResource
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
            'action' => $this->action,
            'description' => $this->description,
            'user' => [
                'id' => $this->user_id,
                'userName' => $this->user->username,
                'firstName' => $this->user->employee?->first_name,
                'lastName' => $this->user->employee?->last_name,
            ],
            'date' => $this->created_at,
        ];
    }
}
