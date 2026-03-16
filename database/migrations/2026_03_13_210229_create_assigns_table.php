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
        Schema::create('assigns', function (Blueprint $table) {
            $table->id();
            $table->string('assign_code')->nullable();
            $table->date('assign_date')->nullable();

            $table->foreignId('locker_id')
                  ->constrained()
                  ->onDelete('cascade'); // Si se borra locker, se borra la asignación

            $table->foreignId('padlock_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('employee_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigns');
    }
};
