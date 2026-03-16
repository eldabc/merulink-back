<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PadlockResource extends JsonResource
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
            'serial' => $this->serial,
            'pass' => $this->pass,
            'status' => $this->status,
            'pattern' => [
                'id' => $this->padlockPattern->id,
                'model_name' => $this->padlockPattern->model_name,
                'reset_instructions' => $this->padlockPattern->reset_instructions,
                'unlock_sequence' => $this->padlockPattern->unlock_sequence
            ]
        ];
    }
}
