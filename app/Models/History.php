<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class History extends Model
    {
        use HasFactory;

        protected $fillable = [
            'auditable_type',
            'auditable_id',
            'user_id',
            'action',
            'description',
            'payload',
        ];

        protected $casts = [
            'payload' => 'array',
        ];

        // Relación polimórfica hacia modelo origen
        public function auditable()
        {
            return $this->morphTo();
        }

        public function user()
        {
            return $this->belongsTo(User::class);
        }
    }