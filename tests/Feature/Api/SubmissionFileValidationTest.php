<?php

use App\Enums\SubmissionKind;
use App\Models\Competition;
use App\Models\CompetitionType;
use App\Models\User;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    $this->participant = User::factory()->participant()->create();
});

it('accepts a valid image file for an image competition', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Image]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $response = $this->actingAs($this->participant, 'sanctum')->postJson(
        "/api/competitions/{$competition->id}/submissions",
        ['file' => UploadedFile::fake()->image('entry.jpg')],
    );

    $response->assertCreated();
});

it('rejects a non-image file for an image competition', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Image]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $response = $this->actingAs($this->participant, 'sanctum')->postJson(
        "/api/competitions/{$competition->id}/submissions",
        ['file' => UploadedFile::fake()->create('entry.exe', 10)],
    );

    $response->assertUnprocessable();
});

it('rejects an oversized image file', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Image]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $response = $this->actingAs($this->participant, 'sanctum')->postJson(
        "/api/competitions/{$competition->id}/submissions",
        ['file' => UploadedFile::fake()->image('entry.jpg')->size(10241)],
    );

    $response->assertUnprocessable();
});

it('rejects a non-pdf file for a pdf competition', function () {
    $type = CompetitionType::factory()->create(['submission_kind' => SubmissionKind::Pdf]);
    $competition = Competition::factory()->create(['competition_type_id' => $type->id]);

    $response = $this->actingAs($this->participant, 'sanctum')->postJson(
        "/api/competitions/{$competition->id}/submissions",
        ['file' => UploadedFile::fake()->image('entry.jpg')],
    );

    $response->assertUnprocessable();
});
