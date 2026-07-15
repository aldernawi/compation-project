<?php

use App\Enums\Role;

it('has the four expected role cases', function () {
    expect(array_column(Role::cases(), 'value'))
        ->toBe(['admin', 'organizer', 'judge', 'participant']);
});
