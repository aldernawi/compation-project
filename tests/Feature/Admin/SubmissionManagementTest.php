<?php

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
});

it('lists submissions for an admin', function () {
    Submission::factory()->count(2)->create();

    $response = $this->actingAs($this->admin)->get('/admin/submissions');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->component('admin/submissions/index'));
});

it('forbids a non-admin from listing submissions', function () {
    $judge = User::factory()->judge()->create();

    $this->actingAs($judge)->get('/admin/submissions')->assertForbidden();
});

it('accepts a submission', function () {
    $submission = Submission::factory()->create(['status' => SubmissionStatus::Submitted]);

    $this->actingAs($this->admin)->patch("/admin/submissions/{$submission->id}/accept")->assertRedirect();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Accepted);
});

it('rejects a submission', function () {
    $submission = Submission::factory()->create(['status' => SubmissionStatus::Submitted]);

    $this->actingAs($this->admin)->patch("/admin/submissions/{$submission->id}/reject")->assertRedirect();

    expect($submission->fresh()->status)->toBe(SubmissionStatus::Rejected);
});

it('deletes a submission', function () {
    $submission = Submission::factory()->create();

    $this->actingAs($this->admin)->delete("/admin/submissions/{$submission->id}")->assertRedirect('/admin/submissions');

    expect(Submission::find($submission->id))->toBeNull();
});
