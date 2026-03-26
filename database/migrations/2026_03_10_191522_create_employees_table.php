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
            $table->date('birthdate');
            $table->string('place_of_birth');
            $table->string('nationality');
            $table->string('sex');
            $table->string('marital_status');
            $table->string('blood_type');
            $table->string('email');
            $table->string('mobile_phone');
            $table->string('home_phone');
            $table->string('address');
            $table->string('join_date');

            $table->foreignId('department_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('position_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->string('user_name');
            $table->string('user_pass');
            $table->boolean('change_pass_next_login')->default(false);
            $table->boolean('status')->default(true);
            $table->boolean('use_meru_link')->default(false);
            $table->boolean('use_hid_card')->default(false);
            $table->boolean('use_locker')->default(false);
            $table->boolean('use_transport')->default(false);
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
