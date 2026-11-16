<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = \App\Models\User::query()->paginate(15);
        return view('pages.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('pages.users.create');
    }

    // TODO: extract to FormRequest if business logic grows
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nama_lengkap' => ['required', 'string'], 'username' => ['required', 'string'], 'password' => ['required', 'string'], 'role' => ['string'], 'remember_token' => ['nullable', 'string']]);
        \App\Models\User::create($validated);
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(\App\Models\User $user): View
    {
        return view('pages.users.edit', ['user' => $user]);
    }

    // TODO: extract to FormRequest if business logic grows
    public function update(Request $request, \App\Models\User $user): RedirectResponse
    {
        $validated = $request->validate(['nama_lengkap' => ['required', 'string'], 'username' => ['required', 'string'], 'password' => ['required', 'string'], 'role' => ['string'], 'remember_token' => ['nullable', 'string']]);
        $user->update($validated);
        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(\App\Models\User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}
