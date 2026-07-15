<?php

it('throttles repeated login attempts from the same ip', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
            ->assertStatus(401);
    }

    $this->postJson('/api/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])
        ->assertStatus(429);
});

it('throttles repeated registration attempts from the same ip', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/register', [
            'name' => 'Someone',
            'email' => "user{$i}@example.com",
            'phone_number' => '+15551234567',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertCreated();
    }

    $this->postJson('/api/register', [
        'name' => 'Overflow',
        'email' => 'overflow@example.com',
        'phone_number' => '+15551234567',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertStatus(429);
});
