<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    public function assignment(): HasOne
    {
        return $this->hasOne(Assign::class);
    }
}
