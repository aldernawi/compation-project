<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'role:admin,organizer'])
        ->get('/test-role-route', fn () => 'ok');
});

it('allows a user whose role is in the allowed list', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/test-role-route')->assertOk();
});

it('forbids a user whose role is not in the allowed list', function () {
    $participant = User::factory()->participant()->create();

    $this->actingAs($participant)->get('/test-role-route')->assertForbidden();
});
