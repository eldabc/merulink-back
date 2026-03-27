<?php

namespace App\Models;

use App\Models\Department;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $fillable = [
        'ci',
        'num_employee',
        'first_name',
        'second_name',
        'last_name',
        'second_last_name',
        'birthdate',
        'place_of_birth',
        'nationality',
        'sex',
        'marital_status',
        'blood_type',
        'email',
        'mobile_phone',
        'home_phone',
        'address',
        'join_date',
        'department_id',
        'position_id',
        'user_name',
        'user_pass',
        'change_pass_next_login',
        'status',
        'use_meru_link',
        'use_hid_card',
        'use_locker',
        'use_transport',
    ];

    public function assignment(): HasOne
    {
        return $this->hasOne(Assign::class);
    }

    public function department() : BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position() {
        return $this->belongsTo(Position::class);
    }

    public function emergencyContacts() : hasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }
}
