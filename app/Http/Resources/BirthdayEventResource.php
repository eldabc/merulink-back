<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BirthdayEventResource extends JsonResource
{
    protected $today;
    protected $eventCategory;

    public function __construct($resource, $today = null, $eventCategory = null)
    {
        parent::__construct($resource);
        $this->today = $today ?? now();
        $this->eventCategory = $eventCategory;
    }

    public function toArray($request)
    {
        $birthdate = \Carbon\Carbon::parse($this->birthdate);

        $birthday = $birthdate->copy()->year($this->today->year);

        $nextAge = $this->today->year - $birthdate->year;

        return [
            'id' => 'birthday-' . $this->id,
            'title' => '🎂 ' . $this->first_name . ' ' . $this->last_name,
            'start' => $birthday->toDateString().'T00:00:00',
            'end' => $birthday->toDateString().'T00:00:00',
            'allDay' => true,

            'extendedProps' => [
                'type' => 'birthday',
                'employee_id' => $this->id,
                'nextAge' => $nextAge,
                'department' => $this->position?->department ? [
                    'id' => $this->position->department->id,
                    'name' => $this->position->department->name,
                ] : null,

                'category' => $this->eventCategory ? [
                    'id' => $this->eventCategory->id,
                    'key' => $this->eventCategory->key,
                    'label' => $this->eventCategory->label,
                    'color' => $this->eventCategory->color,
                ] : null,
            ],
        ];
    }
}
