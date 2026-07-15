<?php

use App\Models\Competition;
use App\Models\User;

it('lists only competitions the judge is assigned to', function () {
    $judge = User::factory()->judge()->create();
    $assigned = Competition::factory()->create();
    $assigned->judges()->attach($judge);
    Competition::factory()->create();

    $response = $this->actingAs($judge)->get('/judge/competitions');

    $response->assertOk();
    $response->assertInertia(
        fn ($page) => $page->component('judge/competitions/index')
            ->has('competitions.data', 1)
    );
});

it('forbids a non-judge from listing judge competitions', function () {
    $organizer = User::factory()->organizer()->create();

    $this->actingAs($organizer)->get('/judge/competitions')->assertForbidden();
});
