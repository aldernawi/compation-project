<?php

namespace App\Http\Requests\Organizer;

use App\Models\Competition;
use Illuminate\Foundation\Http\FormRequest;

class StoreNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Competition $competition */
        $competition = $this->route('competition');

        return $this->user()->can('update', $competition);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:1000'],
        ];
    }
}
