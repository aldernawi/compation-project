<?php

use App\Enums\Role;
use App\Models\User;

it('registers a participant and returns a token', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()->assertJsonStructure(['token', 'user' => ['id', 'name', 'email', 'role']]);

    $user = User::where('email', 'jane@example.com')->firstOrFail();
    expect($user->role)->toBe(Role::Participant);
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
