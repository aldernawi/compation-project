<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreUserRequest extends FormRequest
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'age' => ['nullable', 'integer', 'between:5,120'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', new Enum(Role::class)],
            'can_manage_judges' => ['boolean'],
        ];
    }
}
