@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen p-6">
    <div class="glass w-full max-w-[450px] p-12 rounded-[24px] shadow-2xl animate-slide-up">
        <div class="text-center mb-10">
            <div class="w-20 h-20 bg-orens/10 rounded-[20px] flex items-center justify-center mx-auto mb-6">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#FF6B00" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-[#1A1A1A] mb-2">Welcome Back</h1>
            <p class="text-gray-500">Sign in to continue to <span class="text-orens font-semibold">Orens Pro</span></p>
        </div>

        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-semibold mb-2 text-gray-700">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="w-full p-4 rounded-xl border border-gray-200 bg-white/80 outline-none transition-all focus:border-orens focus:ring-4 focus:ring-orens/10"
                    placeholder="name@example.com">
                @error('email')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-8">
                <div class="flex justify-between items-center mb-2">
                    <label class="text-sm font-semibold text-gray-700">Password</label>
                    <a href="#" class="text-orens text-xs font-medium hover:underline">Forgot Password?</a>
                </div>
                <input type="password" name="password" required 
                    class="w-full p-4 rounded-xl border border-gray-200 bg-white/80 outline-none transition-all focus:border-orens focus:ring-4 focus:ring-orens/10"
                    placeholder="••••••••">
            </div>

            <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold transition-all hover:bg-orens-light hover:-translate-y-1 shadow-lg shadow-orens/30 hover:shadow-xl hover:shadow-orens/40">
                Sign In
            </button>

            <div class="text-center mt-8">
                <p class="text-gray-500 text-sm">
                    Don't have an account? <a href="#" class="text-orens font-bold hover:underline">Join Orens Pro</a>
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
