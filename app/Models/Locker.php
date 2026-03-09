<?php

namespace App\Models;

use App\Models\Assign;
use App\Enums\LockerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function assignment(): HasOne
    {
        return $this->hasOne(Assign::class);
    }
}
