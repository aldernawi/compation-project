<?php

use App\Enums\CompetitionStatus;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists competitions for an admin', function () {
    Competition::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)->get('/admin/competitions');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/competitions/index'));
});

it('forbids a non-admin from listing competitions', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)->get('/admin/competitions')->assertForbidden();
});

it('creates a competition and links it to an organizer', function () {
    $organizer = User::factory()->organizer()->create();
    $type = CompetitionType::factory()->create();

    $response = $this->actingAs($this->admin)->post('/admin/competitions', [
        'organizer_id' => $organizer->id,
        'competition_type_id' => $type->id,
        'title' => 'Best Photo 2026',
        'description' => 'A photo contest',
        'terms' => 'Be nice',
        'starts_at' => now()->addDay()->toDateTimeString(),
        'ends_at' => now()->addMonth()->toDateTimeString(),
        'status' => 'upcoming',
        'requires_approval' => true,
        'evaluation_method' => 'average_score',
    ]);

    $response->assertRedirect('/admin/competitions');

    $competition = Competition::where('title', 'Best Photo 2026')->firstOrFail();
    expect($competition->organizer_id)->toBe($organizer->id);
});

it('updates a competition', function () {
    $competition = Competition::factory()->create();
    $newOrganizer = User::factory()->organizer()->create();

    $response = $this->actingAs($this->admin)->put("/admin/competitions/{$competition->id}", [
        'organizer_id' => $newOrganizer->id,
        'competition_type_id' => $competition->competition_type_id,
        'title' => 'Updated Title',
        'description' => $competition->description,
        'terms' => $competition->terms,
        'starts_at' => $competition->starts_at->toDateTimeString(),
        'ends_at' => $competition->ends_at->toDateTimeString(),
        'status' => 'open',
        'requires_approval' => false,
        'evaluation_method' => 'average_score',
    ]);

    $response->assertRedirect('/admin/competitions');

    expect($competition->fresh()->title)->toBe('Updated Title')
        ->and($competition->fresh()->organizer_id)->toBe($newOrganizer->id)
        ->and($competition->fresh()->status)->toBe(CompetitionStatus::Open);
});

it('deletes a competition', function () {
    $competition = Competition::factory()->create();

    $this->actingAs($this->admin)->delete("/admin/competitions/{$competition->id}")->assertRedirect('/admin/competitions');

    expect(Competition::find($competition->id))->toBeNull();
});
