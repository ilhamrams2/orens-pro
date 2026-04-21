@extends('layouts.dashboard')

@section('content')
<div class="max-w-5xl mx-auto py-8 space-y-10 animate-fade-in">
    <!-- Premium Header & Avatar Section -->
    <div class="relative bg-white rounded-[40px] border border-gray-100 shadow-xl shadow-gray-200/50 overflow-hidden p-8 md:p-12">
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-orens/5 rounded-full blur-3xl opacity-50"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl opacity-30"></div>

        <div class="relative flex flex-col md:flex-row items-center gap-8">
            <!-- Avatar Initials -->
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-[32px] bg-gradient-to-br from-orens to-orange-600 flex items-center justify-center text-white text-5xl md:text-6xl font-black font-outfit shadow-2xl shadow-orens/30 border-4 border-white">
                {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strrchr($user->name, ' ') ?: ' ', 1, 1)) }}
            </div>
            
            <div class="text-center md:text-left space-y-3">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-3">
                    <h1 class="text-4xl md:text-5xl font-black text-gray-800 tracking-tight font-outfit">{{ $user->name }}</h1>
                    <span class="px-4 py-1.5 bg-orens/10 text-orens rounded-full text-xs font-black uppercase tracking-widest border border-orens/20">{{ $user->role }}</span>
                </div>
                <p class="text-lg text-gray-500 font-medium max-w-xl">{{ $user->email }}</p>
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 pt-2">
                    <span class="flex items-center gap-2 text-sm font-bold text-gray-400">
                        <svg class="w-4 h-4 text-orens" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        {{ $user->organisation?->name ?? 'Global System' }}
                    </span>
                    @if($user->division)
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span class="flex items-center gap-2 text-sm font-bold text-gray-400">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        {{ $user->division->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Account Summary & Security Hints -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-gray-900 rounded-[32px] p-8 text-white shadow-2xl relative overflow-hidden group">
                <div class="absolute -right-5 -bottom-5 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-700"></div>
                <h3 class="text-sm font-black uppercase tracking-[0.2em] text-orens-light mb-6">Account Status</h3>
                <div class="space-y-6">
                    <div class="flex items-center justify-between group/item">
                        <span class="text-xs font-bold text-gray-400 uppercase">Verification</span>
                        <span class="px-3 py-1 bg-green-500/20 text-green-400 rounded-lg text-[10px] font-black uppercase tracking-widest border border-green-500/30">Verified</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-400 uppercase">Join Date</span>
                        <span class="text-xs font-black">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                </div>
                <div class="mt-8 pt-8 border-t border-white/10 space-y-4">
                    <p class="text-[10px] text-gray-400 leading-relaxed font-medium italic opacity-60">"Your account is managed under {{ $user->organisation?->name ?? 'Global System' }} policy."</p>
                </div>
            </div>

            <div class="bg-blue-600 rounded-[32px] p-8 text-white shadow-xl shadow-blue-600/20 relative overflow-hidden">
                <div class="absolute -right-8 -top-8 w-20 h-20 bg-white/20 rounded-full blur-xl animate-pulse"></div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <h4 class="font-black text-sm uppercase tracking-widest">Security Tip</h4>
                </div>
                <p class="text-xs font-medium text-blue-50 leading-relaxed">
                    Use a strong password with symbols and numbers to ensure your attendance data remains secure.
                </p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="lg:col-span-8">
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Profile Information Card -->
                <div class="bg-white p-8 md:p-10 rounded-[40px] border border-gray-100 shadow-sm space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-orens rounded-full"></div>
                        <h3 class="text-2xl font-black text-gray-800 font-outfit">Identity Details</h3>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Full Display Name</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-orens transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                </span>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                    class="w-full pl-12 pr-4 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all text-sm font-bold text-gray-700"
                                    placeholder="Enter your name">
                            </div>
                            @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Official Email Address Address</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-orens transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                    class="w-full pl-12 pr-4 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all text-sm font-bold text-gray-700"
                                    placeholder="user@prestasiprima.sch.id">
                            </div>
                            @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Password Card -->
                <div class="bg-white p-8 md:p-10 rounded-[40px] border border-gray-100 shadow-sm space-y-8">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-8 bg-blue-500 rounded-full"></div>
                        <h3 class="text-2xl font-black text-gray-800 font-outfit">Security Update</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">New Password</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </span>
                                <input type="password" name="password"
                                    class="w-full pl-12 pr-4 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-bold text-gray-700"
                                    placeholder="••••••••">
                            </div>
                            @error('password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Confirm New Password</label>
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400 group-focus-within:text-blue-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </span>
                                <input type="password" name="password_confirmation"
                                    class="w-full pl-12 pr-4 py-4 rounded-2xl border border-gray-100 bg-gray-50/50 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all text-sm font-bold text-gray-700"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-12 py-5 bg-gradient-to-r from-orens to-orange-600 text-white rounded-[24px] font-black uppercase tracking-widest text-xs hover:scale-105 transition-all shadow-2xl shadow-orens/30 flex items-center gap-4 group active:scale-95">
                        Update My Account
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>
@endsection
