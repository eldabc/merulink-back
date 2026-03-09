<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LockerCategory extends Model
{
    public function locker(): hasMany
    {
        return $this->hasMany(Locker::class);
    }
}
