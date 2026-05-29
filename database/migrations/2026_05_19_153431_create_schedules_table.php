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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date');

            $table->foreignId('employee_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->string('letter_shift');
            $table->string('color');
            // $table->string('snapshot_code')->index(); // tentativos** para control
            // $table->string('snapshot_type')->default('final_closure'); // tentativos** para control
            $table->string('code');
            $table->string('night_shift')->nullable();
            $table->string('type_shift')->nullable();
            $table->string('check_in_time')->nullable();
            $table->string('check_out_time')->nullable();
            $table->string('rest_period_time')->nullable();
            $table->string('rest_period_unit_time')->nullable();
            $table->string('active_period_time')->nullable();
            $table->string('active_period_unit_time')->nullable();
            $table->string('total_period_time')->nullable();
            $table->string('total_period_unit_time')->nullable();
            $table->string('allow_exit')->nullable();
            $table->string('allow_re_scanned')->nullable();

            $table->foreignId('schedule_planning_id')->constrained('schedule_plannings')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
