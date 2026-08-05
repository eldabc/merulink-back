<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();

            // Relación Polimórfica (auditable_type, auditable_id)
            $table->morphs('auditable');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // Acción: 'created', 'viewed', 'updated', 'reviewed', 'approved', 'closed', etc.
            $table->string('description')->nullable();
            $table->json('payload')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
