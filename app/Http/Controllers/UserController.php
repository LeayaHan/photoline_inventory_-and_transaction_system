<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        $users = User::where('role', 'staff')
            ->orderBy('name')
            ->get();

        return view('manager.users.index', compact('users'));
    }

    public function create()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        return view('manager.users.create');
    }

    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Staff account created successfully.');
    }

    public function edit(User $user)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role !== 'staff') {
            abort(403, 'Only staff accounts can be managed here.');
        }

        return view('manager.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role !== 'staff') {
            abort(403, 'Only staff accounts can be managed here.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Staff account updated successfully.');
    }

    public function destroy(User $user)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!auth()->user()->isManager()) {
            abort(403, 'Unauthorized access.');
        }

        if ($user->role !== 'staff') {
            abort(403, 'Only staff accounts can be deleted here.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Staff account deleted successfully.');
    }
}