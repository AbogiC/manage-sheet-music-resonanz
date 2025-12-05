<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sheet_music', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('composer');
            $table->string('arranger')->nullable();
            $table->string('instrument');
            $table->string('genre');
            $table->enum('difficulty', ['Beginner', 'Intermediate', 'Advanced', 'Professional']);
            $table->integer('pages');
            $table->string('key')->nullable();
            $table->string('time_signature')->nullable();
            $table->integer('tempo')->nullable();
            $table->text('description')->nullable();
            $table->json('tags')->nullable();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_size');
            $table->string('thumbnail_path')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('download_count')->default(0);
            $table->boolean('is_public')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes for faster queries
            $table->index(['user_id', 'instrument']);
            $table->index(['genre', 'difficulty']);
            $table->fullText(['title', 'composer', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sheet_music');
    }
};
