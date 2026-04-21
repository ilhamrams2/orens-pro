@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Attendance Sessions</h1>
            <p class="text-gray-500">Create and manage attendance sessions for divisions.</p>
        </div>
        <a href="{{ route('sessions.create') }}" class="bg-orens text-white px-6 py-3 rounded-xl font-bold hover:bg-orens-light transition-all flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Session
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4 rounded-2xl font-medium text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-25 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="px-8 py-4">Title & Organisation</th>
                        <th class="px-8 py-4">Division</th>
                        <th class="px-8 py-4">Schedule</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-gray-25 transition-all">
                            <td class="px-8 py-5">
                                <span class="font-bold text-gray-700 block">{{ $s->title }}</span>
                                <span class="text-xs text-gray-400 font-medium">{{ $s->organisation->name ?? 'Global' }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-gray-50 text-gray-500 text-[10px] font-bold uppercase rounded-full">
                                    {{ $s->division->name ?? 'Global' }}
                                </span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-sm font-bold text-gray-600 block">{{ \Carbon\Carbon::parse($s->session_date)->format('d M Y') }}</span>
                                <span class="text-xs text-gray-400">{{ $s->start_time }} - {{ $s->end_time }}</span>
                            </td>
                            <td class="px-8 py-5">
                                @if($s->is_active)
                                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase rounded-full">Active</span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-bold uppercase rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-8 py-5 text-right">
                                    <div class="flex items-center gap-2">
                                        <button onclick="showQR('{{ $s->qr_token }}', '{{ $s->title }}')" class="p-2 text-gray-400 hover:text-green-500 transition-colors" title="Show QR Code">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                        <a href="{{ route('sessions.mark', $s) }}" class="p-2 text-gray-400 hover:text-blue-500 transition-colors" title="Mark Attendance">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </a>
                                        @if(auth()->user()->role === 'admin' || auth()->user()->role === 'leader')
                                        <a href="{{ route('sessions.logs', $s) }}" class="p-2 text-gray-400 hover:text-indigo-500 transition-colors" title="View Attempt Logs">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </a>
                                        @endif
                                        @if(auth()->user()->role === 'admin')
                                        <a href="{{ route('sessions.report', $s) }}" target="_blank" class="p-2 text-gray-400 hover:text-orange-500 transition-colors" title="Export Report">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        @endif
                                        <a href="{{ route('sessions.edit', $s) }}" class="p-2 text-gray-400 hover:text-orens transition-colors" title="Edit Session">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('sessions.destroy', $s) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Delete Session">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-12 text-center text-gray-400">No sessions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
    <!-- QR Modal -->
    <div id="qrModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
        <div class="bg-white rounded-[40px] shadow-2xl max-w-sm w-full p-10 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-12 -mt-12 w-40 h-40 bg-orens/5 rounded-full blur-3xl"></div>
            
            <button onclick="hideQR()" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <h3 class="text-2xl font-black text-gray-800 font-outfit mb-2" id="qrTitle">Session QR</h3>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Ready to Scan</p>
            
            <div class="bg-gray-50 p-6 rounded-[32px] border border-gray-100 flex items-center justify-center mb-8">
                <img id="qrImage" src="" alt="QR Code" class="w-full max-w-[200px] aspect-square rounded-2xl shadow-sm">
            </div>

            <p class="text-[10px] text-gray-400 font-medium italic leading-relaxed">
                "Ask members to point their camera at this code while staying within the verified radius."
            </p>
        </div>
    </div>

    <script>
        function showQR(token, title) {
            const modal = document.getElementById('qrModal');
            const img = document.getElementById('qrImage');
            const titleEl = document.getElementById('qrTitle');

            titleEl.textContent = title;
            img.src = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${token}`;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function hideQR() {
            const modal = document.getElementById('qrModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</div>
@endsection
