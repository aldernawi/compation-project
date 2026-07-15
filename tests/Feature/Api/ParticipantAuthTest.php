<?php

use App\Enums\Role;
use App\Models\User;

it('registers a participant with a phone number and returns a token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'phone_number' => '+15551234567',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'phone_number', 'role']]);

    $user = User::where('email', 'jane@example.com')->firstOrFail();
    expect($user->role)->toBe(Role::Participant)
        ->and($user->phone_number)->toBe('+15551234567');
});

it('rejects registration missing a phone number', function () {
    $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertUnprocessable();
});

it('logs a participant in and returns a token', function () {
    $user = User::factory()->participant()->create(['password' => bcrypt('password')]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertOk()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);
});

it('rejects login with a bad password', function () {
    $user = User::factory()->participant()->create(['password' => bcrypt('password')]);

    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertUnauthorized();
});

it('logs the participant out and revokes the token', function () {
    $user = User::factory()->participant()->create();
    $token = $user->createToken('flutter-app')->plainTextToken;

    $this->withHeader('Authorization', "Bearer {$token}")
        ->postJson('/api/logout')
        ->assertNoContent();

    expect($user->tokens()->count())->toBe(0);
});
