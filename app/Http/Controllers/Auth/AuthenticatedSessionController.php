<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /** Display the login view. */
    public function create(): View
    {
        return view('auth.login');
    }

    /** Authenticate and send the account to its own dashboard. */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('staff')->check()) {
            return redirect()->route('staff.dashboard');
        }

        $manager = Auth::guard('web')->user();

        if ($manager && strtolower((string) $manager->email) === 'photolinemanager@gmail.com') {
            return redirect()->route('branch-manager.dashboard');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'This account is not authorized to access the Photoline dashboard.',
        ]);
    }

    /** Log out both guards to avoid a mixed manager/staff session. */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('staff')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
