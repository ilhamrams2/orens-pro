@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen p-6">
    <div class="glass-card w-full max-w-[480px] p-10 md:p-14 animate-slide-up">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-orens/10 rounded-2xl flex items-center justify-center mx-auto mb-6 transition-transform hover:scale-110 duration-500">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="text-orens" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold tracking-tight text-text-primary mb-3">Welcome</h1>
            <p class="text-text-secondary leading-relaxed">Sign in to manage your attendance with <span class="text-orens font-bold">Orens Pro</span></p>
        </div>

        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-bold mb-2.5 text-text-primary ml-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="input-premium"
                    placeholder="name@prestasiprima.sch.id">
                @error('email')
                    <p class="text-red-500 text-xs mt-2.5 font-medium flex items-center gap-1.5 ml-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <div class="flex justify-between items-center mb-2.5 ml-1">
                    <label class="text-sm font-bold text-text-primary">Password</label>
                    <a href="#" class="text-orens text-xs font-bold hover:text-orens-light transition-colors">Forgot Password?</a>
                </div>
                <input type="password" name="password" required 
                    class="input-premium"
                    placeholder="••••••••">
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-premium w-full bg-orens text-white shadow-premium hover:bg-orens-light hover:-translate-y-0.5">
                    Sign In to Portal
                </button>
            </div>

            <div class="text-center mt-10">
                <p class="text-text-secondary text-sm">
                    Don't have an account? <a href="#" class="text-orens font-black hover:underline underline-offset-4">Join Orens Pro</a>
                </p>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes slide-up {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-slide-up {
        animation: slide-up 0.8s ease-out forwards;
    }
</style>
@endsection
