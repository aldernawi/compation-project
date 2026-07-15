<?php

namespace App\Http\Requests\Api;

use App\Enums\SubmissionKind;
use App\Models\Submission;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Submission $submission */
        $submission = $this->route('submission');

        return $submission->participant_id === $this->user()->id
            && $submission->status->value !== 'accepted'
            && now()->lessThanOrEqualTo($submission->competition->ends_at);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Submission $submission */
        $submission = $this->route('submission');
        $kind = $submission->competition->competitionType->submission_kind;

        return match ($kind) {
            SubmissionKind::Text => ['text_content' => ['required', 'string']],
            SubmissionKind::Link => ['link_url' => ['required', 'url']],
            SubmissionKind::Image, SubmissionKind::Pdf, SubmissionKind::Video => [
                'file' => ['nullable', 'file', ...$kind->fileRules()],
            ],
        };
    }
}
