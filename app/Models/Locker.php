<?php

namespace App\Models;

use App\Enums\LockerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Locker extends Model
{
    protected $fillable = [
        'code',
        'status',
        'locker_category_id',
    ];

    protected $casts = [
        'status' => LockerStatus::class,
    ];

    // Locker pertenece a una categoría
    public function lockerCategory(): BelongsTo
    {
        return $this->belongsTo(LockerCategory::class);
    }
}
