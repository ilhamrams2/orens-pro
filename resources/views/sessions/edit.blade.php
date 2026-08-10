@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <a href="{{ route('sessions.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
        <x-ui.header :title="(isset($session) ? 'Ubah' : 'Buat') . ' Sesi'" subtitle="Tentukan jadwal dan divisi untuk sesi presensi ini." />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($session) ? route('sessions.update', $session) : route('sessions.store') }}" method="POST">
            @csrf
            @if(isset($session)) @method('PUT') @endif

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Judul Sesi</label>
                    <input type="text" name="title" value="{{ old('title', $session->title ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="misal: Rapat Mingguan - Pekan 1">
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organisasi</label>
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
                        <label class="block text-sm font-bold text-gray-700 mb-2">Divisi (Opsional)</label>
                        <select name="division_id"
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                            <option value="">Global (Semua Divisi)</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}" data-organisation-id="{{ $div->organisation_id }}" {{ old('division_id', $session->division_id ?? '') == $div->id ? 'selected' : '' }}>
                                    {{ $div->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('division_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tanggal Sesi</label>
                    <input type="date" name="session_date" value="{{ old('session_date', $session->session_date ?? '') }}" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                    @error('session_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Waktu Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $session->start_time ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Waktu Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $session->end_time ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-extrabold text-gray-700 uppercase tracking-wider">Geofencing (GPS)</label>
                        <button type="button" onclick="getLocation()" class="text-xs font-bold text-orens hover:underline flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            Dapatkan Lokasi Saat Ini
                        </button>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $session->latitude ?? '') }}"
                                class="w-full p-3 rounded-xl border border-gray-100 bg-white outline-none focus:border-orens transition-all text-xs font-bold"
                                placeholder="Latitude">
                        </div>
                        <div class="space-y-1">
                            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $session->longitude ?? '') }}"
                                class="w-full p-3 rounded-xl border border-gray-100 bg-white outline-none focus:border-orens transition-all text-xs font-bold"
                                placeholder="Longitude">
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-1">Radius (Meter)</label>
                        <input type="number" name="radius" value="{{ old('radius', $session->radius ?? 100) }}"
                            class="w-full p-3 rounded-xl border border-gray-100 bg-white outline-none focus:border-orens transition-all text-xs font-bold"
                            placeholder="misal: 100">
                    </div>
                    <p class="text-[10px] text-gray-400 font-medium italic">Anggota harus berada di dalam radius ini untuk melakukan presensi.</p>
                </div>

                <script>
                    function getLocation() {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                document.getElementById('latitude').value = position.coords.latitude;
                                document.getElementById('longitude').value = position.coords.longitude;
                            }, function(error) {
                                alert("Gagal mendapatkan lokasi: " + error.message);
                            });
                        } else {
                            alert("Geolokasi tidak didukung oleh peramban ini.");
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        const orgSelect = document.querySelector('select[name="organisation_id"]');
                        const divSelect = document.querySelector('select[name="division_id"]');

                        if (orgSelect && divSelect) {
                            const allDivOptions = Array.from(divSelect.querySelectorAll('option'));

                            function updateDivisionOptions(isUserChange = false) {
                                const selectedOrgId = orgSelect.value;
                                const currentSelectedDivId = divSelect.value;

                                divSelect.innerHTML = '';
                                let hasMatchingSelected = false;

                                allDivOptions.forEach(option => {
                                    const orgId = option.getAttribute('data-organisation-id');
                                    if (!orgId || orgId === selectedOrgId) {
                                        const clonedOpt = option.cloneNode(true);
                                        if (clonedOpt.value === currentSelectedDivId && currentSelectedDivId !== "") {
                                            clonedOpt.selected = true;
                                            hasMatchingSelected = true;
                                        } else if (currentSelectedDivId === "" && clonedOpt.value === "") {
                                            clonedOpt.selected = true;
                                        } else {
                                            clonedOpt.selected = false;
                                        }
                                        divSelect.appendChild(clonedOpt);
                                    }
                                });

                                if (isUserChange && !hasMatchingSelected) {
                                    divSelect.value = '';
                                }
                            }

                            orgSelect.addEventListener('change', function() {
                                updateDivisionOptions(true);
                            });

                            updateDivisionOptions(false);
                        }
                    });
                </script>

                @if(isset($session))
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                    <select name="is_active" required
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        <option value="1" {{ old('is_active', $session->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ old('is_active', $session->is_active) == 0 ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($session) ? 'Perbarui' : 'Buat' }} Sesi
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </span>
                    <h3 class="text-base font-bold font-outfit uppercase tracking-wider">Inspirasi Harian</h3>
                    <p class="text-sm font-medium leading-relaxed opacity-95 italic">
                        "Kedisiplinan bukanlah pembatasan kebebasan, melainkan fondasi utama untuk membangun integritas dan mencapai kesuksesan yang berkelanjutan."
                    </p>
                    <p class="text-xs font-bold text-white/80 text-right">— Orens Pro Team</p>
                </div>
            </div>

            <!-- Card 2: Quick tips/information -->
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 font-outfit">
                    <span class="w-1.5 h-5 bg-orens rounded-full"></span>
                    Panduan Presensi
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">1</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Penentuan Koordinat</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Disarankan menggunakan GPS perangkat saat berada langsung di lokasi latihan ekskul untuk akurasi optimal.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">2</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Radius Aman</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Atur radius 50-100 meter untuk memberikan toleransi jika sensor GPS perangkat siswa mengalami pergeseran titik koordinat.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">3</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Sesi Otomatis</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Siswa yang tidak melakukan check-in mandiri setelah waktu berakhir akan otomatis ditandai Alpha oleh sistem.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
