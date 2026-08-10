@extends('layouts.dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2">
        <a href="{{ route('users.index') }}" class="text-gray-400 hover:text-orens flex items-center gap-2 text-sm font-bold transition-all w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Kembali ke Daftar
        </a>
        <x-ui.header :title="(isset($user) ? 'Edit' : 'Tambah') . ' Pengguna'" subtitle="Konfigurasi kredensial dan hak akses pengguna." />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Form Column -->
        <div class="lg:col-span-2">
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm">
        <form action="{{ isset($user) ? route('users.update', $user) : route('users.store') }}" method="POST">
            @csrf
            @if(isset($user)) @method('PUT') @endif

            <div class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                            placeholder="Nama Lengkap">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Domain Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" required
                            class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                            placeholder="user@smkprestasiprima.sch.id">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi {{ isset($user) ? '(Biarkan kosong jika tidak ingin diubah)' : '' }}</label>
                    <input type="password" name="password" {{ isset($user) ? '' : 'required' }}
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all"
                        placeholder="••••••••">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    @if(in_array(auth()->user()->role, ['pembina', 'superadmin']))
                    <div>
                         <label class="block text-sm font-bold text-gray-700 mb-2">Peran</label>
                         <select name="role" required
                             class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                             <option value="member" {{ old('role', $user->role ?? '') === 'member' ? 'selected' : '' }}>Member</option>
                             <option value="pengurus" {{ old('role', $user->role ?? '') === 'pengurus' ? 'selected' : '' }}>Pengurus</option>
                             <option value="pembina" {{ old('role', $user->role ?? '') === 'pembina' ? 'selected' : '' }}>Pembina</option>
                             @if(auth()->user()->role === 'superadmin')
                                 <option value="superadmin" {{ old('role', $user->role ?? '') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                             @endif
                         </select>
                         @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                         <label class="block text-sm font-bold text-gray-700 mb-2">Organisasi</label>
                         <select name="organisation_id" required
                             class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                             @foreach($organisations as $org)
                                 <option value="{{ $org->id }}" {{ old('organisation_id', $user->organisation_id ?? '') == $org->id ? 'selected' : '' }}>
                                     {{ $org->name }}
                                 </option>
                             @endforeach
                         </select>
                         @error('organisation_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    @else
                    <div class="col-span-2 bg-gray-50 p-4 rounded-2xl flex items-center justify-between border border-gray-100">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Divisi</p>
                            <p class="text-sm font-bold text-gray-700">{{ $user->division->name ?? 'Tanpa Divisi' }}</p>
                        </div>
                        <div class="px-3 py-1 bg-orens/10 text-orens rounded-full text-[10px] font-bold uppercase">
                            {{ auth()->user()->role === 'pembina' ? 'Pembina' : (auth()->user()->role === 'pengurus' ? 'Pengurus' : auth()->user()->role) }}
                        </div>
                    </div>
                    @endif
                </div>

                @if(in_array(auth()->user()->role, ['pembina', 'superadmin']))
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Divisi (Opsional)</label>
                    <select name="division_id"
                        class="w-full p-4 rounded-xl border border-gray-100 bg-gray-50/50 outline-none focus:border-orens focus:ring-4 focus:ring-orens/10 transition-all">
                        <option value="">Tanpa Divisi</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" data-organisation-id="{{ $div->organisation_id }}" {{ old('division_id', $user->division_id ?? '') == $div->id ? 'selected' : '' }}>
                                {{ $div->name }} ({{ $div->organisation->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('division_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="w-full bg-orens text-white p-4 rounded-xl font-bold hover:bg-orens-light transition-all shadow-lg shadow-orens/20">
                        {{ isset($user) ? 'Perbarui' : 'Buat' }} Akun Pengguna
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </span>
                    <h3 class="text-base font-bold font-outfit uppercase tracking-wider">Inspirasi Harian</h3>
                    <p class="text-sm font-medium leading-relaxed opacity-95 italic">
                        "Kepemimpinan yang baik dimulai dari pengelolaan data anggota yang rapi. Setiap anggota memiliki potensi besar untuk dikembangkan."
                    </p>
                    <p class="text-xs font-bold text-white/80 text-right">— Orens Pro Team</p>
                </div>
            </div>

            <!-- Card 2: Quick tips/information -->
            <div class="bg-white p-8 rounded-[32px] border border-gray-100 shadow-sm space-y-6">
                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2 font-outfit">
                    <span class="w-1.5 h-5 bg-orens rounded-full"></span>
                    Panduan Anggota
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">1</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Tentukan Peran</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Pilih Member untuk siswa biasa, Pengurus untuk koordinator divisi, dan Pembina untuk guru penanggung jawab.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">2</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Email Resmi Sekolah</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Gunakan email sekolah (@smkprestasiprima.sch.id / @smaprestasiprima.sch.id) untuk autentikasi yang sah.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-xl bg-orange-50 text-orens flex items-center justify-center shrink-0 font-bold text-xs">3</div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-700">Hubungkan Divisi</h4>
                            <p class="text-[11px] text-gray-400 font-medium leading-relaxed mt-0.5">Pastikan anggota dimasukkan ke divisi yang sesuai agar otomatis terdaftar pada sesi absensi divisi tersebut.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.querySelector('select[name="role"]');
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

            if (roleSelect) {
                const toggleRequired = () => {
                    if (roleSelect.value === 'superadmin') {
                        orgSelect.removeAttribute('required');
                        orgSelect.disabled = true;
                        orgSelect.value = '';
                        if (divSelect) {
                            divSelect.disabled = true;
                            divSelect.value = '';
                        }
                    } else {
                        orgSelect.setAttribute('required', 'required');
                        orgSelect.disabled = false;
                        if (divSelect) {
                            divSelect.disabled = false;
                        }
                    }
                    updateDivisionOptions(false);
                };
                roleSelect.addEventListener('change', toggleRequired);
                toggleRequired(); // Run initially
            } else {
                updateDivisionOptions(false);
            }
        }
    });
</script>
@endsection
