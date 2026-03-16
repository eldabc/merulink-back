<?php

namespace App\Models;

use App\Models\PadlockPattern;
use App\Enums\PadlockStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Padlock extends Model
{
    protected $fillable = [
        'serial',
        'pass',
        'status',
    ];

    protected $casts = [
        'status' => PadlockStatus::class,
    ];

    public function assignment(): HasOne
    {
        return $this->hasOne(Assign::class);
    }

    public function padlockPattern(): BelongsTo
    {
        return $this->belongsTo(PadlockPattern::class);
    }
}
