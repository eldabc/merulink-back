<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePeriod extends Model
{
    protected $fillable = [
        'hire_date',
        'retire_date',
        'retire_reason',
        'notes',
        'employee_id',
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
