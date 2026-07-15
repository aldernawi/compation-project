<?php

use App\Models\Competition;
use App\Models\Prize;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists prizes for a competition', function () {
    $competition = Competition::factory()->create();
    Prize::factory()->count(2)->create(['competition_id' => $competition->id]);

    $response = $this->actingAs($this->admin)->get("/admin/competitions/{$competition->id}/prizes");

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/prizes/index'));
});

it('forbids a non-admin from listing prizes', function () {
    $competition = Competition::factory()->create();
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get("/admin/competitions/{$competition->id}/prizes")->assertForbidden();
});

it('creates a prize for a competition', function () {
    $competition = Competition::factory()->create();

    $response = $this->actingAs($this->admin)->post("/admin/competitions/{$competition->id}/prizes", [
        'title' => 'Gold Medal',
        'description' => 'First place',
        'winners_count' => 1,
        'rank' => 1,
    ]);

    $response->assertRedirect("/admin/competitions/{$competition->id}/prizes");

    $prize = Prize::where('title', 'Gold Medal')->firstOrFail();
    expect($prize->competition_id)->toBe($competition->id);
});

it('updates a prize', function () {
    $competition = Competition::factory()->create();
    $prize = Prize::factory()->create(['competition_id' => $competition->id]);

    $response = $this->actingAs($this->admin)->put("/admin/competitions/{$competition->id}/prizes/{$prize->id}", [
        'title' => 'Updated Prize',
        'description' => 'Updated',
        'winners_count' => 2,
        'rank' => 2,
    ]);

    $response->assertRedirect("/admin/competitions/{$competition->id}/prizes");

    expect($prize->fresh()->title)->toBe('Updated Prize');
});

it('deletes a prize', function () {
    $competition = Competition::factory()->create();
    $prize = Prize::factory()->create(['competition_id' => $competition->id]);

    $this->actingAs($this->admin)
        ->delete("/admin/competitions/{$competition->id}/prizes/{$prize->id}")
        ->assertRedirect("/admin/competitions/{$competition->id}/prizes");

    expect(Prize::find($prize->id))->toBeNull();
});
