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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->dateTime('start')->nullable(); 
            $table->dateTime('end')->nullable();
            $table->boolean('all_day')->default(false);
            
            $table->string('external_source')->nullable(); //
            $table->string('external_id')->nullable(); //

            $table->boolean('repeat_event')->default(false);
            $table->string('repeat_interval')->nullable();

            $table->foreignId('parent_event_id')
                ->nullable()
                ->after('id')
                ->constrained('events')
                ->nullOnDelete();

            $table->json('extended_props');

            $table->foreignId('event_category_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('location_id')
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
        Schema::dropIfExists('events');
    }
};
