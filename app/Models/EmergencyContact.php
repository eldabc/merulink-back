<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmergencyContact extends Model
{
    protected $fillable = [
        'name',
        'last_name',
        'relationship',
        'phone',
        'address',
        'employee_id'
    ];

    public function employee() : BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
