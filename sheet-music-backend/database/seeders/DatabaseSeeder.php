<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\SheetMusic;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create sample sheet music
        SheetMusic::create([
            'user_id' => $admin->id,
            'title' => 'Nocturne Op. 9 No. 2',
            'composer' => 'Frédéric Chopin',
            'instrument' => 'Piano',
            'genre' => 'Classical',
            'difficulty' => 'Intermediate',
            'pages' => 5,
            'description' => "One of Chopin's most famous nocturnes, known for its lyrical melody.",
            'tags' => json_encode(['romantic', 'nocturne', 'solo']),
            'file_path' => 'sample.pdf',
            'file_name' => 'chopin_nocturne.pdf',
            'file_size' => '1024000',
            'view_count' => 150,
            'download_count' => 75,
            'is_public' => true
        ]);

        SheetMusic::create([
            'user_id' => $admin->id,
            'title' => 'Für Elise',
            'composer' => 'Ludwig van Beethoven',
            'instrument' => 'Piano',
            'genre' => 'Classical',
            'difficulty' => 'Beginner',
            'pages' => 3,
            'description' => 'A famous bagatelle, perfect for early-stage piano students.',
            'tags' => json_encode(['bagatelle', 'classical', 'solo']),
            'file_path' => 'sample.pdf',
            'file_name' => 'beethoven_fur_elise.pdf',
            'file_size' => '512000',
            'view_count' => 300,
            'download_count' => 200,
            'is_public' => true
        ]);

        SheetMusic::create([
            'user_id' => $admin->id,
            'title' => 'Autumn Leaves',
            'composer' => 'Joseph Kosma',
            'instrument' => 'Guitar',
            'genre' => 'Jazz',
            'difficulty' => 'Intermediate',
            'pages' => 4,
            'description' => 'A jazz standard with beautiful chord progressions.',
            'tags' => json_encode(['jazz standard', 'chords', 'improvisation']),
            'file_path' => 'sample.pdf',
            'file_name' => 'autumn_leaves.pdf',
            'file_size' => '768000',
            'view_count' => 120,
            'download_count' => 80,
            'is_public' => true
        ]);
    }
}
