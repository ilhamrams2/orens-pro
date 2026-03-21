@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-8">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ isset($user) ? 'Edit' : 'Add' }} User</h1>
        <p class="text-gray-500">Configure user credentials and permissions.</p>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                            placeholder="Full Name">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Email Domain</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                            placeholder="user@smkprestasiprima.sch.id">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Password {{ isset($user) ? '(Leave blank to keep current)' : '' }}</label>
                    <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    @if(auth()->user()->role === 'admin')
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Role</label>
                        <select name="role" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                            <option value="member" {{ old('role', $user->role ?? '') === 'member' ? 'selected' : '' }}>Member</option>
                            <option value="leader" {{ old('role', $user->role ?? '') === 'leader' ? 'selected' : '' }}>Pengurus</option>
                            <option value="admin" {{ old('role', $user->role ?? '') === 'admin' ? 'selected' : '' }}>Pembina</option>
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organisation</label>
                        <select name="organisation_id" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                            @foreach($organisations as $org)
                                <option value="{{ $org->id }}" {{ old('organisation_id', $user->organisation_id ?? '') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organisation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @else
                    <div class="col-span-2 bg-gray-50 p-4 rounded-2xl flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Division</p>
                            <p class="text-sm font-bold text-gray-700">{{ $user->division->name ?? 'No Division' }}</p>
                        </div>
                        <div class="px-3 py-1 bg-orens/10 text-orens rounded-full text-[10px] font-bold uppercase">
                            {{ ucfirst($user->role) }}
                        </div>
                    </div>
                    @endif
                </div>

                @if(auth()->user()->role === 'admin')
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Division (Optional)</label>
                    <select name="division_id"
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        <option value="">No Division</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $user->division_id ?? '') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }} ({{ $div->organisation->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($user) ? 'Update' : 'Create' }} User Account
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
