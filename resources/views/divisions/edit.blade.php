@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-8">
        <a href="{{ route('divisions.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ isset($division) ? 'Edit' : 'Add' }} Division</h1>
        <p class="text-gray-500">Assign this division to an organisation.</p>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($division) ? route('divisions.update', $division) : route('divisions.store') }}" method="POST">
            @csrf
            @if(isset($division)) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Organisation</label>
                    <select name="organisation_id" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        <option value="">Select Organisation</option>
                        @foreach($organisations as $org)
                            <option value="{{ $org->id }}" {{ old('organisation_id', $division->organisation_id ?? '') == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organisation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Division Name</label>
                    <input type="text" name="name" value="{{ old('name', $division->name ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="e.g. Game Development">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($division) ? 'Update' : 'Create' }} Division
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
