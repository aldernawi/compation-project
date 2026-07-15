<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/users/index', [
            'users' => User::query()->latest()->paginate(15),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'can_manage_judges' => $request->boolean('can_manage_judges'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return to_route('admin.users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => $user->only(['id', 'name', 'email', 'role', 'can_manage_judges']),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $user->update([
            ...$request->safe()->only(['name', 'email', 'role']),
            'can_manage_judges' => $request->boolean('can_manage_judges'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return to_route('admin.users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User deleted.')]);

        return to_route('admin.users.index');
    }

    public function suspend(User $user): RedirectResponse
    {
        $user->update(['suspended_at' => now()]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User suspended.')]);

        return to_route('admin.users.index');
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['suspended_at' => null]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User activated.')]);

        return to_route('admin.users.index');
    }
}
