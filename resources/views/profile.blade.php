@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto py-8 space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Settings</h1>
            <p class="text-gray-500 font-medium">Manage your personal information and account security.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4 rounded-2xl font-medium text-sm shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Sidebar Navigation (Inner) -->
        <div class="space-y-2">
            <button class="w-full text-left px-6 py-4 rounded-2xl bg-white border border-orens shadow-sm shadow-orens/5 text-orens font-bold flex items-center gap-3 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Account Details
            </button>
            <div class="p-6 rounded-2xl bg-gray-50 border border-gray-100">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Account Status</p>
                <div class="space-y-4">
                    <div>
                        <p class="text-[10px] text-gray-400 font-medium mb-1 tracking-tight italic">Current Role</p>
                        <span class="px-3 py-1 bg-orens/10 text-orens rounded-full text-[10px] font-bold uppercase">{{ $user->role }}</span>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-400 font-medium mb-1 tracking-tight italic">Organisation</p>
                        <p class="text-sm font-bold text-gray-700">{{ $user->organisation->name ?? '-' }}</p>
                    </div>
                    @if($user->division)
                    <div>
                        <p class="text-[10px] text-gray-400 font-medium mb-1 tracking-tight italic">Division</p>
                        <p class="text-sm font-bold text-gray-700">{{ $user->division->name }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <div class="lg:col-span-2 space-y-8">
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Personal Information -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-orens rounded-full"></span>
                        Personal Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all text-sm font-medium"
                                placeholder="Your full name">
                            @error('name') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all text-sm font-medium"
                                placeholder="user@domain.com">
                            @error('email') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Security -->
                <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
                    <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 bg-orens rounded-full"></span>
                        Update Password
                    </h3>
                    <p class="text-xs text-gray-400 font-medium">Leave blank if you don't want to change your password.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700 ml-1">New Password</label>
                            <input type="password" name="password"
                                class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all text-sm font-medium"
                                placeholder="••••••••">
                            @error('password') <p class="text-red-500 text-[10px] font-bold mt-1 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-gray-700 ml-1">Confirm Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all text-sm font-medium"
                                placeholder="••••••••">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-10 py-4 bg-orens text-white rounded-2xl font-bold hover:bg-orens-light transition-all shadow-xl shadow-orens/20 flex items-center gap-2 group">
                        Save Changes
                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
