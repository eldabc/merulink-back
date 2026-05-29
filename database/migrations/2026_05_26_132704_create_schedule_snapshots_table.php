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
        Schema::create('schedule_snapshots', function (Blueprint $table) {
            $table->id();

            $table->date('date');

            $table->foreignId('employee_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->string('letter_shift')->nullable();
            $table->string('color')->nullable();
            $table->string('snapshot_code')->index();
            $table->string('snapshot_type')->default('final_closure');
            $table->string('code')->unique();
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

            $table->foreignId('schedule_planning_id')->constrained('schedule_plannings')->onDelete('cascade');

            $table->foreignId('schedule_id')
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
        Schema::dropIfExists('schedule_snapshots');
    }
};
