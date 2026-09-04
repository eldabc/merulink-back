<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hace que el campo email de employees deje de ser obligatorio.
     *
     * Se mantiene el índice único: en Postgres los NULL no colisionan entre sí,
     * así que puede haber varios empleados sin email.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};
