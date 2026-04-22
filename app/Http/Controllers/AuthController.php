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
            'password' => 'required|min:1',
        ]);

        $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
        $emailDomain = substr(strrchr($credentials['email'], "@"), 1);

        if (!in_array($emailDomain, $allowedDomains)) {
            return back()->withErrors([
                'email' => 'Login hanya untuk domain Prestasiprima.',
            ])->onlyInput('email');
        }

        // Cek user aktif terlebih dahulu
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
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
            $request->session()->regenerate();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login berhasil',
                    'user' => Auth::user()
                ]);
            }

            // Redirect berdasarkan role
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
