<?php

namespace App\Http\Requests\Admin;

use App\Enums\CompetitionStatus;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreCompetitionRequest extends FormRequest
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
            'organizer_id' => ['required', Rule::exists('users', 'id')->where('role', Role::Organizer->value)],
            'competition_type_id' => ['required', 'exists:competition_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'status' => ['required', new Enum(CompetitionStatus::class)],
            'requires_approval' => ['boolean'],
            'evaluation_method' => ['required', 'string', 'max:255'],
        ];
    }
}
