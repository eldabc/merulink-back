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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('ci')->unique();
            $table->string('num_employee')->unique();
            $table->string('first_name');
            $table->string('second_name');
            $table->string('last_name');
            $table->string('second_last_name');
            $table->date('birthDate');
            $table->string('place_of_birth');
            $table->string('nationality');
            $table->string('sex');
            $table->string('marital_status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
