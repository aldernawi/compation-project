<?php

namespace App\Http\Controllers\Api;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubmissionRequest;
use App\Http\Requests\Api\UpdateSubmissionRequest;
use App\Http\Resources\SubmissionResource;
use App\Models\Competition;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SubmissionController extends Controller
{
    public function myIndex(Request $request): AnonymousResourceCollection
    {
        return SubmissionResource::collection(
            $request->user()->submissions()->latest()->paginate(15)
        );
    }

    public function store(StoreSubmissionRequest $request, Competition $competition): JsonResponse
    {
        $submission = $competition->submissions()->create([
            'participant_id' => $request->user()->id,
            'status' => SubmissionStatus::Submitted,
            'text_content' => $request->input('text_content'),
            'link_url' => $request->input('link_url'),
        ]);

        if ($request->hasFile('file')) {
            $submission->addMediaFromRequest('file')->toMediaCollection('submission_files');
        }

        return (new SubmissionResource($submission))->response()->setStatusCode(201);
    }

    public function update(UpdateSubmissionRequest $request, Submission $submission): SubmissionResource
    {
        $submission->update([
            'text_content' => $request->input('text_content', $submission->text_content),
            'link_url' => $request->input('link_url', $submission->link_url),
        ]);

        if ($request->hasFile('file')) {
            $submission->clearMediaCollection('submission_files');
            $submission->addMediaFromRequest('file')->toMediaCollection('submission_files');
        }

        return new SubmissionResource($submission);
    }
}
