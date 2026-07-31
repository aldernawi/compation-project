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
        // Admins
        User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        User::factory()->admin()->create([
            'name' => 'Second Admin',
            'email' => 'admin2@example.com',
        ]);

        // Organizers
        User::factory()->organizer()->create([
            'name' => 'Organizer',
            'email' => 'organizer@example.com',
            'can_manage_judges' => true,
        ]);

        User::factory()->organizer()->create([
            'name' => 'Second Organizer',
            'email' => 'organizer2@example.com',
            'can_manage_judges' => false,
        ]);

        User::factory()->organizer()->create([
            'name' => 'Suspended Organizer',
            'email' => 'organizer-suspended@example.com',
            'suspended_at' => now(),
        ]);

        // Judges
        User::factory()->judge()->create([
            'name' => 'Judge',
            'email' => 'judge@example.com',
        ]);

        User::factory()->judge()->create([
            'name' => 'Second Judge',
            'email' => 'judge2@example.com',
        ]);

        User::factory()->judge()->count(3)->create();

        // Participants
        User::factory()->participant()->create([
            'name' => 'Participant',
            'email' => 'participant@example.com',
        ]);

        User::factory()->participant()->create([
            'name' => 'Suspended Participant',
            'email' => 'participant-suspended@example.com',
            'suspended_at' => now(),
        ]);

        User::factory()->participant()->unverified()->create([
            'name' => 'Unverified Participant',
            'email' => 'participant-unverified@example.com',
        ]);

        User::factory()->participant()->count(10)->create();

        // Competitions with types, prizes, and submissions
        $this->call(CompetitionSeeder::class);
    }
}
