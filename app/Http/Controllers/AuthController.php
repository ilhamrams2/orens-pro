<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $allowedDomains = ['smkprestasiprima.sch.id', 'smaprestasiprima.sch.id'];
        $emailDomain = substr(strrchr($credentials['email'], "@"), 1);

        if (!in_array($emailDomain, $allowedDomains)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login is restricted to Prestasiprima domains only.',
                ], 403);
            }

            return back()->withErrors([
                'email' => 'Login is restricted to Prestasiprima domains only.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login successful',
                    'user' => Auth::user()
                ]);
            }

            return redirect()->intended('/dashboard');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
            ], 401);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
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

        return redirect('/');
    }

    public function me()
    {
        return response()->json(Auth::user());
    }
}
