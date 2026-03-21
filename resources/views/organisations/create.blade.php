@extends('layouts.dashboard')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-8">
        <a href="{{ route('organisations.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back to List
        </a>
        <h1 class="text-3xl font-bold text-gray-800">{{ isset($organisation) ? 'Edit' : 'Add' }} Organisation</h1>
        <p class="text-gray-500">Fill in the details below to {{ isset($organisation) ? 'update' : 'create' }} an organisation.</p>
    </div>

    <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($organisation) ? route('organisations.update', $organisation) : route('organisations.store') }}" method="POST">
            @csrf
            @if(isset($organisation)) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Organisation Name</label>
                    <input type="text" name="name" value="{{ old('name', $organisation->name ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="e.g. SMK Prestasi Prima">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Address</label>
                    <textarea name="address" rows="3"
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="Complete address...">{{ old('address', $organisation->address ?? '') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="Short description...">{{ old('description', $organisation->description ?? '') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($organisation) ? 'Update' : 'Create' }} Organisation
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
