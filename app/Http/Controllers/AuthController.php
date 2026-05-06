<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

        if (\Illuminate\Support\Facades\RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = \Illuminate\Support\Facades\RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in $seconds seconds.",
            ])->onlyInput('email');
        }

        $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
        $emailDomain = substr(strrchr($credentials['email'], "@"), 1);

        if (!in_array($emailDomain, $allowedDomains)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            Log::warning('Login attempt with unauthorized domain: ' . $credentials['email'], ['ip' => $request->ip()]);
            return back()->withErrors([
                'email' => 'Login hanya untuk domain Prestasiprima.',
            ])->onlyInput('email');
        }

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            \Illuminate\Support\Facades\RateLimiter::hit($throttleKey);
            Log::info('Failed login attempt for: ' . $credentials['email'], ['ip' => $request->ip()]);
            return back()->withErrors([
                'email' => 'Email atau password tidak cocok.',
            ])->onlyInput('email');
        }

        if (!$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            \Illuminate\Support\Facades\RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            Log::info('User logged in: ' . $user->email, ['role' => $user->role]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login berhasil',
                    'user' => Auth::user()
                ]);
            }

            $user = Auth::user();
            if ($user->role === 'member') {
                return redirect()->intended('/attendance');
            }

            return redirect()->intended('/dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak cocok.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Logged out']);
        }

        return redirect('/login');
    }

    public function me()
    {
        return response()->json(Auth::user());
    }
}
