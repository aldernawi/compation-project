<?php

use App\Models\Competition;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('shows aggregate statistics for an admin', function () {
    $competition = Competition::factory()->create();
    Submission::factory()->count(3)->create(['competition_id' => $competition->id]);
    User::factory()->participant()->count(2)->create();

    $response = $this->actingAs($this->admin)->get('/admin/reports');

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->component('admin/reports/index')
            ->where('stats.competitions_count', 1)
            ->where('stats.submissions_count', 3)
    );
});

it('forbids a non-admin from viewing reports', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)->get('/admin/reports')->assertForbidden();
});

it('filters statistics by a date range', function () {
    $oldCompetition = Competition::factory()->create(['created_at' => now()->subMonths(2)]);
    Submission::factory()->create(['competition_id' => $oldCompetition->id, 'created_at' => now()->subMonths(2)]);

    $recentCompetition = Competition::factory()->create(['created_at' => now()]);
    Submission::factory()->create(['competition_id' => $recentCompetition->id, 'created_at' => now()]);

    $response = $this->actingAs($this->admin)->get('/admin/reports?'.http_build_query([
        'from' => now()->subWeek()->toDateString(),
        'to' => now()->addDay()->toDateString(),
    ]));

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page
            ->where('stats.competitions_count', 1)
            ->where('stats.submissions_count', 1)
    );
});
