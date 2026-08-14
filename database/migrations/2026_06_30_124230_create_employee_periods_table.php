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
        Schema::create('employee_periods', function (Blueprint $table) {
            $table->id();
            $table->date('hire_date');
            $table->date('retire_date')->nullable();
            $table->date('scheduled_deactivate_date')->nullable();
            $table->string('retire_reason')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('employee_id')
                  ->constrained('employees')
                  ->onDelete('cascade');
                        
            $table->timestamps();

            $table->index(['hire_date', 'retire_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_periods');
    }
};
