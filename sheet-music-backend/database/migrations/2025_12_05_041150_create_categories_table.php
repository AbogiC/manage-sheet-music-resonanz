<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // instrument, genre, difficulty
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default categories
        DB::table('categories')->insert([
            // Instruments
            ['type' => 'instrument', 'name' => 'Piano', 'slug' => 'piano', 'order' => 1],
            ['type' => 'instrument', 'name' => 'Guitar', 'slug' => 'guitar', 'order' => 2],
            ['type' => 'instrument', 'name' => 'Violin', 'slug' => 'violin', 'order' => 3],
            ['type' => 'instrument', 'name' => 'Flute', 'slug' => 'flute', 'order' => 4],
            ['type' => 'instrument', 'name' => 'Cello', 'slug' => 'cello', 'order' => 5],
            ['type' => 'instrument', 'name' => 'Saxophone', 'slug' => 'saxophone', 'order' => 6],

            // Genres
            ['type' => 'genre', 'name' => 'Classical', 'slug' => 'classical', 'order' => 1],
            ['type' => 'genre', 'name' => 'Jazz', 'slug' => 'jazz', 'order' => 2],
            ['type' => 'genre', 'name' => 'Pop', 'slug' => 'pop', 'order' => 3],
            ['type' => 'genre', 'name' => 'Ragtime', 'slug' => 'ragtime', 'order' => 4],
            ['type' => 'genre', 'name' => 'Film', 'slug' => 'film', 'order' => 5],
            ['type' => 'genre', 'name' => 'Blues', 'slug' => 'blues', 'order' => 6],
            ['type' => 'genre', 'name' => 'Folk', 'slug' => 'folk', 'order' => 7],

            // Difficulties
            ['type' => 'difficulty', 'name' => 'Beginner', 'slug' => 'beginner', 'order' => 1],
            ['type' => 'difficulty', 'name' => 'Intermediate', 'slug' => 'intermediate', 'order' => 2],
            ['type' => 'difficulty', 'name' => 'Advanced', 'slug' => 'advanced', 'order' => 3],
            ['type' => 'difficulty', 'name' => 'Professional', 'slug' => 'professional', 'order' => 4],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
