<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function autoLogin(Request $request): RedirectResponse
    {
        abort_unless(app()->environment('local') && config('auth.auto_login.enabled'), 404);

        $credentials = [
            'email' => config('auth.auto_login.email'),
            'password' => config('auth.auto_login.password'),
        ];

        if (! $credentials['email'] || ! $credentials['password'] || ! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Auto-login is not configured for a valid user.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}