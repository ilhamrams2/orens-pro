@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Members</h1>
            <p class="text-gray-500">Manage all members and their performance grades.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('users.export.excel') }}" class="bg-green-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-green-700 transition-all flex items-center gap-2 shadow-lg shadow-green-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Excel
            </a>
            <a href="{{ route('users.export.pdf') }}" class="bg-red-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-red-700 transition-all flex items-center gap-2 shadow-lg shadow-red-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                PDF
            </a>
            <a href="{{ route('users.create') }}" class="bg-orens text-white px-6 py-3 rounded-xl font-bold hover:bg-orens-light transition-all flex items-center gap-2 shadow-lg shadow-orens/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Member
            </a>
        </div>
    </div>

    @if(($organisation ?? null) && (auth()->user()->role === 'admin' || auth()->user()->role === 'superadmin'))
        <div class="bg-blue-600 rounded-[32px] p-8 text-white flex flex-col md:flex-row justify-between items-center gap-6 shadow-xl shadow-blue-100 mb-8 mt-6">
            <div class="flex items-center gap-6 text-center md:text-left">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/30">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xl font-black font-outfit">Periode Penilaian Aktif</h3>
                    <p class="text-sm opacity-80 font-medium">Nilai dihitung berdasarkan kehadiran sejak:</p>
                    <p class="text-lg font-bold mt-1 small-caps">
                        {{ $organisation->last_grade_reset_at ? \Carbon\Carbon::parse($organisation->last_grade_reset_at)->format('d M Y (H:i)') : 'Awal Sistem' }}
                    </p>
                </div>
            </div>
            <form action="{{ route('users.reset-grades') }}" method="POST" onsubmit="return confirm('PERINGATAN: Semua nilai member akan dihitung ulang dari nol sejak saat ini. Anda yakin?')">
                @csrf
                <button type="submit" class="px-6 py-3 bg-white text-blue-600 rounded-2xl font-bold text-sm hover:scale-105 transition-all shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Reset Periode Nilai
                </button>
            </form>
        </div>
    @endif
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
                        @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                        <th class="px-8 py-4 text-center">Grade (Hadir)</th>
                        @endif
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
                            @if(in_array(auth()->user()->role, ['admin', 'superadmin']))
                            <td class="px-8 py-5">
                                @if($u->role === 'member')
                                <div class="flex flex-col items-center gap-1">
                                    <span class="px-4 py-1 rounded-full text-xs font-black {{ $u->grade_class }} border shadow-sm">
                                        {{ $u->grade }}
                                    </span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $u->attendances_count }} Hadir</span>
                                </div>
                                @else
                                <div class="text-center text-gray-300 text-xs">-</div>
                                @endif
                            </td>
                            @endif
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
                            <td colspan="{{ in_array(auth()->user()->role, ['admin', 'superadmin']) ? 5 : 4 }}" class="px-8 py-12 text-center text-gray-400">No members found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
