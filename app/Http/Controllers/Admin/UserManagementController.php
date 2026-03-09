<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::query()->orderByDesc('created_at')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'sales'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create($validated);

        return redirect()->route('admin.users.index')->with('status', 'User created successfully.');
    }

    public function updateRole(Request $request, int $user): RedirectResponse
    {
        $targetUser = User::query()->findOrFail($user);

        $validated = $request->validate([
            'role' => ['required', Rule::in(['admin', 'sales'])],
        ]);

        $targetUser->update([
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User role updated.');
    }
}
