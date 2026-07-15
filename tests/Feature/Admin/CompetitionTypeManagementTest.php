<?php

use App\Models\CompetitionType;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists competition types for an admin', function () {
    CompetitionType::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)->get('/admin/competition-types');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/competition-types/index'));
});

it('forbids a non-admin from listing competition types', function () {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get('/admin/competition-types')->assertForbidden();
});

it('creates a competition type', function () {
    $response = $this->actingAs($this->admin)->post('/admin/competition-types', [
        'name' => 'Photography',
        'description' => 'Photo contests',
        'submission_kind' => 'image',
    ]);

    $response->assertRedirect('/admin/competition-types');

    $type = CompetitionType::where('name', 'Photography')->firstOrFail();
    expect($type->slug)->toBe('photography')
        ->and($type->submission_kind->value)->toBe('image');
});

it('updates a competition type', function () {
    $type = CompetitionType::factory()->create();

    $response = $this->actingAs($this->admin)->put("/admin/competition-types/{$type->id}", [
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'submission_kind' => 'video',
    ]);

    $response->assertRedirect('/admin/competition-types');

    expect($type->fresh()->name)->toBe('Updated Name')
        ->and($type->fresh()->submission_kind->value)->toBe('video');
});

it('deletes a competition type', function () {
    $type = CompetitionType::factory()->create();

    $this->actingAs($this->admin)->delete("/admin/competition-types/{$type->id}")->assertRedirect('/admin/competition-types');

    expect(CompetitionType::find($type->id))->toBeNull();
});
