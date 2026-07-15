<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        User::factory()->organizer()->create([
            'name' => 'Organizer',
            'email' => 'organizer@example.com',
        ]);

        User::factory()->judge()->create([
            'name' => 'Judge',
            'email' => 'judge@example.com',
        ]);

        User::factory()->participant()->create([
            'name' => 'Participant',
            'email' => 'participant@example.com',
        ]);
    }
}
