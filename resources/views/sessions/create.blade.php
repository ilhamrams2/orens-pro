@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-8">
        <a href="{{ route('sessions.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ isset($session) ? 'Edit' : 'Create' }} Session</h1>
        <p class="text-gray-500">Set the schedule and division for this attendance session.</p>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($session) ? route('sessions.update', $session) : route('sessions.store') }}" method="POST">
            @csrf
            @if(isset($session)) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Session Title</label>
                    <input type="text" name="title" value="{{ old('title', $session->title ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="e.g. Weekly Meeting - Week 1">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organisation</label>
                        <select name="organisation_id" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                            @foreach($organisations as $org)
                                <option value="{{ $org->id }}" {{ old('organisation_id', $session->organisation_id ?? '') == $org->id ? 'selected' : '' }}>
                                    {{ $org->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('organisation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Division (Optional)</label>
                        <select name="division_id"
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                            <option value="">Global (All Divisions)</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" {{ old('division_id', $session->division_id ?? '') == $div->id ? 'selected' : '' }}>
                                    {{ $div->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Session Date</label>
                    <input type="date" name="session_date" value="{{ old('session_date', $session->session_date ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                    @error('session_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Start Time</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $session->start_time ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">End Time</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $session->end_time ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                @if(isset($session))
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select name="is_active" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        <option value="1" {{ old('is_active', $session->is_active) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active', $session->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($session) ? 'Update' : 'Create' }} Session
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
