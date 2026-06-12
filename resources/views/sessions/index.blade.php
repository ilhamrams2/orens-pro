@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <x-ui.header title="Sesi Presensi" subtitle="Buat dan kelola sesi presensi untuk divisi.">
        <x-ui.button variant="secondary" onclick="submitMultiReport()" title="Centang sesi di tabel terlebih dahulu">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-slot>
            Laporan
        </x-ui.button>
        <x-ui.button :href="route('sessions.create')">
            <x-slot name="icon">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </x-slot>
            Sesi Baru
        </x-ui.button>
    </x-ui.header>

    @if(session('success'))
        <div class="bg-green-50 border border-green-100 text-green-600 px-6 py-4 rounded-2xl font-medium text-sm mt-4">
            {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-2xl font-medium text-sm mt-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-[32px] border border-gray-100 shadow-sm overflow-hidden mt-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-25 text-[11px] font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50">
                        <th class="px-4 lg:px-8 py-4 w-10 text-center">
                            <input type="checkbox" onclick="toggleAll(this)" class="rounded text-orens focus:ring-orens border-gray-300">
                        </th>
                        <th class="px-4 lg:px-8 py-4">Judul & Organisasi</th>
                        <th class="px-4 lg:px-8 py-4">Divisi</th>
                        <th class="px-4 lg:px-8 py-4 text-nowrap">Jadwal</th>
                        <th class="px-4 lg:px-8 py-4">Status</th>
                        <th class="px-4 lg:px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sessions as $s)
                        <tr class="hover:bg-gray-25 transition-all">
                            <td class="px-4 lg:px-8 py-5 text-center">
                                <input type="checkbox" value="{{ $s->id }}" class="rounded text-orens focus:ring-orens border-gray-300 session-checkbox">
                            </td>
                            <td class="px-4 lg:px-8 py-5">
                                <span class="font-bold text-gray-700 block text-sm sm:text-base">{{ $s->title }}</span>
                                <span class="text-xs text-gray-400 font-medium">{{ $s->organisation->name ?? 'Global' }}</span>
                            </td>
                            <td class="px-4 lg:px-8 py-5">
                                <span class="px-3 py-1 bg-gray-50 text-gray-500 text-[10px] font-bold uppercase rounded-full border border-gray-100">
                                    {{ $s->division->name ?? 'Global' }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-8 py-5">
                                <span class="text-sm font-bold text-gray-600 block text-nowrap">{{ \Carbon\Carbon::parse($s->session_date)->format('d M Y') }}</span>
                                <span class="text-xs text-gray-400 text-nowrap">{{ $s->start_time }} - {{ $s->end_time }}</span>
                            </td>
                            <td class="px-4 lg:px-8 py-5">
                                @if($s->is_active)
                                    <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-bold uppercase rounded-full border border-green-100">Aktif</span>
                                @else
                                    <span class="px-3 py-1 bg-red-50 text-red-600 text-[10px] font-bold uppercase rounded-full border border-red-100">Selesai</span>
                                @endif
                            </td>
                            <td class="px-4 lg:px-8 py-5 text-right">
                                    <div class="flex items-center gap-1 sm:gap-2 justify-end">
                                        <button type="button" onclick="showQR({{ $s->id }}, '{{ $s->title }}')" class="p-2 text-gray-400 hover:text-green-500 transition-colors" title="Tampilkan Kode QR">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 17h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                                        </button>
                                        <a href="{{ route('sessions.mark', $s) }}" class="p-2 text-gray-400 hover:text-blue-500 transition-colors" title="Tandai Kehadiran">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </a>
                                        @if(auth()->user()->role === 'pembina' || auth()->user()->role === 'pengurus')
                                        <a href="{{ route('sessions.logs', $s) }}" class="p-2 text-gray-400 hover:text-indigo-500 transition-colors" title="Lihat Log Percobaan">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                        </a>
                                        <a href="{{ route('sessions.report', $s) }}" class="p-2 text-gray-400 hover:text-green-600 transition-colors" title="Laporan Sesi" target="_blank">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </a>
                                        @endif
                                        <a href="{{ route('sessions.edit', $s) }}" class="p-2 text-gray-400 hover:text-orens transition-colors" title="Ubah Sesi">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form action="{{ route('sessions.destroy', $s) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors" title="Hapus Sesi">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-gray-400 font-medium italic">Sesi tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Hidden Form for Multi-Report -->
<form id="hiddenReportForm" action="{{ route('reports.multi') }}" method="POST" target="_blank" class="hidden">
    @csrf
    <div id="hiddenInputs"></div>
</form>

<!-- QR Modal -->
<div id="qrModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-[40px] shadow-2xl max-w-sm w-full p-10 text-center relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-12 -mt-12 w-40 h-40 bg-orens/5 rounded-full blur-3xl"></div>
        
        <button onclick="hideQR()" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-2xl font-black text-gray-800 font-outfit mb-2" id="qrTitle">QR Sesi</h3>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-8">Siap Pindai</p>
        
        <div class="bg-gray-50 p-6 rounded-[32px] border border-gray-100 flex items-center justify-center mb-8">
            <img id="qrImage" src="" alt="QR Code" class="w-full max-w-[200px] aspect-square rounded-2xl shadow-sm border-4 border-white">
        </div>

        <p class="text-[10px] text-gray-400 font-medium italic leading-relaxed px-4">
            "Minta anggota mengarahkan kamera mereka ke kode ini dengan tetap berada di radius yang ditentukan."
        </p>
    </div>
</div>

<!-- Alert Modal (Selection Warning) -->
<div id="selectionModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 bg-gray-900/40 backdrop-blur-[2px] animate-fade-in">
    <div class="bg-white rounded-[40px] shadow-2xl max-w-sm w-full p-8 text-center animate-slide-up relative overflow-hidden">
        <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-50 rounded-full blur-3xl opacity-50"></div>
        
        <div class="w-20 h-20 bg-red-50 rounded-3xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h3 class="text-2xl font-black text-gray-800 font-outfit mb-2">Pilih Sesi!</h3>
        <p class="text-sm text-gray-500 font-medium mb-8 leading-relaxed">
            Silakan pilih minimal satu sesi dari tabel sebelum membuat laporan kumulatif.
        </p>
        
        <button onclick="hideSelectionModal()" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-gray-800 transition-all shadow-lg shadow-gray-200 active:scale-95">
            Mengerti
        </button>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

<script>
    function toggleAll(source) {
        const checkboxes = document.getElementsByClassName('session-checkbox');
        for(let i = 0; i < checkboxes.length; i++) {
            checkboxes[i].checked = source.checked;
        }
    }

    function submitMultiReport() {
        const checkboxes = document.querySelectorAll('.session-checkbox:checked');
        if (checkboxes.length === 0) {
            showSelectionModal();
            return;
        }

        const form = document.getElementById('hiddenReportForm');
        const container = document.getElementById('hiddenInputs');
        container.innerHTML = ''; // Clear previous inputs

        checkboxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'session_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });

        form.submit();
    }

    let html5QrCode;
    let currentSessionId;
    let userCoords = null;
    let qrRefreshInterval;

    function showQR(sessionId, title) {
        const modal = document.getElementById('qrModal');
        const img = document.getElementById('qrImage');
        const titleEl = document.getElementById('qrTitle');

        titleEl.textContent = title;
        currentSessionId = sessionId;
        
        // Initial load
        refreshQR();
        
        // Set interval for rotation (Singapore Standard: every 30s)
        clearInterval(qrRefreshInterval);
        qrRefreshInterval = setInterval(refreshQR, 10000); // Check every 10s for smooth transition
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function refreshQR() {
        if (!currentSessionId) return;
        const img = document.getElementById('qrImage');
        // Add cache-buster to force refresh
        img.src = `/sessions/${currentSessionId}/qr?t=` + new Date().getTime();
    }

    function hideQR() {
        const modal = document.getElementById('qrModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        clearInterval(qrRefreshInterval);
        currentSessionId = null;
    }

    function showSelectionModal() {
        const modal = document.getElementById('selectionModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideSelectionModal() {
        const modal = document.getElementById('selectionModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection
