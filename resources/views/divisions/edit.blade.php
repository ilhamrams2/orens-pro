@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <a href="{{ route('divisions.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
        <x-ui.header :title="(isset($division) ? 'Ubah' : 'Tambah') . ' Divisi'" subtitle="Tentukan divisi ini ke organisasi." />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($division) ? route('divisions.update', $division) : route('divisions.store') }}" method="POST">
            @csrf
            @if(isset($division)) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Organisasi</label>
                    <select name="organisation_id" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        <option value="">Pilih Organisasi</option>
                        @foreach($organisations as $org)
                            <option value="{{ $org->id }}" {{ old('organisation_id', $division->organisation_id ?? '') == $org->id ? 'selected' : '' }}>
                                {{ $org->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('organisation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Divisi</label>
                    <input type="text" name="name" value="{{ old('name', $division->name ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="misal: Pengembangan Game">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($division) ? 'Perbarui' : 'Tambah' }} Divisi
                    </button>
                </div>
            </div>
        </form>
            </div>
        </div>

        <!-- Info / Quotes Column -->
        <div class="space-y-6">
            <!-- Card 1: Motivational Quotes -->
            <div class="bg-orens text-white p-8 rounded-[32px] shadow-xl shadow-orens/10 relative overflow-hidden group">
                <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-3xl group-hover:bg-white/20 transition-all duration-700"></div>
                <div class="absolute -left-10 -top-10 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-700"></div>
                
                <div class="relative z-10 space-y-4">
                    <span class="inline-flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </span>
                    <h3 class="text-base font-bold font-outfit uppercase tracking-wider">Inspirasi Harian</h3>
                    <p class="text-sm font-medium leading-relaxed opacity-95 italic">
                        "Kolaborasi dalam divisi-divisi kecil membantu penyaluran bakat siswa secara lebih terfokus dan optimal."
                    </p>
                    <p class="text-xs font-bold text-white/80 text-right">— Orens Pro Team</p>
                </div>
            </div>

            <!-- Card 2: Quick tips/information -->
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 font-outfit">
                    <span class="w-1.5 h-5 bg-orens rounded-full"></span>
                    Panduan Divisi
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">1</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Subdivisi Pembelajaran</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Buat divisi khusus untuk memisahkan fokus bidang pembelajaran (contoh: divisi Web Dev dan Game Dev di ekskul IT).</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">2</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Penugasan Pengurus</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Pengurus divisi yang ditunjuk akan mendapatkan hak penuh untuk mengelola sesi absensi divisi bersangkutan.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">3</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Presensi Terfokus</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Sesi absensi yang diselenggarakan khusus tingkat divisi hanya akan diwajibkan bagi anggota dari divisi tersebut.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
