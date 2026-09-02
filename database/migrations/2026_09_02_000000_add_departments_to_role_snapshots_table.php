<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Añade la columna `departments` a role_snapshots.
     *
     * Guarda los ids de los departamentos a los que el usuario
     * tendrá acceso (array, igual que `permissions`).
     */
    public function up(): void
    {
        Schema::table('role_snapshots', function (Blueprint $table) {
            $table->json('departments')->nullable()->after('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_snapshots', function (Blueprint $table) {
            $table->dropColumn('departments');
        });
    }
};
