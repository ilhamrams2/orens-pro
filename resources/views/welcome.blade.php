<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orens Pro - Sistem Presensi Digital Cerdas & Anti-Cheat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        .circle-blur {
            filter: blur(80px);
            animation: float-bg 15s infinite ease-in-out;
        }
        @keyframes float-bg {
            0% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, 50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0, 0) scale(1); }
        }
    </style>
</head>
<body class="bg-background text-text-primary min-h-screen overflow-x-hidden font-sans">
    
    <!-- Background Blur Ornaments -->
    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="circle-blur absolute w-[500px] h-[500px] rounded-full bg-[#FF6B00]/10 -top-[150px] -right-[150px]"></div>
        <div class="circle-blur absolute w-[400px] h-[400px] rounded-full bg-blue-500/5 -bottom-[100px] -left-[100px]" style="animation-delay: -5s;"></div>
    </div>

    <!-- Navigation Header -->
    <header class="sticky top-0 z-50 bg-white/70 backdrop-blur-xl border-b border-gray-100/80">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Orens Pro" class="w-10 h-10 object-contain">
                <div>
                    <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-text-primary to-gray-700 bg-clip-text text-transparent">Orens Pro</span>
                    <span class="block text-[10px] text-text-secondary font-semibold tracking-widest uppercase">Absensi Ekskul Cerdas</span>
                </div>
            </div>
            
            <nav class="hidden md:flex space-x-8 text-sm font-semibold text-text-secondary">
                <a href="#fitur" class="hover:text-orens transition-colors">Fitur Utama</a>
                <a href="#cara-kerja" class="hover:text-orens transition-colors">Cara Kerja</a>
                <a href="#hak-akses" class="hover:text-orens transition-colors">Hak Akses</a>
                <a href="/panduan.html" class="hover:text-orens transition-colors flex items-center gap-1">
                    Panduan
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </nav>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-slate-800 transition-all shadow-premium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white font-bold text-sm hover:bg-slate-800 transition-all shadow-premium">
                        Masuk Sistem
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 py-12">
        
        <!-- Hero Section -->
        <section class="grid lg:grid-cols-12 gap-12 items-center mb-24">
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-orens-bg border border-orens-light/20 text-orens text-xs font-bold uppercase tracking-wider">
                    <span>Orens Pro v1.0</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-orens animate-pulse"></span>
                    <span>Anti-Cheat Presensi</span>
                </div>
                <h1 class="font-extrabold text-4xl md:text-5xl lg:text-6xl tracking-tight leading-tight">
                    Revolusi Absensi Digital Ekskul<br>
                    <span class="bg-gradient-to-r from-orens to-orens-light bg-clip-text text-transparent">Presisi & Akurat.</span>
                </h1>
                <p class="text-text-secondary text-base md:text-lg leading-relaxed max-w-xl">
                    Sistem kehadiran berbasis wilayah (Geofencing GPS) dan enkripsi dinamis (Rotating QR Code) untuk menghilangkan praktik manipulasi presensi kegiatan ekstrakurikuler di sekolah.
                </p>
                <div class="flex flex-wrap gap-4 pt-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-orens to-orens-light text-white font-bold text-sm hover:shadow-lg shadow-orens/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Buka Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-2xl bg-gradient-to-r from-orens to-orens-light text-white font-bold text-sm hover:shadow-lg shadow-orens/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                            Mulai Presensi Sekarang
                        </a>
                    @endauth
                    <a href="/panduan.html" class="px-6 py-3.5 rounded-2xl bg-white border border-gray-200 text-text-primary font-bold text-sm hover:bg-gray-50 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Pelajari Cara Kerja
                    </a>
                </div>
            </div>
            
            <!-- Hero Mockup Card -->
            <div class="lg:col-span-5 relative">
                <!-- Decorative element behind mockup -->
                <div class="absolute -inset-4 bg-gradient-to-r from-orens to-blue-500 rounded-3xl opacity-10 blur-2xl z-0"></div>
                
                <div class="relative z-10 glass-card p-6 border-white/60 shadow-premium">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-6">
                        <div class="flex items-center space-x-2">
                            <span class="w-3.5 h-3.5 rounded-full bg-red-500"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-yellow-500"></span>
                            <span class="w-3.5 h-3.5 rounded-full bg-green-500"></span>
                        </div>
                        <span class="text-[10px] font-bold text-orens uppercase tracking-wider bg-orens-bg px-2.5 py-1 rounded-md">Demo Live Absensi</span>
                    </div>

                    <!-- Geofencing Visual Preview -->
                    <div class="space-y-4">
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100/50">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-bold text-text-secondary">Koordinat Sesi</span>
                                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">Aktif</span>
                            </div>
                            <p class="font-mono text-[11px] text-text-primary">-6.312948, 106.840291 (Gedung Prima)</p>
                        </div>

                        <!-- Distance Simulation -->
                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100/50">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-bold text-text-secondary">Uji Jarak Perangkat</span>
                                <span class="text-xs font-extrabold text-orens" id="hero-dist-val">12 Meter</span>
                            </div>
                            <div class="w-full bg-gray-200 h-2 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-orens to-orens-light h-full rounded-full transition-all duration-1000" id="hero-dist-bar" style="width: 12%;"></div>
                            </div>
                            <div class="flex justify-between text-[9px] text-text-secondary mt-1.5 font-bold">
                                <span>Aman (0m)</span>
                                <span>Batas Radius (100m)</span>
                            </div>
                        </div>

                        <!-- Scan Status Card -->
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center space-x-3" id="hero-status-card">
                            <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold text-lg">✓</div>
                            <div>
                                <h4 class="text-xs font-bold text-emerald-800" id="hero-status-title">Presensi Diterima</h4>
                                <p class="text-[10px] text-emerald-600" id="hero-status-desc">Berada dalam radius aman absensi.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-24">
            <div class="glass-card p-6 text-center border-gray-100">
                <span class="block text-3xl font-extrabold font-outfit text-orens mb-1">100%</span>
                <span class="text-xs text-text-secondary font-semibold uppercase tracking-wider">Akurasi Geofencing</span>
            </div>
            <div class="glass-card p-6 text-center border-gray-100">
                <span class="block text-3xl font-extrabold font-outfit text-orens mb-1">30 Detik</span>
                <span class="text-xs text-text-secondary font-semibold uppercase tracking-wider">Rotasi Kode QR</span>
            </div>
            <div class="glass-card p-6 text-center border-gray-100">
                <span class="block text-3xl font-extrabold font-outfit text-orens mb-1">Dual Domain</span>
                <span class="text-xs text-text-secondary font-semibold uppercase tracking-wider">Keamanan Email Sekolah</span>
            </div>
            <div class="glass-card p-6 text-center border-gray-100">
                <span class="block text-3xl font-extrabold font-outfit text-orens mb-1">Real-time</span>
                <span class="text-xs text-text-secondary font-semibold uppercase tracking-wider">Log Aktivitas & Audit</span>
            </div>
        </section>

        <!-- Fitur Section -->
        <section id="fitur" class="mb-24 scroll-mt-24">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold text-orens uppercase tracking-widest">Proteksi Berlapis</span>
                <h2 class="font-extrabold text-3xl md:text-4xl mt-1 mb-4">Fitur Utama Pengaman Presensi</h2>
                <p class="text-text-secondary text-sm leading-relaxed">
                    Kami membangun teknologi keamanan berlapis agar data absensi ekstrakurikuler benar-benar mencerminkan kondisi lapangan yang valid.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Geofencing -->
                <div class="glass-card p-8 border-gray-100 hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-orens-bg text-orens flex items-center justify-center mb-6 shadow-md shadow-orens/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-3">GPS Geofencing Absolut</h3>
                    <p class="text-text-secondary text-xs leading-relaxed">
                        Membatasi absensi hanya jika perangkat siswa berada di dalam radius toleransi (misalnya 100m) dari titik koordinat kegiatan resmi. Mencegah manipulasi lokasi palsu.
                    </p>
                </div>

                <!-- Dynamic QR Code -->
                <div class="glass-card p-8 border-gray-100 hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-6 shadow-md shadow-blue-500/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-3">Dynamic QR Token</h3>
                    <p class="text-text-secondary text-xs leading-relaxed">
                        Kode QR di proyektor berotasi otomatis setiap 30 detik. Scan dengan screenshot lama akan langsung diblokir oleh server demi mencegah kecurangan pengiriman gambar.
                    </p>
                </div>

                <!-- Auto-Alpha & Reporting -->
                <div class="glass-card p-8 border-gray-100 hover:-translate-y-2 transition-transform duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mb-6 shadow-md shadow-indigo-500/5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="font-bold text-lg mb-3">Auto-Alpha & Report</h3>
                    <p class="text-text-secondary text-xs leading-relaxed">
                        Ketika jam sesi absensi berakhir, seluruh anggota yang tidak tercatat hadir akan otomatis diset menjadi **Alpha**. Laporan kehadiran dapat diekspor langsung ke Excel & PDF.
                    </p>
                </div>
            </div>
        </section>

        <!-- Cara Kerja Visual Section -->
        <section id="cara-kerja" class="mb-24 scroll-mt-24 bg-slate-900 text-white rounded-3xl p-8 md:p-12 shadow-xl relative overflow-hidden">
            <!-- Subtle gradient inside dark section -->
            <div class="absolute inset-0 bg-gradient-to-tr from-orens/10 to-transparent pointer-events-none"></div>
            
            <div class="relative z-10 grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="text-xs font-bold text-orens-light uppercase tracking-wider">Alur Kerja Sistem</span>
                    <h2 class="font-extrabold text-3xl mt-1 mb-6 font-outfit">Bagaimana Orens Pro Memvalidasi Kehadiran?</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center font-bold text-sm text-orens-light mt-0.5 border border-white/10">1</div>
                            <div>
                                <h4 class="font-bold text-sm text-white">Pengurus Membuat Sesi</h4>
                                <p class="text-xs text-gray-400 leading-relaxed mt-1">
                                    Pengurus mengeset judul kegiatan, mematok koordinat lokasi, radius absensi, serta jam mulai dan selesai.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center font-bold text-sm text-orens-light mt-0.5 border border-white/10">2</div>
                            <div>
                                <h4 class="font-bold text-sm text-white">Siswa Memindai Kode QR</h4>
                                <p class="text-xs text-gray-400 leading-relaxed mt-1">
                                    Siswa membuka aplikasi, melakukan scan QR code yang berputar dinamis di proyektor kelas, serta menyalakan GPS perangkat.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4">
                            <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center font-bold text-sm text-orens-light mt-0.5 border border-white/10">3</div>
                            <div>
                                <h4 class="font-bold text-sm text-white">Validasi Algoritma Dual-Cek</h4>
                                <p class="text-xs text-gray-400 leading-relaxed mt-1">
                                    Server memeriksa: Apakah token QR valid? Apakah jarak GPS siswa &le; radius toleransi? Jika ya, status "Hadir" sukses disimpan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <!-- Dynamic code snippet representation -->
                    <div class="w-full bg-slate-950 rounded-2xl border border-white/10 p-6 font-mono text-xs text-gray-300">
                        <div class="flex items-center justify-between border-b border-white/10 pb-3 mb-4">
                            <span class="text-gray-400 font-bold">AttendanceService.php</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-orens animate-pulse"></span>
                        </div>
                        <p class="text-green-500">// Verifikasi GPS Geofencing</p>
                        <p class="mt-1"><span class="text-purple-400">$distance</span> = <span class="text-blue-400">$this</span>->calculateDistance(<span class="text-orange-400">$session->latitude</span>, ...);</p>
                        <p class="mt-2"><span class="text-pink-500">if</span> (<span class="text-purple-400">$distance</span> > <span class="text-purple-400">$session</span>->radius) {</p>
                        <p class="ml-4 text-red-400">return $this->logAndFail($user, $session, 'Di luar jangkauan');</p>
                        <p class="text-pink-500">}</p>
                        <p class="mt-4 text-green-500">// Verifikasi Dynamic Token</p>
                        <p class="mt-1"><span class="text-pink-500">if</span> (!<span class="text-purple-400">$session</span>->validateDynamicToken(<span class="text-purple-400">$qrToken</span>)) {</p>
                        <p class="ml-4 text-red-400">return $this->logAndFail($user, $session, 'Token kedaluwarsa');</p>
                        <p class="text-pink-500">}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hak Akses Section -->
        <section id="hak-akses" class="mb-24 scroll-mt-24">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-xs font-bold text-orens uppercase tracking-widest">Tingkatan Wewenang</span>
                <h2 class="font-extrabold text-3xl md:text-4xl mt-1 mb-4">Peran & Otoritas Sistem</h2>
                <p class="text-text-secondary text-sm">
                    Hak akses terbagi menjadi 4 level untuk menjaga privasi, akuntabilitas, dan pembagian tugas pengelolaan kegiatan.
                </p>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                <!-- Superadmin -->
                <div class="glass-card p-6 border-t-4 border-t-red-500 border-x border-b border-gray-100 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-red-500 uppercase tracking-widest bg-red-50 px-2 py-0.5 rounded">Level 4</span>
                        <h4 class="font-bold text-lg mt-3 mb-2">Superadmin</h4>
                        <p class="text-text-secondary text-[11px] leading-relaxed mb-4">
                            Mengelola kegiatan ekstrakurikuler global, menambah pembina ekskul, dan memantau log audit lengkap sistem.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-red-600 bg-red-50/50 py-2 rounded-xl text-center border border-red-100/30">Otoritas Penuh</span>
                </div>

                <!-- Pembina -->
                <div class="glass-card p-6 border-t-4 border-t-orens border-x border-b border-gray-100 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-orens uppercase tracking-widest bg-orens-bg px-2 py-0.5 rounded">Level 3</span>
                        <h4 class="font-bold text-lg mt-3 mb-2">Pembina</h4>
                        <p class="text-text-secondary text-[11px] leading-relaxed mb-4">
                            Melihat riwayat kegiatan ekskul, menambah anggota/pengurus ekskul, mengimpor CSV, dan meriset periode grade keaktifan.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-orens bg-orens-bg py-2 rounded-xl text-center border border-orens-light/10">Otoritas Ekskul</span>
                </div>

                <!-- Pengurus -->
                <div class="glass-card p-6 border-t-4 border-t-blue-500 border-x border-b border-gray-100 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-0.5 rounded">Level 2</span>
                        <h4 class="font-bold text-lg mt-3 mb-2">Pengurus</h4>
                        <p class="text-text-secondary text-[11px] leading-relaxed mb-4">
                            Membuat sesi absensi divisi, memimpin rapat, menayangkan QR code, dan mencatat absensi manual jika ada kendala.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 py-2 rounded-xl text-center border border-blue-100">Otoritas Divisi</span>
                </div>

                <!-- Member -->
                <div class="glass-card p-6 border-t-4 border-t-emerald-500 border-x border-b border-gray-100 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2 py-0.5 rounded">Level 1</span>
                        <h4 class="font-bold text-lg mt-3 mb-2">Member (Siswa)</h4>
                        <p class="text-text-secondary text-[11px] leading-relaxed mb-4">
                            Melakukan pemindaian QR absensi mandiri, mengirimkan data lokasi, dan memantau status grade keaktifannya.
                        </p>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 bg-emerald-50 py-2 rounded-xl text-center border border-emerald-100">Akses Absensi</span>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="glass-card p-8 md:p-12 border-gray-100 shadow-premium text-center max-w-4xl mx-auto mb-16 relative overflow-hidden">
            <!-- Background orange sphere -->
            <div class="absolute -right-24 -top-24 w-48 h-48 rounded-full bg-[#FF6B00]/5 blur-2xl"></div>
            
            <h2 class="font-extrabold text-3xl font-outfit mb-4">Mulai Pencatatan Presensi Terpercaya</h2>
            <p class="text-text-secondary text-sm max-w-lg mx-auto mb-8">
                Gunakan email sekolah Prestasi Prima Anda untuk masuk ke sistem dan mencatat kehadiran Anda hari ini.
            </p>
            <div class="flex justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-orens to-orens-light text-white font-bold text-sm hover:shadow-lg shadow-orens/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Masuk ke Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-8 py-4 rounded-2xl bg-gradient-to-r from-orens to-orens-light text-white font-bold text-sm hover:shadow-lg shadow-orens/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Masuk Sistem Sekarang
                    </a>
                @endauth
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-3 gap-8">
            <div>
                <h4 class="font-bold text-lg text-orens-light mb-4 font-outfit">Orens Pro</h4>
                <p class="text-xs text-gray-400 leading-relaxed max-w-sm">
                    Sistem Absensi Ekstrakurikuler Digital Terpercaya untuk SMK & SMA Prestasi Prima.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-sm text-gray-200 mb-4 uppercase tracking-wider">Pranala</h4>
                <ul class="text-xs text-gray-400 space-y-2">
                    <li><a href="#fitur" class="hover:text-white transition-colors">Fitur</a></li>
                    <li><a href="#cara-kerja" class="hover:text-white transition-colors">Alur Kerja</a></li>
                    <li><a href="#hak-akses" class="hover:text-white transition-colors">Pembagian Peran</a></li>
                    <li><a href="/panduan.html" class="hover:text-white transition-colors">Dokumentasi</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm text-gray-200 mb-4 uppercase tracking-wider">Dukungan Admin</h4>
                <p class="text-xs text-gray-400 leading-relaxed mb-3">
                    Jika ada kesalahan pemindaian atau koordinat GPS tidak terbaca, silakan hubungi tim IT.
                </p>
                <span class="block text-xs font-bold text-orens-light">admin@smkprestasiprima.sch.id</span>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 border-t border-slate-800 mt-12 pt-6 text-center text-xs text-gray-500 flex flex-col md:flex-row justify-between items-center gap-4">
            <span>&copy; 2026 Orens Pro. Hak Cipta Dilindungi.</span>
            <span>Institusi Pendidikan Prestasi Prima</span>
        </div>
    </footer>

    <!-- Script Hero Mockup Animation -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const bar = document.getElementById('hero-dist-bar');
            const val = document.getElementById('hero-dist-val');
            const title = document.getElementById('hero-status-title');
            const desc = document.getElementById('hero-status-desc');
            const card = document.getElementById('hero-status-card');

            let state = 0; // 0 = Safe, 1 = Far

            setInterval(() => {
                if (state === 0) {
                    // Change to Far
                    bar.style.width = '85%';
                    val.innerText = '150 Meter';
                    val.classList.remove('text-orens');
                    val.classList.add('text-red-600');
                    
                    card.classList.remove('bg-emerald-50', 'border-emerald-100');
                    card.classList.add('bg-red-50', 'border-red-100');
                    card.querySelector('.w-8').classList.remove('text-emerald-600', 'bg-emerald-500/10');
                    card.querySelector('.w-8').classList.add('text-red-600', 'bg-red-500/10');
                    card.querySelector('.w-8').innerText = '✗';
                    
                    title.innerText = 'Presensi Ditolak';
                    title.classList.remove('text-emerald-800');
                    title.classList.add('text-red-800');
                    
                    desc.innerText = 'Anda berada di luar radius aman (maksimal 100m).';
                    desc.classList.remove('text-emerald-600');
                    desc.classList.add('text-red-600');
                    
                    state = 1;
                } else {
                    // Change to Safe
                    bar.style.width = '12%';
                    val.innerText = '12 Meter';
                    val.classList.remove('text-red-600');
                    val.classList.add('text-orens');
                    
                    card.classList.remove('bg-red-50', 'border-red-100');
                    card.classList.add('bg-emerald-50', 'border-emerald-100');
                    card.querySelector('.w-8').classList.remove('text-red-600', 'bg-red-500/10');
                    card.querySelector('.w-8').classList.add('text-emerald-600', 'bg-emerald-500/10');
                    card.querySelector('.w-8').innerText = '✓';
                    
                    title.innerText = 'Presensi Diterima';
                    title.classList.remove('text-red-800');
                    title.classList.add('text-emerald-800');
                    
                    desc.innerText = 'Berada dalam radius aman absensi.';
                    desc.classList.remove('text-red-600');
                    desc.classList.add('text-emerald-600');
                    
                    state = 0;
                }
            }, 4000);
        });
    </script>
</body>
</html>
