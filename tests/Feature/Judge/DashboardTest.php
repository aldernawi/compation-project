<?php

use App\Models\User;

it('allows a judge to view the judge dashboard', function () {
    $judge = User::factory()->judge()->create();

    $response = $this->actingAs($judge)->get('/judge');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('judge/index'));
});

it('forbids a non-judge from viewing the judge dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get('/judge')->assertForbidden();
});

it('redirects guests to login', function () {
    $this->get('/judge')->assertRedirect('/login');
});
