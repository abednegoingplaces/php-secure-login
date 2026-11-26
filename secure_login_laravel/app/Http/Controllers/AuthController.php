<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_SECONDS = 60;

    /** Question 19/20(a): show the HTML login form. */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /** Validate input, verify the password and start an authenticated session. */
    public function login(Request $request): RedirectResponse
    {
        // --- Server-side input validation ---
        $credentials = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_]+$/'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        // --- Brute-force protection: 5 failed attempts lock the form for 60 seconds ---
        $throttleKey = Str::lower($credentials['username']) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'username' => "Too many failed attempts. Try again in {$seconds} seconds.",
            ]);
        }

        // --- Password verification ---
        // Auth::attempt() finds the user by username and checks the password
        // against the stored bcrypt hash (password_verify under the hood).
        if (! Auth::attempt($credentials)) {
            RateLimiter::hit($throttleKey, self::LOCKOUT_SECONDS);

            // One generic message, so an attacker cannot tell which usernames exist.
            throw ValidationException::withMessages([
                'username' => 'Invalid username or password.',
            ]);
        }

        RateLimiter::clear($throttleKey);

        // --- Session authentication ---
        // A new session ID after login stops session fixation.
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login' => now()])->save();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    /** Create an account. The "hashed" cast on the User model hashes the password. */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[A-Za-z0-9_]+$/', 'unique:users,username'],
            'email'    => ['required', 'string', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'max:255', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        // "role" is not mass-assignable, so new accounts are always ordinary users.
        User::create($data);

        return redirect()->route('login')->with('status', 'Account created. You can log in now.');
    }

    /** Question 20(c): log out and destroy the session. */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();                          // forget the logged-in user
        $request->session()->invalidate();       // destroy the session data and issue a new ID
        $request->session()->regenerateToken();  // new CSRF token

        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
