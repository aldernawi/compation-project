<?php

use App\Enums\Role;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists users for an admin', function () {
    User::factory()->count(3)->create();

    $response = $this->actingAs($this->admin)->get('/admin/users');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/users/index'));
});

it('forbids a non-admin from listing users', function () {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get('/admin/users')->assertForbidden();
});

it('creates a user with a given role', function () {
    $response = $this->actingAs($this->admin)->post('/admin/users', [
        'name' => 'New Organizer',
        'email' => 'organizer@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'organizer',
    ]);

    $response->assertRedirect('/admin/users');

    $user = User::where('email', 'organizer@example.com')->firstOrFail();
    expect($user->role)->toBe(Role::Organizer);
});

it('updates a user', function () {
    $user = User::factory()->participant()->create();

    $response = $this->actingAs($this->admin)->put("/admin/users/{$user->id}", [
        'name' => 'Updated Name',
        'email' => $user->email,
        'role' => 'judge',
    ]);

    $response->assertRedirect('/admin/users');

    expect($user->fresh()->name)->toBe('Updated Name')
        ->and($user->fresh()->role)->toBe(Role::Judge);
});

it('suspends and activates a user', function () {
    $user = User::factory()->participant()->create();

    $this->actingAs($this->admin)->patch("/admin/users/{$user->id}/suspend")->assertRedirect();
    expect($user->fresh()->isSuspended())->toBeTrue();

    $this->actingAs($this->admin)->patch("/admin/users/{$user->id}/activate")->assertRedirect();
    expect($user->fresh()->isSuspended())->toBeFalse();
});

it('deletes a user', function () {
    $user = User::factory()->participant()->create();

    $this->actingAs($this->admin)->delete("/admin/users/{$user->id}")->assertRedirect('/admin/users');

    expect(User::find($user->id))->toBeNull();
});
