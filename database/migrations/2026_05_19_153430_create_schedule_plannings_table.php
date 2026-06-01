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
        Schema::create('schedule_plannings', function (Blueprint $table) {
            $table->id();
                    
            $table->date('start');
            $table->date('end');
            $table->tinyInteger('month_number');
            $table->tinyInteger('fortnight_number');
            $table->string('status')->default('created'); // created, reviewed, approved, closed
            $table->text('observations')->nullable();

            $table->foreignId('department_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_plannings');
    }
};
