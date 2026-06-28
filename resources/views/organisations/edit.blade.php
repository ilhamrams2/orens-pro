@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <a href="{{ route('organisations.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
        <x-ui.header :title="(isset($organisation) ? 'Edit' : 'Tambah') . ' Organisasi'" subtitle="Isi detail di bawah untuk memproses organisasi." />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($organisation) ? route('organisations.update', $organisation) : route('organisations.store') }}" method="POST">
            @csrf
            @if(isset($organisation)) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Organisasi</label>
                    <input type="text" name="name" value="{{ old('name', $organisation->name ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="Misal: SMK Prestasi Prima">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Alamat</label>
                    <textarea name="address" rows="3"
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="Alamat lengkap...">{{ old('address', $organisation->address ?? '') }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="Deskripsi singkat...">{{ old('description', $organisation->description ?? '') }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($organisation) ? 'Perbarui' : 'Buat' }} Organisasi
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </span>
                    <h3 class="text-base font-bold font-outfit uppercase tracking-wider">Inspirasi Harian</h3>
                    <p class="text-sm font-medium leading-relaxed opacity-95 italic">
                        "Organisasi yang sukses adalah organisasi yang memiliki struktur, tujuan, dan integritas pencatatan yang kokoh."
                    </p>
                    <p class="text-xs font-bold text-white/80 text-right">— Orens Pro Team</p>
                </div>
            </div>

            <!-- Card 2: Quick tips/information -->
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 font-outfit">
                    <span class="w-1.5 h-5 bg-orens rounded-full"></span>
                    Panduan Organisasi
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">1</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Nama Ekskul Resmi</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Gunakan nama ekstrakurikuler resmi sekolah yang terdaftar di kesiswaan.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">2</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Lokasi Basecamp</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Tentukan lokasi sekretariat atau basecamp utama kegiatan latihan ekskul.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">3</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Otomasi Penilaian</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Organisasi yang terdaftar dapat mengaktifkan presensi pintar dan melakukan ekspor rekapitulasi keaktifan siswa secara bulanan.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
