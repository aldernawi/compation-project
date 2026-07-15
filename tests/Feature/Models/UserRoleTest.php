<?php

use App\Enums\Role;
use App\Models\User;

it('casts role to the Role enum and defaults new users to participant', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(Role::Participant);

    $organizer = User::factory()->organizer()->create();

    expect($organizer->role)->toBe(Role::Organizer);
});
