<?php

use App\Models\User;

it('allows an admin to view the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->get('/admin');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/index'));
});

it('forbids a non-admin from viewing the admin dashboard', function () {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get('/admin')->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get('/admin')->assertRedirect('/login');
});
