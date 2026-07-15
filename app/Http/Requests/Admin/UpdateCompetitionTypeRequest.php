<?php

namespace App\Http\Requests\Admin;

use App\Enums\SubmissionKind;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCompetitionTypeRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'submission_kind' => ['required', new Enum(SubmissionKind::class)],
        ];
    }
}
