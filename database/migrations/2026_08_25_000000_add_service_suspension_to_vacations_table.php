<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos para la desactivación temporal de servicios durante una ausencia.
     * - services_suspended_at / services_restore_at: controlan que sea idempotente (no suspender/reanudar dos veces).
     * - prev_services: snapshot JSON del estado previo de los servicios.
     */
    public function up(): void
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->timestamp('services_suspended_at')->nullable();
            $table->timestamp('services_restore_at')->nullable();
            $table->jsonb('prev_services')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vacations', function (Blueprint $table) {
            $table->dropColumn([
                'services_suspended_at',
                'services_restore_at',
                'prev_services',
            ]);
        });
    }
};
