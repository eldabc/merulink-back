<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('padlock_patterns', function (Blueprint $table) {
            $table->id();
            $table->string('model_name');
            $table->string('reset_instructions');
            $table->json('unlock_sequence');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('padlock_patterns');
    }
};
