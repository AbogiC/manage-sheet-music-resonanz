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
        Schema::create('event_score', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('sheet_music_id')->constrained('sheet_music')->onDelete('cascade');
            $table->integer('order')->default(0); // Order in the event setlist
            $table->text('notes')->nullable(); // Additional notes for this piece in the event
            $table->timestamps();

            // Ensure unique combination
            $table->unique(['event_id', 'sheet_music_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_score');
    }
};
