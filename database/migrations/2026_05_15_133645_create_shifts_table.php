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
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique();
            $table->string('description');
            $table->string('active_period');
            $table->string('rest_period');
            $table->string('total_period');
            $table->string('check_in_time');
            $table->string('check_out_time');
            $table->string('allow_check_out');
            $table->string('re_scanned');
            $table->string('available');
            $table->string('night_shift');
            $table->string('observations');

            $table->foreignId('department_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shifts');
    }
};
