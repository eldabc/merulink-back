<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleSnapshot extends Model
{
    public function department() : BelongsTo
    {
        return $this->BelongsTo(Department::class);
    }

    public function schedule() : BelongsTo
    {
        return $this->BelongsTo(Schedule::class);
    }
}
