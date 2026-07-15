<?php

use App\Models\User;

it('blocks login for a suspended user', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password'),
        'suspended_at' => now(),
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('allows login for a non-suspended user', function () {
    $user = User::factory()->create(['password' => bcrypt('password')]);

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
});
