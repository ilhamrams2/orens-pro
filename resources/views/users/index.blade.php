@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <x-ui.header title="Anggota" subtitle="Kelola semua anggota dan nilai performa mereka.">
        <x-ui.button variant="outline" size="sm" :href="route('users.export.excel')">
            <x-slot name="icon">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-slot>
            Excel
        </x-ui.button>
        <x-ui.button variant="outline" size="sm" :href="route('users.export.pdf')" class="!text-red-500 !border-red-100 hover:!bg-red-50">
            <x-slot name="icon">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </x-slot>
            PDF
        </x-ui.button>
        @if(in_array(auth()->user()->role, ['pembina', 'superadmin']))
        <x-ui.button variant="outline" size="sm" onclick="showImportModal()" class="!text-green-600 !border-green-100 hover:!bg-green-50">
            <x-slot name="icon">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
            </x-slot>
            Import CSV
        </x-ui.button>
        <x-ui.button :href="route('users.create')">
            <x-slot name="icon">
                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </x-slot>
            Tambah Anggota
        </x-ui.button>
        @endif
    </x-ui.header>

    @if(($organisation ?? null) && (auth()->user()->role === 'pembina' || auth()->user()->role === 'superadmin'))
        <div class="bg-blue-600 rounded-[32px] p-6 sm:p-8 text-white flex flex-col lg:flex-row justify-between items-center gap-6 shadow-xl shadow-blue-100 mb-8 mt-6">
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-6 text-center sm:text-left">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-md border border-white/30 shrink-0">
                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-lg sm:text-xl font-black font-outfit">Periode Penilaian Aktif</h3>
                    <p class="text-xs sm:text-sm opacity-80 font-medium">Nilai dihitung berdasarkan kehadiran sejak:</p>
                    <p class="text-base sm:text-lg font-bold mt-1">
                        {{ $organisation->last_grade_reset_at ? \Carbon\Carbon::parse($organisation->last_grade_reset_at)->format('d M Y (H:i)') : 'Awal Sistem' }}
                    </p>
                </div>
            </div>
            <form action="{{ route('users.reset-grades') }}" method="POST" onsubmit="return confirm('PERINGATAN: Semua nilai member akan dihitung ulang dari nol sejak saat ini. Anda yakin?')" class="w-full lg:w-auto">
                @csrf
                <x-ui.button variant="outline" class="!bg-white !text-blue-600 w-full lg:w-auto">
                    <x-slot name="icon">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </x-slot>
                    Reset Periode Nilai
                </x-ui.button>
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
                        <th class="px-4 lg:px-8 py-4">Pengguna</th>
                        <th class="px-4 lg:px-8 py-4">Peran</th>
                        <th class="px-4 lg:px-8 py-4">Organisasi / Divisi</th>
                        @if(in_array(auth()->user()->role, ['pembina', 'superadmin']))
                        <th class="px-4 lg:px-8 py-4 text-center">Nilai (Hadir)</th>
                        @endif
                        <th class="px-4 lg:px-8 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $u)
                        <tr class="hover:bg-gray-25 transition-all">
                            <td class="px-4 lg:px-8 py-5">
                                <span class="font-bold text-gray-700 block text-sm">{{ $u->name }}</span>
                                <span class="text-xs text-gray-400 truncate block max-w-[150px]">{{ $u->email }}</span>
                            </td>
                            <td class="px-4 lg:px-8 py-5">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold uppercase {{ $u->role === 'superadmin' ? 'bg-red-50 text-red-600' : ($u->role === 'pembina' ? 'bg-purple-50 text-purple-600' : ($u->role === 'pengurus' ? 'bg-blue-50 text-blue-600' : 'bg-gray-50 text-gray-500')) }} border border-current/10">
                                    {{ $u->role === 'superadmin' ? 'Super Admin' : ($u->role === 'pembina' ? 'Pembina' : ($u->role === 'pengurus' ? 'Pengurus' : 'Anggota')) }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-8 py-5">
                                <span class="text-sm font-medium text-gray-600 block">{{ $u->organisation->name ?? '-' }}</span>
                                <span class="text-xs text-gray-400">{{ $u->division->name ?? 'Tanpa Divisi' }}</span>
                            </td>
                            @if(in_array(auth()->user()->role, ['pembina', 'superadmin']))
                            <td class="px-4 lg:px-8 py-5">
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
                            <td class="px-4 lg:px-8 py-5 text-right">
                                <div class="flex justify-end gap-1 sm:gap-2">
                                    <a href="{{ route('users.edit', $u) }}" class="p-2 text-gray-400 hover:text-orens hover:bg-orens/5 rounded-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus anggota ini? Aktifitas dan riwayat absensi mereka juga akan dihapus.')">
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
                            <td colspan="{{ in_array(auth()->user()->role, ['pembina', 'superadmin']) ? 5 : 4 }}" class="px-8 py-12 text-center text-gray-400">Anggota tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>

@if(in_array(auth()->user()->role, ['pembina', 'superadmin']))
<!-- Import CSV Modal -->
<div id="importModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm animate-fade-in">
    <div class="bg-white rounded-[40px] shadow-2xl max-w-lg w-full p-8 md:p-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 -mr-12 -mt-12 w-40 h-40 bg-orens/5 rounded-full blur-3xl"></div>
        
        <button onclick="hideImportModal()" class="absolute top-6 right-6 p-2 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <h3 class="text-2xl font-black text-gray-800 font-outfit mb-2">Impor Anggota</h3>
        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-6">Unggah Berkas CSV</p>
        
        <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="border-2 border-dashed border-gray-200 rounded-3xl p-6 text-center hover:border-orens/40 transition-colors relative group">
                <input type="file" name="csv_file" accept=".csv,.txt" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                <div class="space-y-2">
                    <div class="w-12 h-12 bg-orens/5 text-orens rounded-2xl flex items-center justify-center mx-auto group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    </div>
                    <p class="text-sm font-bold text-gray-700" id="file-label-text">Pilih berkas CSV atau seret ke sini</p>
                    <p class="text-xs text-gray-400 font-medium">Hanya mendukung berkas .csv (Maks. 2MB)</p>
                </div>
            </div>

            <!-- CSV Formatting Help Box -->
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 text-left">
                <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-orens shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Format Kolom CSV
                </h4>
                <p class="text-[11px] text-gray-500 font-medium leading-relaxed mb-3">
                    Baris pertama berkas CSV Anda harus berupa tajuk (header) dengan penamaan kolom persis seperti berikut (pisahkan dengan koma atau titik koma):
                </p>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-[10px] border-collapse">
                        <thead>
                            <tr class="text-gray-400 font-bold uppercase tracking-tight border-b border-gray-200">
                                <th class="pb-1 pr-2">Kolom</th>
                                <th class="pb-1 pr-2">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-600 font-medium">
                            <tr>
                                <td class="py-1 pr-2 font-mono text-orens">nama</td>
                                <td class="py-1">Nama lengkap member (Wajib)</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-2 font-mono text-orens">email</td>
                                <td class="py-1">Alamat email resmi sekolah (Wajib)</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-2 font-mono text-orens">password</td>
                                <td class="py-1">Password awal member, min. 8 karakter (Wajib)</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-2 font-mono text-gray-500">telepon</td>
                                <td class="py-1">Nomor telepon member (Opsional)</td>
                            </tr>
                            <tr>
                                <td class="py-1 pr-2 font-mono text-gray-500">divisi</td>
                                <td class="py-1">Nama divisi persis seperti di database (Opsional)</td>
                            </tr>
                            @if(auth()->user()->role === 'superadmin')
                            <tr>
                                <td class="py-1 pr-2 font-mono text-orens">organisasi</td>
                                <td class="py-1">Nama organisasi persis seperti di database (Wajib untuk Superadmin)</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="hideImportModal()" class="w-1/3 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition-all active:scale-95 text-sm">
                    Batal
                </button>
                <button type="submit" class="w-2/3 py-4 bg-orens text-white rounded-2xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20 active:scale-95 text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    Mulai Impor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function showImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function hideImportModal() {
        const modal = document.getElementById('importModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function updateFileName(input) {
        const textEl = document.getElementById('file-label-text');
        if (input.files && input.files.length > 0) {
            textEl.textContent = `Terpilih: ${input.files[0].name}`;
            textEl.className = "text-sm font-bold text-green-600";
        } else {
            textEl.textContent = "Pilih berkas CSV atau seret ke sini";
            textEl.className = "text-sm font-bold text-gray-700";
        }
    }
</script>

<style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    .animate-fade-in { animation: fadeIn 0.3s ease-out forwards; }
</style>
@endif
@endsection
