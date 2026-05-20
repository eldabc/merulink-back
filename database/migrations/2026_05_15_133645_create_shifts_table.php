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
            $table->string('rest_period_time');
            $table->string('rest_period_unit_time');
            $table->string('active_period_time');
            $table->string('active_period_unit_time');
            $table->string('total_period_time');
            $table->string('total_period_unit_time');

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
