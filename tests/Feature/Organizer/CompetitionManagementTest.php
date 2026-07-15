<?php

use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;

beforeEach(function () {
    $this->organizer = User::factory()->organizer()->create();
});

it('lists only the organizer own competitions', function () {
    Competition::factory()->create(['organizer_id' => $this->organizer->id]);
    Competition::factory()->create();

    $response = $this->actingAs($this->organizer)->get('/organizer/competitions');

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page->component('organizer/competitions/index')
            ->has('competitions.data', 1)
    );
});

it('forbids a non-organizer from listing organizer competitions', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)->get('/organizer/competitions')->assertForbidden();
});

it('creates a competition owned by the current organizer', function () {
    $type = CompetitionType::factory()->create();

    $response = $this->actingAs($this->organizer)->post('/organizer/competitions', [
        'competition_type_id' => $type->id,
        'title' => 'My Contest',
        'description' => 'desc',
        'terms' => 'terms',
        'starts_at' => now()->addDay()->toDateTimeString(),
        'ends_at' => now()->addMonth()->toDateTimeString(),
        'requires_approval' => true,
        'evaluation_method' => 'average_score',
    ]);

    $response->assertRedirect('/organizer/competitions');

    $competition = Competition::where('title', 'My Contest')->firstOrFail();
    expect($competition->organizer_id)->toBe($this->organizer->id);
});

it('lets an organizer edit their own competition', function () {
    $competition = Competition::factory()->create(['organizer_id' => $this->organizer->id]);

    $response = $this->actingAs($this->organizer)->put("/organizer/competitions/{$competition->id}", [
        'competition_type_id' => $competition->competition_type_id,
        'title' => 'Updated Title',
        'description' => $competition->description,
        'terms' => $competition->terms,
        'starts_at' => $competition->starts_at->toDateTimeString(),
        'ends_at' => $competition->ends_at->toDateTimeString(),
        'requires_approval' => false,
        'evaluation_method' => 'average_score',
    ]);

    $response->assertRedirect('/organizer/competitions');
    expect($competition->fresh()->title)->toBe('Updated Title');
});

it('forbids an organizer from editing another organizer competition', function () {
    $competition = Competition::factory()->create();

    $this->actingAs($this->organizer)->get("/organizer/competitions/{$competition->id}/edit")->assertForbidden();
});
