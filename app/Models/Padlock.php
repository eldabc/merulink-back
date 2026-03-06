<?php

namespace App\Models;

use App\Enums\PadlockStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
