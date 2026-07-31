<?php

namespace App\Http\Requests\Api;

use App\Enums\SubmissionKind;
use App\Models\Competition;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var Competition $competition */
        $competition = $this->route('competition');

        $kind = $competition->competitionType->submission_kind;

        return match ($kind) {
            SubmissionKind::Text => ['text_content' => ['required', 'string']],
            SubmissionKind::Link => ['link_url' => ['required', 'url']],
            SubmissionKind::Image, SubmissionKind::Pdf, SubmissionKind::Video => [
                'file' => ['required', 'file', ...$kind->fileRules()],
            ],
            SubmissionKind::None => [],
        };
    }
}
