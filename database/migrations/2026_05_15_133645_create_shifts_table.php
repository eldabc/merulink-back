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
            $table->string('night_shift');
            $table->string('type_shift');
            $table->string('check_in_time');
            $table->string('check_out_time');
            $table->string('time_rest_period');
            $table->string('duration_unit_rest_period');
            $table->string('time_active_period');
            $table->string('duration_unit_active_period');
            $table->string('time_total_period');
            $table->string('duration_unit_total_period');

            $table->string('allow_exit');
            $table->string('allow_re_scanned');
            $table->string('available');
            $table->string('observation')->nullable();

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
