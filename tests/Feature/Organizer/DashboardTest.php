<?php

use App\Models\User;

it('allows an organizer to view the organizer dashboard', function () {
    $organizer = User::factory()->organizer()->create();

    $response = $this->actingAs($organizer)->get('/organizer');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('organizer/index'));
});

it('forbids a non-organizer from viewing the organizer dashboard', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)->get('/organizer')->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get('/organizer')->assertRedirect('/login');
});
