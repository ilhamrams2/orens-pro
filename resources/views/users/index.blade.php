@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Users</h1>
            <p class="text-gray-500">Manage all users and their roles.</p>
        </div>
        <a href="{{ route('users.create') }}" class="bg-orens text-white px-6 py-3 rounded-xl font-bold hover:bg-orens-light transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add User
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4 rounded-2xl font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-2xl font-medium text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-25 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="px-8 py-4">User</th>
                        <th class="px-8 py-4">Role</th>
                        <th class="px-8 py-4">Organisation / Division</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-25 transition-all">
                            <td class="px-8 py-5">
                                <span class="font-bold text-gray-700 block">{{ $u->name }}</span>
                                <span class="text-xs text-gray-400">{{ $u->email }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ $u->role === 'admin' ? 'bg-purple-50 text-purple-600' : ($u->role === 'leader' ? 'bg-blue-50 text-blue-600' : 'bg-gray-50 text-gray-500') }}">
                                    {{ $u->role === 'admin' ? 'Pembina' : ($u->role === 'leader' ? 'Pengurus' : 'Member') }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-medium text-gray-600 block">{{ $u->organisation->name ?? '-' }}</span>
                                <span class="text-xs text-gray-400">{{ $u->division->name ?? 'No Division' }}</span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('users.edit', $u) }}" class="p-2 text-gray-400 hover:text-orens hover:bg-orens/5 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-8 py-12 text-center text-gray-400">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
